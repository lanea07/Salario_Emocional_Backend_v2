<?php

namespace App\Services;

use App\Enums\BenefitActionIsEnum;
use App\Enums\BenefitDecisionEnum;
use App\Events\BenefitDecisionEvent;
use App\Events\NewBenefitUserWithLeaderEvent;
use App\Events\NewBenefitUserWithoutLeaderEvent;
use App\Mail\BenefitUserExcelExport;
use App\Models\Benefit;
use App\Models\BenefitDetail;
use App\Models\BenefitUser;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Spatie\IcalendarGenerator\Enums\RecurrenceDay;
use Spatie\IcalendarGenerator\Enums\RecurrenceFrequency;
use Spatie\IcalendarGenerator\Properties\TextProperty;
use Spatie\IcalendarGenerator\ValueObjects\RRule;
use Yajra\DataTables\Facades\DataTables;

class BenefitUserService
{

    /**
     * Returns all the benefits of a user
     * 
     * @param int $userId
     * @param int $year
     * @return Collection
     */
    public function getAllBenefitUser(int $userId, int $year): Collection
    {
        return User::with([
            'benefit_user' => function ($q) use ($year) {
                $q->is_approved();
                $q->whereYear('benefit_begin_time', $year);
                $q->orderBy('benefit_begin_time');
            },
            'benefit_user.benefits',
            'benefit_user.benefit_detail',
            'benefit_user.user.dependency'
        ])
            ->where('id', $userId)
            ->get()
            ->each(function ($model) {
                return $model->benefit_user->each(function ($related) {
                    $related->benefits->setAppends(['encoded_logo']);
                });
            });
    }

    /**
     * Saves a new benefit for a user
     * 
     * @param array $benefitUserData
     * @return BenefitUser
     */
    public function saveBenefitUser(array $benefitUserData): BenefitUser
    {
        $created = DB::transaction(function () use ($benefitUserData) {
            $requestedBenefit = Benefit::find($benefitUserData['benefit_id']);
            $bancoHoras = new Collection();
            $miViernes = new Collection();

            $this->validateBenefitRules($benefitUserData, $requestedBenefit, BenefitActionIsEnum::CREATE);
            $benefitUserData = BenefitUser::create($benefitUserData);
            $benefitUserData = $benefitUserData->load(['user', 'benefits', 'benefit_detail', 'user.leader_user']);

            //Get additional benefits info
            if ($requestedBenefit->name === 'Mi Banco de Horas') {
                $bancoHoras = $this->getAdditionalBancoHoras($benefitUserData);
            }
            if ($requestedBenefit->name === 'Mi Viernes') {
                $miViernes = $this->getAdditionalMiViernes($benefitUserData);
            }

            // Auto approve new benefits
            if ($benefitUserData->benefits->settings()->get('is_auto_approve_new')) {
                $this->decideBenefitUser('approve', "Aprobado automáticamente", $benefitUserData);
                return $benefitUserData;
            }

            if ($benefitUserData->user->leader_user !== null) {
                $leader = $benefitUserData->user->leader_user;
                if ($leader->settings()->get('Auto Aprobar Beneficios de mis Colaboradores') === 'Sí') {
                    $this->decideBenefitUser('approve', "Aprobado automáticamente", $benefitUserData);
                } else {
                    $data = [$benefitUserData, $bancoHoras, $miViernes];
                    event(new NewBenefitUserWithLeaderEvent($benefitUserData->user, $data));
                }
            } else {
                $benefitUserData->is_approved = BenefitDecisionEnum::APPROVED->value;
                $benefitUserData->approved_at = Carbon::now();
                $benefitUserData->approved_by = auth()->user()->id;
                $benefitUserData->save();
                event(new NewBenefitUserWithoutLeaderEvent($benefitUserData->user, $benefitUserData));
            }
            return $benefitUserData;
        });
        return $created;
    }

    /**
     * Returns a benefit by its ID
     * 
     * @param BenefitUser $benefitUser
     * @return Collection
     */
    public function getBenefitUserByID(BenefitUser $benefitUser): Collection
    {
        return User::with([
            'benefit_user' => function ($q) use ($benefitUser) {
                $q->where('id', $benefitUser->id);
                $q->orderBy('benefit_begin_time');
            },
            'benefit_user.benefits',
            'benefit_user.benefit_detail',
            'benefit_user.user.dependency'
        ])->wherehas('benefit_user', function ($q) use ($benefitUser) {
            $q->where('id', '=', $benefitUser->id);
            $q->orderBy('benefit_begin_time');
        })->get();
    }

    /**
     * Updates a benefit for a user
     * 
     * @param array $benefitUserData
     * @param BenefitUser $benefitUser
     * @return BenefitUser
     */
    public function updateBenefitUser(array $benefitUserData, BenefitUser $benefitUser): BenefitUser
    {
        $updated = DB::transaction(function () use ($benefitUserData, $benefitUser) {
            $requestedBenefit = Benefit::find($benefitUserData['benefit_id']);
            $this->validateBenefitRules($benefitUserData, $requestedBenefit, BenefitActionIsEnum::UPDATE);
            $benefitUser->update($benefitUserData);
            return $benefitUser;
        });
        return $updated;
    }

    /**
     * Deletes a benefit for a user
     * 
     * @param BenefitUser $benefitUser
     * @return void
     */
    public function deleteBenefitUser(BenefitUser $benefitUser): void
    {
        DB::transaction(function () use ($benefitUser) {
            $benefitUser->delete();
        });
    }

    /**
     * Returns all the benefits of a user that are not approved
     * 
     * @param int $userId
     * @return Collection
     */
    public function getAllBenefitUserNonApproved(int $userId)
    {
        $model = BenefitUser::with([
            'benefits',
            'benefit_detail',
            'user.dependency'
        ])
            ->where('user_id', $userId)
            ->where('is_approved', false);
        return DataTables::of($model)->toJson()->getData();
    }

    /**
     * Returns all the benefits of users that are not approved
     * 
     * @param Request $request
     * @return Collection
     */
    public function getAllBenefitCollaboratorsNonApproved(Request $request)
    {
        $user = $request->user();
        $model = BenefitUser::withWhereHas(
            'user',
            function ($q) use ($user) {
                $q->with('descendantsAndSelf');
            }
        )
            ->with(['benefits', 'benefit_detail'])
            ->whereRelation('user', function ($q) use ($user) {
                $q->whereIn('id', $user->descendants()->pluck('id'));
            })->is_pending();
        return DataTables::of($model)->toJson()->getData();
    }

    /**
     * Returns all the benefits of user descendants and self
     * 
     * @param Request $request
     * @return Collection
     */
    public function getAllBenefitCollaborators(Request $request)
    {
        $user = $request->user();
        return User::where('id', '=', $user->id)->with([
            'descendantsAndSelf.benefit_user' => function ($q) use ($request) {
                $q->whereYear('benefit_begin_time', $request->year);
                $q->is_approved();
                $q->when(request()->has('benefit_id'), function ($q) use ($request) {
                    $q->where('benefit_id', '=', $request->benefit_id);
                });
            },
            'descendantsAndSelf.benefit_user.benefits' => function ($q) use ($request) {
                $q->select('id', 'name', 'politicas_path', 'logo_file');
            },
            'descendantsAndSelf.benefit_user.benefit_detail' => function ($q) {
                $q->select('id', 'name', 'time_hours', 'valid_id');
            },
            'descendantsAndSelf.benefit_user.user' => function ($q) {
                $q->select('id', 'name',);
            },
        ])->oldest('name')
            ->get();
    }

    /**
     * Returns an *.ics file to be attached to the new benefit
     * 
     * @param BenefitUser $benefitUser
     * @return object
     */
    static function generateICS(BenefitUser $benefitUser)
    {
        $event = self::generateCalendarEvent($benefitUser);
        $icsAttachment = Calendar::create($benefitUser->user->email)->event([$event]);
        return $icsAttachment->get();
    }

    static private function generateCalendarEvent(BenefitUser $benefitUser): Event
    {
        $newEvent = null;
        if ($benefitUser->benefits->name === "Viernes Corto") {
            $month = date("M", strtotime($benefitUser['benefit_begin_time']));
            $year = date("Y", strtotime($benefitUser['benefit_begin_time']));
            $stopDate = new Carbon("third friday of {$month} {$year}");
            $stopDate = $stopDate->addDay(1);
            $newEvent = Event::create()
                ->name($benefitUser->benefits->name)
                ->createdAt(new DateTime(Carbon::now(), new DateTimeZone('America/Bogota')))
                ->startsAt(new DateTime($benefitUser->benefit_begin_time, new DateTimeZone('America/Bogota')))
                ->endsAt(new DateTime($benefitUser->benefit_end_time, new DateTimeZone('America/Bogota')))
                ->appendProperty(TextProperty::create('X-MICROSOFT-CDO-BUSYSTATUS', 'OOF'))
                ->rrule(
                    RRule::frequency(RecurrenceFrequency::weekly())->interval(2)->onWeekDay(
                        RecurrenceDay::friday()
                    )->until($stopDate)
                );
        } else {
            $newEvent = Event::create()
                ->name($benefitUser->benefits->name)
                ->createdAt(new DateTime(Carbon::now(), new DateTimeZone('America/Bogota')))
                ->startsAt(new DateTime($benefitUser->benefit_begin_time, new DateTimeZone('America/Bogota')))
                ->endsAt(new DateTime($benefitUser->benefit_end_time, new DateTimeZone('America/Bogota')))
                ->appendProperty(TextProperty::create('X-MICROSOFT-CDO-BUSYSTATUS', 'OOF'));
        }
        return $newEvent;
    }

    /**
     * Decides if a benefit is approved or rejected
     * 
     * @param string $decision
     * @param string $decision_comment
     * @param BenefitUser $benefitUser
     * @return BenefitUser
     */
    public function decideBenefitUser(string $decision, string $decision_comment = null, BenefitUser $benefitUser)
    {
        $decision = DB::transaction(function () use ($decision, $decision_comment, $benefitUser) {
            switch ($decision) {
                case 'approve':
                    $benefitUser->is_approved = BenefitDecisionEnum::APPROVED->value;
                    break;
                case 'reject':
                    $benefitUser->is_approved = BenefitDecisionEnum::DENIED->value;
                    break;
                default:
                    throw new Exception("No se pudo reconocer la acción. Intente más tarde", 1);
                    break;
            }
            $benefitUser->approved_at = Carbon::now();
            $benefitUser->approved_by = auth()->user()->id ?? 1;
            $benefitUser->decision_comment = $decision_comment;

            if ($benefitUser->benefits->name === "Viernes Corto" && $decision === 'approve') {
                $month = date("M", strtotime($benefitUser['benefit_begin_time']));
                $year = date("Y", strtotime($benefitUser['benefit_begin_time']));
                $firstFridayMonth = new Carbon("first friday of {$month} {$year}");
                $firstFridayMonth = $firstFridayMonth->addHours(13)->addMinute(30);
                $benefitUser->benefit_begin_time = $firstFridayMonth->format('Y-m-d H:i:s');
                $benefitUser->benefit_end_time = $firstFridayMonth->addHours(3)->addMinutes(30)->format('Y-m-d H:i:s');

                $secondBenefit = new BenefitUser();
                $secondBenefit->forceFill($benefitUser->only([
                    'benefit_id',
                    'benefit_detail_id',
                    'user_id',
                    'is_approved',
                    'approved_at',
                    'request_comment',
                    'decision_comment',
                ]));
                $secondBenefit->created_at = $benefitUser->created_at;
                $secondBenefit->approved_at = Carbon::now();
                $secondBenefit->approved_by = auth()->user()->id;
                $lastFridayMonth = new Carbon("third friday of {$month} {$year}");
                $lastFridayMonth = $lastFridayMonth->addHours(13)->addMinute(30);
                $secondBenefit->benefit_begin_time = $lastFridayMonth->format('Y-m-d H:i:s');
                $secondBenefit->benefit_end_time = $lastFridayMonth->addHours(3)->addMinutes(30)->format('Y-m-d H:i:s');
                $secondBenefit->save();
            }
            $benefitUser->save();
            event(new BenefitDecisionEvent($benefitUser));
            return $benefitUser;
        });
        return $decision;
    }

    /**
     * Exports the benefits of a user and descendants to an Excel file and mails it
     * 
     * @param Request $request
     * @return void
     */
    public function exportBenefits(Request $request)
    {
        $year = $request->years;
        $user_id = auth()->user()->id;
        $data = ['year' => $year, 'user_id' => $user_id];
        Mail::to(auth()->user()->email)->queue(new BenefitUserExcelExport($data));
    }

    /**
     * Returns all the additional Banco de Horas benefits of a user
     * 
     * @param BenefitUser $benefitUserData
     * @return Collection
     */
    private function getAdditionalBancoHoras(BenefitUser $benefitUserData): Collection
    {
        return BenefitUser::with(['benefit_detail'])->where(
            function ($q) use ($benefitUserData) {
                $q->where('user_id', $benefitUserData->user_id);
                $q->where('benefit_id', $benefitUserData->benefit_id);
                $q->where('id', '<>', $benefitUserData->id);
                $q->whereYear('benefit_begin_time', date("Y", strtotime($benefitUserData['benefit_begin_time'])));
                $q->is_Approved();
            }
        )->orderBy('benefit_begin_time')
            ->get();
    }

    /**
     * Returns all the additional Mi Viernes benefits of a user
     * 
     * @param BenefitUser $benefitUserData
     * @return Collection
     */
    private function getAdditionalMiViernes(BenefitUser $benefitUserData): Collection
    {
        return BenefitUser::where(
            function ($q) use ($benefitUserData) {
                $q->where('user_id', $benefitUserData->user_id);
                $q->where('benefit_id', $benefitUserData->benefit_id);
                $q->where('id', '<>', $benefitUserData->id);
                $q->whereYear('benefit_begin_time', date("Y", strtotime($benefitUserData['benefit_begin_time'])));
                $q->is_Approved();
            }
        )->orderBy('benefit_begin_time')
            ->get();
    }

    /**
     * Validates Benefit rules settings, if any of the validations fail an exception is thrown, otherwise it returns true
     * 
     * @param array $requestedBenefitData
     * @param Benefit|null $benefit
     * @param BenefitActionIs $action
     * @return bool
     */
    public function validateBenefitRules(array $requestedBenefitData, Benefit $benefit, BenefitActionIsEnum $action)
    {
        // Benefit settings that must be evaluated according to benefits rules
        $allowedRepeatFrecuency = $benefit->settings()->get('allowed_repeat_frecuency');
        $allowedRepeatInterval = $benefit->settings()->get('allowed_repeat_interval');
        $allowedUpdateApprovedBenefits = $benefit->settings()->get('allowed_to_update_approved_benefits');
        $cantCombineWith = $benefit->settings()->get('cant_combine_with');
        $maxAllowedHours = $benefit->settings()->get('max_allowed_hours');
        $month = Carbon::create($requestedBenefitData['benefit_begin_time'])->month;
        $year = Carbon::create($requestedBenefitData['benefit_begin_time'])->year;

        $this->tryCanCombineWith($cantCombineWith, $benefit, $month, $year, $requestedBenefitData);
        $claimed = $this->tryAllowedRepeatFrecuency($allowedRepeatFrecuency, $month, $year, $requestedBenefitData);
        if ($claimed === true) {
            return $claimed;
        }
        $this->tryMaxAllowedHours($maxAllowedHours, $requestedBenefitData, $claimed);
        $this->tryAllowedRepeatInterval($allowedRepeatInterval, $allowedRepeatFrecuency, $action, $benefit, $claimed, $allowedUpdateApprovedBenefits);
        return true;
    }

    /**
     * Returns all the benefits of a user by its ID
     * 
     * @param User $user
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBenefitUserByUserID(User $user, int $year): Collection
    {
        return User::where('id', '=', $user->id)->with([
            'benefit_user' => function ($q) use ($year) {
                $q->whereYear('benefit_begin_time', $year);
                $q->is_approved();
            },
            'benefit_user.benefits' => function ($q) {
                $q->select('id', 'name', 'politicas_path', 'logo_file');
            },
            'benefit_user.benefit_detail' => function ($q) {
                $q->select('id', 'name', 'time_hours', 'valid_id');
            },
            'benefit_user.user' => function ($q) {
                $q->select('id', 'name',);
            },
        ])->oldest('name')
            ->get()
            ->each(function ($model) {
                return $model->benefit_user->each(function ($related) {
                    $related->benefits->setAppends(['encoded_logo']);
                });
            });
    }

    /**
     * Validates if requested benefit can be combined with other benefits
     * 
     * @param User $user
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tryCanCombineWith($cantCombineWith, $benefit, $month, $year, $requestedBenefitData)
    {
        // Setting cant_combine_with
        if ($cantCombineWith !== $benefit->settings()->getDefault('cant_combine_with') || (is_array($cantCombineWith) && !array_search('no aplica', $cantCombineWith, true))) {
            $forbiddenBenefits = BenefitUser::with(['benefits'])
                ->where(function ($q) use ($month, $year, $cantCombineWith, $requestedBenefitData) {
                    $q->where('user_id', $requestedBenefitData['user_id']);
                    $q->whereYear('benefit_begin_time', $year);
                    $q->whereMonth('benefit_begin_time', $month);
                    $q->whereRelation('benefits', function ($q) use ($cantCombineWith) {
                        $q->whereIn('name', $cantCombineWith);
                    })->get();
                    $q->is_approved();
                })->get();
            $imploded = implode(', ', $cantCombineWith);
            if (!$forbiddenBenefits->isEmpty()) {
                throw new Exception("El beneficio \"$benefit->name\" que está intentando solicitar no se puede combinar con el/los beneficios \"$imploded\". Ya has solicitado uno de estos beneficios en este periodo.");
            }
        }
    }

    /**
     * Returns another instances of the requested benefit and its sum of time_hours used
     * 
     * @param string $allowedRepeatFrecuency
     * @param int $month
     * @param int $year
     * @param array $requestedBenefitData
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function tryAllowedRepeatFrecuency($allowedRepeatFrecuency, $month, $year, $requestedBenefitData): Collection | bool
    {
        $initialDate = null;
        $finalDate = null;
        // Setting allowed_repeat_frecuency
        switch ($allowedRepeatFrecuency) {
            case 'mensual':
                $initialDate = Carbon::create($year, $month, '01', '00', '00', '00')->startOfMonth()->startOfDay()->format('Y-m-d H:i:s');
                $finalDate =  Carbon::create($year, $month, '01', '00', '00', '00')->endOfMonth()->endOfDay()->format('Y-m-d H:i:s');
                break;
            case 'trimestral':
                switch (true) {
                    case ($month >= 1 && $month <= 3):
                        $initialDate = Carbon::create($year, '01', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '03', '31', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                    case ($month >= 4 && $month <= 6):
                        $initialDate = Carbon::create($year, '04', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '06', '30', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                    case ($month >= 7 && $month <= 9):
                        $initialDate = Carbon::create($year, '07', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '09', '30', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                    case ($month >= 10 && $month <= 12):
                        $initialDate = Carbon::create($year, '10', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '12', '31', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                }
                break;
            case 'cuatrimestral':
                switch (true) {
                    case ($month >= 1 && $month <= 4):
                        $initialDate = Carbon::create($year, '01', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '04', '30', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                    case ($month >= 5 && $month <= 8):
                        $initialDate = Carbon::create($year, '05', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '08', '31', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                    case ($month >= 9 && $month <= 12):
                        $initialDate = Carbon::create($year, '09', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '12', '31', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                }
                break;
            case 'semestral':
                switch (true) {
                    case ($month >= 1 && $month <= 6):
                        $initialDate = Carbon::create($year, '01', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '06', '30', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                    case ($month >= 7 && $month <= 12):
                        $initialDate = Carbon::create($year, '07', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                        $finalDate =  Carbon::create($year, '12', '31', '23', '59', '59')->format('Y-m-d H:i:s');
                        break;
                }
                break;
            case 'anual':
                $initialDate = Carbon::create($year, '01', '01', '00', '00', '00')->format('Y-m-d H:i:s');
                $finalDate =  Carbon::create($year, '12', '31', '23', '59', '59')->format('Y-m-d H:i:s');
                break;
            default:
                return true;
                break;
        }

        $claimed = BenefitUser::with(['benefit_detail'])
            ->where(function ($q) use ($requestedBenefitData, $initialDate, $finalDate) {
                $q->where('benefit_id', '=', $requestedBenefitData['benefit_id']);
                $q->where('user_id', '=', $requestedBenefitData['user_id']);
                $q->whereBetween('benefit_begin_time', [$initialDate, $finalDate]);
                $q->is_approved();
            })
            ->withSum('benefit_detail', 'time_hours')
            ->get();
        return $claimed;
    }

    /**
     * Validates if requested benefit exceeds the max_allowed_hours
     * 
     * @param int $maxAllowedHours
     * @param array $requestedBenefitData
     * @param \Illuminate\Database\Eloquent\Collection $claimed
     * @return void
     * @throws Exception
     */
    public function tryMaxAllowedHours($maxAllowedHours, $requestedBenefitData, $claimed)
    {
        // Setting max_allowed_hours
        if ($maxAllowedHours) {
            $requestedTime = BenefitDetail::find($requestedBenefitData['benefit_detail_id'])->time_hours;
            $total_time_hours = $claimed->sum('benefit_detail.time_hours');
            if ($total_time_hours >= $maxAllowedHours) {
                throw new Exception("El beneficio que estás intentando registrar ya tiene utilizado todas las horas disponibles.");
            }
            if ($total_time_hours + $requestedTime > $maxAllowedHours) {
                throw new Exception("El beneficio que estás intentando registrar supera las horas disponibles.");
            }
        }
    }

    /**
     * Validates if requested benefit exceeds the allowed_repeat_interval
     * 
     * @param int $allowedRepeatInterval
     * @param string $allowedRepeatFrecuency
     * @param BenefitActionIs $action
     * @param Benefit $benefit
     * @param \Illuminate\Database\Eloquent\Collection $claimed
     * @param bool $allowedUpdateApprovedBenefits
     * @return void
     * @throws Exception
     */
    public function tryAllowedRepeatInterval($allowedRepeatInterval, $allowedRepeatFrecuency, $action, $benefit, $claimed, $allowedUpdateApprovedBenefits)
    {
        // Setting allowed_repeat_interval
        if ($allowedRepeatInterval) {
            if ($action === BenefitActionIsEnum::UPDATE) {
                if (!$claimed->isEmpty() && $claimed->count() >= $allowedRepeatInterval && !$allowedUpdateApprovedBenefits) {
                    throw new Exception("El beneficio \"$benefit->name\" que está intentando solicitar se puede redimir máximo $allowedRepeatFrecuency $allowedRepeatInterval veces. Ya has solicitado este beneficio {$claimed->count()} veces en este periodo.");
                }
            } else {
                if (!$claimed->isEmpty() && $claimed->count() >= $allowedRepeatInterval) {
                    throw new Exception("El beneficio \"$benefit->name\" que está intentando solicitar se puede redimir máximo $allowedRepeatFrecuency $allowedRepeatInterval veces. Ya has solicitado este beneficio {$claimed->count()} veces en este periodo.");
                }
            }
        }
    }
}

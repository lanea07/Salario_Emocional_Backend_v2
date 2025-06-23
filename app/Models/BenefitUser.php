<?php

namespace App\Models;

use App\Enums\BenefitDecisionEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Throwable;

class BenefitUser extends Model
{

    protected $table = 'benefit_user';

    protected $fillable = [
        'benefit_id',
        'benefit_detail_id',
        'user_id',
        'benefit_begin_time',
        'benefit_end_time',
        'is_approved',
        'approved_at',
        'request_comment',
        'decision_comment',
    ];

    protected $casts = [
        'is_approved' => BenefitDecisionEnum::class,
        'benefit_begin_time' => 'datetime',
        'benefit_end_time' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function benefits()
    {
        return $this->belongsTo(Benefit::class, 'benefit_id', 'id');
    }

    public function benefit_detail()
    {
        return $this->belongsTo(BenefitDetail::class);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        try {
            return $this->where('id', $value)->firstOrFail();
        } catch (Throwable $th) {
            throw new ModelNotFoundException('Beneficio de Colaborador no encontrado');
        }
    }

    public function scopeIs_Pending(Builder $query): void
    {
        $query->where('is_approved', BenefitDecisionEnum::PENDING);
    }

    public function scopeIs_Approved(Builder $query): void
    {
        $query->where('is_approved', BenefitDecisionEnum::APPROVED);
    }

    public function scopeIs_Denied(Builder $query): void
    {
        $query->where('is_approved', BenefitDecisionEnum::DENIED);
    }
}

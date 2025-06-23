<?php

namespace App\Models;

use App\Enums\CarbonBoundariesEnum;
use App\Enums\CarbonTimePeriodsEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{

    protected $primaryKey = 'command';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'command',
        'next_run',
        'last_run'
    ];

    /**
     * Validates if a given artisan command can run in function of the current timestamp and returns a boolean for the command execution conditional.
     * 
     * @param string $command The command to execute
     * @param int $interval Units to be added to next execution
     * @param CarbonTimePeriodsEnum $period Period of time in which interval will be incremented
     * @param string $beginDate Sets initial date job must run
     * 
     * @return bool Returns true if the command should run, false otherwise
     */
    public static function shouldIRun(
        string $command,
        CarbonTimePeriodsEnum $period,
        int $periodInterval,
        CarbonBoundariesEnum $boundary,
        string $beginDate = '',
        int $beginTime = 0,
    ): bool
    {
        $cron = Cron::find($command);
        $now  = Carbon::now();
        $carbonTimePeriod = $period->value;
        $carbonBoundary = $boundary->value;
        $futureRun = new Carbon($now->toDateString());
        $futureRun = $futureRun->$carbonTimePeriod($periodInterval)->$carbonBoundary()->addHours($beginTime);
        if ($cron && $cron->next_run > $now->timestamp) {
            return false;
        }
        if (!$cron && $beginDate) {
            $futureRun = new Carbon($beginDate);
            Cron::updateOrCreate(
                [
                    'command'  => $command,
                    'next_run' => $futureRun->timestamp,
                    'last_run' => Carbon::now()->timestamp
                ]
            );
            return false;
        }
        Cron::updateOrCreate(
            ['command'  => $command],
            [
                'next_run' => $futureRun->timestamp,
                'last_run' => Carbon::now()->timestamp
            ]
        );
        return true;
    }
}

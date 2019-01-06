<?php

namespace App\Enums;

class BotStatusEnum
{
    const IDLE = 'idle';
    const JOB_ASSIGNED = 'job_assigned';
    const OFFLINE = 'offline';
    const WORKING = 'working';
    const WAITING = 'waiting';

    public static function allStates()
    {
        return collect([
            self::IDLE,
            self::JOB_ASSIGNED,
            self::OFFLINE,
            self::WORKING,
            self::WAITING,
        ]);
    }
}

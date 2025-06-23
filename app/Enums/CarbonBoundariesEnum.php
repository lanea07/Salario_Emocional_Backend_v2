<?php

namespace App\Enums;

enum CarbonBoundariesEnum: string
{
    case startOfDay = 'startOfDay';
    case endOfDay = 'endOfDay';
    case startOfMonth = 'startOfMonth';
    case endOfMonth = 'endOfMonth';
    case startOfQuarter = 'startOfQuarter';
    case endOfQuarter = 'endOfQuarter';
    case startOfYear = 'startOfYear';
    case endOfYear = 'endOfYear';
    case startOfDecade = 'startOfDecade';
    case endOfDecade = 'endOfDecade';
    case startOfCentury = 'startOfCentury';
    case endOfCentury = 'endOfCentury';
    case startOfMillennium = 'startOfMillennium';
    case endOfMillennium = 'endOfMillennium';
    case startOfWeek = 'startOfWeek';
    case endOfWeek = 'endOfWeek';
    case startOfHour = 'startOfHour';
    case endOfHour = 'endOfHour';
    case startOfMinute = 'startOfMinute';
    case endOfMinute = 'endOfMinute';
    case startOfSecond = 'startOfSecond';
    case endOfSecond = 'endOfSecond';
    case startOf = 'startOf';
    case endOf = 'endOf';
}

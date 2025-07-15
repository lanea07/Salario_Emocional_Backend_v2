<?php

namespace App\Framework\Traits;

use App\Framework\Exceptions\ForbiddenActionException;
use Illuminate\Support\Facades\Auth;

trait ValidatesPermissions
{

    /**
     * Validates if the current user has assigned the given permissions, if validation fails and endSession is true, the execution is stopped and an exception is thrown
     * 
     * @param array $permissions The permissions to validate
     * @param bool $endSession Indicates if the current session execution is stopped
     * 
     * @return array $hasPermissions If user passes permission validation, an array with its permissions is returned
     * 
     * @throws ForbiddenActionException If validation fails the exception is raised
     */
    public static function hasPermissions(array $permissions, bool $endSession = false)
    {
        $userPermissions = Auth::user()->getAllPermissions()->pluck('id')->toArray();
        $hasPermissions = array_intersect($permissions, $userPermissions);

        if ($endSession && !count($hasPermissions)) {
            throw new ForbiddenActionException();
        }

        return $hasPermissions;
    }
}

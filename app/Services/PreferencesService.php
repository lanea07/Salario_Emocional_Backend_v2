<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PreferencesService
{
    /**
     * Returns all available preferences for a default User model
     * 
     * @return Collection
     */
    public function getAllAvailablePreferences(): Collection
    {
        return collect([(new User)->allowedSetting()]);;
    }

    /**
     * Returns all preferences for the authenticated user
     * 
     * @param User $user
     * @return Collection
     */
    public function userPreferences(User $user): Collection
    {
        return collect([$user->allSettings()]);
    }

    /**
     * Store a set of settings for a user
     *
     * @param  User  $user
     * @param  array  $preferences
     * @return void
     */
    public function savePreferences(User $user, array $preferences): array
    {
        $updated = DB::transaction(function () use ($user, $preferences) {
            $user->setSettings($preferences);
            return ['message' => 'Preferencias actualizadas'];
        });
        return $updated;
    }
}

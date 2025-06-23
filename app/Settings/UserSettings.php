<?php

namespace App\Settings;

use LaravelPropertyBag\Settings\ResourceConfig;

class UserSettings extends ResourceConfig
{
    /**
     * Registered settings for the user. Register settings by setting name. Each
     * setting must have an associative array set as its value that contains an
     * array of 'allowed' values and a single 'default' value.
     *
     * @var array
     */

    protected $registeredSettings = [
        'Auto Aprobar Beneficios de mis Colaboradores' => [
            'allowed' => ['Sí', 'No'],
            'default' => 'No',
            'title' => 'Auto Aprobar Beneficios de mis Colaboradores',
            'description' => 'Aprueba automáticamente los beneficios de los colaboradores a cargo.'
        ],
    ];
}

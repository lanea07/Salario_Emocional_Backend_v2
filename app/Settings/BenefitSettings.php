<?php

namespace App\Settings;

use App\Models\Benefit;
use LaravelPropertyBag\Settings\ResourceConfig;

class BenefitSettings extends ResourceConfig
{

    /**
     * Return a collection of registered settings.
     *
     * @return Collection
     */
    public function registeredSettings()
    {
        $allBenefits = [];
        array_push($allBenefits, 'Ninguno');
        $allBenefits = array_merge($allBenefits, Benefit::pluck('name')->all());

        /**
         * The settings that are allowed to be set for this resource.
         * If new settings are added, make sure to also include settings validation in Benefit model canCreate and canUpdate methods.
         */
        return collect([
            'is_auto_approve_new' => [
                'allowed' => [true, false],
                'default' => false,
                'title' => 'Aprobar automáticamente',
                'description' => 'Aprobar automáticamente este beneficio.'
            ],
            'is_auto_approve_update' => [
                'allowed' => [true, false],
                'default' => false,
                'title' => 'Aprobar automáticamente actualizaciones',
                'description' => 'Aprobar automáticamente las actualizaciones de este beneficio.'
            ],
            'is_full_day' => [
                'allowed' => [true, false],
                'default' => false,
                'title' => 'Es todo el día',
                'description' => 'Indica si el beneficio va de las 00:00 a las 23:59 horas.'
            ],
            'allowed_repeat_frecuency' => [
                'allowed' => ['no aplica', 'mensual', 'trimestral', 'cuatrimestral', 'semestral', 'anual'],
                'default' => 'no aplica',
                'title' => 'Frecuencia de repetición',
                'description' => 'Frecuencia con la que se puede repetir el beneficio. Si no aplica, seleccionar "no aplica".'
            ],
            'max_allowed_hours' => [
                'allowed' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
                'default' => 0,
                'title' => 'Horas máximas permitidas',
                'description' => 'Horas máximas permitidas para este beneficio. Si no aplica, seleccionar "0".'
            ],
            'allowed_repeat_interval' => [
                'allowed' => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31],
                'default' => 0,
                'title' => 'Intervalo de repetición',
                'description' => 'Intervalo con el que se puede repetir el beneficio. Si no aplica, seleccionar "0".'
            ],
            'cant_combine_with' => [
                'allowed' => $allBenefits,
                'default' => 'ninguno',
                'title' => 'No se puede combinar con',
                'description' => 'Otros beneficios con los que no se puede combinar este beneficio.'
            ],
            'allowed_to_update_approved_benefits' => [
                'allowed' => [true, false],
                'default' => false,
                'title' => 'Permitir actualizar beneficios aprobados',
                'description' => 'Permite a los usuarios actualizar beneficios que ya han sido aprobados.'
            ],
            'uses_daterange' => [
                'allowed' => [true, false],
                'default' => false,
                'title' => 'Usa rango de fechas',
                'description' => 'Indica si el beneficio usa un rango de fechas.'
            ],
            'uses_barchart' => [
                'allowed' => [true, false],
                'default' => false,
                'title' => 'Usar gráfico de barras',
                'description' => 'Usar gráfico de barras para mostrar el uso de este beneficio.'
            ],
            'uses_doughnutchart' => [
                'allowed' => [true, false],
                'default' => false,
                'title' => 'Usar gráfico de dona',
                'description' => 'Usar gráfico de dona para mostrar el uso de este beneficio.'
            ],
        ]);
    }
}

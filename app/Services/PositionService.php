<?php

namespace App\Services;

use App\Models\Position;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PositionService
{

    /**
     * Get all positions
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllPositions(): Collection
    {
        return Position::where('id', '<>', 1)->get();
    }

    /**
     * Save a new position
     *
     * @param  array  $positionData
     * @return Position
     */
    public function savePosition(array $positionData): Position
    {
        $created = DB::transaction(function () use ($positionData) {
            return Position::create($positionData);
        });
        return $created;
    }

    /**
     * Get a position by ID
     *
     * @param  Position $position
     * @return Position
     */
    public function getPositionByID(Position $position): Position
    {
        return $position;
    }

    /**
     * Update a position
     *
     * @param  array  $positionData
     * @param  Position $position
     * @return Position
     */
    public function updatePosition(array $positionData, Position $position): Position
    {
        $updated = DB::transaction(function () use ($positionData, $position) {
            return tap($position)->update($positionData);
        });
        return $updated;
    }

    /**
     * Delete a position
     *
     * @param  Position $position
     * @return void
     * @throws \Exception
     */
    public function deletePosition(Position $position): void
    {
        throw new \Exception('No se puede eliminar un cargo');
    }

    public function getDatatable()
    {
        $model = Position::where('id', '<>', 1);
        return DataTables::of($model)->toJson();
    }
}

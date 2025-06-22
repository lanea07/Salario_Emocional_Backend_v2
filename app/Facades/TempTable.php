<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static TempTableService create(string $tableName, string|array|\App\Contracts\TempTableSchemaContract $schema)
 * @method static TempTableService insert(array $data)
 * @method static TempTableService update(array $where, array $data)
 * @method static TempTableService delete(array $where)
 * @method static TempTableService truncate()
 * @method static TempTableService drop()
 * @method static \Illuminate\Support\Collection get()
 * @method static object|null first()
 * @method static bool exists()
 * @method static int count()
 * @method static TempTableService join(string $table, \Closure|string $first, ?string $operator = null, ?string $second = null, string $type = 'inner', bool $where = false)
 * @method static TempTableService leftJoin(string $table, \Closure|string $first, ?string $operator = null, ?string $second = null)
 * @method static TempTableService rightJoin(string $table, \Closure|string $first, ?string $operator = null, ?string $second = null)
 * @method static TempTableService crossJoin(string $table, ?string $first = null, ?string $second = null)
 * @method static TempTableService where(...$parameters)
 * @method static TempTableService orderBy(string $column, string $direction = 'asc')
 * @method static TempTableService limit(int $value)
 * @method static \Illuminate\Database\Query\Builder query()
 *
 * @see \App\Services\TempTableService
 */
class TempTable extends Facade {

    protected static function getFacadeAccessor(): string {
        return 'tempTable';
    }
}

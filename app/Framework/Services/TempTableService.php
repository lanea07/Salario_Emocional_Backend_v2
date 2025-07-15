<?php

namespace App\Framework\Services;

use App\Framework\ValueObjects\TempTableColumn;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class TempTableService
{

    protected string $tableName;
    protected Builder $builder;

    public function create(string $tableName, string|array $schema): static
    {
        $this->tableName = $tableName;

        $sql = is_array($schema)
            ? "CREATE TEMPORARY TABLE {$tableName} (" . $this->buildSchemaFromArray($schema) . ")"
            : (
                str_starts_with(strtolower(trim($schema)), 'select')
                ? "CREATE TEMPORARY TABLE {$tableName} AS {$schema}"
                : "CREATE TEMPORARY TABLE {$tableName} ({$schema})"
            );

        DB::statement($sql);
        $this->builder = DB::table($this->tableName);

        return $this;
    }

    protected function buildSchemaFromArray(array $schema): string
    {
        return collect($schema)->map(function (TempTableColumn $column) {
            return trim("{$column->name} {$column->type} {$column->options}");
        })->implode(', ');
    }

    public function insert(array $data): static
    {
        $this->builder->insert($data);
        return $this;
    }

    public function update(array $where, array $data): static
    {
        $this->builder->where($where)->update($data);
        return $this;
    }

    public function delete(array $where): static
    {
        $this->builder->where($where)->delete();
        return $this;
    }

    public function truncate(): static
    {
        DB::statement("TRUNCATE TABLE {$this->tableName}");
        return $this;
    }

    public function drop(): static
    {
        DB::statement("DROP TEMPORARY TABLE IF EXISTS {$this->tableName}");
        return $this;
    }

    public function get(): \Illuminate\Support\Collection
    {
        return $this->builder->get();
    }

    public function first(): ?object
    {
        return $this->builder->first();
    }

    public function exists(): bool
    {
        return $this->builder->exists();
    }

    public function count(): int
    {
        return $this->builder->count();
    }

    public function query(): Builder
    {
        return $this->builder;
    }

    public function reset(): static
    {
        $this->builder = DB::table($this->tableName);
        return $this;
    }

    public function join(
        string $table,
        \Closure|string $first,
        ?string $operator = null,
        ?string $second = null,
        string $type = 'inner',
        bool $where = false
    ): static {
        $this->builder->join($table, $first, $operator, $second, $type, $where);
        return $this;
    }

    public function leftJoin(string $table, string|\Closure $first, ?string $operator = null, ?string $second = null): static
    {
        $this->builder->leftJoin($table, $first, $operator, $second);
        return $this;
    }

    public function rightJoin(string $table, string|\Closure $first, ?string $operator = null, ?string $second = null): static
    {
        $this->builder->rightJoin($table, $first, $operator, $second);
        return $this;
    }

    public function crossJoin(string $table, ?string $first = null, ?string $second = null): static
    {
        $this->builder->crossJoin($table, $first, $second);
        return $this;
    }

    public function __call(string $method, array $arguments): mixed
    {
        $result = $this->builder->$method(...$arguments);
        return $result === $this->builder ? $this : $result;
    }
}

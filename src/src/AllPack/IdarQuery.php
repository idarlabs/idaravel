<?php

namespace Idaravel\AllPack;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;

class IdarQuery
{
    protected $table;
    protected $alias;
    protected $query;
    protected $with = [];
    protected $useSoftDelete = true;

    public function __construct($table)
    {
        $this->table = $table;
        $this->query = DB::table($table);
        $this->query->whereNull("{$this->table}.deleted_at");
    }

    public function alias($alias)
    {
        $this->alias = $alias;
        $this->query = DB::table("{$this->table} as {$alias}");
        
        if ($this->useSoftDelete) {
            $this->query->whereNull("{$alias}.deleted_at");
        }
        
        return $this;
    }

    public function withTrashed()
    {
        $this->useSoftDelete = false;
        
        if ($this->alias) {
            $this->query = DB::table("{$this->table} as {$this->alias}");
        } else {
            $this->query = DB::table($this->table);
        }
        
        return $this;
    }

    public function onlyTrashed()
    {
        $this->useSoftDelete = false;
        $target = $this->alias ?: $this->table;

        if ($this->alias) {
            $this->query = DB::table("{$this->table} as {$this->alias}");
        } else {
            $this->query = DB::table($this->table);
        }

        $this->query->whereNotNull("{$target}.deleted_at");
        return $this;
    }

    public function where(...$args)
    {
        $this->query->where(...$args);
        return $this;
    }

    public function orWhere(...$args)
    {
        $this->query->orWhere(...$args);
        return $this;
    }

    public function whereIn($col, $vals)
    {
        $this->query->whereIn($col, $vals);
        return $this;
    }

    public function join(...$args)
    {
        $this->query->join(...$args);
        return $this;
    }

    public function leftJoin(...$args)
    {
        $this->query->leftJoin(...$args);
        return $this;
    }

    public function create(array $data)
    {
        return $this->query->insert($data);
    }

    public function insertGetId(array $data)
    {
        return $this->query->insertGetId($data);
    }

    public function update(array $data)
    {
        return $this->query->update($data);
    }

    public function delete()
    {
        return $this->query->update([
            'deleted_at' => Carbon::now()
        ]);
    }

    public function restore()
    {
        return $this->query->update([
            'deleted_at' => null
        ]);
    }

    public function forceDelete()
    {
        return $this->query->delete();
    }

    public function select(...$args)
    {
        $this->query->select(...$args);
        return $this;
    }

    public function orderBy(...$args)
    {
        $this->query->orderBy(...$args);
        return $this;
    }

    public function groupBy(...$args)
    {
        $this->query->groupBy(...$args);
        return $this;
    }

    public function limit($limit)
    {
        $this->query->limit($limit);
        return $this;
    }

    public function offset($offset)
    {
        $this->query->offset($offset);
        return $this;
    }

    public function count()
    {
        return $this->query->count();
    }

    public function exists()
    {
        return $this->query->exists();
    }

    public function when(...$args)
    {
        $this->query->when(...$args);
        return $this;
    }

    public function with($relation)
    {
        $this->with[] = $relation;
        return $this;
    }

    public function one($where = null, callable $callback = null)
    {
        if ($where) {
            $this->query->where($where);
        }

        $result = $this->query->first();
        $result = $this->loadRelations($result);

        if ($result && $callback) {
            $result = $callback($result);
        }

        return $result;
    }

    public function all($where = null, \Closure $callback = null)
    {
        if ($where) {
            $this->query->where($where);
        }

        $results = $this->query->get()->map(function ($row) {
            return $this->loadRelations($row);
        });

        if ($callback instanceof \Closure) {
            return $results->map($callback);
        }

        return $results;
    }

    public function find($id)
    {
        $target = $this->alias ?: $this->table;
        $result = $this->where("{$target}.id", '=', $id)->one();
        
        return $result ? new IdarRecord($this->table, $result) : null;
    }

    protected function loadRelations($row)
    {
        if (!$row || empty($this->with)) return $row;

        foreach ($this->with as $relation) {
            $relationTable = Helpers::guessTableName($relation);
            $foreignKey = "{$relation}_id";

            if (property_exists($row, $foreignKey) || isset($row->{$foreignKey})) {
                $relatedId = $row->{$foreignKey};
                
                $relQuery = DB::table($relationTable)->where('id', $relatedId);
                
                if ($this->useSoftDelete) {
                    $relQuery->whereNull("{$relationTable}.deleted_at");
                }

                $row->{$relation} = $relQuery->first();
            }
        }

        return $row;
    }

    public function toQuery()
    {
        return $this->query;
    }

    public function toSql()
    {
        return $this->query->toSql();
    }

    public function dataTable(array $raw = [], array $only = [])
    {
        $dt = DataTables::of($this->query);

        if (!empty($raw)) {
            $dt->rawColumns($raw);
        }

        if (!empty($only)) {
            $dt->only($only);
        }

        return $dt;
    }
}
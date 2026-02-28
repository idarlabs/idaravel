<?php

namespace Idaravel\AllPack;

use Illuminate\Support\Facades\DB;

class IdarRecord
{
    protected $table;
    protected $attributes = [];

    public function __construct($table, $data)
    {
        $this->table = $table;
        $this->attributes = (array) $data;
    }

    public function __get($key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function save()
    {
        $id = $this->attributes['id'] ?? null;
        if (!$id) {
            throw new \Exception("Cannot save without ID");
        }

        DB::table($this->table)->where('id', $id)->update($this->attributes);
    }

    public function toArray()
    {
        return $this->attributes;
    }
}

<?php

namespace Idaravel\AllPack;

class Idar {

    private static $helpers = [
        'tglIndo', 'tglIndo2', 'terbilang',
        'formatRupiah', 'navlinknya', 'listBulan',
        'listHari', 'randomPassword', 'tglBarat',
        'hitungUsia', 'reqPost', 'raw'
    ];

    public static function __callStatic($method, $args){
        if(in_array($method, self::$helpers)){ return Helpers::$method(...$args); }

        if(method_exists(HelperHtml::class, $method)){
            return call_user_func([HelperHtml::class, $method], ...$args);
        }

        $table = \Illuminate\Support\Str::snake($method);
        return new IdarQuery($table);
    }
}

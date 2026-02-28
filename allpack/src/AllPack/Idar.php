<?php

namespace Idaravel\AllPack;

class Idar {

    private static $helpers = [
      'tglIndo', 'tglIndo2', 'terbilang',
      'formatRupiah', 'navlinknya', 'listBulan',
      'listHari', 'randomPassword', 'tglBarat',
      'hitungUsia', 'reqPost'
    ];

    public static function __callStatic($method, $args){
      if(in_array($method, self::$helpers)){
          return Helpers::$method(...$args);
      }

      $table = \Illuminate\Support\Str::snake($method);
      return new IdarQuery($table);
    }
}

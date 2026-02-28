<?php

namespace Idaravel\AllPack;

use Illuminate\Support\Str;

class Helpers {

  public static function guessTableName($relation){
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $relation));
  }

  function reqPost(string $url, array $data = [], array $headers = []){
    try {
        $res = \Illuminate\Support\Facades\Http::withHeaders($headers)->post($url, $data);

        return [
            'status' => $res->status(),
            'body'   => $res->json(),
            'raw'    => $res->body(),
        ];
    } catch (\Exception $e) {
        return [
            'status' => 500,
            'body'   => ['success' => false, 'message' => 'Request gagal: ' . $e->getMessage(), 'output' => ''],
            'raw'    => '',
        ];
    }
}

  public static function tglIndo($tanggal){
    return date('d/m/Y', strtotime($tanggal));
  }

  public static function tglIndo2($tanggal){
    $bulan = [
      1  => 'Januari',
      2  => 'Februari',
      3  => 'Maret',
      4  => 'April',
      5  => 'Mei',
      6  => 'Juni',
      7  => 'Juli',
      8  => 'Agustus',
      9  => 'September',
      10 => 'Oktober',
      11 => 'November',
      12 => 'Desember'
    ];

    $tgl = strtotime($tanggal);
    $hari = date('d', $tgl);
    $bln = date('n', $tgl);
    $thn = date('Y', $tgl);

    return $hari . ' ' . $bulan[$bln] . ' ' . $thn;
  }

  public static function terbilang($x){
    $x = (int) $x;
    $angka = ["", "Satu", "Dua", "Tiga", "Empat",
        "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];

    if ($x < 12)
        return " " . $angka[$x];
    elseif ($x < 20)
        return self::terbilang($x - 10) . " Belas";
    elseif ($x < 100)
        return self::terbilang(intval($x / 10)) . " Puluh" . self::terbilang($x % 10);
    elseif ($x < 200)
        return "Seratus" . self::terbilang($x - 100);
    elseif ($x < 1000)
        return self::terbilang(intval($x / 100)) . " Ratus" . self::terbilang($x % 100);
    elseif ($x < 2000)
        return "Seribu" . self::terbilang($x - 1000);
    elseif ($x < 1000000)
        return self::terbilang(intval($x / 1000)) . " Ribu" . self::terbilang($x % 1000);
    elseif ($x < 1000000000)
        return self::terbilang(intval($x / 1000000)) . " Juta" . self::terbilang($x % 1000000);
    else
        return "Nilai terlalu besar";
  }

  public static function formatRupiah($angka, $prefix = ''){
    $number_string = preg_replace('/[^,\d]/', '', $angka);
    $split = explode(',', $number_string);
    $sisa = strlen($split[0]) % 3;
    $rupiah = substr($split[0], 0, $sisa);
    $ribuan = substr($split[0], $sisa);
    preg_match_all('/\d{3}/', $ribuan, $matches);
    $ribuan = $matches[0] ?? [];

    if(count($ribuan)){
      $separator = $sisa ? '.' : '';
      $rupiah .= $separator . implode('.', $ribuan);
    }

    $rupiah = isset($split[1]) ? $rupiah . ',' . $split[1] : $rupiah;
    return $prefix . $rupiah;
  }

  public static function navlinknya($path){
    if(\Illuminate\Support\Str::contains(request()->path(), $path)){
      return 'nav-link active';
    }
    return 'nav-link';
  }

  public static function listBulan(){
    return [
      '01' => 'Januari',
      '02' => 'Februari',
      '03' => 'Maret',
      '04' => 'April',
      '05' => 'Mei',
      '06' => 'Juni',
      '07' => 'Juli',
      '08' => 'Agustus',
      '09' => 'September',
      '10' => 'Oktober',
      '11' => 'November',
      '12' => 'Desember'
    ];
  }

  public static function listHari(){
    return [
      'Sunday'    => 'Minggu',
      'Monday'    => 'Senin',
      'Tuesday'   => 'Selasa',
      'Wednesday' => 'Rabu',
      'Thursday'  => 'Kamis',
      'Friday'    => 'Jumat',
      'Saturday'  => 'Sabtu',
    ];
  }

  public static function randomPassword($batas = 8){
    $pasnya = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
    $pass = substr($pasnya, 0, $batas);

    return [
      'hash' => bcrypt($pass),
      'asli' => $pass
    ];
  }

  public static function tglBarat($date){
    $tgl = substr($date, 0, 2);
    $bln = substr($date, 3, 2);
    $thn = substr($date, 6, 4);
    return "{$thn}-{$bln}-{$tgl}";
  }

  public static function hitungUsia($tanggalLahir, $tanggalSekarang = null){
    $tanggalSekarang = $tanggalSekarang ?? date('Y-m-d');
    $lahir = new DateTime($tanggalLahir);
    $sekarang = new DateTime($tanggalSekarang);
    $umur = $sekarang->diff($lahir);

    return $umur->y . ' Thn, ' . $umur->m . ' Bln, ' . $umur->d . ' Hr';
  }

}

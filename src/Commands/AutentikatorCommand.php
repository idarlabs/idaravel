<?php

namespace Idaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AutentikatorCommand extends Command {
    protected $signature = 'idaravel:auth';
    protected $description = 'Generate full production-ready Auth Module with Controller, Middleware, Views, and Captcha Routes';

    public function handle(){
        $this->info('====================================================');
        $this->info('     MEMULAI GENERATE EKOSISTEM AUTENTIKASI    ');
        $this->info('====================================================');

        $this->call('make:module', ['name' => 'Auth']);

        $modulePath = app_path('Modules/Auth');
        $backendPath = "{$modulePath}/Backend";
        $frontendPath = "{$modulePath}/Frontend";
        $routesPath = "{$modulePath}/routes";

        $this->comment("\Generate File...");

        // ==========================================
        // CONFIG 1: DATA BACKEND (DefaultController.php)
        // ==========================================
        $controllerCode = <<<'PHP'
<?php

namespace App\Modules\Auth\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Idaravel\AllPack\Idar;

class DefaultController extends Controller
{
    public function index()
    {
        return redirect()->route('auth.login');
    }

    public function login(Request $r){

        if(Auth::check()){
            return redirect()->route('beranda.index');
        }

        return view('auth::index');
    }

    public function proses(Request $r){
        $kredensial = $r->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($r->captcha_input != session('captcha_code')) {
            return back()->with('error', 'Verifikasi Kode Salah!')->withInput();
        }

        if(Auth::attempt($kredensial)){

            $r->session()->regenerate();

            $user = Idar::Users()->with('level')->one(['email' => $r->email]);
            
            if(empty($user)){
                Auth::logout();
                $r->session()->invalidate();
                $r->session()->regenerateToken();
                
                return redirect()->route('auth.login')->with('error','Mohon maaf, Akun Anda telah di Non-Aktifkan!');
            }

            session()->put('id', $user->id);
            session()->put('nama', $user->name);
            session()->put('email', $user->email);
            session()->put('level', $user->level_id);
            session()->put('nama_level', $user->level->nama);
            
            $unit = Idar::MUnit()->find($user->m_unit_id);
            session()->put('unit', $unit->id);
            session()->put('nama_unit', $unit->nama_unit);

            Idar::LogLogin()->create([
                'ip' => request()->ip(),
                'user_agen' => $r->header('User-Agent'),
                'user_id' => $user->id,
                'waktu_login' => date("Y-m-d H:i:s"),
            ]);

            return redirect()->route('beranda.index')->with(['success' => 'Login berhasil.<div><strong>Selamat Datang..!!!</strong></div>']);
        }

        return back()->with('error','Email atau Kata Sandi salah !!!');

    }

    public function akses_ditolak(Request $r){
        return view("auth::akses_ditolak");
    }

    public function logout(Request $r){
        $r->session()->forget(['exception', 'throttle_wait']);

        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return response()->json(['status' => 'logout']);
    }
}
PHP;
        File::put("{$backendPath}/DefaultController.php", $controllerCode);
        $this->info('-> Backend/DefaultController.php [OK]');


        // ==========================================
        // CONFIG 2: DATA BACKEND MIDDLEWARE (Middleware.php)
        // ==========================================
        $middlewareCode = <<<'PHP'
<?php

namespace App\Modules\Auth\Backend;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class Middleware {

  public function handle(Request $r, Closure $next, $tamu = null): Response {

    if(!Auth::check()){
      return redirect()->route('auth.login');
    }

    $levelId = session('level');
    $currentRouteName = $r->route()->getName();
    $currentPath = $r->path(); 

    if ($levelId == 1) {
        $jumlahCentangan = DB::table('menu_akses')
            ->where('level_id', 1)
            ->whereNull('deleted_at')
            ->count();

        if ($jumlahCentangan == 0) {
            if (!str_contains($currentPath, 'aksesmenu') && !str_contains($currentPath, 'logout')) {
                return redirect()->to('/aksesmenu');
            }
        }
    }

    if ($currentRouteName == 'auth.logout' || str_contains($currentPath, 'logout')) {
        return $next($r);
    }

    $isMenuRoute = DB::table('menu')
        ->where('url', $currentRouteName)
        ->orWhere('url', $currentPath)
        ->orWhere('url', '/' . $currentPath)
        ->exists();

    if ($isMenuRoute) {
        if ($levelId == 1 && str_contains($currentPath, 'aksesmenu')) {
            return $next($r);
        }

        $hasAkses = DB::table('menu')
            ->join('menu_akses', 'menu.id', '=', 'menu_akses.menu_id')
            ->where('menu_akses.level_id', $levelId)
            ->where(function($query) use ($currentRouteName, $currentPath) {
                $query->where('menu.url', $currentRouteName)
                      ->orWhere('menu.url', $currentPath)
                      ->orWhere('menu.url', '/' . $currentPath);
            })
            ->whereNull('menu_akses.deleted_at')
            ->exists();

        if (!$hasAkses) {
            return redirect()->route('auth.akses_ditolak')->with('error', 'Akses Ditolak: Anda tidak memiliki otoritas untuk menu ini.');
        }
    } 
    
    return $next($r);
  }
}
PHP;
        File::put("{$backendPath}/Middleware.php", $middlewareCode);
        $this->info('-> Backend/Middleware.php [OK]');


        // ==========================================
        // CONFIG 3: DATA FRONTEND (index.blade.php)
        // ==========================================
        $viewIndexCode = <<<'PHP'
@extends('idaravel.login')
@section('konten')
<style>
  html, body {
      height: 100%;
      margin: 0;
      padding: 0;
      overflow: hidden;
  }

  body {
      background-color: #000;
      display: flex;
      align-items: center;
      justify-content: center;
  }

  body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url("{{ asset('bg-login.jpg') }}") no-repeat center center fixed;
      background-size: cover;
      filter: blur(5px) brightness(80%); 
      z-index: 1;
      transform: scale(1.1);
  }

  .login-wrapper {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: center;
  }

  .card {
      background: rgba(255, 255, 255, 0.9);
      overflow: hidden;
  }

  .no-focus-shadow:focus {
      box-shadow: none !important;
      outline: none !important;
  }
</style>

<div class="container login-wrapper">
    <div class="card shadow shadow-lg border-0" style="width: 85%;">
        <div class="row">
            <div class="col-sm-6 bg-white">
                <div class="text-center p-2 d-flex flex-column justify-content-between" style="height:100%;">
                        
                    <div class="pt-3">
                        <img src="{{ url('logotext.png') }}" width="40%">
                        <div style="border:1px dashed #ccc;" class="mb-1 mt-1"></div>
                        <span class="font-weight-bold" style="font-size:22px;color:#0e8207;">RSD dr. Soebandi</span>
                    </div>

                    <div class="mt-0 mb-0">
                        <small class="text-primary">Copyright &copy; 2026 RSD dr. Soebandi Jember</small>
                    </div>

                </div>
            </div>
            <div class="col-sm-6 pr-4 pl-3 pt-3 pb-3">
                <form action="{{ route('auth.proses') }}" method="POST">
                    @csrf
                    <div class="mb-1">
                        <label for="email" class="form-label">
                        <i class="bx bx-mail-send bx-fw"></i>
                        Email
                        </label>
                        <input type="email" autocomplete="off" class="form-control" id="email" name="email" placeholder="Masukkan email" required>
                    </div>

                    <div class="mb-2">
                        <label for="password" class="form-label">
                            <i class="bx bxs-lock bx-fw"></i>
                            Kata Sandi
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan kata sandi" required>
                            <span class="input-group-text bg-success">
                                <input type="checkbox" id="ceklist" onclick="centang()">
                            </span>
                        </div>
                    </div>

                    <div class="mt-1 text-right">
                        <input type="text"
                        name="captcha_input"
                        class="form-control no-focus-shadow"
                        style="border:none;border-bottom:1px solid #eee;margin-bottom:10px;"
                        placeholder="Verifikasi kode dibawah ini"
                        required>
                        <div class="mb-1">
                            <img id="captcha_img" src="{{ route('auth.captcha.generate') }}" 
                                style="border-radius: 0px; border:1px solid #ccc;">
                            <button type="button" class="btn btn-sm alert-dark ml-1" onclick="refreshCaptcha()">
                                <i class="bx bx-rotate-left fs-5 text-secondary"></i>
                            </button>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="mt-3 btn btn-success w-100">
                        MASUK KE SISTEM
                        <i class="bx bx-log-in-circle"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
  
});

function refreshCaptcha() {
    document.getElementById("captcha_img").src = "{{ route('auth.captcha.generate') }}?" + Date.now();
}
function centang() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script>
@endsection
PHP;
        File::put("{$frontendPath}/index.blade.php", $viewIndexCode);
        $this->info('-> Frontend/index.blade.php [OK]');


        // ==========================================
        // CONFIG 4: DATA FRONTEND (akses_ditolak.blade.php)
        // ==========================================
        $viewDeniedCode = <<<'PHP'
@extends('idaravel.layout')
@section('konten')
<div class="container bg-white login-wrapper">
    <div class="card">
        <div class="p-2"></div>
        <div class="text-center">
            <img src="{{ url('medisty.png') }}" width="30%">
        </div>
        <div class="text-center">
            <div class="text-info fs-3 font-weight-bold">Haloo Medisty..!!!</div>
            @if(session()->has('gagal'))
            {!! session('gagal') !!}
            @else
            <span class="text-primary font-weight-bold">Mohon maaf nih..</span>, kamu <strong class="text-danger">tidak memiliki hak</strong> untuk mengakses halaman ini.
            @endif
        </div>
        <div class="p-4"></div>
    </div>
</div>

<script>
$(document).ready(function(){
  
});
</script>
@endsection
PHP;
        File::put("{$frontendPath}/akses_ditolak.blade.php", $viewDeniedCode);
        $this->info('-> Frontend/akses_ditolak.blade.php [OK]');


        // ==========================================
        // CONFIG 5: DATA ROUTES (web.php)
        // ==========================================
        $routesCode = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Backend\DefaultController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function(){
    Route::get('/', [DefaultController::class, 'index'])->name('index');
    Route::get('akses_ditolak', [DefaultController::class, 'akses_ditolak'])->name('akses_ditolak');
    Route::get('login', [DefaultController::class, 'login'])->name('login');
    Route::post('logout', [DefaultController::class, 'logout'])->name('logout');
    Route::post('proses', [DefaultController::class, 'proses'])->name('proses');
    
    Route::get('captcha-generate', function () {
        $code = rand(10000, 99999);
        session(['captcha_code' => $code]);

        $width = 150;
        $height = 50;

        $img = imagecreatetruecolor($width, $height);

        for ($y = 0; $y < $height; $y++) {
            $gray = rand(180, 230);
            $color = imagecolorallocate($img, $gray, $gray, $gray);
            imageline($img, 0, $y, $width, $y, $color);
        }

        for ($i = 0; $i < 200; $i++) {
            $g = rand(90, 200);
            $dotColor = imagecolorallocate($img, $g, $g, $g);
            imagesetpixel($img, rand(0,$width), rand(0,$height), $dotColor);
        }

        for ($i = 0; $i < 3; $i++) {
            $g = rand(80, 150);
            $lineColor = imagecolorallocate($img, $g, $g, $g);
            imageline($img, rand(0,$width), rand(0,$height), rand(0,$width), rand(0,$height), $lineColor);
        }

        $small = imagecreatetruecolor(70, 20);
        imagealphablending($small, false);
        imagesavealpha($small, true);

        $transparent = imagecolorallocatealpha($small, 0, 0, 0, 127);
        imagefill($small, 0, 0, $transparent);

        $textColor = imagecolorallocatealpha($small, 57, 76, 98, 0);
        imagestring($small, 5, 5, 2, $code, $textColor);

        $big = imagecreatetruecolor($width, $height);
        imagealphablending($big, false);
        imagesavealpha($big, true);

        $transparentBig = imagecolorallocatealpha($big, 0, 0, 0, 127);
        imagefill($big, 0, 0, $transparentBig);

        imagecopyresampled($big, $small, 0, 0, 0, 0, $width, $height, 70, 20);
        imagecopy($img, $big, 0, 0, 0, 0, $width, $height);

        ob_start();
        imagepng($img);
        $imageData = ob_get_clean();
        imagedestroy($img);
        imagedestroy($small);
        imagedestroy($big);

        return response($imageData)->header('Content-Type', 'image/png');
    })->name('captcha.generate');
});
PHP;
        File::put("{$routesPath}/web.php", $routesCode);
        $this->info('-> routes/web.php [OK]');

        // ========================================================
        // CONFIG 6: OTOMATISASI KONFIGURASI CORE (bootstrap/app.php Laravel 13)
        // ========================================================
        $this->comment("\nMengonfigurasi Core Laravel (bootstrap/app.php)...");
        
        $appConfigCode = <<<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Modules\Auth\Backend\Middleware as AksesLogin;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'aksesLogin' => AksesLogin::class
        ]);

        $middleware->validateCsrfTokens(except: [
            'bpjs/apotek/*',
            'bpjs/vclaim/*',
            'api/*'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'metaData' => [
                        'code' => 500,
                        'message' => $e->getMessage()
                    ],
                    'response' => null
                ], 500);
            }
        });
    })->create();
PHP;

        File::put(base_path('bootstrap/app.php'), $appConfigCode);
        $this->info('-> bootstrap/app.php [REPLACED & OPTIMIZED]');

        $this->info("\n====================================================");
        $this->info('   SCAFFOLDING IDARAVEL:AUTH SELESAI PERFECT! 🚀    ');
        $this->info('====================================================');
    }
}
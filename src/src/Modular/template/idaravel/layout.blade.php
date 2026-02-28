<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Idaravel</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @include('partials.css')
    @vite(['resources/js/app.js', 'resources/css/fonts.css', 'resources/css/premium-line-icons.min.css'])
    <link rel="stylesheet" href="{{asset('assets/css/utama.css')}}">
    <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/css/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/jquery-ui.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/select22.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/datatables/datatables.min.css')}}">
    <link href="https://vjs.zencdn.net/8.5.2/video-js.css" rel="stylesheet" />
  </head>
  <body class="jumping">
    <div id="root" class="root mn--max hd--expanded">
      <section id="content" class="content">
        @push('style')

        @endpush
        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <input type="hidden" id="base" value="{{url('/')}}">
        <div class="preloader" id="preloader">
          <div class="loading">
            <div class="text-center">
              <img src="{{ asset('assets/img/loading-buffering.gif') }}" width="80">
              <p class="fs-4">Silahkan tunggu, sedang memuat data...</p>
            </div>
          </div>
        </div>
        <div class="content__header content__boxed overlapping">
          <div class="content__wrap">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="javascript:(0)">@yield('jdl0')</a></li>
                <li class="breadcrumb-item active text-light" aria-current="page">@yield('jdl1')</li>
              </ol>
            </nav>
            <h1 class="page-title mb-0 mt-2">@yield('jdl2')</h1>
            <p class="lead"></p>
          </div>
        </div>
        <div class="content__boxed">
          <div class="content__wrap">
            <div class="row">@yield('konten')</div>
          </div>
        </div>

        <div class="modalInit">@yield('modinit')</div>
        <div class="modal fade" id="modkonfirmasi" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">KONFIRMASI</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <h2 id="judulkonfirmasi"></h2>
                <div id="subjudulkonfirmasi"></div>
              </div>
              <div class="modal-footer">
                <button type="button" id="batalkonfirmasi" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                  <i class="pli-close fs-5 me-1"></i>
                  Batal
                </button>
                <button type="button" id="submitkonfirmasi" class="btn btn-sm btn-primary">
                  Ya, Saya Yakin!
                  <i class="pli-yes fs-5 ms-1"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        @push('script')
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
          <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
          <script src="{{ asset('assets/datatables/datatables.min.js') }}"></script>
          <script src="{{ asset('assets/js/moment.min.js') }}"></script>
          <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
          <script src="{{ asset('assets/js/select22.full.min.js') }}"></script>
          <script src="{{ asset('assets/js/idaravel.js') }}"></script>
          <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
          <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
          @yield('skrip')
          <script>
            @if(session()->has('success'))
              toastr.success("{!! addslashes(session('success')) !!}", 'BERHASIL');
            @elseif(session()->has('error'))
              toastr.error("{!! addslashes(session('error')) !!}", 'GAGAL');
            @elseif(session()->has('warning'))
              toastr.warning("{!! addslashes(session('warning')) !!}", 'PERINGATAN');
            @endif
          </script>
          @if ($errors->any())
          <script>
          @foreach ($errors->all() as $error)
            toastr.error(`{!! addslashes($error) !!}`, 'GAGAL', { escapeHtml: false });
          @endforeach
          </script>
          @endif
          <script type="text/javascript">
          $(document).ready(function () {
            $(".preloader").fadeOut(500);
            $('.select2').select2()
            $('.select2bs4').select2({
              theme: 'bootstrap4'
            })
          })
          </script>
        @endpush
      </section>
      @include('partials.header')
      @include('partials.navigation')
    </div>
    <div class="scroll-container">
      <a href="#root" class="scroll-page rounded-circle ratio ratio-1x1" aria-label="Scroll button"></a>
    </div>
    @include('partials.footer')
    @include('partials.javascript')
  </body>
</html>

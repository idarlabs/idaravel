<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1">
    <title>Idaravel</title>
    @include('partials.css')
    @vite(['resources/js/app.js', 'resources/css/fonts.css', 'resources/css/premium-line-icons.min.css'])
    <link rel="stylesheet" href="{{asset('assets/css/utama.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/toastr.min.css')}}">
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <link href="{{ asset('assets/css/flatpickr.min.css') }}" rel="stylesheet">
  </head>
  <body class="jungle" style="background-image: url('{{ asset('assets/img/17.jpg') }}')">
    @yield('konten')
    @include('partials.javascript')
    <script>
      function centang(){
        if (document.getElementById('ceklist').checked === true) {
          document.getElementById('password').type = 'text'
        }else{
          document.getElementById('password').type = 'password'
        }
      }
    </script>
    <script>
      @if(session()->has('success'))
        toastr.success('{{ session('success') }}', 'BERHASIL');
      @elseif(session()->has('error'))
        toastr.error('{{ session('error') }}', 'GAGAL');
      @endif
      @if ($errors->any())
      document.addEventListener("DOMContentLoaded", function () {
        @foreach ($errors->all() as $error)
          toastr.error(`{{ $error }}`, 'GAGAL');
        @endforeach
      });
      @endif
    </script>
  </body>
</html>

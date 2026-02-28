<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/nifty.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/icon.css') }}">
<style type="text/css">
  .preloader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    background-color: #fff;
  }
  .preloader .loading {
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    font: 14px arial;
  }
</style>
@vite(['resources/js/app.js', 'resources/css/fonts.css'])
@stack('style')
<script src="{{ asset('assets/js/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/nifty.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://cdn.ckeditor.com/4.22.0/full/ckeditor.js"></script>
<script src="https://vjs.zencdn.net/8.5.2/video.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
  function showLoading(){
    $('#preloader').show()
  }
  function hideLoading(){
    $('#preloader').hide()
  }
</script>
@stack('script')

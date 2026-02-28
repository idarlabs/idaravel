$(document).ready(function(){
  $('#login').on('click', function(){
    location.href = loginRoute;
  });

  $('.form').on('submit', function(e){
    $('.toast-container').remove();

    let valid = true;
    const nikRegex = /^[0-9]{16}$/;
    const telpRegex = /^[0-9]{10,15}$/;
    const waRegex = /^[0-9]{10,15}$/;
    const tglRegex = /^\d{2}\/\d{2}\/\d{4}$/;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const nama = $('[name="nama_lengkap"]').val().trim();
    const email = $('[name="email"]').val().trim();
    const t4 = $('[name="t4_lahir"]').val().trim();
    const tgl = $('[name="tgl_lahir"]').val().trim();
    const noiden = $('[name="noiden"]').val().trim();
    const alamat = $('[name="alamat"]').val().trim();
    const domisili = $('[name="domisili"]').val().trim();
    const telp = $('[name="telp"]').val().trim();
    const wa = $('[name="wa"]').val().trim();
    const insta = $('input[name="instagram"]').val().trim();
    const tikt = $('input[name="tiktok"]').val().trim();
    const faceb = $('input[name="facebook"]').val().trim();
    // const sosmed = $('[name="sosmed"]:checked').length;

    function showToast(msg){
      toastr.error(`${msg}`, 'GAGAL');
    }

    if (nama === '') { showToast('Nama lengkap wajib diisi.'); valid = false; }
    else if (email === '') { showToast('Email wajib diisi.'); valid = false; }
    else if (!emailRegex.test(email)) { showToast('Format email tidak valid.'); valid = false; }
    else if (t4 === '') { showToast('Tempat lahir wajib diisi.'); valid = false; }
    else if (tgl === '') { showToast('Tanggal lahir wajib diisi.'); valid = false; }
    else if (!tglRegex.test(tgl)) { showToast('Format tanggal lahir harus d/m/Y.'); valid = false; }
    else if (noiden === '') { showToast('NIK wajib diisi.'); valid = false; }
    else if (!nikRegex.test(noiden)) { showToast('NIK harus 16 digit angka.'); valid = false; }
    else if (alamat === '') { showToast('Alamat wajib diisi.'); valid = false; }
    else if (domisili === '') { showToast('Alamat domisili wajib diisi.'); valid = false; }
    else if (telp === '') { showToast('Nomor telepon wajib diisi.'); valid = false; }
    else if (!telpRegex.test(telp)) { showToast('Nomor telepon harus 10-15 digit angka.'); valid = false; }
    else if (wa === '') { showToast('Nomor Whatsapp wajib diisi.'); valid = false; }
    else if (!waRegex.test(wa)) { showToast('Nomor Whatsapp harus 10-15 digit angka.'); valid = false; }
    else if (insta === '' && tikt === '' && faceb === '')
    { showToast('Sosial Media Wajib di isi minimal 1 (satu) akun.'); valid = false; }

    if (!valid) e.preventDefault();
  });
});

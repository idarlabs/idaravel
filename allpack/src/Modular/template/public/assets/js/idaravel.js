function hariIndo(tanggal) {
  const hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  const d = new Date(tanggal);
  return hari[d.getDay()];
}

function jenis_beasiswa_sebelumnya(kd){
  var arr = [
    'Prestasi',
    'Kompetisi',
    'Guru dan Perangkat Daerah',
    'Fakir Miskin & Org Tidak Mampu',
    'Beasiswa Khusus',
    'Belum Menerima Beasiswa'
  ];

  return arr[(Number(kd))-1]
}

function jenis_beasiswa_sekarang(kd){
  var arr = [
    'Prestasi',
    'Afirmasi Ekonomi',
    'Guru & Perangkat Daerah / Desa',
    'Kompetisi',
    'Santri PP dan Hafiz Qur\'an',
    'Khusus'
  ];

  return arr[Number(kd)-1]
}

function konfirmasi(judul, subjudulkonfirmasi = '', callBack){
  $('#judulkonfirmasi').html(`${judul}`)
  $('#subjudulkonfirmasi').html(`${subjudulkonfirmasi}`)

  $('#submitkonfirmasi').off('click');
  $('#submitkonfirmasi').on('click', function(){
    $('#modkonfirmasi').modal('hide');
    if (typeof callBack === 'function') {
      callBack();
    }
  });

  $('#batalkonfirmasi').off('click');
  $('#batalkonfirmasi').on('click', function(){
    $('#modkonfirmasi').modal('hide');
  });

  setTimeout(function () {
    $('#modkonfirmasi').modal('show');
  }, 100);
}

function kirim(dataInput, urlnya, callBack) {
  var csrfToken = $('meta[name="csrf-token"]').attr("content");

  var form = new FormData();
  form.append("validasi", "go")
  form.append("_token", csrfToken)
  for (var key in dataInput) {
    form.append(key, dataInput[key])
  }
  $.ajax({
    url: urlnya,
    type: "POST",
    data: form,
    processData: false,
    contentType: false,
    success: function(hasil) {
      var obj = JSON.parse(hasil)
      callBack(obj)
    },
    error: function(xhr, status, errorThrown) {
      if (xhr.responseJSON && xhr.responseJSON.message && xhr.responseJSON.message == "CSRF token mismatch.") {
        location.href=`/beranda`
        return;
      }
      alert('error')
    }
  })
}
function makeid(length) {
  let result = '';
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  const charactersLength = characters.length;
  let counter = 0;
  while (counter < length) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
    counter += 1;
  }
  return result;
}
function fokuskan(kode, namaid){
  $('#'+namaid+kode).select()
}
function kirim2(dataInput, urlnya, callBack) {
  var csrfToken = $('meta[name="csrf-token"]').attr("content");

  var form = new FormData();
  form.append("validasi", "go")
  form.append("_token", csrfToken)
  for (var key in dataInput) {
    form.append(key, dataInput[key])
  }
  $.ajax({
    url: urlnya,
    type: "POST",
    data: form,
    processData: false,
    contentType: false,
    success: function(hasil) {
      // var obj = JSON.parse(hasil)
      callBack(hasil)
    },
    error: function(error) {
      alert("error");
      console.error(error);
    }
  })
}
function kirimGet(data, urlnya, callBack) {
  var urlx = new URL(urlnya);
  var meta = $('meta[name="csrf-token"]');
  var tokennya = meta.attr("content");
  urlx.searchParams.set('token', tokennya);

  for(var y in data){
    urlx.searchParams.append(y, data[y])
  }

  $.ajax({
    url: urlx.href,
    type: "GET",
    data: [],
    processData: false,
    contentType: false,
    success: function(hasil) {
      callBack(hasil)
    },
    error: function(error) {
      alert("error");
      console.error(error);
    }
  })
}
function terbilang(nilai) {
  // deklarasi variabel nilai sebagai angka matemarika
  // Objek Math bertujuan agar kita bisa melakukan tugas matemarika dengan javascript
  nilai = Math.floor(Math.abs(nilai));

  // deklarasi nama angka dalam bahasa indonesia
  var huruf = [
    '',
    'Satu',
    'Dua',
    'Tiga',
    'Empat',
    'Lima',
    'Enam',
    'Tujuh',
    'Delapan',
    'Sembilan',
    'Sepuluh',
    'Sebelas',
  ];

   var bagi = 0;
   var penyimpanan = '';

  if (nilai < 12) {
    penyimpanan = ' ' + huruf[nilai];
  } else if (nilai < 20) {
    penyimpanan = terbilang(Math.floor(nilai - 10)) + ' Belas';
  } else if (nilai < 100) {
    bagi = Math.floor(nilai / 10);
    penyimpanan = terbilang(bagi) + ' Puluh' + terbilang(nilai % 10);
  } else if (nilai < 200) {
    penyimpanan = ' Seratus' + terbilang(nilai - 100);
  } else if (nilai < 1000) {
    bagi = Math.floor(nilai / 100);
    penyimpanan = terbilang(bagi) + ' Ratus' + terbilang(nilai % 100);
  } else if (nilai < 2000) {
    penyimpanan = ' Seribu' + terbilang(nilai - 1000);
  } else if (nilai < 1000000) {
    bagi = Math.floor(nilai / 1000);
    penyimpanan = terbilang(bagi) + ' Ribu' + terbilang(nilai % 1000);
  } else if (nilai < 1000000000) {
    bagi = Math.floor(nilai / 1000000);
    penyimpanan = terbilang(bagi) + ' Juta' + terbilang(nilai % 1000000);
  } else if (nilai < 1000000000000) {
    bagi = Math.floor(nilai / 1000000000);
    penyimpanan = terbilang(bagi) + ' Miliar' + terbilang(nilai % 1000000000);
  } else if (nilai < 1000000000000000) {
    bagi = Math.floor(nilai / 1000000000000);
    penyimpanan = terbilang(nilai / 1000000000000) + ' Triliun' + terbilang(nilai % 1000000000000);
  }

  return penyimpanan;
}
function dDtable(infoTbl, data, field, aksi = null, posisi = {tengah: [], kanan: []}){
  var datax = [];
  var no = 1;
  $('#list'+infoTbl.substr(3)).empty()
  modalUbahD('Form - Ubah Data')

  data.forEach((x, i) => {
    var tmpNilai = [];
    field.forEach(y => {
      var nilai = (y == 'no') ? no : x[y];
      tmpNilai.push(nilai)
    })

    if(aksi !== null){
      tmpNilai.push(btnAksi(x, aksi))
    }else{
      $('.dinamis').remove()
    }

    datax.push(tmpNilai)
    no++
  })

  $('#'+infoTbl).DataTable(dTbl(datax, posisi));
}
function btnAksi(x, aksi, act){
  return `
  <a href="#" class="btn btn-xs btn-warning" onclick="showUbah('${encodeURIComponent(JSON.stringify(x))}', '${x[aksi]}')">
    <i class="pli-pen-5"></i>
  </a>
  <a href="#" class="btn btn-xs btn-danger" onclick="actHapus('${x[aksi]}')">
    <i class="pli-trash"></i>
  </a>
  `;
}
function dTbl(data, cls = {tengah: [0], kanan: []}){
  return {
    "data": data,
    "scrollX": true,
    "scrollY": 200,
    "pageLength": 150,
    "lengthMenu": [150, 250, 350, 500],
    "dom": "<'row'<'col-sm-6 kiriatas'l><'col-sm-6'f>><'row'<'col-sm-12 pt-2't>><'row'<'col-sm-4'i><'col-sm-4 text-center ketx'><'col-sm-4'p>>",
    "language": {
      "lengthMenu": `
      Show _MENU_ &nbsp; entries`,
    },
    "columnDefs": [
      {"className": "dt-center", "targets": cls.tengah},
      {"className": "dt-right", "targets": cls.kanan}
    ],
  }
}
function modalUbahD(){
  $('#modalInit').empty()
  setTimeout(function(){
    $('.modalInit').append(`<div class="modal fade" id="modUbahData" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="titleUbahData">Form - Ubah Data</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        <form class="fubahD" method="post">
          <div class="modal-body" id="modBody">
          <div class="row" id="lubah"></div>
          </div>
          <div class="modal-footer">
            <div class="btn-group">
              <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                <i class="pli-close fs-5 me-1"></i>
                Batal
              </button>
              <button type="submit" class="btn btn-sm btn-primary">
                Simpan Data
                <i class="pli-save ms-1 fs-5"></i>
              </button>
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>`);
    $('.modalInit').append(`<div class="modal fade" id="modhapusdata" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">VALIDASI DATA</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form class="cmodhapusdata" action="" method="post">
          <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
          <div class="modal-body text-center">
            <h3 id="judulhapus">Hapus Data</h3>
            <img src="${$('#base').val()}/idarHapus.gif" id="gbrhapus" width="64">
            <p></p>
            <div id="tekshapus">Anda yakin akan menghapus data ini?</div>
          </div>
          <div class="modal-footer">
            <div class="btn-group">
              <a href="#" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                <i class="fa fa-times"></i>
                Batal
              </a>
              <input type="hidden" name="idHapus" id="idHapus">
              <button type="submit" class="btn btn-sm btn-primary">
                <i class="fa fa-check"></i>
                Ya, Hapus Sekarang
              </button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>`);
  }, 100)
}

function formatkan(nilai, id){
  var n = nilai.replace(/\./g, '')
  $('#'+id).val(formatRupiah(n))
}
function kontenUbah(act, id){
  $('#lubah').empty()
  setTimeout(function(){
    $(".fubahD").attr("action", act);
    $('#lubah').append(`
      <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr("content")}">
      <input type="hidden" name="idUbah" value="${id}">
    `)
    $('#modUbahData').modal('show')
  }, 100)
}
function actHapus(id, aksi){
  $('#idHapus').val(id)
	$(".cmodhapusdata").attr("action", aksi);
	$('#modhapusdata').modal('show')
}
function formatRupiah(angka, prefix){
    if(angka === undefined || angka === null)
    return 0;
  var number_string = angka.replace(/[^,\d]/g, '').toString(),
  split   		= number_string.split(','),
  sisa     		= split[0].length % 3,
  rupiah     		= split[0].substr(0, sisa),
  ribuan     		= split[0].substr(sisa).match(/\d{3}/gi);

  if(ribuan){
    separator = sisa ? '.' : '';
    rupiah += separator + ribuan.join('.');
  }

  rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
  return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}
function tglIndo(tanggal){
  var bln = [
    'Januari', 'Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
  ]

  var thn = tanggal.substr(0, 4);
  var b = Number(tanggal.substr(5, 2)) -1;
  var tgl = tanggal.substr(8, 2);

  // return tgl+' '+bln[b]+' '+thn;
  return tgl+'/'+tanggal.substr(5, 2)+'/'+thn;
}
function tglIndo2(tanggal){
  var bln = [
    'Januari', 'Februari','Maret','April','Mei','Juni',
    'Juli','Agustus','September','Oktober','November','Desember'
  ]

  var thn = tanggal.substr(0, 4);
  var b = Number(tanggal.substr(5, 2)) -1;
  var tgl = tanggal.substr(8, 2);

  return tgl+' '+bln[b]+' '+thn;
}
function tglBarat(tanggal){
  var tgl = tanggal.substr(0, 2);
  var bln = tanggal.substr(3, 2);
  var thn = tanggal.substr(6, 4);
  return thn+'-'+bln+'-'+tgl;
}

function _btoa(str) {
  const encoder = new TextEncoder();
  const charCodes = encoder.encode(str);
  return btoa(String.fromCharCode(...charCodes));
}

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hotel Management - Room Monitoring</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      color: #333;
    }

    main {
      padding-top: 80px;
      padding-bottom: 60px;
    }

    .container {
      max-width: 1100px;
    }

    .text-toska {
      color: #1E88E5;
    }

    .card {
      border-radius: 16px !important;
      overflow: hidden;
      backdrop-filter: blur(5px);
    }

    .card-header {
      background-color: #1E88E5;
      color: white;
      font-weight: 600;
      font-size: 1rem;
      border: none;
    }

    .btn-toska {
      background-color: #1E88E5;
      color: #fff;
      border: none;
      transition: all 0.2s ease;
    }
    .btn-toska:hover {
      background-color: #2ea897;
      transform: translateY(-2px);
    }

    .room-btn {
      border: none;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      transition: 0.2s ease-in-out;
    }

    /* Available (Default) */
    .room-btn:not([data-status]) {
      background-color: #4CAF50;
    }
    .room-btn:not([data-status]):hover {
      background-color: #43A047;
    }

    /* Occupied / Booked */
    .room-btn[data-status="occupied"] {
      background-color: #90A4AE;
    }
    .room-btn[data-status="occupied"]:hover {
      background-color: #78909C;
    }

    /* Cleaning / In Progress */
    .room-btn[data-status="cleaning"] {
      background-color: #EF5350;
    }
    .room-btn[data-status="cleaning"]:hover {
      background-color: #E53935;
    }

    .modal-content {
      border-radius: 20px;
      border: none;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
      background-color: #1E88E5;
      color: white;
      border: none;
    }

    .modal-body {
      background-color: #fdfdfd;
    }

    .modal-footer {
      background: #f8f9fa;
      border-top: 1px solid #e6e6e6;
    }

    #cameraPreview, #photoCanvas {
      border: 2px solid #dee2e6;
      background-color: #f8f9fa;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .alert {
      border-radius: 12px;
    }

  .filter-box {
    background: #ffffff;
    padding: 20px 25px;
    border-radius: 16px;
    border: 1px solid #e6e6e6;
    transition: 0.3s ease;
  }

  .filter-box:hover {
    box-shadow: 0 6px 20px rgba(30, 136, 229, 0.15);
    border-color: #d5e7ff;
  }

  .filter-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: black;
    letter-spacing: 0.3px;
  }

  .filter-input,
  .filter-select {
    border-radius: 12px;
    padding: 10px 14px;
    border: 1px solid #d7d7d7;
    transition: 0.2s ease;
  }

  .filter-input:focus,
  .filter-select:focus {
    border-color: #1E88E5;
    box-shadow: 0 0 0 0.15rem rgba(30, 136, 229, 0.25);
  }

  .input-group-text {
    border-radius: 12px 0 0 12px;
    background-color: #f8f9fa;
  }

  .bi-search {
    color: #1E88E5;
  }

    h3 {
      font-weight: 700;
      letter-spacing: 0.5px;
    }
  </style>
</head>
<body>

<main class="container">

  <!-- Header -->
  <div class="text-center mb-4">
    <h3 class="fw-bold text-toska mb-1">🏨 Room Monitoring Dashboard</h3>
    <p class="text-muted" style="margin-top:-4px;">
      Pemantauan status kamar hotel secara real-time untuk operasional PLN
    </p>
  </div>

<div class="filter-box mb-4 shadow-sm">
  <div class="row g-3">

    <!-- Pencarian -->
    <div class="col-md-4">
      <label class="filter-label">Pencarian</label>
      <div class="input-group">
        <span class="input-group-text border-end-0">
          <i class="bi bi-search"></i>
        </span>
        <input type="text" class="form-control border-start-0 filter-input" placeholder="Cari ruangan..."  id="filter-search">
      </div>
    </div>

    <!-- Lantai -->
    <div class="col-md-4">
      <label class="filter-label">Lantai</label>
      <select class="form-select filter-select" id="filter-lantai">
        <option selected>Pilih Lantai</option>

        <?php foreach($floors as $key){ ?>
        <option value="<?= $key['floor_number'] ?>"><?= $key['description'] ?></option>

        <?php } ?>

      </select>
    </div>

    <!-- Status -->
    <div class="col-md-4">
      <label class="filter-label">Status</label>
      <select class="form-select filter-select" id="filter-status">
        <option selected>Pilih Status</option>
        <option>Tersedia</option>
        <option>Terisi</option>
        <option>Sedang Dibersihkan</option>
      </select>
    </div>

  </div>
</div>

  <!-- Tombol Booking -->
  <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold mb-0 text-muted">Daftar Kamar</h5>
      <button class="btn btn-toska shadow-sm" data-bs-toggle="modal" data-bs-target="#bookingModal">
        <i class="bi bi-calendar-plus"></i> Booking Baru
      </button>
  </div>

  <!-- Lantai 2 -->
  <div class="filter-box mb-4 shadow-sm">


  <?php 

  $current_floor = null;
  foreach($rooms as $key){

    if($current_floor != $key['floor_id']){
      if($current_floor !== null){
        echo "</div></div></div>";
      }

      $current_floor = $key['floor_id'];
  ?>

  <div class="card mb-4 shadow-sm">
    <div class="card-header">Lantai <?= $key['floor_id'] ?></div>
    <div class="card-body">
      <div class="row row-cols-2 row-cols-md-4 g-3">

    <?php } ?>

        <?php if($key['status'] == 'available'){ ?>
          <div class="col">
            <button class="room-btn w-100 py-3" onclick="loadDataRoom(<?= $key['room_number'] ?>)" data-room="<?= $key['room_number'] ?>"><?= $key['room_number'] ?></button>
          </div>
        <?php } else if ($key['status'] == 'booked') { ?>
          <div class="col">
            <button class="room-btn w-100 py-3"onclick="loadDataRoom(<?= $key['room_number'] ?>)" data-status="occupied"  data-room="<?= $key['room_number'] ?>"><?= $key['room_number'] ?></button>
          </div>
        <?php } else { ?>
          <div class="col">
            <button class="room-btn w-100 py-3" onclick="loadDataRoom(<?= $key['room_number'] ?>)" data-status="cleaning"  data-room="<?= $key['room_number'] ?>"><?= $key['room_number'] ?></button>
          </div>
        <?php } ?>
        
       

      <?php } ?>

      </div>
    </div>
  </div>
</div>

</main>


<!-- Room Detail Modal -->
<div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="roomModalLabel">Room Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row mb-3">
          <div class="col-md-6">
            <h6 class="fw-bold text-muted">Judul Pembelajaran</h6>
            <p>PLN Go Live Aplikasi Hotel Management</p>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold text-muted">Nama</h6>
            <p>Alif Prasetyo Aji</p>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold text-muted">Jabatan</h6>
            <p>Staff IT</p>
          </div>
          <div class="col-md-6">
            <h6 class="fw-bold text-muted">Unit Induk</h6>
            <p>PT PLN (Persero)</p>
          </div>
        </div>
        <hr>

        <div id="checkinSection">
          <div class="alert alert-warning text-center">
            <i class="bi bi-camera-video"></i> Belum Check-in
          </div>
          <div class="text-center">
            <button class="btn btn-toska shadow-sm" id="startCameraBtn">
              <i class="bi bi-camera"></i> Buka Kamera untuk Check-in
            </button>
          </div>
        </div>

        <div id="cameraSection" class="d-none text-center">
          <video id="cameraPreview" autoplay playsinline class="img-fluid rounded mb-3" style="max-height:300px;"></video>
          <div class="d-flex gap-2 justify-content-center">
            <button class="btn btn-success" id="captureBtn"><i class="bi bi-camera-fill"></i> Ambil Foto</button>
            <button class="btn btn-secondary" id="retakeBtn"><i class="bi bi-arrow-repeat"></i> Ulangi</button>
            <button class="btn btn-toska" id="checkinBtn"><i class="bi bi-check-circle"></i> Check-in</button>
          </div>
        </div>

        <div id="photoPreview" class="text-center d-none">
          <h6 class="fw-bold text-muted mb-3">Preview Foto</h6>
          <canvas id="photoCanvas" class="img-fluid rounded mb-3" style="max-height: 300px;"></canvas>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal Entry Data Booking -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="roomModalLabel">Formulir Booking Kamar</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <div class="modal-body p-4">
        <form id="bookingForm">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold text-muted">Nama Lengkap</label>
              <input type="text" class="form-control shadow-sm" id="namaTamu" placeholder="Masukkan nama tamu..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-muted">Nomor Identitas (KTP / Passport)</label>
              <input type="text" class="form-control shadow-sm" id="noIdentitas" placeholder="Masukkan nomor identitas..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-muted">Nomor Kamar</label>
              <select class="form-select shadow-sm" id="nomorKamar" required>
                <option selected disabled>Pilih Nomor Kamar</option>
                <option>201</option>
                <option>202</option>
                <option>203</option>
                <option>204</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-muted">Lantai</label>
              <select class="form-select shadow-sm" id="lantaiKamar" required>
                <option selected disabled>Pilih Lantai</option>
                <option>Lantai 2</option>
                <option>Lantai 3</option>
                <option>Lantai 4</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-muted">Tanggal Check-in</label>
              <input type="date" class="form-control shadow-sm" id="tglCheckin" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold text-muted">Tanggal Check-out</label>
              <input type="date" class="form-control shadow-sm" id="tglCheckout" required>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold text-muted">Catatan</label>
              <textarea class="form-control shadow-sm" id="catatanBooking" rows="2" placeholder="Tambahkan catatan (opsional)..."></textarea>
            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-toska" id="simpanBookingBtn">
          <i class="bi bi-save"></i> Simpan Booking
        </button>
      </div>
    </div>
  </div>
</div>


<script>

  $(document).ready(function() {

  $('#simpanBookingBtn').click(function(e) {
    e.preventDefault();

    // Ambil semua nilai form
    let dataBooking = {
      nama_tamu: $('#namaTamu').val(),
      no_identitas: $('#noIdentitas').val(),
      nomor_kamar: $('#nomorKamar').val(),
      lantai: $('#lantaiKamar').val(),
      tgl_checkin: $('#tglCheckin').val(),
      tgl_checkout: $('#tglCheckout').val(),
      catatan: $('#catatanBooking').val()
    };

    // Validasi sederhana
    if (!dataBooking.nama_tamu || !dataBooking.no_identitas || !dataBooking.nomor_kamar || !dataBooking.lantai || !dataBooking.tgl_checkin || !dataBooking.tgl_checkout) {
      alert("⚠️ Mohon lengkapi semua field wajib!");
      return;
    }

    // Kirim AJAX ke Controller CodeIgniter
    $.ajax({
      url: "<?= base_url('booking/simpan'); ?>", // arahkan ke controller method simpan()
      type: "POST",
      dataType: "json",
      data: dataBooking,
      beforeSend: function() {
        $('#simpanBookingBtn').prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');
      },
      success: function(response) {
        if (response.status === 'success') {
          alert("✅ Booking berhasil disimpan!");
          $('#bookingForm')[0].reset();
          $('#bookingModal').modal('hide');
        } else {
          alert("❌ Gagal menyimpan booking: " + response.message);
        }
      },
      error: function(xhr, status, error) {
        console.error(xhr.responseText);
        alert("❌ Terjadi kesalahan sistem. Coba lagi nanti.");
      },
      complete: function() {
        $('#simpanBookingBtn').prop('disabled', false).html('<i class="bi bi-save"></i> Simpan Booking');
      }
    });
  });

});
  
  let stream = null;
  let photoData = null;

  document.getElementById('roomModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const roomNumber = button.getAttribute('data-room');
    const modalTitle = this.querySelector('.modal-title');
    modalTitle.textContent = `Detail Ruangan ${roomNumber}`;
    stopCamera();
  });

  document.getElementById('startCameraBtn').addEventListener('click', async function() {
    try {
      stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
      const video = document.getElementById('cameraPreview');
      video.srcObject = stream;
      document.getElementById('checkinSection').classList.add('d-none');
      document.getElementById('cameraSection').classList.remove('d-none');
    } catch (error) {
      alert('Tidak dapat mengakses kamera.');
    }
  });

  document.getElementById('captureBtn').addEventListener('click', function() {
    const video = document.getElementById('cameraPreview');
    const canvas = document.getElementById('photoCanvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    photoData = canvas.toDataURL('image/jpeg');
    document.getElementById('cameraSection').classList.add('d-none');
    document.getElementById('photoPreview').classList.remove('d-none');
    stopCamera();
  });

  document.getElementById('checkinBtn').addEventListener('click', function() {
    if (photoData) {
      document.getElementById('checkinSection').innerHTML = `
        <div class="alert alert-success text-center">
          <i class="bi bi-check-circle"></i> Sudah Check-in
        </div>
        <div class="text-center">
          <img src="${photoData}" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
        </div>`;
      document.getElementById('photoPreview').classList.add('d-none');
    }
  });

  function stopCamera() {
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
      stream = null;
    }
  }

  document.getElementById('roomModal').addEventListener('hidden.bs.modal', function() {
    stopCamera();
  });


  function loadRooms() {
    let search  = $('#filter-search').val();
    let lantai  = $('#filter-lantai').val();
    let status  = $('#filter-status').val();

    $.ajax({
        url: "<?= base_url('dashboard/loadRooms') ?>",     // ganti sesuai route backend
        method: "POST",
        data: {
            search: search,
            floor_id: lantai,
            status: status
        },
        beforeSend: function() {
            // opsional loading state
            $('#room-list').html('<p>Loading...</p>');
        },
        success: function (res) {
            // tampilkan hasil pencarian
            $('#room-list').html(res);
        }
    });
}


function loadDataRoom(room_id){
   $.get("dashboard/room_detail/" + room_id, function(res) {
    if (res.data) {
      $("#roomModal").modal("show");
    } else {
      $("#roomModal").modal("show");
    }
  })
}

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

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
  .progress {
    border-radius: 10px;
    overflow: hidden;
  }

  #wizardProgress {
    transition: width .4s ease;
  }
.camera-wrapper {
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
  }

  .camera-video {
      width: 700px !important;
      max-width: 95%;
      border-radius: 12px;
      background: #000;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
  }

  .camera-status {
      text-align: center;
      font-size: 18px;
      font-weight: bold;
      margin-top: 8px;
  }

  .camera-preview-img {
      width: 110px;
      height: 90px;
      object-fit: cover;
      border-radius: 6px;
      border: 2px solid #0d6efd;
  }
  </style>
</head>
<body>

<main class="container">

  <!-- Header -->
  <div class="text-center mb-4">
    <h3 class="fw-bold text-toska mb-1">🏨 Room Monitoring Dashboard</h3>
    <p class="text-muted" style="margin-top:-4px;">Pemantauan status kamar hotel secara real-time</p>
  </div>

  <!-- FILTER BOX -->
  <div class="filter-box mb-4 shadow-sm p-3">
    <div class="row g-3">

      <!-- Pencarian -->
      <div class="col-md-4">
        <label class="filter-label">Pencarian</label>
        <div class="input-group">
          <span class="input-group-text border-end-0"><i class="bi bi-search"></i></span>
          <input type="text" class="form-control border-start-0 filter-input" placeholder="Cari ruangan..." id="filter-search">
        </div>
      </div>

      <!-- Lantai -->
      <div class="col-md-4">
        <label class="filter-label">Lantai</label>
        <select class="form-select filter-select" id="filter-lantai">
          <option value="">Semua Lantai</option>
          <?php foreach($floors as $floor){ ?>
            <option value="<?= $floor['floor_number'] ?>"><?= $floor['description'] ?></option>
          <?php } ?>
        </select>
      </div>

      <!-- Status -->
      <div class="col-md-4">
        <label class="filter-label">Status</label>
        <select class="form-select filter-select" id="filter-status">
          <option value="">Semua Status</option>
          <option value="available">Tersedia</option>
          <option value="booked">Terisi</option>
          <option value="cleaning">Sedang Dibersihkan</option>
        </select>
      </div>

    </div>
  </div>

  <!-- Tombol Booking -->
  <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="fw-semibold mb-0 text-muted">Daftar Kamar</h5>
      <button class="btn btn-toska shadow-sm" data-bs-toggle="modal" data-bs-target="#bookingWizardModal">
        <i class="bi bi-calendar-plus"></i> Booking Baru
      </button>
  </div>

  <!-- CONTAINER TOMBOL KAMAR -->
  <div id="rooms-container">
    <!-- Tombol kamar akan di-render di sini -->
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

      <form id="formCheckin">

        <div class="modal-body">

          <!-- HIDDEN ROOM & USER -->
          <input type="hidden" id="room_id" name="room_id">
          <input type="hidden" id="user_id_checkin" name="user_id">
          <input type="hidden" id="checkin_user_face_id" name="checkin_user_face_id">

          <!-- GUEST TYPE BADGE -->
          <div class="text-center mb-3">
            <span id="checkin_guestTypeBadge" class="badge bg-secondary fs-6">Loading...</span>
          </div>

          <!-- IDENTITAS -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">NIPP</label>
              <input type="text" id="modalNipp" name="nipp" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">Nama</label>
              <input type="text" id="modalNama" name="nama" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">Jabatan</label>
              <input type="text" id="modalJabatan" name="jabatan" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">Unit Induk</label>
              <input type="text" id="modalUnitInduk" name="unit_induk" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">Nomor Polisi</label>
              <input type="text" id="modalNopol" name="nopol" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">Kendaraan</label>
              <input type="text" id="modalKendaraan" name="kendaraan" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">No HP</label>
              <input type="text" id="modalTelepon" name="telepon" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">Email</label>
              <input type="text" id="modalEmail" name="email" class="form-control" readonly>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold text-muted">NIK</label>
              <input type="text" id="modalNik" name="nik" class="form-control" readonly>
            </div>

            <div class="col-md-12">
              <label class="form-label fw-bold text-muted">Alamat</label>
              <input type="text" id="modalAlamat" name="alamat" class="form-control" readonly>
            </div>
          </div>

          <hr>

          <!-- SECTION TAMU LAMA -->
          <div id="checkin_sectionTamuLama" style="display:none;">
            <div class="alert alert-info text-center">
              <i class="bi bi-person-check"></i> Tamu Lama - Verifikasi Wajah
            </div>
            
            <!-- CHECK-IN SECTION TAMU LAMA -->
            <div id="checkin_sectionLama">
              <div class="text-center">
                <button type="button" class="btn btn-primary shadow-sm" id="checkin_startCameraLamaBtn">
                  <i class="bi bi-camera"></i> Buka Kamera untuk Verifikasi
                </button>
              </div>
            </div>

            <!-- CAMERA FRAME TAMU LAMA -->
            <div id="checkin_cameraFrameLama" class="mt-3 text-center" style="display:none;">
              <div class="camera-wrapper mx-auto" style="max-width:420px;">
                <video id="checkin_videoLama" autoplay playsinline class="camera-video"
                       style="width:100%; border-radius:10px; background:black;"></video>
              </div>

              <div id="checkin_statusLama" class="camera-status text-warning mt-2 fw-semibold">
                Menyalakan kamera...
              </div>
            </div>

            <div id="checkin_previewLama" class="mt-3"></div>

            <div id="checkin_autoFillLama" class="alert alert-success py-2 mb-3" style="display:none;">
              <i class="bi bi-check-circle"></i> Verifikasi berhasil! Wajah cocok.
            </div>
          </div>

          <!-- SECTION TAMU BARU -->
          <div id="checkin_sectionTamuBaru" style="display:none;">
            <div class="alert alert-warning text-center">
              <i class="bi bi-person-plus"></i> Tamu Baru - Daftar Wajah (6 Foto)
            </div>
            
            <div class="text-center mb-3">
              <button type="button" class="btn btn-success shadow-sm" id="checkin_openCameraBaruBtn">
                <i class="bi bi-camera"></i> Buka Kamera untuk Pendaftaran
              </button>
            </div>

            <!-- CAMERA FRAME TAMU BARU -->
            <div id="checkin_cameraFrameBaru" class="mt-3 text-center" style="display:none;">
              <div class="camera-wrapper mx-auto" style="max-width:420px;">
                <video id="checkin_videoBaru" autoplay playsinline class="camera-video"
                       style="width:100%; border-radius:10px; background:black;"></video>
              </div>

              <div id="checkin_statusBaru" class="camera-status text-warning mt-2 fw-semibold">
                Menyalakan kamera...
              </div>

              <div class="mt-2">
                <button type="button" class="btn btn-primary" id="checkin_captureBaruBtn" disabled>
                  <i class="bi bi-camera-fill"></i> Ambil Foto
                </button>
              </div>
            </div>

            <!-- PROGRESS & PREVIEW TAMU BARU -->
            <div class="mt-3">
              <div class="progress mb-2" style="height: 20px;">
                <div id="checkin_progressBarBaru" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
              </div>
              <div class="text-center">
                <span id="checkin_fotoProgressBaru" class="badge bg-secondary">0 / 6 Foto</span>
              </div>
              
              <div id="checkin_previewListBaru" class="d-flex flex-wrap gap-2 justify-content-center mt-2"></div>
              
              <div id="checkin_responseBaru" class="mt-2 text-center fw-semibold"></div>
            </div>
          </div>

          <hr>

          <!-- BUTTON CHECK-IN -->
          <button type="submit" class="btn btn-toska w-100" id="checkinBtn" disabled>
            <i class="bi bi-check-circle"></i> Check-in
          </button>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
        </div>

      </form>

    </div>
  </div>
</div>


<!-- Modal Entry Data Booking -->
<!-- ============================
      MODAL FORM WIZARD BOOKING
=============================== -->
<div class="modal fade" id="bookingWizardModal" tabindex="-1">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title fw-bold">Booking Kamar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <!-- PROGRESS BAR -->
        <div class="progress mb-3" style="height: 12px;">
          <div id="wizardProgress" class="progress-bar bg-success" role="progressbar" style="width: 33%;"></div>
        </div>

        <!-- STEP TITLE -->
        <h5 id="wizardStepTitle" class="fw-bold mb-3">Step 1: Pilih Kamar</h5>

        <form id="wizardForm">

          <!-- STEP 1 -->
          <div id="step1">
            <div class="row g-3">

          <div class="col-md-6">
  <label class="form-label fw-semibold text-muted">Lantai</label>
  <select class="form-select" id="lantaiKamar" required>
    <option selected disabled>Pilih Lantai</option>
    <?php foreach ($floors as $f) { ?>
      <option value="<?= $f['floor_number']; ?>">
        <?= $f['description']; ?>
      </option>
    <?php } ?>
  </select>
</div>

<div class="col-md-6">
  <label class="form-label fw-semibold text-muted">Nomor Kamar</label>
  <select class="form-select" id="nomorKamar" required>
    <option selected disabled>Pilih Nomor Kamar</option>
  </select>
</div>

              <div class="col-md-6">
                <label class="form-label fw-semibold text-muted">Tanggal Check-in</label>
                <input type="date" class="form-control" id="tglCheckin" required>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold text-muted">Tanggal Check-out</label>
                <input type="date" class="form-control" id="tglCheckout" required>
              </div>

            </div>
          </div>

          <!-- STEP 2 -->
          <div id="step2" style="display:none;">
            <!-- JENIS BOOKING -->
            <div class="mb-3">
              <label class="form-label fw-semibold text-muted">Jenis Booking</label>
              <select class="form-select" id="jenisBooking" required>
                <option selected disabled>Pilih Jenis Booking</option>
                <option value="reservation_only">Reservation Only</option>
                <option value="reservation_checkin">Reservation + Check-in</option>
              </select>
            </div>

            <!-- JENIS TAMU (Hanya tampil jika Reservation + Check-in) -->
            <div id="jenisTamuSection" style="display:none;">
              <div class="mb-3">
                <label class="form-label fw-semibold text-muted">Jenis Tamu</label>
                <select class="form-select" id="jenisTamu">
                  <option selected disabled>Pilih Jenis Tamu</option>
                  <option value="baru">Tamu Baru</option>
                  <option value="lama">Tamu Lama</option>
                </select>
              </div>

              <!-- FORM TAMU BARU -->
              <div id="formTamuBaru" style="display:none;">
                <div class="row g-3">
                  <input type="text" class="form-control" id="user_face_id_baru" >

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="namaBaru">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">No HP</label>
                    <input type="text" class="form-control" id="hpBaru">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">NIK</label>
                    <input type="text" class="form-control" id="nikBaru">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Alamat</label>
                    <input type="text" class="form-control" id="alamatBaru">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Kendaraan</label>
                    <input type="text" class="form-control" id="kendaraanBaru">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Nomor Polisi</label>
                    <input type="text" class="form-control" id="nomorPolisiBaru">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <input type="text" class="form-control" id="jabatanBaru">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Unit Induk</label>
                    <input type="text" class="form-control" id="unitIndukBaru">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">NIPP</label>
                    <input type="text" class="form-control" id="nippBaru">
                  </div>

                  <div class="col-md-6">
                    <label class="filter-label">Gender</label>
                    <select class="form-select filter-select" id="kelaminBaru">
                      <option value="L">Laki-laki</option>
                      <option value="P">Wanita</option>
                    </select>
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="text" class="form-control" id="emailBaru">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Face Recognition</label><br>
                    <button type="button" class="btn btn-primary" id="openCameraBaru">
                      <i class="bi bi-camera"></i> Open Camera
                    </button>

                    <div id="previewBaru" class="mt-2"></div>
                  </div>

                  <!-- CAMERA FRAME BARU -->
                  <div id="cameraFrameBaru" class="mt-3" style="display:none;">
                    <div class="camera-wrapper">
                      <video id="videoBaru" autoplay playsinline class="camera-video"></video>
                    </div>

                    <div id="statusBaru" class="camera-status text-primary">Menyalakan kamera...</div>

                    <div class="text-center mt-2">
                      <span class="badge bg-primary" id="fotoProgressBaru">0 / 6 Foto</span>
                    </div>

                    <div class="text-center">
                      <button type="button" class="btn btn-success mt-2" id="captureBtnBaru" disabled>
                        📸 Ambil Foto
                      </button>
                    </div>

                    <div id="previewListBaru" class="mt-3 d-flex flex-wrap justify-content-center gap-2"></div>
                   <div id="responseBaru" class="p-2 mt-2 text-success bg-white" style="display:block;"></div>
                  </div>
                </div>
              </div>

              <!-- FORM TAMU LAMA -->
              <div id="formTamuLama" style="display:none;">

                <!-- INPUT OTOMATIS -->
                <div class="row g-3 mb-3">

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lama" readonly>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">NIK</label>
                    <input type="text" class="form-control" id="nik_lama" readonly>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label fw-semibold">No HP</label>
                    <input type="text" class="form-control" id="hp_lama" readonly>
                  </div>
                  
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Alamat</label>
                    <input type="text" class="form-control" id="alamat" readonly>
                  </div>
                
                  <div class="col-md-4">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="text" class="form-control" id="email" readonly>
                  </div>
                    <div class="col-md-6">
                    <label class="form-label fw-semibold">Kendaraan</label>
                    <input type="text" class="form-control" id="kendaraanLama">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Nomor Polisi</label>
                    <input type="text" class="form-control" id="nomorPolisi">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <input type="text" class="form-control" id="jabatanLama">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Unit Induk</label>
                    <input type="text" class="form-control" id="unitIndukLama">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">NIPP</label>
                    <input type="text" class="form-control" id="nippLama">
                  </div>
                    <div class="col-md-6">
                     <label class="filter-label">Gender</label>
                    <select class="form-select filter-select" id="kelaminLama">
                      <option value="L">Laki-laki</option>
                      <option value="P">Wanita</option>
                    </select>
                  </div>
                  
               
                  <input type="text" class="form-control" id="user_face_id" hidden>
                </div>

                <!-- ALERT WAJAH COCOK -->
                <div id="autoFillLama" class="alert alert-success py-2 mb-3" style="display:none;">
                  <i class="bi bi-check-circle"></i> Wajah cocok! Data tamu sudah terisi otomatis.
                </div>

                <!-- BUTTON SCAN -->
                <div class="text-center mb-3">
                  <button type="button" class="btn btn-warning px-4" id="openCameraLama">
                    <i class="bi bi-camera"></i> Scan Wajah
                  </button>
                </div>

                <!-- CAMERA SCAN LAMA -->
                <div id="cameraFrameLama" class="mt-3 text-center" style="display:none;">
                  <div class="camera-wrapper mx-auto" style="max-width:420px;">
                    <video id="videoLama" autoplay playsinline class="camera-video"
                          style="width:100%; border-radius:10px; background:black;"></video>
                  </div>

                  <div id="statusLama" class="camera-status text-warning mt-2 fw-semibold">
                    Menyalakan kamera...
                  </div>
                </div>

                <div id="previewLama" class="mt-3"></div>
              </div>
            </div>

            <!-- FORM RESERVATION ONLY -->
            <div id="formReservationOnly" style="display:none;">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Nama Lengkap</label>
                  <input type="text" class="form-control" id="namaReservation">
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">No HP</label>
                  <input type="text" class="form-control" id="hpReservation">
                </div>

                <div class="col-md-6">
                  <label class="form-label fw-semibold">NIK</label>
                  <input type="text" class="form-control" id="nikReservation">
                </div>
                
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Alamat</label>
                  <input type="text" class="form-control" id="alamatReservation">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Kendaraan</label>
                  <input type="text" class="form-control" id="kendaraanReservation">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Nomor Polisi</label>
                  <input type="text" class="form-control" id="nomorPolisiReservation">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <input type="text" class="form-control" id="jabatanReservation">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">Unit Induk</label>
                    <input type="text" class="form-control" id="unitIndukReservation">
                  </div>
                  
                  <div class="col-md-6">
                    <label class="form-label fw-semibold">NIPP</label>
                    <input type="text" class="form-control" id="nippReservation">
                  </div>
                  
                  <div class="col-md-6">
                     <label class="filter-label">Gender</label>
                    <select class="form-select filter-select" id="kelaminReservation">
                      <option value="L">Laki-laki</option>
                      <option value="P">Wanita</option>
                    </select>
                  </div>
                  
                
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Email</label>
                  <input type="text" class="form-control" id="emailReservation">
                </div>
              </div>
            </div>

          </div>
        </form>

      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" id="prevBtn" style="display:none;">Sebelumnya</button>
        <button type="button" class="btn btn-primary" id="nextBtn">Lanjut</button>
        <button type="button" class="btn btn-success" id="finishBtn" style="display:none;">
          <i class="bi bi-save"></i> Simpan Booking
        </button>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.18.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>

<script>
$(document).ready(function() {

     // Event untuk tombol close/tutup manual
    $('.btn-close, .btn-secondary').on('click', function() {
        const modal = $(this).closest('.modal');
        if (modal.attr('id') === 'roomModal') {
            checkin_stopAllCameras();
            checkin_resetAllForms();
        } else if (modal.attr('id') === 'bookingWizardModal') {
            stopAllCameras();
            resetAllForms();
        }
    });
  // =============================================
// MODAL CLOSE EVENT HANDLERS
// =============================================

// Event ketika modal room ditutup
$('#roomModal').on('hidden.bs.modal', function () {
    checkin_stopAllCameras();
    checkin_resetAllForms();
});

// Event ketika modal booking wizard ditutup
$('#bookingWizardModal').on('hidden.bs.modal', function () {
    stopAllCameras();
    resetAllForms();
});

// =============================================
// FUNCTIONS UNTUK STOP KAMERA
// =============================================

// Function untuk stop semua kamera di checkin modal
function checkin_stopAllCameras() {
    // Stop kamera tamu lama
    if (checkin_cameraLama) {
        checkin_cameraLama.stop();
        checkin_cameraLama = null;
    }
    if (checkin_faceMeshLama) {
        checkin_faceMeshLama.close();
        checkin_faceMeshLama = null;
    }

    // Stop kamera tamu baru
    if (checkin_cameraBaru) {
        checkin_cameraBaru.stop();
        checkin_cameraBaru = null;
    }
    if (checkin_faceMeshBaru) {
        checkin_faceMeshBaru.close();
        checkin_faceMeshBaru = null;
    }

    // Reset state
    checkin_alreadyCapturedLama = false;
    checkin_fotoListBaru = [];
    checkin_fotoCounter = 0;
}

// Function untuk stop semua kamera di booking wizard modal
function stopAllCameras() {
    // Stop kamera tamu baru
    if (cameraBaru) {
        cameraBaru.stop();
        cameraBaru = null;
    }
    if (faceMeshBaru) {
        faceMeshBaru.close();
        faceMeshBaru = null;
    }

    // Stop kamera tamu lama
    if (cameraLama) {
        cameraLama.stop();
        cameraLama = null;
    }
    if (faceMeshLama) {
        faceMeshLama.close();
        faceMeshLama = null;
    }

    // Reset state
    alreadyCapturedLama = false;
    fotoListBaru = [];
    fotoCounter = 0;
}

// =============================================
// FUNCTIONS UNTUK RESET FORM
// =============================================

// Function untuk reset semua form di checkin modal
function checkin_resetAllForms() {
    // Reset form checkin
    $("#formCheckin")[0].reset();
    
    // Reset badge
    $("#checkin_guestTypeBadge")
        .removeClass("bg-info bg-warning")
        .addClass("bg-secondary")
        .text("Loading...");
    
    // Sembunyikan semua section
    $("#checkin_sectionTamuLama").hide();
    $("#checkin_sectionTamuBaru").hide();
    
    // Reset camera frames
    $("#checkin_cameraFrameLama").hide();
    $("#checkin_cameraFrameBaru").hide();
    
    // Reset status messages
    $("#checkin_statusLama").html("Menyalakan kamera...").css("color", "yellow");
    $("#checkin_statusBaru").html("Menyalakan kamera...").css("color", "yellow");
    
    // Reset previews
    $("#checkin_previewLama").empty();
    $("#checkin_previewListBaru").empty();
    
    // Reset progress
    $("#checkin_fotoProgressBaru").text("0 / 6 Foto");
    $("#checkin_progressBarBaru").css("width", "0%").text("0%");
    
    // Reset response messages
    $("#checkin_responseBaru").empty();
    $("#checkin_autoFillLama").hide();
    
    // Disable checkin button
    $("#checkinBtn").prop("disabled", true);
}

// Function untuk reset semua form di booking wizard modal
function resetAllForms() {
    // Reset wizard form
    $("#wizardForm")[0].reset();
    
    // Reset progress wizard
    step = 1;
    $("#step1").show();
    $("#step2").hide();
    $("#prevBtn").hide();
    $("#nextBtn").show();
    $("#finishBtn").hide();
    $("#wizardProgress").css("width", "33%");
    $("#wizardStepTitle").text("Step 1: Pilih Kamar");
    
    // Reset semua section
    $("#jenisTamuSection").hide();
    $("#formReservationOnly").hide();
    $("#formTamuBaru").hide();
    $("#formTamuLama").hide();
    
    // Reset camera frames
    $("#cameraFrameBaru").hide();
    $("#cameraFrameLama").hide();
    
    // Reset status messages
    $("#statusBaru").html("Menyalakan kamera...").css("color", "yellow");
    $("#statusLama").html("Menyalakan kamera...").css("color", "yellow");
    
    // Reset previews
    $("#previewListBaru").empty();
    $("#previewLama").empty();
    $("#previewBaru").empty();
    
    // Reset progress
    $("#fotoProgressBaru").text("0 / 6 Foto");
    
    // Reset response messages
    $("#responseBaru").empty();
    $("#autoFillLama").hide();
    
    // Remove invalid classes
    $(".is-invalid").removeClass("is-invalid");
    
    // Reset finish button
    $("#finishBtn").prop("disabled", true);
}

// =============================================
// UPDATE EXISTING FUNCTIONS DENGAN AUTO-CLOSE
// =============================================

// Update function checkin_stopCameraLama
function checkin_stopCameraLama() {
    if (checkin_cameraLama) {
        checkin_cameraLama.stop();
        checkin_cameraLama = null;
    }
    if (checkin_faceMeshLama) {
        checkin_faceMeshLama.close();
        checkin_faceMeshLama = null;
    }
}

// Update function untuk stop camera tamu baru di booking wizard
function stopCameraBaru() {
    if (cameraBaru) {
        cameraBaru.stop();
        cameraBaru = null;
    }
    if (faceMeshBaru) {
        faceMeshBaru.close();
        faceMeshBaru = null;
    }
}

// Update function untuk stop camera tamu lama di booking wizard
function stopCameraLama() {
    if (cameraLama) {
        cameraLama.stop();
        cameraLama = null;
    }
    if (faceMeshLama) {
        faceMeshLama.close();
        faceMeshLama = null;
    }
}


  $("#formCheckin").on("submit", function(e) {
    e.preventDefault();

   
    $.ajax({
        url: "<?= base_url('Booking/checkin'); ?>",
        method: "POST",
        data: $(this).serialize(),
        success: function(res) {
          try { res = JSON.parse(res); } catch(e){}

          console.log('asas', res)
            if (res.success) {
                alert("✅ Check-in berhasil!");
                $("#roomModal").modal("hide");
                window.location.reload();
            } else {
                alert("Check in Gagal");
            }
        },
        error: function() {
                alert("Check in Gagal");
        }
    });
});


  $('#lantaiKamar').change(function () {

    let floorId = $(this).val();

    $.ajax({
        url: '<?= base_url("Dashboard/get_rooms_by_floor"); ?>',
        type: 'POST',
        data: { floor_id: floorId },
        dataType: 'json',
        success: function(response) {

            let rooms = response.rooms;
            let html = `<option selected disabled>Pilih Nomor Kamar</option>`;

            rooms.forEach(r => {
                html += `<option value="${r.room_number}">${r.room_number}</option>`;
            });

            $("#nomorKamar").html(html);
        }
    });

});

    // =============================================
    // BOOKING WIZARD FUNCTIONALITY
    // =============================================
    
    let step = 1;
    loadRooms();

    // --- UPDATE PROGRESS BAR & TITLE ---
    function updateProgress() {
        if (step === 1) {
            $("#wizardProgress").css("width", "33%");
            $("#wizardStepTitle").text("Step 1: Pilih Kamar");
        } else {
            $("#wizardProgress").css("width", "100%");
            $("#wizardStepTitle").text("Step 2: Data Tamu");
        }
    }

    // --- VALIDASI STEP 1 ---
    function validateStep1() {
        let valid = true;
        $("#step1 [required]").each(function() {
            if (!$(this).val()) {
                valid = false;
                $(this).addClass("is-invalid");
            } else {
                $(this).removeClass("is-invalid");
            }
        });
        return valid;
    }

    // --- VALIDASI STEP 2 BERDASARKAN JENIS BOOKING ---
    function validateStep2() {
        let valid = true;
        const jenisBooking = $("#jenisBooking").val();

        if (!jenisBooking) {
            valid = false;
            $("#jenisBooking").addClass("is-invalid");
        } else {
            $("#jenisBooking").removeClass("is-invalid");
        }

        if (jenisBooking === "reservation_checkin") {
            const jenisTamu = $("#jenisTamu").val();
            if (!jenisTamu) {
                valid = false;
                $("#jenisTamu").addClass("is-invalid");
            } else {
                $("#jenisTamu").removeClass("is-invalid");
            }

            if (jenisTamu === "baru") {
                const fields = [
                    "#user_face_id_baru",
                    "#namaBaru",
                    "#hpBaru",
                    "#nikBaru",
                    "#alamatBaru",
                    "#emailBaru",
                    "#kendaraanBaru",
                    "#nomorPolisiBaru",
                    "#unitIndukBaru",
                    "#jabatanBaru",
                    "#nippBaru",
                    "#kelaminBaru"

                ];
                fields.forEach(function(selector) {
                    if (!$(selector).val()) {
                        valid = false;
                        $(selector).addClass("is-invalid");
                    } else {
                        $(selector).removeClass("is-invalid");
                    }
                });
            } else if (jenisTamu === "lama") {
                const fields = [
                    "#user_face_id",
                    "#nama_lama",
                    "#hp_lama",
                    "#nik_lama",
                    "#alamat",
                    "#email",
                    "#kendaraanLama",
                    "#nomorPolisi",
                    "#unitIndukLama",
                    "#jabatanLama", 
                    "#nippLama",
                    "#kelaminLama"
                ];
                fields.forEach(function(selector) {
                    if (!$(selector).val()) {
                        valid = false;
                        $(selector).addClass("is-invalid");
                    } else {
                        $(selector).removeClass("is-invalid");
                    }
                });
            }
        } else if (jenisBooking === "reservation_only") {
            const fields = [
                "#namaReservation",
                "#hpReservation",
                "#nikReservation",
                "#alamatReservation",
                "#emailReservation",
                "#kendaraanReservation",
                "#nomorPolisiReservation",
                "#unitIndukReservation",
                "#jabatanReservation",
                "#nippReservation",
                "#kelaminReservation"
            ];
            fields.forEach(function(selector) {
                if (!$(selector).val()) {
                    valid = false;
                    $(selector).addClass("is-invalid");
                } else {
                    $(selector).removeClass("is-invalid");
                }
            });
        }

        return valid;
    }

    // --- STEP 1 NEXT BUTTON ---
    $("#nextBtn").click(function () {
        if (!validateStep1()) {
            alert("⚠ Harap isi semua data di Step 1!");
            return;
        }

        $("#step1").hide();
        $("#step2").show();

        $("#prevBtn").show();
        $("#nextBtn").hide();
        $("#finishBtn").show();

        step = 2;
        updateProgress();

        // cek Step 2 langsung
        if (validateStep2()) {
            $("#finishBtn").prop("disabled", false);
        } else {
            $("#finishBtn").prop("disabled", true);
        }
    });

    // --- STEP 2 PREV BUTTON ---
    $("#prevBtn").click(function () {
        if (step === 2) {
            $("#step2").hide();
            $("#step1").show();

            $("#prevBtn").hide();
            $("#nextBtn").show();
            $("#finishBtn").hide();

            step = 1;
            updateProgress();
        }
    });

    // --- JENIS BOOKING CHANGE ---
    $("#jenisBooking").change(function () {
        const jenis = $(this).val();
        
        // Reset semua form
        $("#jenisTamuSection").hide();
        $("#formReservationOnly").hide();
        $("#formTamuBaru").hide();
        $("#formTamuLama").hide();
        $("#jenisTamu").val("").trigger("change");

        if (jenis === "reservation_checkin") {
            $("#jenisTamuSection").show();
        } else if (jenis === "reservation_only") {
            $("#formReservationOnly").show();
        }

        // cek validasi
        if (validateStep2()) {
            $("#finishBtn").prop("disabled", false);
        } else {
            $("#finishBtn").prop("disabled", true);
        }
    });

    // --- JENIS TAMU CHANGE ---
    $("#jenisTamu").change(function () {
        const jenis = $(this).val();
        if (jenis === "baru") {
            $("#formTamuBaru").show();
            $("#formTamuLama").hide();
        } else {
            $("#formTamuBaru").hide();
            $("#formTamuLama").show();
        }

        // cek validasi
        if (validateStep2()) {
            $("#finishBtn").prop("disabled", false);
        } else {
            $("#finishBtn").prop("disabled", true);
        }
    });

    // --- CEK SEMUA INPUT STEP 2 UNTUK ENABLE FINISH ---
    $("#wizardForm input, #wizardForm select").on("input change", function() {
        if (step === 2) {
            if (validateStep2()) {
                $("#finishBtn").prop("disabled", false);
            } else {
                $("#finishBtn").prop("disabled", true);
            }
        }
    });

    // --- FINISH BUTTON CLICK ---
    $("#finishBtn").click(function() {
        if (!validateStep2()) {
            alert("⚠ Harap isi semua data tamu sebelum simpan!");
            return;
        }

        // Kirim data AJAX
        const data = {
            nomorKamar: $("#nomorKamar").val(),
            lantaiKamar: $("#lantaiKamar").val(),
            tglCheckin: $("#tglCheckin").val(),
            tglCheckout: $("#tglCheckout").val(),
            jenisBooking: $("#jenisBooking").val(),
        };

        const jenisBooking = $("#jenisBooking").val();
        
        if (jenisBooking === "reservation_checkin") {
            data.jenisTamu = $("#jenisTamu").val();
            
            if ($("#jenisTamu").val() === "baru") {
                data.user_face_id = $("#user_face_id_baru").val();
                data.nama = $("#namaBaru").val();
                data.hp = $("#hpBaru").val();
                data.nik = $("#nikBaru").val();
                data.alamat = $("#alamatBaru").val();
                data.email = $("#emailBaru").val();
                data.kendaraan = $("#kendaraanBaru").val();
                data.nomor_polisi = $("#nomorPolisiBaru").val();
                data.unit_induk = $("#unitIndukBaru").val();
                data.jabatan = $("#jabatanBaru").val();
                data.nipp = $("#nippBaru").val();
                data.kelamin = $("#kelaminBaru").val();
            } else {
                data.user_face_id = $("#user_face_id").val();
                data.nama = $("#nama_lama").val();
                data.hp = $("#hp_lama").val();
                data.nik = $("#nik_lama").val();
                data.alamat = $("#alamat").val();
                data.email = $("#email").val();
                data.kendaraan = $("#kendaraanLama").val();
                data.nomor_polisi = $("#nomorPolisi").val();
                data.unit_induk = $("#unitIndukLama").val();
                data.jabatan = $("#jabatanLama").val();
                data.kelamin = $("#kelaminLama").val();
                data.nipp = $("#nippLama").val();
            }
        } else if (jenisBooking === "reservation_only") {
            data.nama = $("#namaReservation").val();
            data.hp = $("#hpReservation").val();
            data.nik = $("#nikReservation").val();
            data.alamat = $("#alamatReservation").val();
            data.email = $("#emailReservation").val();
            data.kendaraan = $("#kendaraanReservation").val();
            data.nomor_polisi = $("#nomorPolisiReservation").val();
            data.unit_induk = $("#unitIndukReservation").val();
            data.jabatan = $("#jabatanReservation").val();
            data.kelamin = $("#kelaminReservation").val();
            data.nipp = $("#nippReservation").val();
        }

        $.ajax({
            url: "<?= base_url('booking/simpan'); ?>",
            type: "POST",
            dataType: "json",
            data: data,
            beforeSend: function() {
                $("#finishBtn").prop("disabled", true).text("Menyimpan...");
            },
            success: function(response) {
                if (response.status === "success") {
                    alert("✅ Booking berhasil disimpan!");
                    $("#wizardForm")[0].reset();
                    $("#bookingWizardModal").modal("hide");
                    $("#finishBtn").prop("disabled", true);
                    window.location.reload();
                } else {
                    alert("❌ Gagal menyimpan booking: " + response.message);
                    $("#finishBtn").prop("disabled", false).text("Simpan Booking");
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                alert("❌ Terjadi kesalahan sistem. Coba lagi nanti.");
                $("#finishBtn").prop("disabled", false).text("Simpan Booking");
            },
            complete: function() {
                $("#finishBtn").text("Simpan Booking");
            }
        });
    });

    // --- INISIALISASI ---
    updateProgress();
    $("#finishBtn").prop("disabled", true);

    // =============================================
    // FACE RECOGNITION VARIABLES
    // =============================================
    
    let cameraBaru = null;
    let faceMeshBaru = null;
    let fotoListBaru = [];
    let fotoCounter = 0;

    let cameraLama = null;
    let faceMeshLama = null;
    let alreadyCapturedLama = false;
    // let alreadyCapturedCheckin = false;
    


    // =============================================
    // FACE RECOGNITION FUNCTIONS
    // =============================================

    /* HELPER: GENERATE FaceMesh Instance */
    function createFaceMesh(onResultsCallback) {
        const fm = new FaceMesh({
            locateFile: file => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
        });

        fm.setOptions({
            maxNumFaces: 1,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5,
        });

        fm.onResults(onResultsCallback);
        return fm;
    }

    /* HELPER: CAPTURE CURRENT FRAME TO BLOB */
    function captureFrame(videoEl) {
        return new Promise(resolve => {
            const canvas = document.createElement("canvas");
            canvas.width = videoEl.videoWidth;
            canvas.height = videoEl.videoHeight;

            const ctx = canvas.getContext("2d");
            ctx.drawImage(videoEl, 0, 0);

            canvas.toBlob(blob => resolve(blob), "image/jpeg");
        });
    }

    /* TAMU BARU: OPEN CAMERA */
    document.getElementById("openCameraBaru").onclick = function () {
        document.getElementById("cameraFrameBaru").style.display = "block";
        startFaceDetectionBaru();
    };

    /* TAMU BARU: FACE DETECTION + ENABLE CAPTURE */
    function startFaceDetectionBaru() {
        const video = document.getElementById("videoBaru");
        const status = document.getElementById("statusBaru");
        const captureBtn = document.getElementById("captureBtnBaru");

        faceMeshBaru = createFaceMesh(results => {
            if (!results.multiFaceLandmarks?.length) {
                status.innerHTML = "❌ Tidak ada wajah terdeteksi";
                status.style.color = "red";
                captureBtn.disabled = true;
                return;
            }

            status.innerHTML = "✅ Wajah terdeteksi";
            status.style.color = "green";
            captureBtn.disabled = false;
        });

        cameraBaru = new Camera(video, {
            onFrame: async () => await faceMeshBaru.send({ image: video }),
            width: 400,
            height: 300
        });

        cameraBaru.start();
    }

    /* TAMU BARU: CAPTURE FOTO (TOTAL 6) */
    document.getElementById("captureBtnBaru").onclick = async function () {
        if (fotoCounter >= 6) return;

        const video = document.getElementById("videoBaru");
        const blob = await captureFrame(video);

        fotoListBaru.push(blob);
        fotoCounter++;

        document.getElementById("fotoProgressBaru").textContent = fotoCounter + " / 6 Foto";

        // preview
        const img = document.createElement("img");
        img.src = URL.createObjectURL(blob);
        img.width = 80;
        img.classList.add("border", "rounded");
        document.getElementById("previewListBaru").appendChild(img);

        if (fotoCounter === 6) registerNewGuestFaces();
    };

    /* TAMU BARU: REGISTER WAJAH KE API */
    async function registerNewGuestFaces() {
        const responseBox = document.getElementById("responseBaru");
        responseBox.innerHTML = "<b>Mengirim data ke server...</b>";
        responseBox.style.color = "orange";

        const formData = new FormData();
        fotoListBaru.forEach((file, i) => {
            formData.append("photos[]", file, `face_${i + 1}.jpg`);
        });

        try {
            const response = await fetch("<?= base_url('face/register'); ?>", {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                responseBox.innerHTML = "❌ Error " + response.status;
                responseBox.style.color = "red";
                return;
            }

            const result = await response.json();
            if(result.success !== true){
                responseBox.innerHTML = "❌ Gagal register wajah: " + (result.message || "Unknown error");
                responseBox.style.color = "red";
                return;
            }
            $('#user_face_id_baru').val(result.user_id);
            fillFaceId(result.user_id);

            responseBox.innerHTML = "✅ Berhasil register wajah! ";
            console.log("REGISTER RESULT:", result);

        } catch (err) {
            console.error(err);
            responseBox.innerHTML = "❌ Gagal register wajah (JS Error)";
            responseBox.style.color = "red";
        }
    }

    /* TAMU LAMA: OPEN CAMERA */
    document.getElementById("openCameraLama").onclick = function () {
        document.getElementById("cameraFrameLama").style.display = "block";
        alreadyCapturedLama = false;
        startFaceDetectionLama();
    };
    // Global variables untuk tamu lama
let checkin_faceMeshLama = null;
let checkin_cameraLama = null;
let checkin_alreadyCapturedLama = false;

// Event Listener untuk tamu lama
document.getElementById("checkin_startCameraLamaBtn").onclick = function () {
    document.getElementById("checkin_cameraFrameLama").style.display = "block";
    checkin_alreadyCapturedLama = false;
    checkin_startFaceDetectionLama();
};

function checkin_startFaceDetectionLama() {
    const videoEl = document.getElementById("checkin_videoLama");
    const statusEl = document.getElementById("checkin_statusLama");

    checkin_faceMeshLama = new FaceMesh({
        locateFile: file => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
    });

    checkin_faceMeshLama.setOptions({
        maxNumFaces: 1,
        minDetectionConfidence: 0.5,
        minTrackingConfidence: 0.5,
    });

    checkin_faceMeshLama.onResults(results => {
        if (!results.multiFaceLandmarks?.length) {
            statusEl.innerHTML = "❌ Tidak ada wajah terdeteksi";
            statusEl.style.color = "red";
            checkin_alreadyCapturedLama = false;
            return;
        }

        if(!checkin_alreadyCapturedLama) {
            statusEl.innerHTML = "✅ Wajah terdeteksi, sedang memverifikasi...";
            statusEl.style.color = "lime";
        }

        // ANTI SPAM
        if (checkin_alreadyCapturedLama) return;

        checkin_alreadyCapturedLama = true;
        checkin_captureAndSendLama();
    });

    checkin_cameraLama = new Camera(videoEl, {
        onFrame: async () => await checkin_faceMeshLama.send({ image: videoEl }),
        width: 450,
        height: 350
    });

    checkin_cameraLama.start();
}

async function checkin_captureAndSendLama() {
    const video = document.getElementById("checkin_videoLama");
    const statusEl = document.getElementById("checkin_statusLama");

    statusEl.innerHTML = "📤 Mengambil foto & mengirim ke server...";
    statusEl.style.color = "yellow";

    const canvas = document.createElement("canvas");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;

    const ctx = canvas.getContext("2d");
    ctx.drawImage(video, 0, 0);

    canvas.toBlob(async blob => {
        const formData = new FormData();
        formData.append("file", blob, "capture.jpg");

        try {
            const response = await fetch("<?php echo base_url('face/verify'); ?>", {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                statusEl.innerHTML = "❌ API error (" + response.status + ")";
                statusEl.style.color = "red";
                return;
            }

            const result = await response.json().catch(() => {
                statusEl.innerHTML = "❌ Response bukan JSON";
                statusEl.style.color = "red";
                return;
            });

            if (!result) return;

            if (result.success) {
                // Verifikasi booking dengan room_id
                $.ajax({
                    url: "<?php echo base_url('dashboard/verify_booking'); ?>",
                    type: "POST",
                    data: {
                        room_id: $('#room_id').val(),
                        user_id: result.id
                    },
                    success: function (res) {
                        try { res = JSON.parse(res); } catch(e){}

                        if (res.valid === true) {
                            statusEl.innerHTML = "✅ Booking valid! Anda dapat check-in.";
                            statusEl.style.color = "lime";
                            
                            // Isi data user
                            $('#user_id_checkin').val(result.id);
                            $("#modalNipp").val(result.nipp);
                            $("#modalNama").val(result.nama);
                            $("#modalJabatan").val(result.jabatan);
                            $("#modalUnitInduk").val(result.unit_induk);
                            $("#modalNopol").val(result.nomor_polisi);
                            $("#modalKendaraan").val(result.kendaraan);
                            $("#modalTelepon").val(result.hp);
                            $("#modalEmail").val(result.email);
                            $("#modalNik").val(result.nik);
                            $("#modalAlamat").val(result.alamat);
                            
                            // Enable checkin button
                            $('#checkinBtn').prop('disabled', false);
                            
                            // Tampilkan success message
                            document.getElementById("checkin_autoFillLama").style.display = "block";

                        } else {
                            statusEl.innerHTML = "❌ Booking tidak ditemukan / bukan milik Anda!";
                            statusEl.style.color = "red";
                            checkin_alreadyCapturedLama = false; // Reset untuk coba lagi
                        }
                    },
                    error: function () {
                        statusEl.innerHTML = "❌ Gagal memverifikasi booking!";
                        statusEl.style.color = "red";
                        checkin_alreadyCapturedLama = false; // Reset untuk coba lagi
                    }
                });

            } else {
                statusEl.innerHTML = "❌ Wajah tidak cocok dengan data tamu.";
                statusEl.style.color = "red";
                checkin_alreadyCapturedLama = false; // Reset untuk coba lagi
            }

        } catch (error) {
            console.error(error);
            statusEl.innerHTML = "❌ Gagal mengirim / server down";
            statusEl.style.color = "red";
            checkin_alreadyCapturedLama = false; // Reset untuk coba lagi
        }
    }, "image/jpeg");
}

// Function untuk stop camera tamu lama
function checkin_stopCameraLama() {
    if (checkin_cameraLama) {
        checkin_cameraLama.stop();
    }
    if (checkin_faceMeshLama) {
        checkin_faceMeshLama.close();
    }
}
    // document.getElementById("startCameraCheckinBtn").onclick = function () {
    //     document.getElementById("cameraFrameCheckin").style.display = "block";
    //     alreadyCapturedCheckin = false;
    //     startFaceDetectionCheckin();
    // };

    /* START FACE DETECTION TAMU LAMA */
    function startFaceDetectionLama() {
        const videoEl = document.getElementById("videoLama");
        const statusEl = document.getElementById("statusLama");

        faceMeshLama = new FaceMesh({
            locateFile: file => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
        });

        faceMeshLama.setOptions({
            maxNumFaces: 1,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5,
        });

        faceMeshLama.onResults(results => {
            if (!results.multiFaceLandmarks?.length) {
                statusEl.innerHTML = "❌ Tidak ada wajah terdeteksi";
                statusEl.style.color = "red";
                return;
            }

            statusEl.innerHTML = "✅ Wajah terdeteksi, sedang memverifikasi...";
            statusEl.style.color = "lime";

            // ANTI SPAM
            if (alreadyCapturedLama) return;

            alreadyCapturedLama = true;
            captureAndSendLama();
        });

        cameraLama = new Camera(videoEl, {
            onFrame: async () => await faceMeshLama.send({ image: videoEl }),
            width: 450,
            height: 350
        });

        cameraLama.start();
    }
    // function startFaceDetectionCheckin() {
    //     const videoEl = document.getElementById("videoCheckin");
    //     const statusEl = document.getElementById("statusCheckin");

    //     faceMeshLama = new FaceMesh({
    //         locateFile: file => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
    //     });

    //     faceMeshLama.setOptions({
    //         maxNumFaces: 1,
    //         minDetectionConfidence: 0.5,
    //         minTrackingConfidence: 0.5,
    //     });

    //     faceMeshLama.onResults(results => {
    //         if (!results.multiFaceLandmarks?.length) {
    //             statusEl.innerHTML = "❌ Tidak ada wajah terdeteksi";
    //             statusEl.style.color = "red";
    //             alreadyCapturedCheckin = false;
    //             return;
    //         }

    //         if(!alreadyCapturedCheckin) {
    //             statusEl.innerHTML = "✅ Wajah terdeteksi, sedang memverifikasi...";
    //             statusEl.style.color = "lime";
    //         }

    //         // ANTI SPAM
    //         if (alreadyCapturedCheckin) return;

    //         alreadyCapturedCheckin = true;
    //         captureAndSendCheckin();
    //     });

    //     cameraLama = new Camera(videoEl, {
    //         onFrame: async () => await faceMeshLama.send({ image: videoEl }),
    //         width: 450,
    //         height: 350
    //     });

    //     cameraLama.start();
    // }

    /* CAPTURE & SEND FOTO (AUTO SEND 1x) */
    async function captureAndSendLama() {
        const video = document.getElementById("videoLama");
        const statusEl = document.getElementById("statusLama");

        statusEl.innerHTML = "📤 Mengambil foto & mengirim ke server...";
        statusEl.style.color = "yellow";

        const canvas = document.createElement("canvas");
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext("2d");
        ctx.drawImage(video, 0, 0);

        canvas.toBlob(async blob => {
            const formData = new FormData();
            formData.append("file", blob, "capture.jpg");

            try {
                const response = await fetch("<?php echo base_url('face/verify'); ?>", {
                    method: "POST",
                    body: formData
                });

                if (!response.ok) {
                    statusEl.innerHTML = "❌ API error (" + response.status + ")";
                    statusEl.style.color = "red";
                    return;
                }

                const result = await response.json().catch(() => {
                    statusEl.innerHTML = "❌ Response bukan JSON";
                    statusEl.style.color = "red";
                    return;
                });

                if (!result) return;

                if (result.success) {
                    statusEl.innerHTML = "✅ Wajah cocok! Mengisi data...";
                    statusEl.style.color = "lime";

                    document.getElementById("nama_lama").value = result.nama;
                    document.getElementById("nik_lama").value = result.nik;
                    document.getElementById("hp_lama").value = result.hp;
                    document.getElementById("alamat").value = result.alamat;
                    document.getElementById("email").value = result.email;
                    document.getElementById("user_face_id").value = result.user_face_id;
                    document.getElementById("kendaraanLama").value = result.kendaraan;
                    document.getElementById("nomorPolisi").value = result.nomor_polisi;
                    document.getElementById("jabatanLama").value = result.jabatan;
                    document.getElementById("unitIndukLama").value = result.unit_induk
                    document.getElementById("nippLama").value = result.nipp;
                    document.getElementById("kelaminLama").value = result.kelamin;
                    fillFaceId(result.user_face_id);
                    document.getElementById("autoFillLama").style.display = "block";
                } else {
                    statusEl.innerHTML = "❌ Tidak cocok.";
                    statusEl.style.color = "red";
                }

            } catch (error) {
                console.error(error);
                statusEl.innerHTML = "❌ Gagal mengirim / server down";
                statusEl.style.color = "red";
            }
        }, "image/jpeg");
    }
    /* CAPTURE & SEND FOTO (AUTO SEND 1x) */
    // async function captureAndSendCheckin() {
    //     const video = document.getElementById("videoCheckin");
    //     const statusEl = document.getElementById("statusCheckin");

    //     statusEl.innerHTML = "📤 Mengambil foto & mengirim ke server...";
    //     statusEl.style.color = "yellow";

    //     const canvas = document.createElement("canvas");
    //     canvas.width = video.videoWidth;
    //     canvas.height = video.videoHeight;

    //     const ctx = canvas.getContext("2d");
    //     ctx.drawImage(video, 0, 0);

    //     canvas.toBlob(async blob => {
    //         const formData = new FormData();
    //         formData.append("file", blob, "capture.jpg");

    //         try {
    //             const response = await fetch("<?php echo base_url('face/verify'); ?>", {
    //                 method: "POST",
    //                 body: formData
    //             });

    //             if (!response.ok) {
    //                 statusEl.innerHTML = "❌ API error (" + response.status + ")";
    //                 statusEl.style.color = "red";
    //                 return;
    //             }

    //             const result = await response.json().catch(() => {
    //                 statusEl.innerHTML = "❌ Response bukan JSON";
    //                 statusEl.style.color = "red";
    //                 return;
    //             });

    //             if (!result) return;

    //             if (result.success) {
    //                 // statusEl.innerHTML = "✅ Wajah cocok! Mengisi data...";
    //                 // statusEl.style.color = "lime";
    //                 $.ajax({
    //                     url: "<?php echo base_url('dashboard/verify_booking'); ?>",
    //                     type: "POST",
    //                     data: {
    //                         room_id: $('#room_id').val(),
    //                         user_id: result.id
    //                     },
    //                     success: function (res) {
    //                         try { res = JSON.parse(res); } catch(e){}

    //                         if (res.valid === true) {

    //                             statusEl.innerHTML = "✅ Booking valid! Anda dapat check-in.";
    //                             // statusEl.style.color = "lime";
    //                           $('#user_id_checkin').val(result.id);
    //                           $("#modalNipp").val(result.nipp);
    //                           $("#modalNama").val(result.nama);
    //                           $("#modalJabatan").val(result.jabatan);
    //                           $("#modalUnitInduk").val(result.unit_induk);
    //                           $("#modalNopol").val(result.nomor_polisi);
    //                           $("#modalKendaraan").val(result.kendaraan);
    //                           $("#modalTelepon").val(result.hp);
    //                           $("#modalEmail").val(result.email);
    //                           $("#modalNik").val(result.nik);
    //                           $("#modalAlamat").val(result.alamat);
    //                           $('#checkinBtn').prop('disabled', false);
    //                             // // Lanjut ke proses check-in
    //                             // // contoh:
    //                             // completeCheckin(roomId, userFaceId);

    //                         } else {
    //                             // statusEl.innerHTML = "❌ Booking tidak ditemukan / bukan milik Anda!";
    //                             // statusEl.style.color = "red";
    //                         }
    //                     },
    //                     error: function () {
    //                         statusEl.innerHTML = "❌ Gagal memverifikasi booking!";
    //                         statusEl.style.color = "red";
    //                     }
    //                 });


    //             } else {
    //                 statusEl.innerHTML = "❌ Tidak cocok.";
    //                 statusEl.style.color = "red";
    //             }

    //         } catch (error) {
    //             console.error(error);
    //             statusEl.innerHTML = "❌ Gagal mengirim / server down";
    //             statusEl.style.color = "red";
    //         }
    //     }, "image/jpeg");
    // }

    /* FILL FACE ID DAN VALIDASI */
    function fillFaceId(faceId) {
        if ($("#jenisTamu").val() === "baru") {
            $("#user_face_id_baru").val(faceId);
        } else {
            $("#user_face_id").val(faceId);
        }

        if (validateStep2()) {
            $("#finishBtn").prop("disabled", false);
        } else {
            $("#finishBtn").prop("disabled", true);
        }
    }
});

// =============================================
// ROOM MANAGEMENT FUNCTIONS (GLOBAL)
// =============================================

let stream = null;
let photoData = null;

// Room Modal Events
document.getElementById('roomModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const roomNumber = $('#room_id').val();
    // $('#room_id').val(roomNumber);
    const modalTitle = this.querySelector('.modal-title');
    modalTitle.textContent = `Detail Ruangan ${roomNumber}`;
    // stopCamera();
});

// Camera Functions for Room Modal
// document.getElementById('startCameraCheckinBtn').addEventListener('click', async function() {
//     try {
//         stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
//         const video = document.getElementById('cameraPreview');
//         video.srcObject = stream;
//         document.getElementById('checkinSection').classList.add('d-none');
//         document.getElementById('cameraSection').classList.remove('d-none');
//     } catch (error) {
//         alert('Tidak dapat mengakses kamera.');
//     }
// });

// document.getElementById('captureBtn').addEventListener('click', function() {
//     const video = document.getElementById('cameraPreview');
//     const canvas = document.getElementById('photoCanvas');
//     const context = canvas.getContext('2d');
//     canvas.width = video.videoWidth;
//     canvas.height = video.videoHeight;
//     context.drawImage(video, 0, 0, canvas.width, canvas.height);
//     photoData = canvas.toDataURL('image/jpeg');
//     document.getElementById('cameraSection').classList.add('d-none');
//     document.getElementById('photoPreview').classList.remove('d-none');
//     stopCamera();
// });

// document.getElementById('checkinBtn').addEventListener('click', function() {
//     if (photoData) {
//         document.getElementById('checkinSection').innerHTML = `
//             <div class="alert alert-success text-center">
//                 <i class="bi bi-check-circle"></i> Sudah Check-in
//             </div>
//             <div class="text-center">
//                 <img src="${photoData}" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
//             </div>`;
//         document.getElementById('photoPreview').classList.add('d-none');
//     }
// });

// function stopCamera() {
//     if (stream) {
//         stream.getTracks().forEach(track => track.stop());
//         stream = null;
//     }
// }

document.getElementById('roomModal').addEventListener('hidden.bs.modal', function() {
    stopCamera();
});

// Room Data Functions
let checkin_faceMeshBaru = null;
let checkin_cameraBaru = null;
let checkin_fotoListBaru = [];
let checkin_fotoCounter = 0;

function loadDataRoom(room_id) {
    $.get("dashboard/room_detail/" + room_id, function(res) {
        $("#room_id").val(room_id);
        
        // Check apakah tamu baru atau lama
        checkUserFace(room_id);
        
        $("#roomModal").modal("show");
    });
}

function checkUserFace(room_id) {
    $.ajax({
        url: "booking/check_guest_face",
        type: "GET",
        data: { room_id: room_id },
        success: function(res) {
            try { res = JSON.parse(res); } catch(e){}
            if (!res.success) {
               alert("Info", res.message, "info");
                return;
            }

            // Tampilkan badge status tamu
            const guestTypeBadge = document.getElementById("checkin_guestTypeBadge");
            
            if (res.data.user_face_id) {
                // TAMU LAMA
                guestTypeBadge.className = "badge bg-info fs-6";
                guestTypeBadge.textContent = "Tamu Lama";
                
                // Tampilkan section tamu lama, sembunyikan tamu baru
                document.getElementById("checkin_sectionTamuLama").style.display = "block";
                document.getElementById("checkin_sectionTamuBaru").style.display = "none";
                
                // Set checkin_user_face_id untuk tamu lama
                $("#checkin_user_face_id").val(res.data.user_face_id);
                
            } else {
                // TAMU BARU
                guestTypeBadge.className = "badge bg-warning fs-6";
                guestTypeBadge.textContent = "Tamu Baru";
                  $("#modalNipp").val(res.data.nipp || '');
                  $("#modalNama").val(res.data.guest_name || '');
                  $("#user_id_checkin").val(res.data.guest_id || '');
                  $("#modalJabatan").val(res.data.jabatan || '');
                  $("#modalUnitInduk").val(res.data.unit_induk || '');
                  $("#modalNopol").val(res.data.nomor_polisi || '');
                  $("#modalKendaraan").val(res.data.kendaraan || '');
                  $("#modalTelepon").val(res.data.hp || '');
                  $("#modalEmail").val(res.data.email || '');
                  $("#modalNik").val(res.data.nik || '');
                  $("#modalAlamat").val(res.data.alamat || '');

                
                // Tampilkan section tamu baru, sembunyikan tamu lama
                document.getElementById("checkin_sectionTamuLama").style.display = "none";
                document.getElementById("checkin_sectionTamuBaru").style.display = "block";
                
                // Reset state untuk tamu baru
                checkin_resetTamuBaruState();
            }

        },
        error: function() {
            alert("Error", "Gagal memeriksa status tamu", "error");
        }
    });
}

// Reset state untuk tamu baru
function checkin_resetTamuBaruState() {
    checkin_fotoListBaru = [];
    checkin_fotoCounter = 0;
    document.getElementById("checkin_fotoProgressBaru").textContent = "0 / 6 Foto";
    document.getElementById("checkin_progressBarBaru").style.width = "0%";
    document.getElementById("checkin_progressBarBaru").textContent = "0%";
    document.getElementById("checkin_previewListBaru").innerHTML = "";
    document.getElementById("checkin_responseBaru").innerHTML = "";
    document.getElementById("checkinBtn").disabled = true;
}

// ========== TAMU BARU FUNCTIONS ==========

document.getElementById("checkin_openCameraBaruBtn").onclick = function () {
    document.getElementById("checkin_cameraFrameBaru").style.display = "block";
    checkin_startFaceDetectionBaru();
};

function checkin_startFaceDetectionBaru() {
    const video = document.getElementById("checkin_videoBaru");
    const status = document.getElementById("checkin_statusBaru");
    const captureBtn = document.getElementById("checkin_captureBaruBtn");

    // Stop camera sebelumnya jika ada
    if (checkin_cameraBaru) {
        checkin_cameraBaru.stop();
    }

    checkin_faceMeshBaru = createFaceMeshNew(results => {
        if (!results.multiFaceLandmarks?.length) {
            status.innerHTML = "❌ Tidak ada wajah terdeteksi";
            status.style.color = "red";
            captureBtn.disabled = true;
            return;
        }

        status.innerHTML = "✅ Wajah terdeteksi";
        status.style.color = "green";
        captureBtn.disabled = false;
    });

    checkin_cameraBaru = new Camera(video, {
        onFrame: async () => await checkin_faceMeshBaru.send({ image: video }),
        width: 400,
        height: 300
    });

    checkin_cameraBaru.start();
}

/* TAMU BARU: CAPTURE FOTO (TOTAL 6) */
document.getElementById("checkin_captureBaruBtn").onclick = async function () {
    if (checkin_fotoCounter >= 6) return;

    const video = document.getElementById("checkin_videoBaru");
    const blob = await captureFrameNew(video);

    checkin_fotoListBaru.push(blob);
    checkin_fotoCounter++;

    // Update progress
    const progressPercent = (checkin_fotoCounter / 6) * 100;
    document.getElementById("checkin_fotoProgressBaru").textContent = checkin_fotoCounter + " / 6 Foto";
    document.getElementById("checkin_progressBarBaru").style.width = progressPercent + "%";
    document.getElementById("checkin_progressBarBaru").textContent = Math.round(progressPercent) + "%";

    // preview
    const img = document.createElement("img");
    img.src = URL.createObjectURL(blob);
    img.width = 80;
    img.classList.add("border", "rounded");
    document.getElementById("checkin_previewListBaru").appendChild(img);

    if (checkin_fotoCounter === 6) {
        checkin_registerNewGuestFaces();
    }
};

function captureFrameNew(videoEl) {
        return new Promise(resolve => {
            const canvas = document.createElement("canvas");
            canvas.width = videoEl.videoWidth;
            canvas.height = videoEl.videoHeight;

            const ctx = canvas.getContext("2d");
            ctx.drawImage(videoEl, 0, 0);

            canvas.toBlob(blob => resolve(blob), "image/jpeg");
        });
    }

/* TAMU BARU: REGISTER WAJAH KE API */
async function checkin_registerNewGuestFaces() {
    const responseBox = document.getElementById("checkin_responseBaru");
    responseBox.innerHTML = "<b>Mengirim data ke server...</b>";
    responseBox.style.color = "orange";

    const formData = new FormData();
    checkin_fotoListBaru.forEach((file, i) => {
        formData.append("photos[]", file, `face_${i + 1}.jpg`);
    });

    try {
        const response = await fetch("<?= base_url('face/register'); ?>", {
            method: "POST",
            body: formData
        });

        if (!response.ok) {
            responseBox.innerHTML = "❌ Error " + response.status;
            responseBox.style.color = "red";
            return;
        }

        const result = await response.json();
        if(result.success !== true){
            responseBox.innerHTML = "❌ Gagal register wajah: " + (result.message || "Unknown error");
            responseBox.style.color = "red";
            return;
        }
        
        // Set checkin_user_face_id dan enable checkin button
        $('#checkin_user_face_id').val(result.user_id);
        document.getElementById("checkinBtn").disabled = false;

        responseBox.innerHTML = "✅ Berhasil register wajah! Silakan check-in.";
        responseBox.style.color = "green";

    } catch (err) {
        console.error(err);
        responseBox.innerHTML = "❌ Gagal register wajah (JS Error)";
        responseBox.style.color = "red";
    }
}

// ========== TAMU LAMA FUNCTIONS ==========

document.getElementById("checkin_startCameraLamaBtn").onclick = function () {
    document.getElementById("checkin_cameraFrameLama").style.display = "block";
    checkin_startFaceDetectionLama();
};

function checkin_startFaceDetectionLama() {
    const video = document.getElementById("checkin_videoLama");
    const status = document.getElementById("checkin_statusLama");

    // Implementasi face recognition untuk tamu lama
    // (gunakan fungsi yang sudah ada untuk verifikasi wajah)
    
    // Contoh sederhana - langsung enable checkin button
    // Dalam implementasi nyata, lakukan face recognition dulu
    setTimeout(() => {
        status.innerHTML = "✅ Verifikasi berhasil!";
        status.style.color = "green";
        document.getElementById("checkinBtn").disabled = false;
        document.getElementById("checkin_autoFillLama").style.display = "block";
    }, 2000);
}

// Form submission
document.getElementById("formCheckin").onsubmit = function(e) {
    e.preventDefault();
    // Implementasi submit form checkin
    // alert("Check-in berhasil!");
};

function createFaceMeshNew(onResultsCallback) {
        const fm = new FaceMesh({
            locateFile: file => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
        });

        fm.setOptions({
            maxNumFaces: 1,
            minDetectionConfidence: 0.5,
            minTrackingConfidence: 0.5,
        });

        fm.onResults(onResultsCallback);
        return fm;
    }

function loadRooms(search = '', floor = '', status = '') {
    $.ajax({
        url: '<?= base_url("dashboard/filter") ?>',
        method: 'POST',
        data: { search, floor, status },
        dataType: 'json',
        success: function(result) {
            if (!result.success) return;

            const rooms = result.rooms;
            let currentFloor = null;
            let html = '';

            rooms.forEach(room => {
                // buka card baru tiap lantai
                if (currentFloor !== room.floor_id) {
                    if (currentFloor !== null) {
                        html += '</div></div></div>';
                    }
                    currentFloor = room.floor_id;
                    html += `<div class="card mb-4 shadow-sm">
                                <div class="card-header">Lantai ${room.floor_id}</div>
                                <div class="card-body">
                                    <div class="row row-cols-2 row-cols-md-4 g-3">`;
                }

                let statusAttr = '';
                if (room.status === 'booked') statusAttr = 'data-status="occupied"';
                else if (room.status === 'cleaning') statusAttr = 'data-status="cleaning"';

                html += `<div class="col">
                            <button class="room-btn w-100 py-3" ${statusAttr} onclick="loadDataRoom('${room.room_id}')" data-room="${room.room_number}">
                                ${room.room_number}
                            </button>
                         </div>`;
            });

            if (currentFloor !== null) html += '</div></div></div>';
            $('#rooms-container').html(html);
        },
        error: function(err) {
            console.error(err);
        }
    });
}

// Event filter
$('#filter-search, #filter-lantai, #filter-status').on('input change', function() {
    const search = $('#filter-search').val();
    const floor = $('#filter-lantai').val();
    const status = $('#filter-status').val();
    loadRooms(search, floor, status);
});
</script>
<!-- ================================
       FACE MESH & CAMERA LIBRARIES
================================= -->


<script>



</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

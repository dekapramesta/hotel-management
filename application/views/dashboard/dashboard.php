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
          <div id="wizardProgress" class="progress-bar bg-success" role="progressbar" style="width: 50%;"></div>
        </div>

        <!-- STEP TITLE -->
        <h5 id="wizardStepTitle" class="fw-bold mb-3">Step 1: Pilih Kamar</h5>

        <form id="wizardForm">

          <!-- STEP 1 -->
          <div id="step1">
            <div class="row g-3">

              <div class="col-md-6">
                <label class="form-label fw-semibold text-muted">Nomor Kamar</label>
                <select class="form-select" id="nomorKamar" required>
                  <option selected disabled>Pilih Nomor Kamar</option>
                  <option value="201">201</option>
                  <option value="202">202</option>
                 
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold text-muted">Lantai</label>
                <select class="form-select" id="lantaiKamar" required>
                  <option selected disabled>Pilih Lantai</option>
                  <option value="2">Lantai 2</option>
                  <option value="3">Lantai 3</option>
                  <option value="3">Lantai 4</option>
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

            <!-- JENIS TAMU -->
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

  

     loadRooms();

     let step = 1;

    // --- UPDATE PROGRESS BAR & TITLE ---
    function updateProgress() {
        if (step === 1) {
            $("#wizardProgress").css("width", "50%");
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

    // --- VALIDASI STEP 2 BERDASARKAN JENIS TAMU ---
    function validateStep2() {
        let valid = true;
        const jenis = $("#jenisTamu").val();

        if (jenis === "baru") {
            const fields = [
                "#user_face_id_baru",
                "#namaBaru",
                "#hpBaru",
                "#nikBaru",
                "#alamatBaru",
                "#emailBaru"
            ];
            fields.forEach(function(selector) {
                if (!$(selector).val()) {
                    valid = false;
                    $(selector).addClass("is-invalid");
                } else {
                    $(selector).removeClass("is-invalid");
                }
            });
        } else if (jenis === "lama") {
            const fields = [
                "#user_face_id",
                "#nama_lama",
                "#hp_lama",
                "#nik_lama",
                "#alamat",
                "#email"
            ];
            fields.forEach(function(selector) {
                if (!$(selector).val()) {
                    valid = false;
                    $(selector).addClass("is-invalid");
                } else {
                    $(selector).removeClass("is-invalid");
                }
            });
        } else {
            valid = false; // belum pilih jenis tamu
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

        // cek Step 2 setelah pilih jenis tamu
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
            jenisTamu: $("#jenisTamu").val(),
        };

        if ($("#jenisTamu").val() === "baru") {
            data.user_face_id = $("#user_face_id_baru").val();
            data.nama = $("#namaBaru").val();
            data.hp = $("#hpBaru").val();
            data.nik = $("#nikBaru").val();
            data.alamat = $("#alamatBaru").val();
            data.email = $("#emailBaru").val();
        } else {
            data.user_face_id = $("#user_face_id").val();
            data.nama = $("#nama_lama").val();
            data.hp = $("#hp_lama").val();
            data.nik = $("#nik_lama").val();
            data.alamat = $("#alamat").val();
            data.email = $("#email").val();
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
    // Tampilkan form sesuai jenis tamu
    $("#jenisTamu").change(function () {
        let jenis = $(this).val();
        if (jenis === "baru") {
            $("#formTamuBaru").show();
            $("#formTamuLama").hide();
        } else {
            $("#formTamuBaru").hide();
            $("#formTamuLama").show();
        }

        // Reset tombol simpan
        $("#finishBtn").prop("disabled", true);
    });

    /* ======================================================================
    GLOBAL VARIABLES
====================================================================== */
let cameraBaru = null;
let faceMeshBaru = null;
let fotoListBaru = [];
let fotoCounter = 0;

let cameraLama = null;
let faceMeshLama = null;
let alreadyCapturedLama = false;


/* ======================================================================
    HELPER: GENERATE FaceMesh Instance
====================================================================== */
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

/* ======================================================================
   HELPER: CAPTURE CURRENT FRAME TO BLOB
====================================================================== */
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

/* ======================================================================
    TAMU BARU: OPEN CAMERA
====================================================================== */
document.getElementById("openCameraBaru").onclick = function () {
    document.getElementById("cameraFrameBaru").style.display = "block";
    startFaceDetectionBaru();
};

/* ======================================================================
    TAMU BARU: FACE DETECTION + ENABLE CAPTURE
====================================================================== */
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

/* ======================================================================
    TAMU BARU: CAPTURE FOTO (TOTAL 6)
====================================================================== */
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

/* ======================================================================
    TAMU BARU: REGISTER WAJAH KE API
====================================================================== */
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

        // tampilkan JSON ke UI
        responseBox.innerHTML = "✅ Berhasil register wajah! ";
        // responseBox.style.color = "lime";

        console.log("REGISTER RESULT:", result);

    } catch (err) {
        console.error(err);
        responseBox.innerHTML = "❌ Gagal register wajah (JS Error)";
        responseBox.style.color = "red";
    }
}


/* ======================================================================
    TAMU LAMA: OPEN CAMERA
====================================================================== */
document.getElementById("openCameraLama").onclick = function () {
    document.getElementById("cameraFrameLama").style.display = "block";
    alreadyCapturedLama = false;
    startFaceDetectionLama();
};


// ======================================
// START FACE DETECTION TAMU LAMA
// ======================================
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

        // ==== ANTI SPAM ====
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


// ======================================
// CAPTURE & SEND FOTO (AUTO SEND 1x)
// ======================================
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
            console.log('cek data', response)

            // ==== jika API balikan HTML / 404 ====
            if (!response.ok) {
                statusEl.innerHTML = "❌ API error (" + response.status + ")";
                statusEl.style.color = "red";
                return; // TIDAK RESET, anti spam
            }

            const result = await response.json().catch(() => {
                statusEl.innerHTML = "❌ Response bukan JSON";
                statusEl.style.color = "red";
                return;
            });
            console.log('hasul',result)

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

// Misal kamu dapat response dari API
function fillFaceId(faceId) {
    $("#user_face_id_baru").val(faceId); // atau #user_face_id untuk tamu lama

    // panggil validasi Step 2 agar tombol Finish otomatis aktif
    if (validateStep2()) {
        $("#finishBtn").prop("disabled", false);
    } else {
        $("#finishBtn").prop("disabled", true);
    }
}
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


//  function loadRooms() {
//     $.ajax({
//         url: "<?= base_url('rooms/filter') ?>",
//         type: "POST",
//         data: {
//             search: $("#filter-search").val(),
//             floor: $("#filter-lantai").val(),
//             status: $("#filter-status").val()
//         },
//         dataType: "json",
//         success: function(rooms){
//             let html = '';
//             let current_floor = null;

//             rooms.forEach(room => {
//                 if(current_floor != room.floor_id){
//                     if(current_floor !== null) html += '</div></div></div>';
//                     current_floor = room.floor_id;
//                     html += '<div class="card mb-4 shadow-sm">'+
//                             '<div class="card-header">Lantai '+current_floor+'</div>'+
//                             '<div class="card-body"><div class="row row-cols-2 row-cols-md-4 g-3">';
//                 }

//                 let btn_class = "room-btn w-100 py-3 ";
//                 if(room.status == 'available') btn_class += "btn-success";
//                 else if(room.status == 'booked') btn_class += "btn-warning";
//                 else btn_class += "btn-secondary";

//                 html += '<div class="col">'+
//                         '<button class="'+btn_class+'" data-room="'+room.room_number+'" onclick="loadDataRoom('+room.room_number+')">'+
//                         room.room_number+
//                         '</button></div>';
//             });

//             html += '</div></div></div>';
//             $("#rooms-container").html(html);
//         }
//     });
// }


function loadDataRoom(room_id){
   $.get("dashboard/room_detail/" + room_id, function(res) {
    if (res.data) {
      $("#roomModal").modal("show");
    } else {
      $("#roomModal").modal("show");
    }
  })
}

function loadRooms(search = '', floor = '', status = '') {
        $.ajax({
            url: '<?= base_url("dashboard/filter") ?>', // sesuaikan URL controller
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
                            html += '</div></div></div>'; // tutup lantai sebelumnya
                        }
                        currentFloor = room.floor_id;
                        html += `<div class="card mb-4 shadow-sm">
                                    <div class="card-header">Lantai ${room.floor_id}</div>
                                    <div class="card-body">
                                        <div class="row row-cols-2 row-cols-md-4 g-3">`;
                    }

                    // set data-status
                    let statusAttr = '';
                    if (room.status === 'booked') statusAttr = 'data-status="occupied"';
                    else if (room.status === 'cleaning') statusAttr = 'data-status="cleaning"';
                    // available = tidak ada atribut

                    html += `<div class="col">
                                <button class="room-btn w-100 py-3" ${statusAttr} onclick="loadDataRoom('${room.room_number}')" data-room="${room.room_number}">
                                    ${room.room_number}
                                </button>
                             </div>`;
                });

                // tutup tag lantai terakhir
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

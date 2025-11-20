<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Room Cleaning | Safeguard Hotel Management</title>

    <!-- Bootstrap 5 -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <!-- Google Font -->
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />

    <style>
      body {
        font-family: "Poppins", sans-serif;
        background: linear-gradient(135deg, #e8edf3 0%, #ffffff 100%);
        min-height: 100vh;
        margin: 0;
        display: flex;
        flex-direction: column;
      }

      main {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background: url("https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1350&q=80")
          center/cover no-repeat;
        position: relative;
      }

      main::before {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(5px);
      }

      .cleaning-card {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 2rem;
        padding: 2rem 2.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        width: 90%;
        text-align: center;
        transition: 0.3s ease;
        animation: fadeIn 0.8s ease;
      }

      .cleaning-card:hover {
        transform: translateY(-3px);
      }

      .cleaning-card img {
        width: 100px;
        margin-bottom: 1rem;
      }

      h5 {
        font-weight: 700;
        color: #c2a54d;
        margin-bottom: 0.2rem;
      }

      p.subtitle {
        font-size: 0.9rem;
        color: #777;
        margin-bottom: 1.5rem;
      }

      .form-select {
        border-radius: 50px;
        padding: 0.75rem 1rem;
        text-align: center;
        font-weight: 500;
        font-size: 1.05rem;
        border: 2px solid #eee;
        transition: 0.2s;
      }

      .form-select:focus {
        border-color: #c2a54d;
        box-shadow: 0 0 0 0.2rem rgba(194, 165, 77, 0.25);
      }

      .btn-clean {
        display: inline-block;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.1rem;
        color: #fff;
        padding: 0.8rem 2rem;
        width: 48%;
        transition: all 0.3s ease;
      }

      .btn-start {
        background: #00c851;
      }

      .btn-start:hover {
        background: #00b246;
        transform: scale(1.05);
      }

      .btn-finish {
        background: #ff4444;
      }

      .btn-finish:hover {
        background: #e03a3a;
        transform: scale(1.05);
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(15px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      footer {
        text-align: center;
        padding: 1rem;
        color: #999;
        font-size: 0.85rem;
      }

      @media (max-width: 576px) {
        .btn-clean {
          width: 100%;
          margin-bottom: 0.8rem;
        }
      }
    </style>
  </head>

  <body>
    <main>
      <div class="cleaning-card">
        <img src="<?= base_url('assets/img/hotel.png') ?>" alt="Hotel Logo" />

        <h5>SAFEGUARD HOTEL MANAGEMENT</h5>

        <!-- Pilih Lantai -->
        <div class="mb-3">
          <select class="form-select" id="floorSelect">
            <option selected disabled>🏢 Pilih Lantai</option>
            <option value="1">Lantai 1</option>
            <option value="2">Lantai 2</option>
            <option value="3">Lantai 3</option>
            <option value="4">Lantai 4</option>
          </select>
        </div>

        <!-- Pilih Kamar -->
        <div class="mb-4">
          <select class="form-select" id="roomSelect">
            <option selected disabled>🚪 Pilih Nomor Kamar</option>
            <option>101</option>
            <option>102</option>
            <option>103</option>
            <option>104</option>
          </select>
        </div>

        <!-- Tombol Mulai / Selesai -->
        <div class="d-flex justify-content-center gap-3 flex-wrap">
          <button class="btn-clean btn-start" id="btnStart">Mulai</button>
          <button class="btn-clean btn-finish" id="btnFinish">Selesai</button>
        </div>
      </div>
    </main>

    <footer>© 2025 Safeguard Hotel Management</footer>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
  const startBtn = document.getElementById("btnStart");
  const finishBtn = document.getElementById("btnFinish");

  let startTime = null;
  let timerInterval = null;

  // Display waktu
  const timerDisplay = document.createElement("div");
  timerDisplay.style.marginTop = "15px";
  timerDisplay.style.fontSize = "1.2rem";
  timerDisplay.style.fontWeight = "600";
  timerDisplay.style.color = "#333";
  timerDisplay.innerText = "";
  document.querySelector(".cleaning-card").appendChild(timerDisplay);

  function formatTime(ms) {
    let totalSeconds = Math.floor(ms / 1000);
    let hours = Math.floor(totalSeconds / 3600);
    let minutes = Math.floor((totalSeconds % 3600) / 60);
    let seconds = totalSeconds % 60;

    return (
      String(hours).padStart(2, "0") +
      ":" +
      String(minutes).padStart(2, "0") +
      ":" +
      String(seconds).padStart(2, "0")
    );
  }

  startBtn.addEventListener("click", () => {
    const floor = document.getElementById("floorSelect").value;
    const room = document.getElementById("roomSelect").value;

    if (!floor || !room) {
      Swal.fire({
        icon: "warning",
        title: "Data Belum Lengkap",
        text: "Harap pilih lantai dan nomor kamar terlebih dahulu!",
      });
      return;
    }

    startTime = Date.now();
    timerDisplay.innerText = "⏳ 00:00:00";

    timerInterval = setInterval(() => {
      const elapsed = Date.now() - startTime;
      timerDisplay.innerText = "⏳ " + formatTime(elapsed);
    }, 1000);

    startBtn.innerText = "Sedang Dikerjakan...";
    startBtn.disabled = true;
    startBtn.style.background = "#ffc107";

    Swal.fire({
      icon: "success",
      title: "Pembersihan Dimulai!",
      text: `Kamar ${room} di Lantai ${floor} sedang dibersihkan.`,
      timer: 1500,
      showConfirmButton: false
    });
  });

  finishBtn.addEventListener("click", () => {
    if (!startTime) {
      Swal.fire({
        icon: "info",
        title: "Belum Mulai",
        text: "Klik Mulai terlebih dahulu sebelum menyelesaikan.",
      });
      return;
    }

    // KONFIRMASI TERLEBIH DAHULU
    Swal.fire({
      title: "Yakin sudah selesai?",
      text: "Pastikan kamar benar-benar sudah selesai dibersihkan.",
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Ya, Selesai",
      cancelButtonText: "Belum",
      reverseButtons: true
    }).then((result) => {
      if (result.isConfirmed) {
        clearInterval(timerInterval);

        const total = Date.now() - startTime;
        const formatted = formatTime(total);

        Swal.fire({
          icon: "success",
          title: "Pembersihan Selesai!",
          html: `
            <div style="font-size:1.3rem; margin-top:10px;">
              Durasi Pengerjaan:<br>
              <span style="font-weight:700; color:#2ecc71;">${formatted}</span>
            </div>
          `,
          confirmButtonText: "OK",
        });

        startBtn.innerText = "Mulai";
        startBtn.disabled = false;
        startBtn.style.background = "#00c851";

        timerDisplay.innerText = "";
        startTime = null;
      }
    });
  });
</script>

  </body>
</html>

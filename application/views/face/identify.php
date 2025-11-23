<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.18.0/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>

<style>
  .pulse {
    animation: pulse 1.2s ease-in-out infinite;
  }
  @keyframes pulse {
    0% { opacity: .4; }
    50% { opacity: 1; }
    100% { opacity: .4; }
  }
</style>

<main>
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-4xl border border-gray-200">
      
      <h2 class="text-2xl font-bold text-gray-700 mb-2 text-center">
        🔍 Face Recognition Identify
      </h2>
      <p class="text-gray-500 text-center mb-6">
        Kamera aktif — sistem akan mengenali wajah secara otomatis
      </p>
      
      <div class="flex gap-8 items-start">
        <!-- Camera Section -->
        <div class="flex-1">
          <div class="w-full flex justify-center text-center relative">
            <div class="relative inline-block">
              <video id="videoFeed" autoplay playsinline 
                class="rounded-xl shadow-md border border-gray-300 w-[400px] h-[320px] object-cover">
              </video>

              <div id="statusBadge"
                class="absolute top-3 right-3 bg-gray-800 text-white text-sm py-1 px-3 rounded-lg">
                Memulai kamera...
              </div>
            </div>
          </div>

          <!-- Loading / Result -->
          <div class="mt-5 text-center">
            <div id="loadingText" class="text-gray-600 text-base pulse">Mendeteksi wajah...</div>
            <div id="resultText" class="text-lg font-semibold mt-2"></div>
          </div>
        </div>

        <!-- Booking Box -->
        <div id="bookingBox" class="flex-1 hidden bg-gray-50 border border-gray-300 rounded-xl p-4 min-h-[320px]">
          <h3 class="text-xl font-bold text-gray-700 mb-4">📦 Booking Anda</h3>
          <div id="bookingList" class="text-sm text-gray-700 space-y-2 max-h-[260px] overflow-y-auto">
            Memuat data booking…
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<script>
let faceMesh, camera;
let alreadyCaptured = false;
let lastDetected = 0;

function startDetection() {
    const video = document.getElementById("videoFeed");
    const statusBadge = document.getElementById("statusBadge");

    faceMesh = new FaceMesh({
        locateFile: file => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${file}`,
    });

    faceMesh.setOptions({
        maxNumFaces: 1,
        minDetectionConfidence: 0.55,
        minTrackingConfidence: 0.55
    });

    faceMesh.onResults(results => {
        const now = Date.now();

        // TIDAK ADA WAJAH
        if (!results.multiFaceLandmarks?.length) {
            statusBadge.innerHTML = "Tidak ada wajah";
            statusBadge.className = "absolute top-3 right-3 bg-red-600 text-white text-sm py-1 px-3 rounded-lg";

            if (now - lastDetected > 1200) {
                alreadyCaptured = false; // reset scan
            }
            return;
        }

        // WAJAH TERDETEKSI
        statusBadge.innerHTML = "Wajah terdeteksi";
        statusBadge.className = "absolute top-3 right-3 bg-green-600 text-white text-sm py-1 px-3 rounded-lg";

        lastDetected = now;

        // KIRIM SEKALI
        if (!alreadyCaptured) {
            alreadyCaptured = true;
            captureAndVerify();
        }
    });

    camera = new Camera(video, {
        onFrame: async () => await faceMesh.send({ image: video }),
        width: 400,
        height: 320
    });

    camera.start();
}

// CAPTURE + SEND
async function captureAndVerify() {
    const video = document.getElementById("videoFeed");
    const resultText = document.getElementById("resultText");
    const loadingText = document.getElementById("loadingText");

    loadingText.innerHTML = "⏳ Memverifikasi wajah...";
    loadingText.classList.add("pulse");

    // capture canvas
    const canvas = document.createElement("canvas");
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext("2d").drawImage(video, 0, 0);

    const blob = await new Promise(r => canvas.toBlob(r, "image/jpeg", 0.9));
    const formData = new FormData();
    formData.append("file", blob, "capture.jpg");

    try {
        const res = await fetch("<?= base_url('face/verify'); ?>", {
            method: "POST",
            body: formData
        });

        const data = await res.json();
        console.log("Verify:", data);

        handleVerifyResponse(data);

    } catch (err) {
        resultText.innerHTML = "<span class='text-red-600'>Gagal verifikasi!</span>";
        alreadyCaptured = false;
    }
}

// HANDLE RESPONSE
function handleVerifyResponse(data) {
    const resultText = document.getElementById("resultText");

    if (!data.success) {
        resultText.innerHTML = "<span class='text-red-600'>❌ Wajah tidak dikenali</span>";
        alreadyCaptured = false;
        return;
    }

    resultText.innerHTML =
        `<span class='text-green-600'>✔ Halo <b>${data.nama}</b> (${data.nipp})</span>`;

    loadBooking(data.id);
}

// LOAD BOOKING
async function loadBooking(userId) {
    const box = document.getElementById("bookingBox");
    const list = document.getElementById("bookingList");

    box.classList.remove("hidden");
    list.innerHTML = "⏳ Mengambil data booking...";

    try {
        const res = await fetch("<?= base_url('booking/get_by_user/'); ?>" + userId);
        const data = await res.json();

        if (!data.length) {
            list.innerHTML = "<span class='text-gray-500'>Tidak ada booking aktif</span>";
            return;
        }

        let html = "";
        data.forEach(b => {
            html += `
              <div class="p-3 bg-white border border-gray-200 rounded-lg text-sm">
                <div class="font-semibold">🏨 ${b.room_number} (${b.floor_name})</div>
                <div class="text-xs text-gray-600 mt-1">
                  <div><b>Nama:</b> ${b.nama}</div>
                  <div><b>Jabatan:</b> ${b.jabatan}</div>
                  <div><b>Check-in:</b> ${b.check_in_date}</div>
                  <div><b>Check-out:</b> ${b.check_out_date}</div>
                </div>
              </div>`;
        });

        list.innerHTML = html;

    } catch (e) {
        list.innerHTML = "<span class='text-red-600'>Gagal load booking!</span>";
    }
}

startDetection();
</script>
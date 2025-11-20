
 <style>
    /* Smooth loading animation */
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

  <div class="bg-white shadow-xl rounded-2xl p-6 w-full max-w-lg border border-gray-200">
    
    <h2 class="text-2xl font-bold text-gray-700 mb-2 text-center">
      🔍 Face Recognition Identify
    </h2>
    <p class="text-gray-500 text-center mb-4">
      Kamera aktif — sistem akan mengenali wajah secara otomatis
    </p>
    <div class="w-full flex justify-center text-center relative">
  <div class="relative inline-block">
    <video id="video" autoplay playsinline 
      class="rounded-xl shadow-md border border-gray-300 w-[350px] max-w-full"></video>

    <div id="status_badge"
      class="absolute top-2 right-2 bg-gray-800 text-white text-xs py-1 px-2 rounded-lg">
      Memulai kamera...
    </div>
  </div>
</div>


    <!-- Loading / Result -->
    <div class="mt-5 text-center">
      <div id="loading_text" class="text-gray-600 text-sm pulse">Mendeteksi wajah gaes...</div>
      <div id="result_text" class="text-lg font-semibold mt-2"></div>
    </div>

  </div>

</div>
</main>

<script>
  const video = document.getElementById("video");
  const statusBadge = document.getElementById("status_badge");
  const resultText = document.getElementById("result_text");
  const loadingText = document.getElementById("loading_text");
  
  let isProcessing = false;
  let lastCaptureTime = 0;
  const CAPTURE_INTERVAL = 60000; // 1 menit dalam milidetik
  let captureInterval;

  // 1. HIDUPKAN KAMERA
  function startCamera() {
    navigator.mediaDevices.getUserMedia({ 
      video: { 
        width: 640, 
        height: 480,
        facingMode: "user" 
      } 
    })
    .then(stream => {
      video.srcObject = stream;
      statusBadge.textContent = "Kamera aktif";
      statusBadge.classList.remove("bg-gray-800");
      statusBadge.classList.add("bg-green-600");
      
      // Mulai capture interval setelah kamera ready
      video.addEventListener('loadeddata', startCaptureInterval);
    })
    .catch((err) => {
      console.error("Error accessing camera:", err);
      statusBadge.textContent = "Gagal akses kamera";
      statusBadge.classList.remove("bg-gray-800");
      statusBadge.classList.add("bg-red-600");
    });
  }

  // 2. START CAPTURE INTERVAL
  function startCaptureInterval() {
    // Hapus interval sebelumnya jika ada
    if (captureInterval) {
      clearInterval(captureInterval);
    }
    
    // Capture pertama langsung
    setTimeout(() => {
      captureAndSend();
    }, 2000);
    
    // Set interval untuk capture berikutnya
    captureInterval = setInterval(() => {
      captureAndSend();
    }, CAPTURE_INTERVAL);
    
    console.log("🔄 Capture interval started: 1 minute");
  }

  // 3. FUNGSI CAPTURE DAN KIRIM
  function captureAndSend() {
    const now = Date.now();
    
    // Cek jika masih dalam interval atau sedang processing
    if (isProcessing || (now - lastCaptureTime) < CAPTURE_INTERVAL) {
      console.log("⏳ Skip capture - masih dalam interval atau processing");
      return;
    }

    // Validasi video ready
    if (!video.videoWidth || !video.videoHeight) {
      console.log("⏳ Video belum ready");
      return;
    }

    isProcessing = true;
    lastCaptureTime = now;

    // Update UI
    updateUIProcessing();

    // Buat canvas dan capture
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    
    // Set canvas size sesuai video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame ke canvas
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Kompresi gambar untuk mengurangi size
    const base64 = canvas.toDataURL("image/jpeg", 0.7); // 70% quality

    // Kirim ke server
    sendToServer(base64);
  }

  // 4. KIRIM KE SERVER
  function sendToServer(base64Image) {
    fetch("<?= base_url('dashboard/scan_face') ?>", {
      method: "POST",
      headers: { 
        "Content-Type": "application/json" 
      },
      body: JSON.stringify({ 
        image: base64Image,
        timestamp: Date.now()
      })
    })
    .then(async (res) => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.json();
    })
    .then(data => {
      handleServerResponse(data);
    })
    .catch(err => {
      console.error("Fetch error:", err);
      handleError("Error memproses wajah");
    })
    .finally(() => {
      isProcessing = false;
    });
  }

  // 5. HANDLE RESPONSE SERVER
  function handleServerResponse(data) {
    loadingText.style.display = "none";

    if (data.status === "ok" && data.data) {
      // Wajah dikenali
      resultText.innerHTML = `
        <span class="text-green-600 font-semibold">✔ ${data.data.name}</span><br>
        <span class="text-gray-600 text-sm">Email: ${data.data.email}</span><br>
        <span class="text-gray-500 text-xs">Similarity: ${data.data.distance.toFixed(4)}</span>
      `;
      
      // Tampilkan pesan sukses lebih lama
      showTemporarySuccess();
      
    } else if (data.status === "not_found") {
      // Wajah tidak dikenali
      resultText.innerHTML = `
        <span class="text-red-500 font-semibold">✘ Tidak Dikenali</span><br>
        <span class="text-gray-600 text-sm">Wajah tidak terdaftar</span>
      `;
    } else {
      // Error dari server
      handleError(data.message || "Error tidak diketahui");
    }
  }

  // 6. UPDATE UI SAAT PROCESSING
  function updateUIProcessing() {
    loadingText.style.display = "block";
    resultText.innerHTML = `
      <span class="text-blue-600">🔄 Memindai wajah...</span><br>
      <span class="text-gray-500 text-xs">${new Date().toLocaleTimeString()}</span>
    `;
  }

  // 7. HANDLE ERROR
  function handleError(message) {
    loadingText.style.display = "none";
    resultText.innerHTML = `
      <span class="text-red-600 font-semibold">⚠ ${message}</span><br>
      <span class="text-gray-500 text-xs">Akan dicoba lagi dalam 1 menit</span>
    `;
  }

  // 8. TAMPILKAN SUKSES SEMENTARA
  function showTemporarySuccess() {
    // Optional: Tambahkan efek visual untuk success
    resultText.classList.add("p-2", "bg-green-50", "rounded");
    
    setTimeout(() => {
      resultText.classList.remove("p-2", "bg-green-50", "rounded");
    }, 5000);
  }

  // 9. MANUAL CAPTURE (jika perlu tombol manual)
  function manualCapture() {
    const now = Date.now();
    if (isProcessing) {
      alert("Sedang memproses, tunggu sebentar...");
      return;
    }
    
    if ((now - lastCaptureTime) < 10000) { // Minimal 10 detik untuk manual
      alert("Tunggu 10 detik sebelum capture berikutnya");
      return;
    }
    
    captureAndSend();
  }

  // 10. CLEANUP
  function stopCamera() {
    if (captureInterval) {
      clearInterval(captureInterval);
      captureInterval = null;
    }
    
    if (video.srcObject) {
      const tracks = video.srcObject.getTracks();
      tracks.forEach(track => track.stop());
      video.srcObject = null;
    }
  }

  // Event listener untuk page visibility
  document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
      // Page tidak terlihat, hentikan sementara
      if (captureInterval) {
        clearInterval(captureInterval);
        captureInterval = null;
      }
    } else {
      // Page visible again, restart interval
      startCaptureInterval();
    }
  });

  // Start aplikasi
  startCamera();

  // Cleanup ketika page unload
  window.addEventListener('beforeunload', stopCamera);
</script>

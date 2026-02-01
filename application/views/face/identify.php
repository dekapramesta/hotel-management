<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Identify | Premium Experience</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@3.18.0/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        toska: '#20c997',
                        premium: '#0f172a',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f8fafc;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .scan-line {
            height: 2px;
            background: linear-gradient(to right, transparent, #20c997, transparent);
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            z-index: 10;
            animation: scan 3s linear infinite;
            box-shadow: 0 0 15px #20c997;
        }

        @keyframes scan {
            0% { top: 0%; }
            100% { top: 100%; }
        }

        .pulse {
            animation: pulse-glow 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .7; transform: scale(1.02); }
        }

        .video-container {
            position: relative;
            overflow: hidden;
            border-radius: 1.5rem;
            border: 4px solid rgba(255, 255, 255, 0.05);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(32, 201, 151, 0.3);
            border-radius: 10px;
        }
    </style>
</head>
<body class="min-h-screen overflow-x-hidden pt-20">

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header Section -->
        <div class="text-center mb-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 tracking-tight">
                <span class="text-toska">Smart</span> Identify
            </h1>
            <p class="text-slate-400 text-lg">Sistem pengenalan wajah otomatis untuk verifikasi pesanan tamu</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
            <!-- Left Side: Scanner -->
            <div class="lg:col-span-7 flex flex-col">
                <div class="glass-card rounded-3xl p-6 flex-1 flex flex-col relative overflow-hidden">
                    <!-- Tech decorations -->
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="bi bi-cpu text-6xl"></i>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-toska animate-ping"></div>
                            <span class="font-medium text-slate-200 uppercase tracking-widest text-xs">Live Scanner</span>
                        </div>
                        <div id="statusBadge" class="px-4 py-1.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700 transition-all duration-300">
                             Inisialisasi...
                        </div>
                    </div>

                    <div class="video-container flex-1 bg-black aspect-video flex items-center justify-center">
                        <video id="videoFeed" autoplay playsinline class="w-full h-full object-cover opacity-70"></video>
                        <div id="scanLine" class="scan-line hidden"></div>
                        
                        <!-- Overlay Corners -->
                        <div class="absolute top-6 left-6 w-10 h-10 border-t-4 border-l-4 border-toska opacity-40 rounded-tl-lg"></div>
                        <div class="absolute top-6 right-6 w-10 h-10 border-t-4 border-r-4 border-toska opacity-40 rounded-tr-lg"></div>
                        <div class="absolute bottom-6 left-6 w-10 h-10 border-b-4 border-l-4 border-toska opacity-40 rounded-bl-lg"></div>
                        <div class="absolute bottom-6 right-6 w-10 h-10 border-b-4 border-r-4 border-toska opacity-40 rounded-br-lg"></div>
                    </div>

                    <div class="mt-8 text-center min-h-[50px]">
                        <div id="loadingText" class="text-slate-400 text-sm italic mb-2">Posisikan wajah Anda di depan kamera</div>
                        <div id="resultText" class="text-xl font-medium transition-all duration-500"></div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Booking Panel -->
            <div class="lg:col-span-5 flex flex-col">
                <div id="bookingBoxWrapper" class="glass-card rounded-3xl p-8 flex-1 flex flex-col transition-all duration-500 opacity-40 grayscale translate-y-4">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl bg-toska/20 flex items-center justify-center text-toska">
                            <i class="bi bi-card-checklist text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold">Informasi Tamu</h3>
                            <p class="text-slate-500 text-xs">Detail reservasi aktif ditemukan</p>
                        </div>
                    </div>

                    <div id="bookingList" class="flex-1 space-y-4 custom-scrollbar overflow-y-auto pr-2 max-h-[450px]">
                        <div class="flex flex-col items-center justify-center h-full text-center text-slate-500 py-10 opacity-50">
                            <i class="bi bi-person-badge text-5xl mb-4"></i>
                            <p>Menunggu verifikasi wajah...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let faceMesh, camera;
let alreadyCaptured = false;
let lastDetected = 0;

function startDetection() {
    const video = document.getElementById("videoFeed");
    const statusBadge = document.getElementById("statusBadge");
    const scanLine = document.getElementById("scanLine");

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
            statusBadge.innerHTML = "Siap Digunakan";
            statusBadge.className = "px-4 py-1.5 rounded-full text-xs font-semibold bg-slate-800 text-slate-300 border border-slate-700";
            video.classList.add("opacity-40");
            scanLine.classList.add("hidden");

            if (now - lastDetected > 1500) {
                alreadyCaptured = false; 
            }
            return;
        }

        // WAJAH TERDETEKSI
        statusBadge.innerHTML = "Wajah Terdeteksi";
        statusBadge.className = "px-4 py-1.5 rounded-full text-xs font-semibold bg-toska/20 text-toska border border-toska/30";
        video.classList.remove("opacity-40");
        scanLine.classList.remove("hidden");

        lastDetected = now;

        // KIRIM SEKALI
        if (!alreadyCaptured) {
            alreadyCaptured = true;
            captureAndVerify();
        }
    });

    camera = new Camera(video, {
        onFrame: async () => await faceMesh.send({ image: video }),
        width: 1280,
        height: 720
    });

    camera.start();
}

// CAPTURE + SEND
async function captureAndVerify() {
    const video = document.getElementById("videoFeed");
    const resultText = document.getElementById("resultText");
    const loadingText = document.getElementById("loadingText");

    loadingText.innerHTML = `<span class="flex items-center justify-center gap-2"><div class="w-4 h-4 border-2 border-toska border-t-transparent rounded-full animate-spin"></div> Menganalisis Biometrik...</span>`;

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
        handleVerifyResponse(data);

    } catch (err) {
        resultText.innerHTML = "<span class='text-red-400'><i class='bi bi-x-circle me-1'></i> Jaringan Terputus</span>";
        alreadyCaptured = false;
        loadingText.innerHTML = "Posisikan wajah Anda di depan kamera";
    }
}

// HANDLE RESPONSE
function handleVerifyResponse(data) {
    const resultText = document.getElementById("resultText");
    const loadingText = document.getElementById("loadingText");
    const bookingBox = document.getElementById("bookingBoxWrapper");

    if (!data.success) {
        resultText.innerHTML = "<span class='text-red-400 scale-110'><i class='bi bi-shield-x me-2'></i>Akses Ditolak</span>";
        loadingText.innerHTML = "Mencoba mengenali ulang...";
        setTimeout(() => {
            alreadyCaptured = false;
            loadingText.innerHTML = "Posisikan wajah Anda di depan kamera";
            resultText.innerHTML = "";
        }, 2000);
        return;
    }

    loadingText.innerHTML = "";
    resultText.innerHTML = `<div class="animate-bounce mb-2"><i class="bi bi-patch-check-fill text-toska text-4xl"></i></div>
        <div class="text-slate-100 font-bold tracking-wide">Selamat Datang, ${data.nama}</div>`;
    
    // Animate Box
    bookingBox.classList.remove("opacity-40", "grayscale", "translate-y-4");
    bookingBox.classList.add("border-toska/50", "ring-1", "ring-toska/20");

    loadBooking(data.id);
}

// LOAD BOOKING
async function loadBooking(userId) {
    const list = document.getElementById("bookingList");
    list.innerHTML = `<div class="flex flex-col items-center justify-center py-20"><div class="w-8 h-8 border-4 border-toska border-t-transparent rounded-full animate-spin"></div></div>`;

    try {
        const res = await fetch("<?= base_url('booking/get_by_user/'); ?>" + userId);
        const data = await res.json();

        if (!data.length) {
            list.innerHTML = `
                <div class="p-8 text-center bg-slate-800/20 border border-slate-700/50 rounded-2xl">
                    <i class="bi bi-calendar-x text-4xl text-slate-600 mb-4 block"></i>
                    <p class="text-slate-400">Tidak ditemukan reservasi yang aktif untuk hari ini.</p>
                </div>`;
            return;
        }

        let html = "";
        data.forEach(b => {
            html += `
              <div class="p-5 bg-white/5 border border-white/10 rounded-2xl hover:bg-white/10 transition-all duration-300 transform hover:-translate-x-1">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 rounded-lg bg-toska/20 text-toska text-xs font-bold uppercase tracking-tighter">
                        Active Booking
                    </span>
                    <div class="text-toska flex items-center gap-1 font-bold">
                        <i class="bi bi-door-closed text-lg"></i> ${b.room_number}
                    </div>
                </div>
                
                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Check-in</span>
                        <span class="text-slate-200 font-medium">${b.check_in_date}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Check-out</span>
                        <span class="text-slate-200 font-medium">${b.check_out_date}</span>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-white/10 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-xs">
                           <i class="bi bi-person"></i>
                        </div>
                        <div class="text-[10px] leading-tight text-slate-400">
                             <div class="font-bold text-slate-200 uppercase">${b.nipp}</div>
                             <div>${b.jabatan}</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-slate-700"></i>
                </div>
              </div>`;
        });

        list.innerHTML = html;

    } catch (e) {
        list.innerHTML = "<div class='p-5 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-center text-xs font-medium'>Gagal memuat detail reservasi</div>";
    }
}

startDetection();
</script>
</body>
</html>
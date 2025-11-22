<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Face extends CI_Controller {

    private $apiUrl = "http://127.0.0.1:8000/face/register";

    public function register()
    {
        // Pastikan ada file upload
        if (empty($_FILES['photos'])) {
            echo json_encode([
                "success" => false,
                "message" => "Tidak ada foto dikirim."
            ]);
            return;
        }

        $photos = $_FILES['photos'];

        // Generate ID unik 12 karakter
        $user_id = $this->generateUniqueId();

        $results = [];
        $total = count($photos['name']);

        for ($i = 0; $i < $total; $i++) {

            // Setup file untuk CURL
            $tmp_name = $photos['tmp_name'][$i];
            $file_name = $photos['name'][$i];

            $curlFile = new CURLFile($tmp_name, mime_content_type($tmp_name), $file_name);

            $payload = [
                "user_id" => $user_id,
                "file"    => $curlFile
            ];

            // Init CURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

            curl_close($ch);

            $results[] = [
                "photo" => $file_name,
                "status" => $httpCode,
                "response" => json_decode($response, true)
            ];
        }

        echo json_encode([
            "success" => true,
            "message" => "Pendaftaran wajah selesai.",
            "user_id" => $user_id,
            "results" => $results
        ]);
    }

    private function generateUniqueId($length = 12)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $id = '';

        for ($i = 0; $i < $length; $i++) {
            $id .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $id;
    }

    public function verify() 
    {
        if (empty($_FILES['file']['tmp_name'])) {
            echo json_encode(["success" => false, "message" => "No file uploaded"]);
            return;
        }

        // 1. Forward ke API FastAPI
        $curl = curl_init();
        $cfile = new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']);
        $data = ['file' => $cfile];

        curl_setopt_array($curl, [
            CURLOPT_URL => "http://127.0.0.1:8000/face/verify",
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $data
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            echo json_encode(["success" => false, "message" => $error]);
            return;
        }

        $apiResult = json_decode($response, true);

        if (empty($apiResult['success']) || !$apiResult['success']) {
            echo json_encode(["success" => false, "message" => "Wajah tidak dikenali"]);
            return;
        }

        // 2. Ambil user_id dari respons API
        $user_face_id = $apiResult['user_id'];

        // 3. Load model dan ambil data guest
        $this->load->model('Guest_model');
        $guest = $this->Guest_model->get_by_face_id($user_face_id);

        if (!$guest) {
            echo json_encode(["success" => false, "message" => "Tamu tidak ditemukan"]);
            return;
        }

        // 4. Balikan data ke frontend
        echo json_encode(array_merge(["success" => true], $guest));
    }

}

<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sheetdb
{

    // Ganti dengan URL API SheetDB.io Anda
    private $api_url = 'https://sheetdb.io/api/v1/ouefdlulja3km';

    public function __construct()
    {
        // Load CodeIgniter instance
        $this->CI = &get_instance();
    }

    public function get_data()
    {
        // Inisialisasi cURL
        $ch = curl_init();

        // Set URL target
        curl_setopt($ch, CURLOPT_URL, $this->api_url);

        // Atur agar cURL mengembalikan hasilnya sebagai string, bukan menampilkannya
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Eksekusi permintaan cURL
        $response = curl_exec($ch);

        // Tangani jika terjadi error cURL
        if (curl_errno($ch)) {
            log_message('error', 'SheetDB cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return false;
        }

        // Tutup koneksi cURL
        curl_close($ch);

        // Decode JSON response
        $data = json_decode($response, true);

        // Cek jika data berhasil di-decode
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data; // Mengembalikan data sebagai array PHP
        } else {
            log_message('error', 'SheetDB JSON Decode Error: ' . json_last_error_msg());
            return false;
        }
    }
}

<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('generateGoQrAndSave')) {
    /**
     * Generate QR menggunakan GoQR API dan simpan ke uploads/qrcode
     *
     * @param string $qrContent Isi QR Code
     * @param string $fileName Nama file dengan .png
     * @return string Nama file yang disimpan
     * @throws Exception jika gagal
     */
    function generateGoQrAndSave($qrContent, $fileName)
    {
        $folder = public_path('uploads/qrcode');
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $filePath = $folder . '/' . $fileName;
        $encodedData = urlencode($qrContent);
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$encodedData}";

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $qrUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // bypass SSL error XAMPP

            $qrImage = curl_exec($ch);
            $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($qrImage === false || $httpStatus !== 200) {
                Log::error("Gagal unduh QR: HTTP {$httpStatus}, error: {$error}");
                throw new Exception("Gagal mengunduh QR dari GoQR API.");
            }

            file_put_contents($filePath, $qrImage);
            Log::info("QR Code berhasil disimpan di: {$filePath}");

            return $fileName;
        } catch (\Exception $e) {
            Log::error("Error generateGoQrAndSave: " . $e->getMessage());
            throw $e;
        }
    }
}

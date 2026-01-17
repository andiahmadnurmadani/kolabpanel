<?php
/**
 * File untuk testing API endpoint kampanye relawan
 * 
 * Sebelum menjalankan test, pastikan:
 * 1. Aplikasi Laravel sudah berjalan
 * 2. Sudah ada token admin untuk autentikasi
 * 3. Database sudah terhubung
 */

// Konfigurasi dasar
$base_url = 'http://localhost:8000/api'; // Sesuaikan dengan URL aplikasi Anda
$admin_token = 'isi_token_admin_disini'; // Ganti dengan token admin yang valid

// Fungsi untuk melakukan request HTTP
function makeRequest($method, $url, $headers = [], $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'status_code' => $http_code,
        'response' => json_decode($response, true) ?: $response
    ];
}

echo "Testing API Endpoint Kampanye Relawan\n";
echo "=====================================\n\n";

// 1. Test GET /api/admin/volunteer-campaigns (harus gagal tanpa token)
echo "1. Testing GET /api/admin/volunteer-campaigns (tanpa token)...\n";
$result = makeRequest('GET', $base_url . '/admin/volunteer-campaigns');
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . print_r($result['response'], true) . "\n\n";

// 2. Test GET /api/admin/volunteer-campaigns (dengan token - hanya contoh format)
echo "2. Testing GET /api/admin/volunteer-campaigns (dengan token)...\n";
$headers = [
    'Authorization: Bearer ' . $admin_token,
    'Content-Type: application/json',
    'Accept: application/json'
];
$result = makeRequest('GET', $base_url . '/admin/volunteer-campaigns', $headers);
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . print_r($result['response'], true) . "\n\n";

// 3. Test POST /api/admin/volunteer-campaigns (tanpa token)
echo "3. Testing POST /api/admin/volunteer-campaigns (tanpa token)...\n";
$test_data = [
    'judul' => 'Testing Campaign',
    'deskripsi' => 'Deskripsi untuk testing',
    'lokasi' => 'Jakarta',
    'tanggal_mulai' => date('Y-m-d'),
    'tanggal_selesai' => date('Y-m-d', strtotime('+7 days')),
    'kategori' => 'Pendidikan',
    'kuota_total' => 10,
    'status' => 'Aktif'
];
$result = makeRequest('POST', $base_url . '/admin/volunteer-campaigns', [], $test_data);
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . print_r($result['response'], true) . "\n\n";

echo "Testing selesai. Untuk testing lengkap, pastikan:\n";
echo "- Sudah ada user admin di database\n";
echo "- Sudah mendapatkan token Sanctum untuk admin\n";
echo "- Ganti \$admin_token dengan token yang valid\n";
echo "- Jalankan php artisan serve sebelum menjalankan test ini\n";
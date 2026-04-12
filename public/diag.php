<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;font-weight:bold;} .fail{color:red;font-weight:bold;} .warn{color:orange;font-weight:bold;}</style>";
echo "<h2>🔍 Server Diagnostic Report</h2>";

// 1. PHP Version
$phpVersion = PHP_VERSION;
$phpOk = version_compare($phpVersion, '8.2.0', '>=');
echo "<p>PHP Version: <span class='" . ($phpOk ? 'ok' : 'fail') . "'>$phpVersion " . ($phpOk ? '✓ OK' : '✗ BUTUH 8.2+') . "</span></p>";

// 2. Extensions
$required = ['pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'fileinfo', 'bcmath', 'json'];
echo "<p><strong>Ekstensi PHP:</strong></p><ul>";
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    echo "<li>$ext: <span class='" . ($loaded ? 'ok' : 'fail') . "'>" . ($loaded ? '✓ OK' : '✗ MISSING') . "</span></li>";
}
echo "</ul>";

// 3. Autoloader
echo "<p>Vendor Autoload: ";
$autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    echo "<span class='ok'>✓ Ditemukan</span>";
    require $autoload;
} else {
    echo "<span class='fail'>✗ TIDAK ADA - Jalankan composer install!</span></p>";
    exit;
}
echo "</p>";

// 4. .env file
$envPath = dirname(__DIR__) . '/.env';
echo "<p>.env File: ";
if (file_exists($envPath)) {
    echo "<span class='ok'>✓ Ditemukan</span></p>";
    $env = parse_ini_file($envPath);
    echo "<ul>";
    echo "<li>APP_KEY: <span class='" . (!empty($env['APP_KEY']) ? 'ok' : 'fail') . "'>" . (!empty($env['APP_KEY']) ? '✓ Ada' : '✗ KOSONG!') . "</span></li>";
    echo "<li>DB_DATABASE: <span class='warn'>" . ($env['DB_DATABASE'] ?? 'Tidak diset') . "</span></li>";
    echo "<li>DB_USERNAME: <span class='warn'>" . ($env['DB_USERNAME'] ?? 'Tidak diset') . "</span></li>";
    echo "<li>DB_HOST: <span class='warn'>" . ($env['DB_HOST'] ?? 'Tidak diset') . "</span></li>";
    echo "</ul>";
} else {
    echo "<span class='fail'>✗ TIDAK ADA - Upload file .env!</span></p>";
}

// 5. Database connection test
echo "<p>Koneksi Database: ";
try {
    $pdo = new PDO(
        "mysql:host=" . ($env['DB_HOST'] ?? 'localhost') . ";dbname=" . ($env['DB_DATABASE'] ?? ''),
        $env['DB_USERNAME'] ?? '',
        $env['DB_PASSWORD'] ?? ''
    );
    echo "<span class='ok'>✓ Berhasil terhubung!</span></p>";
} catch (Exception $e) {
    echo "<span class='fail'>✗ GAGAL: " . $e->getMessage() . "</span></p>";
}

// 6. Storage writable
$storagePath = dirname(__DIR__) . '/storage';
echo "<p>Storage Writable: <span class='" . (is_writable($storagePath) ? 'ok' : 'fail') . "'>" . (is_writable($storagePath) ? '✓ OK' : '✗ Tidak bisa ditulis - chmod 775') . "</span></p>";

// 7. Bootstrap cache writable
$cachePath = dirname(__DIR__) . '/bootstrap/cache';
echo "<p>Bootstrap Cache: <span class='" . (is_writable($cachePath) ? 'ok' : 'fail') . "'>" . (is_writable($cachePath) ? '✓ OK' : '✗ Tidak bisa ditulis - chmod 775') . "</span></p>";

echo "<hr><p><strong>✅ Selesai. Screenshot ini dan kirim ke developer.</strong></p>";

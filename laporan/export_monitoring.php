<?php
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('Asia/Jakarta');

// =====================
// AMBIL ID MONITORING
// =====================
$monitoring_id = $_GET['monitoring_id'] ?? 0;
if ($monitoring_id == 0) {
    die("Data monitoring tidak ditemukan.");
}

// =====================
// QUERY DATA MONITORING + JOIN
// =====================
$query = mysqli_query($conn, "
    SELECT 
        mo.*,
        p.tanam_id,
        b.nama_bkph,
        b.rph,
        b.petak
    FROM monitoring mo
    LEFT JOIN penanaman p ON mo.tanam_id = p.tanam_id
    LEFT JOIN ajir a ON p.ajir_id = a.ajir_id
    LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
    LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
    LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
    WHERE mo.monitoring_id = '$monitoring_id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan.");
}

// =====================
// HITUNG TOTAL & PERSENTASE
// =====================
$total = $data['jumlah_hidup'] + $data['jumlah_mati'];
$persen = $total > 0 
    ? round(($data['jumlah_hidup'] / $total) * 100, 2) 
    : 0;

// =====================
// FOTO BASE64
// =====================
// =====================
// FOTO BASE64
// =====================

function getImage($folder, $filename)
{
    $base = __DIR__ . '/../';

    // lokasi default image
    $default = $base . 'assets/images/default.jpg';

    // jika nama file kosong
    if (empty($filename)) {
        $path = $default;
    } else {
        $path = $base . $folder . '/' . $filename;

        // jika file tidak ditemukan pakai default
        if (!file_exists($path)) {
            $path = $default;
        }
    }

    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);

    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// ambil foto monitoring
$fotoHidup = getImage('gambar_monitoring', $data['foto_hidup']);
$fotoMati  = getImage('gambar_monitoring', $data['foto_mati']);
// =====================
// HTML PDF
// =====================
// =====================
// LOGO BASE64
// =====================
$logoPath = '../assets/images/logos/logo-m.png';
$logoBase64 = '';

if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// =====================
// HTML PDF
// =====================
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 11px;
    color:#2c3e50;
    margin: 0;
    padding: 0;
}

.kop { 
    text-align: center; 
    border-bottom: 4px solid #1f4e79; 
    padding-bottom: 15px; 
    margin-bottom: 25px; 
    position: relative;
}

.logo { 
    position: absolute; 
    top: 5px; 
    left: 0px; 
    width: 85px; 
}

.title { 
    font-size: 20px; 
    font-weight: bold; 
    letter-spacing: 1px;
    margin-top: 5px;
}

.subtitle {
    font-size: 13px;
    margin-top: 5px;
}

.alamat { 
    font-size: 10px; 
    margin-top: 6px;
    color:#555;
}

.section-title {
    font-weight:bold;
    margin-top:20px;
    margin-bottom:8px;
    padding:6px 8px;
    background:#1f4e79;
    color:white;
    font-size:12px;
}

.box {
    border:1px solid #ccc;
    padding:10px;
    margin-bottom:15px;
}

.stat-container {
    width:100%;
    margin-top:10px;
}

.stat-box {
    width:30%;
    display:inline-block;
    text-align:center;
    border:1px solid #ccc;
    padding:12px 5px;
    margin-right:1%;
}

.stat-title {
    font-size:10px;
    color:#555;
}

.stat-value {
    font-size:18px;
    font-weight:bold;
    margin-top:5px;
}

.info-table {
    width:100%;
    border-collapse:collapse;
}

.info-table td {
    border:1px solid #ccc;
    padding:6px;
}

.info-table td:first-child {
    width:35%;
    background:#f2f2f2;
    font-weight:bold;
}

.two-col {
    width:100%;
}

.two-col td {
    width:50%;
    vertical-align:top;
    padding:8px;
}

img {
    max-height:180px;
}

.footer {
    border-top:1px solid #ccc;
    margin-top:25px;
    padding-top:8px;
    font-size:10px;
    text-align:right;
    color:#777;
}
</style>
</head>
<body>

<!-- ================= HEADER ================= -->
<div class="kop">
    <img src="' . $logoBase64 . '" class="logo">
    <div class="title">LAPORAN MONITORING PENANAMAN</div>
    <div class="subtitle">Sistem Monitoring dan Evaluasi Tanaman</div>
    <div class="alamat">
        Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten Bojonegoro<br>
        Jawa Timur 62111
    </div>
</div>

<!-- ================= RINGKASAN ================= -->
<div class="box">
<table width="100%">
<tr>
<td width="50%">
<strong>Tanggal Monitoring :</strong><br>
' . date('d F Y', strtotime($data['tanggal'])) . '
</td>
<td width="50%">
<strong>Status Monitoring :</strong><br>
' . ucfirst($data['status']) . '
</td>
<td width="50%">
<strong>Total Tanaman :</strong><br>
' . $total . '
</td>
</tr>
</table>
</div>

<!-- ================= STATISTIK ================= -->
<div class="section-title">STATISTIK TANAMAN</div>

<div class="stat-container">
    <div class="stat-box">
        <div class="stat-title">Tanaman Hidup</div>
        <div class="stat-value">' . number_format($data['jumlah_hidup']?? 0) . '</div>
    </div>

    <div class="stat-box">
        <div class="stat-title">Tanaman Mati</div>
        <div class="stat-value">' . number_format($data['jumlah_mati']?? 0) . '</div>
    </div>

    <div class="stat-box">
        <div class="stat-title">Persentase Hidup</div>
        <div class="stat-value">' . $persen . '%</div>
    </div>
</div>

<!-- ================= INFORMASI LOKASI ================= -->
<div class="section-title">INFORMASI LOKASI & KONDISI TANAMAN</div>

<table class="info-table">
<tr><td>BKPH</td><td>' . htmlspecialchars($data['nama_bkph']?? '') . '</td></tr>
<tr><td>RPH</td><td>' . htmlspecialchars($data['rph']?? '') . '</td></tr>
<tr><td>Petak</td><td>' . htmlspecialchars($data['petak']?? '') . '</td></tr>
<tr><td>Tinggi / Diameter</td><td>' . htmlspecialchars($data['tinggi_diameter']?? '') . '</td></tr>
<tr><td>Gangguan</td><td>' . ($data['gangguan'] ?: 'Tidak ada') . '</td></tr>
</table>

<!-- ================= FOTO ================= -->
<div class="section-title">DOKUMENTASI LAPANGAN</div>

<table class="two-col">
<tr>
<td align="center">
<strong>Tanaman Hidup</strong><br><br> <img src="' . $fotoHidup . '"  width="250" height="180""> 
</td>
<td align="center">
<strong>Tanaman Mati</strong><br><br><img src="' . $fotoMati . '"  width="250" height="180"">
</td>
</tr>
</table>

<!-- ================= EVALUASI ================= -->
<div class="section-title">EVALUASI DAN CATATAN MONITORING</div>

<table class="two-col">
<tr>
<td>
<div class="box">
<strong>Evaluasi :</strong><br><br>' .
    ($data['evaluasi'] ? nl2br(htmlspecialchars($data['evaluasi'])) : 'Belum terdapat evaluasi.') . '
</div>
</td>
<td>
<div class="box">
<strong>Catatan :</strong><br><br>' .
    ($data['catatan'] ? nl2br(htmlspecialchars($data['catatan'])) : 'Tidak terdapat catatan tambahan.') . '
</div>
</td>
</tr>
</table>

<div class="footer">
Dokumen ini dicetak secara otomatis melalui Sistem Monitoring Penanaman<br>
Tanggal Cetak: ' . date('d-m-Y H:i') . ' WIB
</div>

</body>
</html>
';

// =====================
// GENERATE PDF
// =====================
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream(
    "Monitoring_" . $monitoring_id . "_" . date('Ymd_His') . ".pdf",
    ["Attachment" => 0]
);
exit;

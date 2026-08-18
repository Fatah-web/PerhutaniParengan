<?php
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// =======================
// AMBIL ID DARI URL
// =======================
$data_awal_id = $_GET['data_awal_id'] ?? 0;
if ($data_awal_id == 0) {
    die("ID Data Awal tidak ditemukan.");
}

// =======================
// AMBIL DATA (data_awal + bkph + mandor)
// =======================
$query = mysqli_query($conn, "
    SELECT 
        da.*,
        b.nama_bkph,
        b.rph,
        b.petak,
        b.luas_baku,
        b.jenis_tanaman,
        b.jarak_tanam,
        m.nama AS nama_mandor
    FROM data_awal da
    LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
    LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
    WHERE da.data_awal_id = '$data_awal_id'
");

$data = mysqli_fetch_assoc($query);
if (!$data) {
    die("Data Awal tidak ditemukan.");
}

// =======================
// LOGO BASE64
// =======================
$logoPath = '../assets/images/logos/logo-m.png';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// =======================
// HTML PDF
// =======================
$html = '
<!DOCTYPE html>
<html>
<head>
<title>Laporan Detail Data Awal</title>
<style>
    body { 
        font-family: DejaVu Sans, sans-serif; 
        font-size: 13px; 
        color:#333;
    }

    .kop { 
        text-align: center; 
        border-bottom: 3px solid #2c3e50; 
        padding-bottom: 15px; 
        margin-bottom: 25px; 
        position: relative;
    }

    .logo { 
        position: absolute; 
        top: 0px; 
        left: 0px; 
        width: 90px; 
    }

    .title { 
        font-size: 22px; 
        font-weight: bold; 
        letter-spacing: 1px;
        color:#2c3e50;
    }

    .alamat { 
        font-size: 12px; 
        margin-top:5px;
    }

    .highlight-box {
        background:#f8f9fa;
        border-left:5px solid #007bff;
        padding:15px;
        margin-bottom:20px;
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px; 
    }

    .info-table td {
        border:1px solid #ddd;
        padding:8px;
    }

    .info-table td:first-child {
        width:35%;
        font-weight:bold;
        background:#f4f6f9;
    }

    .footer {
        text-align:center;
        margin-top:30px;
        font-size:12px;
        color:#777;
    }

</style>
</head>
<body>

<div class="kop">
    <img src="'.$logoBase64.'" class="logo">
    <div class="title">LAPORAN DETAIL DATA AWAL</div>
    <div class="alamat">
        Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten<br>
        Bojonegoro, Jawa Timur 62111
    </div>
</div>

<div class="highlight-box">
    <strong>Tahun Tanam: '.htmlspecialchars($data['tahun_tanam'] ?? '-').'</strong><br>
    Target Pohon: <strong>'.number_format($data['target_pohon'] ?? 0).' Pohon</strong>
</div>

<table class="info-table">

<tr>
    <td>BKPH</td>
    <td>'.htmlspecialchars($data['nama_bkph'] ?? '-').'</td>
</tr>

<tr>
    <td>RPH</td>
    <td>'.htmlspecialchars($data['rph'] ?? '-').'</td>
</tr>

<tr>
    <td>Petak</td>
    <td>'.htmlspecialchars($data['petak'] ?? '-').'</td>
</tr>

<tr>
    <td>Luas Baku (Ha)</td>
    <td>'.htmlspecialchars($data['luas_baku'] ?? '-').'</td>
</tr>

<tr>
    <td>Jenis Tanaman</td>
    <td>'.htmlspecialchars($data['jenis_tanaman'] ?? '-').'</td>
</tr>

<tr>
    <td>Jarak Tanam</td>
    <td>'.htmlspecialchars($data['jarak_tanam'] ?? '-').'</td>
</tr>


<tr>
    <td>Mandor</td>
    <td>'.htmlspecialchars($data['nama_mandor'] ?? '-').'</td>
</tr>

</table>

<div class="footer">
    Dicetak pada: '.date('d-m-Y H:i').'
</div>

</body>
</html>
';


// =======================
// DOMPDF
// =======================
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// =======================
// STREAM PDF
// =======================
$dompdf->stream(
    "data_awal_" . $data_awal_id . ".pdf",
    ["Attachment" => 0]
);
exit;

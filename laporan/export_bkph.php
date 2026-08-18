<?php
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// =======================
// AMBIL ID DARI URL
// =======================
$bkph_id = $_GET['bkph_id'] ?? 0;
if ($bkph_id == 0) {
    die("ID BKPH tidak ditemukan.");
}

// =======================
// AMBIL DATA BKPH
// =======================
$query = mysqli_query($conn, "
    SELECT b.*, m.nama AS nama_mandor, m.status
    FROM bkph b
    LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
    WHERE b.bkph_id = '$bkph_id'
");

$bkph = mysqli_fetch_assoc($query);
if (!$bkph) {
    die("Data BKPH tidak ditemukan.");
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
<title>Laporan Detail BKPH</title>
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

    .badge {
        display:inline-block;
        padding:6px 12px;
        font-size:12px;
        font-weight:bold;
        border-radius:20px;
    }

    .aktif { background:#28a745; color:white; }
    .nonaktif { background:#dc3545; color:white; }

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
    <div class="title">LAPORAN DETAIL BKPH</div>
    <div class="alamat">
        Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten<br>
        Bojonegoro, Jawa Timur 62111
    </div>
</div>

<div class="highlight-box">
    <strong>'.htmlspecialchars($bkph['nama_bkph'] ?? '-').'</strong><br>
    RPH: '.htmlspecialchars($bkph['rph'] ?? '-').' | 
    Petak: '.htmlspecialchars($bkph['petak'] ?? '-').'<br>
    Mandor: '.htmlspecialchars($bkph['nama_mandor'] ?? '-').'
</div>

<table class="info-table">

<tr>
    <td>Nama BKPH</td>
    <td>'.htmlspecialchars($bkph['nama_bkph'] ?? '-').'</td>
</tr>

<tr>
    <td>RPH</td>
    <td>'.htmlspecialchars($bkph['rph'] ?? '-').'</td>
</tr>

<tr>
    <td>Petak</td>
    <td>'.htmlspecialchars($bkph['petak'] ?? '-').'</td>
</tr>

<tr>
    <td>Luas Baku (Ha)</td>
    <td>'.htmlspecialchars($bkph['luas_baku'] ?? '-').'</td>
</tr>

<tr>
    <td>Rencana Tanam</td>
    <td>'.htmlspecialchars($bkph['rencana_tanam'] ?? '-').'</td>
</tr>

<tr>
    <td>Jenis Tanaman</td>
    <td>'.htmlspecialchars($bkph['jenis_tanaman'] ?? '-').'</td>
</tr>

<tr>
    <td>Jarak Tanam</td>
    <td>'.htmlspecialchars($bkph['jarak_tanam'] ?? '-').'</td>
</tr>


<tr>
    <td>Mandor</td>
    <td>'.htmlspecialchars($bkph['nama_mandor'] ?? '-').'</td>
</tr>

<tr>
    <td>Status</td>
    <td>'.(
        strtolower($bkph['status'] ?? '') == "aktif"
        ? '<span class="badge aktif">Aktif</span>'
        : '<span class="badge nonaktif">'.ucfirst(htmlspecialchars($bkph['status'] ?? '-')).'</span>'
    ).'</td>
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
    "bkph_" . $bkph_id . ".pdf",
    ["Attachment" => 0]
);
exit;

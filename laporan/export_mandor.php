<?php
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Ambil id dari URL
$mandor_id = $_GET['mandor_id'] ?? 0;
if ($mandor_id == 0) {
    die("ID mandor tidak ditemukan.");
}

// Ambil data mandor
$query = mysqli_query($conn, "
    SELECT * FROM mandor 
    WHERE mandor_id = '$mandor_id'
");

$mandor = mysqli_fetch_assoc($query);
if (!$mandor) {
    die("Data mandor tidak ditemukan.");
}

// Logo
$logoPath = '../assets/images/logos/logo-m.png';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// FOTO mandor BASE64
$fotoPath = '../image/' . $mandor['foto'];
$fotoBase64 = '';

if (!empty($mandor['foto']) && file_exists($fotoPath)) {
    $fotoExt = pathinfo($fotoPath, PATHINFO_EXTENSION);
    $fotoBase64 = 'data:image/' . $fotoExt . ';base64,' . base64_encode(file_get_contents($fotoPath));
}


// HTML PDF
$html = '
<!DOCTYPE html>
<html>
<head>
<title>Laporan Data Mandor</title>
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

    .profile-box {
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

    .foto {
        width:150px;
        border:1px solid #ccc;
        border-radius:6px;
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
    <img src="' . $logoBase64 . '" class="logo">
    <div class="title">LAPORAN DATA MANDOR</div>
    <div class="alamat">
        Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten<br>
        Bojonegoro, Jawa Timur 62111
    </div>
</div>

<div class="profile-box">
    <strong>' . htmlspecialchars($mandor['nama'] ?? '-') . '</strong><br>
    NIP: ' . htmlspecialchars($mandor['nip'] ?? '-') . ' <br>
    BKPH: ' . htmlspecialchars($mandor['bkph'] ?? '-') . '
</div>

<table class="info-table">

<tr>
    <td>Foto</td>
    <td>' . (!empty($fotoBase64) 
        ? '<img src="'.$fotoBase64.'" class="foto">'
        : '<i>Tidak ada foto</i>') . '</td>
</tr>

<tr>
    <td>Nama Lengkap</td>
    <td>' . htmlspecialchars($mandor['nama'] ?? '-') . '</td>
</tr>

<tr>
    <td>NIP</td>
    <td>' . htmlspecialchars($mandor['nip'] ?? '-') . '</td>
</tr>

<tr>
    <td>Alamat</td>
    <td>' . htmlspecialchars($mandor['alamat'] ?? '-') . '</td>
</tr>

<tr>
    <td>No HP</td>
    <td>' . htmlspecialchars($mandor['no_hp'] ?? '-') . '</td>
</tr>

<tr>
    <td>BKPH</td>
    <td>' . htmlspecialchars($mandor['bkph'] ?? '-') . '</td>
</tr>

<tr>
    <td>Status</td>
    <td>' . (
        strtolower($mandor['status'] ?? '') == "aktif"
        ? '<span class="badge aktif">Aktif</span>'
        : '<span class="badge nonaktif">' . ucfirst(htmlspecialchars($mandor['status'] ?? '-')) . '</span>'
    ) . '</td>
</tr>

</table>

<div class="footer">
    Dicetak pada: ' . date('d-m-Y H:i') . '
</div>

</body>
</html>';

// DOMPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Tampilkan PDF
$dompdf->stream(
    "mandor_" . $mandor_id . ".pdf",
    ["Attachment" => 0]
);
exit;

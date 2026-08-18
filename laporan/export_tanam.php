<?php
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

date_default_timezone_set('Asia/Jakarta');

// =====================
// AMBIL ID TANAM
// =====================
$tanam_id = $_GET['tanam_id'] ?? 0;
if ($tanam_id == 0) {
    die("Data penanaman tidak ditemukan.");
}

// =====================
// DATA PENANAMAN + JOIN
// =====================
$query = mysqli_query($conn, "
    SELECT 
        p.tanam_id,
        p.jumlah_tanam,
        p.foto_tanam,
        p.tanggal,
        p.catatan,
        p.status,

        a.ajir_id,
        a.jumlah_ajir,

        l.lubang_id,
        l.jumlah_lubang,

        da.data_awal_id,
        da.tahun_tanam,
        da.target_pohon,

        b.bkph_id,
        b.nama_bkph,
        b.rph,
        b.petak,
        b.jenis_tanaman,
        b.jarak_tanam,

        m.mandor_id,
        m.nama AS nama_mandor,
        m.nip,
        m.no_hp

    FROM penanaman p
    LEFT JOIN ajir a ON p.ajir_id = a.ajir_id
    LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
    LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
    LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
    LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
    WHERE p.tanam_id = '$tanam_id'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan.");
}

// =====================
// FOTO TANAM BASE64
// =====================
$fotoPath = '../gambar_tanam/' . $data['foto_tanam'];
$fotoBase64 = '';

if (!empty($data['foto_tanam']) && file_exists($fotoPath)) {
    $ext = pathinfo($fotoPath, PATHINFO_EXTENSION);
    $fotoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fotoPath));
}

// =====================
// LOGO BASE64
// =====================
$logoPath = '../assets/images/logos/logo-m.png';
$logoBase64 = '';

if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}
$total = $data['jumlah_ajir'];
$persen = $total > 0
    ? round(($data['jumlah_tanam'] / $total) * 100, 2)
    : 0;

// =====================
// HTML PDF
// =====================
$html = '
<!DOCTYPE html>
<html>
<head>
<title>Data Penanaman - BKPH ' . htmlspecialchars($data['nama_bkph']) . '</title>
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

    .section-title {
        font-size:14px;
        font-weight:bold;
        margin-top:20px;
        margin-bottom:10px;
        color:#2c3e50;
        border-bottom:1px solid #ccc;
        padding-bottom:4px;
    }

    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 10px; 
    }

    td { 
        padding: 8px; 
        vertical-align: top;
    }

    .info-table td:first-child {
        width:35%;
        font-weight:bold;
        background:#f4f6f9;
    }

    .highlight-box {
        background:#f8f9fa;
        border-left:5px solid #28a745;
        padding:15px;
        margin-bottom:20px;
    }

    .badge {
        display:inline-block;
        padding:6px 12px;
        font-size:12px;
        font-weight:bold;
        border-radius:20px;
    }

    .success { background:#28a745; color:black; }
    .info { background:#17a2b8; color:black; }
    .warning { background:#ffc107; color:black; }

    .footer {
        text-align:center;
        margin-top:30px;
        font-size:12px;
        color:#777;
    }

    .two-col td {
        width:50%;
        vertical-align:top;
        padding-right:15px;
    }

    .foto {
        height: 220px;
        max-height:230px;
        object-fit:cover;
        border:1px solid #ddd;
        border-radius:6px;
    }

</style>
</head>
<body>

<div class="kop">
    <img src="' . $logoBase64 . '" class="logo">
    <div class="title">LAPORAN DATA PENANAMAN</div>
    <div class="alamat">
        Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten<br>
        Bojonegoro, Jawa Timur 62111
    </div>
</div>

<div class="highlight-box">
    <strong>BKPH ' . htmlspecialchars($data['nama_bkph']) . '</strong> 
    (RPH ' . htmlspecialchars($data['rph']) . ')<br>
    Tanggal Tanam: ' . date('d F Y', strtotime($data['tanggal'])) . '<br><br>

    <span class="badge info">' . number_format($data['jumlah_ajir']?? 0) . ' Ajir Tersedia</span>
    <span class="badge warning">' . number_format($data['jumlah_tanam']?? 0) . ' Pohon Ditanam</span>
    <span class="badge success">' . $persen . ' % Selesai</span>
</div>

<table class="two-col">
<tr>

<td>

<div class="section-title">Informasi Lokasi</div>
<table class="info-table" border="1">
<tr>
<td>Petak</td>
<td>' . htmlspecialchars($data['petak']) . '</td>
</tr>
<tr>
<td>Tahun Tanam</td>
<td>' . htmlspecialchars($data['tahun_tanam']) . '</td>
</tr>
<tr>
<td>Jenis Tanaman</td>
<td>' . htmlspecialchars($data['jenis_tanaman']) . '</td>
</tr>
<tr>
<td>Target Pohon</td>
<td>' . number_format($data['target_pohon']) . '</td>
</tr>
<tr>
<td>Status</td>
<td>' . ucfirst($data['status']) . '</td>
</tr>
</table>

</td>

<td>

<div class="section-title">Foto Penanaman</div>
' . (!empty($fotoBase64) 
    ? '<img src="' . $fotoBase64 . '" class="foto">'
    : '<i>Tidak ada foto tersedia</i>') . '

<div class="section-title">Petugas Lapangan</div>
<table class="info-table" border="1">
<tr>
<td>Mandor</td>
<td>' . htmlspecialchars($data['nama_mandor']) . '</td>
</tr>
<tr>
<td>NIP</td>
<td>' . htmlspecialchars($data['nip']) . '</td>
</tr>
<tr>
<td>No HP</td>
<td>' . htmlspecialchars($data['no_hp']) . '</td>
</tr>
</table>

</td>

</tr>
</table>

<div class="section-title">Catatan Lapangan</div>
<div style="border:1px solid #ccc; padding:12px; background:#fafafa; border-radius:5px;">
' . ($data['catatan'] 
    ? nl2br(htmlspecialchars($data['catatan'])) 
    : '<span style="color:#777;">Tidak ada catatan tambahan</span>') . '
</div>

<div class="footer">
    Dicetak pada: ' . date('d-m-Y H:i') . '
</div>

</body>
</html>
';

// =====================
// CETAK PDF
// =====================
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream(
    "Penanaman_" . $tanam_id . "_" . date('Ymd_His') . ".pdf",
    ["Attachment" => 0]
);
exit;

<?php
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// =======================
// SET TIMEZONE
// =======================
date_default_timezone_set('Asia/Jakarta');

// Ambil semua data pegawai
$query = mysqli_query($conn, "SELECT * FROM pegawai");
if (mysqli_num_rows($query) == 0) {
    die("Data pegawai kosong.");
}

// Logo
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
<meta charset="UTF-8">
<title>Laporan Data Pegawai</title>
<style>
    body { 
        font-family: Arial, Helvetica, sans-serif; 
        font-size: 14px; 
        color: #333;
    }

    @page {
    margin: 140px 40px 60px 40px; /* atas kanan bawah kiri */
}
    .kop { 
        position: fixed;
        text-align: center; 
        border-bottom: 3px double #000; 
        padding-bottom: 15px; 
        margin-bottom: 25px; 
         top: -120px;
        left: 0;
        right: 0;
    }

    .logo { 
        position: absolute; 
        top: 5px; 
        left: 20px; 
        width: 90px; 
        border-radius: 0px;
    }

    .title { 
        font-size: 18px; 
        font-weight: bold; 
        letter-spacing: 1px;
    }

    .subtitle {
        font-size: 12px;
        margin-top: 5px;
    }

    .alamat { 
        font-size: 14px; 
        margin-top: 5px;
    }

    table { 
        width: 100%; 
        border-collapse: collapse;
    }

    th { 
        background:#1f4e79;
        color: #fff;
        padding: 8px 5px;
        border: 1px solid #555;
        font-size: 16px;
    }

    td { 
        border: 1px solid #999; 
        padding: 6px 5px; 
        text-align: center; 
        vertical-align: middle;
    }

    td.alamat {
        text-align: left;
    }

    img { 
        width: 100px;
        height: auto;
        object-fit: cover;
        border-radius: 4px;
    }

    .footer {
        margin-top: 25px;
        font-size: 10px;
        text-align: right;
    }

    .footer-line {
        border-top: 1px solid #aaa;
        margin-top: 10px;
        padding-top: 5px;
         text-align:center;
    }
    .success { background:#28a745; color:white; }
    .danger { background:#dc3545; color:white; }
</style>
</head>
<body>

<div class="kop">
    <img src="' . $logoBase64 . '" class="logo">
    <div class="title">LAPORAN DATA PEGAWAI</div>
    <div class="subtitle">Dokumen Resmi Data Kepegawaian</div>
    <div class="alamat">
       Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten Bojonegoro<br>
       Jawa Timur 62111
    </div>
</div>

<table>
<thead>
<tr>
    <th width="4%">No</th>
    <th width="8%">Foto</th>
    <th width="14%">Nama</th>
    <th width="10%">NIP</th>
    <th width="12%">Jabatan</th>
    <th width="18%">Alamat</th>
    <th width="10%">No HP</th>
    <th width="8%">Status</th>
    <th width="10%">Tgl Dibuat</th>
</tr>
</thead>
<tbody>
';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {

    // Foto base64
    $fotoBase64 = '';
    if (!empty($row['foto'])) {
        $fotoPath = '../image/' . $row['foto'];
        if (file_exists($fotoPath)) {
            $ext = pathinfo($fotoPath, PATHINFO_EXTENSION);
            $fotoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fotoPath));
        }
    }

    $html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . (!empty($fotoBase64) ? '<img src="' . $fotoBase64 . '">' : '-') . '</td>
        <td>' . htmlspecialchars($row['nama'] ?? '-') . '</td>
        <td>' . htmlspecialchars($row['nip'] ?? '-') . '</td>
        <td>' . htmlspecialchars($row['jabatan'] ?? '-') . '</td>
        <td class="alamat">' . htmlspecialchars($row['alamat'] ?? '-') . '</td>
        <td>' . htmlspecialchars($row['no_hp'] ?? '-') . '</td>
        <td>' . ucfirst(htmlspecialchars($row['status'] ?? '-')) . '</td>
        <td>' . date('d-m-Y', strtotime($row['created_at'])) . '</td>
    </tr>
    ';
}

$html .= '
</tbody>
</table>

<div class="footer">
    <div class="footer-line">
        Dicetak pada: ' . date('d-m-Y H:i') . ' WIB
    </div>
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
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

$dompdf->stream(
    "laporan_data_pegawai.pdf",
    ["Attachment" => 0]
);
exit;

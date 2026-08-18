<?php
session_start();
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// =======================
// SET TIMEZONE
// =======================
date_default_timezone_set('Asia/Jakarta');


$role = $_SESSION['role'] ?? '';
$mandor_id = $_SESSION['mandor_id'] ?? '';
$tahun = $_GET['tahun'] ?? 'all';
// =======================
// QUERY DATA Ajir
// =======================
$sql = "
    SELECT 
              l.lubang_id,
              l.jumlah_lubang,

              a.ajir_id,
              a.jumlah_ajir,
              a.foto_ajir,
              a.tanggal,
              a.catatan,
              a.status,

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

          FROM ajir a
          LEFT JOIN lubang l ON a.lubang_id = l.lubang_id
          LEFT JOIN data_awal da ON l.data_awal_id = da.data_awal_id
          LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
          LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
        
";

$where = [];

/* Jika Mandor → filter sesuai mandor yang login */
if ($role === 'mandor' && !empty($mandor_id)) {
    $where[] = "b.mandor_id = '$mandor_id'";
}

/* Filter Tahun */
if ($tahun != 'all' && $tahun != '') {
    $where[] = "da.tahun_tanam = '".mysqli_real_escape_string($conn, $tahun)."'";
}


/* Tambahkan WHERE jika ada */
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

/* ORDER BY harus terakhir */
$sql .= "   ORDER BY a.ajir_id DESC;";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($query) == 0) {
    die("Data Ajir kosong.");
}

// =======================
// LOGO
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
<meta charset="UTF-8">
<title>Laporan Data Ajir</title>
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
        font-size: 11px; 
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

    img { 
        width: 100px;
        height: auto;
        object-fit: cover;
        border-radius: 4px;
    }

    .footer {
        margin-top: 25px;
        font-size: 10px;
         text-align:center;
    }

    .footer-line {
        border-top: 1px solid #aaa;
        margin-top: 10px;
        padding-top: 5px;
    }
</style>
</head>
<body>

<div class="kop">
    <img src="' . $logoBase64 . '" class="logo">
    <div class="title">LAPORAN DATA AJIR</div>
    <div class="subtitle">Dokumen Resmi Monitoring Pembuatan Ajir</div>
    <div class="alamat">
       Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten Bojonegoro<br>
       Jawa Timur 62111
    </div>
</div>

<table>
<thead>
<tr>
    <th width="4%">No</th>
    <th width="10%">Foto</th>
    <th width="10%">Tanggal</th>
    <th width="16%">Data Lokasi</th>
    <th width="13%">Mandor</th>
    <th width="21%">Rencana Tanam</th>
    <th width="10%">Jumlah Ajir</th>
    <th width="10%">Status</th>
</tr>
</thead>
<tbody>
';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {

    // Foto base64
    $fotoBase64 = '';
    if (!empty($row['foto_ajir'])) {
        $fotoPath = '../gambar_ajir/' . $row['foto_ajir'];
        if (file_exists($fotoPath)) {
            $ext = pathinfo($fotoPath, PATHINFO_EXTENSION);
            $fotoBase64 = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($fotoPath));
        }
    }

    $lokasi =
        'BKPH:' . htmlspecialchars($row['nama_bkph'] ?? '-') . '<br>' .
        'RPH: ' . htmlspecialchars($row['rph'] ?? '-') . '<br>' .
        'Petak: ' . htmlspecialchars($row['petak'] ?? '-');

    $rencana =
        'Tahun: ' . htmlspecialchars($row['tahun_tanam'] ?? '-') . '<br>' .
        'tersedia: ' . htmlspecialchars($row['jumlah_lubang'] ?? '-') . ' Lubang' . '<br>' .
        'Jenis: ' . htmlspecialchars($row['jenis_tanaman'] ?? '-');

    $html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . (!empty($fotoBase64) ? '<img src="' . $fotoBase64 . '">' : '-') . '</td>
        <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
        <td style="text-align:left;">' . $lokasi . '</td>
        <td style="text-align:left;">
            ' . htmlspecialchars($row['nama_mandor'] ?? '-') . '
        </td>
        <td style="text-align:left;">' . $rencana . '</td>
        <td>' . htmlspecialchars($row['jumlah_ajir'] ?? '-') . ' Lubang</td>
        <td>' . ucfirst(htmlspecialchars($row['status'] ?? '-')) . '</td>
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
$dompdf->setPaper('A4', 'potrait');
$dompdf->render();

$dompdf->stream(
    "laporan_data_ajir.pdf",
    ["Attachment" => 0]
);
exit;
?>
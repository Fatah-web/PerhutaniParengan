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

// =======================
// AMBIL DATA
// =======================
$role      = $_SESSION['role'] ?? '';
$mandor_id = $_SESSION['mandor_id'] ?? '';
$tahun = $_GET['tahun'] ?? 'all';
$sql = "
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
$sql .= " ORDER BY da.data_awal_id DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($query) == 0) {
    die("Data Awal kosong.");
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
<title>Laporan Data Awal</title>
<style>
    body { 
        font-family: Arial, Helvetica, sans-serif; 
        font-size: 10px; 
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

    .alamat-kantor { 
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
        padding: 6px 4px;
        border: 1px solid #555;
        font-size: 16px;
    }

    td { 
        border: 1px solid #999; 
        padding: 5px 4px; 
        text-align: center; 
        vertical-align: middle;
        font-size: 14px;
    }

    td.text-left {
        text-align: left;
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
    <img src="'.$logoBase64.'" class="logo">
    <div class="title">LAPORAN DATA AWAL</div>
    <div class="subtitle">Dokumen Resmi Perencanaan Penanaman</div>
    <div class="alamat-kantor">
        Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten Bojonegoro<br>
        Jawa Timur 62111
    </div>
</div>

<table>
<thead>
<tr>
    <th width="3%">No</th>
    <th width="7%">Tahun</th>
    <th width="8%">Target Pohon</th>
    <th width="10%">BKPH</th>
    <th width="8%">RPH</th>
    <th width="6%">Petak</th>
    <th width="8%">Luas (Ha)</th>
    <th width="10%">Jenis Tanaman</th>
    <th width="8%">Jarak Tanam</th>
    <th width="10%">Jumlah Lubang</th>
    <th width="10%">Mandor</th>
</tr>
</thead>
<tbody>
';

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {

    $html .= '
    <tr>
        <td>'.$no++.'</td>
        <td>'.htmlspecialchars($row['tahun_tanam'] ?? '-').'</td>
        <td>'.htmlspecialchars($row['target_pohon'] ?? '-').'</td>
        <td class="text-left">'.htmlspecialchars($row['nama_bkph'] ?? '-').'</td>
        <td>'.htmlspecialchars($row['rph'] ?? '-').'</td>
        <td>'.htmlspecialchars($row['petak'] ?? '-').'</td>
        <td>'.htmlspecialchars($row['luas_baku'] ?? '-').'</td>
        <td>'.htmlspecialchars($row['jenis_tanaman'] ?? '-').'</td>
        <td>'.htmlspecialchars($row['jarak_tanam'] ?? '-').'</td>
        <td>'.htmlspecialchars($row['nama_mandor'] ?? '-').'</td>
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
$options->set("isRemoteEnabled", true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "potrait");
$dompdf->render();

// =======================
// STREAM PDF
// =======================
$dompdf->stream(
    "laporan_data_awal.pdf",
    ["Attachment" => 0]
);
exit;
?>

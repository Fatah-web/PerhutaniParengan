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


$role      = $_SESSION['role'] ?? '';
$mandor_id = $_SESSION['mandor_id'] ?? '';
$tahun = $_GET['tahun'] ?? 'all';

// =======================
// QUERY FULL DATA MONITORING
// =======================
$sql = "
    SELECT 
        mo.monitoring_id,
        mo.tanggal,
        mo.jumlah_hidup,
        mo.jumlah_mati,
        mo.evaluasi,
        mo.catatan,
        mo.status,
        mo.foto_hidup,
        mo.foto_mati,

        p.tanam_id,
        p.jumlah_tanam,

        da.tahun_tanam,
        da.target_pohon,

        b.nama_bkph,
        b.rph,
        b.petak,
        b.jenis_tanaman,

        m.nama AS nama_mandor

    FROM monitoring mo
    LEFT JOIN penanaman p ON mo.tanam_id = p.tanam_id
    LEFT JOIN ajir a ON p.ajir_id = a.ajir_id
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
$sql .= " ORDER BY mo.monitoring_id DESC";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}

if (mysqli_num_rows($query) == 0) {
    die("Data monitoring kosong.");
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
// HTML
// =======================
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size:11px; color:#333; }

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
.logo { position:absolute; left:10px; top:0; border-radius: 0px; width:80px; }

.title { font-size:18px; font-weight:bold; }

.subtitle { font-size:12px; margin-top:4px; }

table { width:100%; border-collapse:collapse; }

th { 
    background:#1f4e79; 
    color:#fff; 
    padding:6px; 
    border:1px solid #ccc;
    font-size:11px;
}

td { 
    border:1px solid #ccc; 
    padding:5px; 
    vertical-align:top;
}

img { width:150px; height:auto; border-radius: 4px; }

.footer {
    margin-top:20px;
    font-size:10px;
    text-align:right;
    border-top:1px solid #ccc;
    padding-top:6px;
}
</style>
</head>
<body>

<div class="kop">
    <img src="' . $logoBase64 . '" class="logo">
    <div class="title">LAPORAN DATA MONITORING PENANAMAN</div>
    <div class="subtitle">Dokumen Resmi Monitoring dan Evaluasi Tanaman</div>
    <div class="alamat-kantor">
       Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten Bojonegoro<br>
       Jawa Timur 62111
    </div>
</div>

<table>
<thead>
<tr>
<th width="3%">No</th>
<th width="8%">Tanggal</th>
<th width="13%">Informasi</th>
<th width="10%">kondisi</th>
<th width="6%">status</th>
<th width="15%">Catatan</th>
<th width="15%">Evaluasi</th>
<th width="15%">Foto Hidup</th>
<th width="15%">Foto Mati</th>
</tr>
</thead>
<tbody>
';

$no = 1;

while ($row = mysqli_fetch_assoc($query)) {

    $total = $row['jumlah_hidup'] + $row['jumlah_mati'];
    $persen = $total > 0 
    ? round(($row['jumlah_hidup'] / $total) * 100, 2) 
    : 0;

    // FOTO HIDUP
    $fotoHidup = '-';
    if (!empty($row['foto_hidup'])) {
        $path = '../gambar_monitoring/' . $row['foto_hidup'];
        if (file_exists($path)) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            $fotoHidup = '<img src="data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($path)) . '">';
        }
    }

    // FOTO MATI
    $fotoMati = '-';
    if (!empty($row['foto_mati'])) {
        $path2 = '../gambar_monitoring/' . $row['foto_mati'];
        if (file_exists($path2)) {
            $ext2 = pathinfo($path2, PATHINFO_EXTENSION);
            $fotoMati = '<img src="data:image/' . $ext2 . ';base64,' . base64_encode(file_get_contents($path2)) . '">';
        }
    }

    $lokasi =
        'Mandor: ' . $row['nama_mandor'] . '<br>' .
        'BKPH: ' . $row['nama_bkph'] . '<br>' .
        'RPH: ' . $row['rph'] . '<br>' .
        'Petak: ' . $row['petak'];
    $kondisi =
        'Jenis: ' . $row['jenis_tanaman'] . '<br>' .
        'Hidup: ' . $row['jumlah_hidup'] . '<br>' .
        'Mati: ' . $row['jumlah_mati'] . '<br>' .
        $persen . '% hidup';


    $html .= '
    <tr>
        <td>' . $no++ . '</td>
        <td>' . date('d-m-Y', strtotime($row['tanggal'])) . '</td>
        <td>' . $lokasi . '</td>
        <td>' . $kondisi . '</td>
        <td align="center">' . $row['status'] . '</td>
        <td>' . ($row['catatan'] ?? '-') . '</td>
        <td>' . ($row['evaluasi'] ?? '-') . '</td>
        <td align="center">' . $fotoHidup . '</td>
        <td align="center">' . $fotoMati . '</td>
    </tr>
    ';
}

$html .= '
</tbody>
</table>

<div class="footer">
Dicetak pada: ' . date('d-m-Y H:i') . ' WIB
</div>

</body>
</html>
';

// =======================
// GENERATE PDF
// =======================
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // landscape agar tabel muat
$dompdf->render();

$dompdf->stream(
    "laporan_full_monitoring.pdf",
    ["Attachment" => 0]
);
exit;

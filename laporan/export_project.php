<?php
require '../vendor/autoload.php';
require '../function/koneksi.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ✅ QR versi PHP 8.1 (v4)
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

date_default_timezone_set('Asia/Jakarta');

$data_awal_id = $_GET['data_awal_id'] ?? 0;

if ($data_awal_id == 0) {
    die("Data tidak ditemukan");
}

/* =========================
QUERY DATA SESUAI MODAL
========================= */

$query = mysqli_query($conn, "
SELECT
da.*,
b.nama_bkph,
b.rph,
b.petak,
b.jenis_tanaman,
b.jarak_tanam,
b.luas_baku,

m.nama AS nama_mandor,
m.no_hp,
m.alamat,
m.foto AS foto_mandor,

l.jumlah_lubang,
l.tanggal AS tanggal_lubang,
l.foto_lokasi,

a.jumlah_ajir,
a.tanggal AS tanggal_ajir,
a.foto_ajir,

p.jumlah_tanam,
p.tanggal AS tanggal_tanam,
p.foto_tanam,

mo.jumlah_hidup,
mo.jumlah_mati,
mo.tanggal AS tanggal_monitor,
mo.foto_hidup,
mo.foto_mati,
mo.catatan,
mo.evaluasi,
mo.gangguan

FROM data_awal da

LEFT JOIN bkph b ON da.bkph_id = b.bkph_id
LEFT JOIN mandor m ON b.mandor_id = m.mandor_id
LEFT JOIN lubang l ON da.data_awal_id = l.data_awal_id
LEFT JOIN ajir a ON l.lubang_id = a.lubang_id
LEFT JOIN penanaman p ON a.ajir_id = p.ajir_id
LEFT JOIN monitoring mo ON p.tanam_id = mo.tanam_id

WHERE da.data_awal_id = '$data_awal_id'
");

$d = mysqli_fetch_assoc($query);

if (!$d) {
    die("Data tidak ditemukan");
}


/* =========================
HITUNG PERSENTASE
========================= */

$total = $d['jumlah_hidup'] + $d['jumlah_mati'];

$persen = $total > 0
    ? round(($d['jumlah_hidup'] / $total) * 100, 2)
    : 0;

/* =========================
FOTO BASE64
========================= */

function getBase64($path)
{
    if (!empty($path) && file_exists($path)) {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        return 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($path));
    }
    return '';
}

$defaultFoto = getBase64('../assets/images/default.jpg');

$fotoLubang = !empty($d['foto_lokasi'])
    ? getBase64('../gambar_lubang/' . $d['foto_lokasi'])
    : $defaultFoto;

$fotoAjir = !empty($d['foto_ajir'])
    ? getBase64('../gambar_ajir/' . $d['foto_ajir'])
    : $defaultFoto;

$fotoTanam = !empty($d['foto_tanam'])
    ? getBase64('../gambar_tanam/' . $d['foto_tanam'])
    : $defaultFoto;

$fotoMonitor = !empty($d['foto_hidup'])
    ? getBase64('../gambar_monitoring/' . $d['foto_hidup'])
    : $defaultFoto;
/* =========================
LOGO BASE64
========================= */

$logoPath = '../assets/images/logos/logo-m.png';
$logoBase64 = '';

if (file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}


$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";

$linkCetak = $protocol . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/export_awal.php?data_awal_id=" . $data_awal_id;

// =======================
// QR + LOGO (FIX PHP 8.1)
// =======================
$qrCode = '';

try {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($linkCetak)
        ->size(350)              // ukuran QR
        ->margin(20)

        // 🔥 LOGO DI TENGAH
        ->logoPath('../assets/images/logos/logo_bulat.png')
        ->logoResizeToWidth(80)   // atur ukuran logo (60 - 100 biasanya ideal)
        ->logoPunchoutBackground(true) // biar background logo bersih

        ->build();

    $qrCode = 'data:image/png;base64,' . base64_encode($result->getString());

} catch (Exception $e) {
    echo "QR ERROR: " . $e->getMessage();
    exit;
}


/* =========================
HTML PDF
========================= */

$html = '

<style>

body{
font-family: DejaVu Sans;
font-size:11px;
color:#2c2c2c;
line-height:1.5;
}

/* HEADER */

.kop { 
    text-align: center; 
    border-bottom: 4px solid #1f4e79; 
    padding-bottom: 15px; 
    margin-bottom: 25px; 
    position: relative;
}

.logo { 
    position: absolute; 
    top: 8px; 
    left: 0px; 
    width: 115px; 
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


/* SECTION */

.section{
margin-top:18px;
font-weight:bold;
background:#2f5597;
color:white;
padding:6px 8px;
font-size:12px;
letter-spacing:0.5px;
}


/* TABLE */

.table{
width:100%;
border-collapse:collapse;
margin-top:5px;
}

.table td{
border:1px solid #dcdcdc;
padding:6px 8px;
}

.table tr:nth-child(even){
background:#f7f7f7;
}

.label{
width:35%;
font-weight:bold;
background:#f1f1f1;
}


/* STAT BOX */

.stat-wrapper{
width:100%;
margin-top:10px;
}

.stat{
width:28%;
display:inline-block;
text-align:center;
border:1px solid #dcdcdc;
padding:12px;
margin-right:1%;
background:#fafafa;
}

.stat-title{
font-size:11px;
color:#777;
}

.stat-value{
font-size:18px;
font-weight:bold;
color:#2f5597;
margin-top:3px;
}


/* FOTO */
.foto-box{
border:1px solid #ccc;
padding:1px;
width:300px;
height:260px;
margin:0 auto;
text-align:center;
}

.foto-title{
font-weight:bold;
margin-bottom:5px;
}

.foto-box img{
width:100%;
height:200px;
object-fit:cover;
}

/* FOOTER */

.footer{
margin-top:40px;
font-size:10px;
color:#666;
text-align:right;
border-top:1px solid #ccc;
padding-top:6px;
}

</style>

<!-- ================= HEADER ================= -->
<div class="kop">
    <img src="' . $logoBase64 . '" class="logo">
    <div class="title">KESATUAN PENGELOLAAN HUTAN</div>
    <div class="subtitle">Sistem Monitoring dan Evaluasi Tanaman</div>
    <div class="alamat">
        Jl. Teuku Umar No.2, Kadipaten, Kec. Bojonegoro, Kabupaten Bojonegoro<br>
        Jawa Timur 62111
    </div>
</div>


<div class="section">INFORMASI LOKASI</div>

<table class="table">

<tr>
<td class="label">BKPH</td>
<td>' . (!empty($d['nama_bkph']) ? $d['nama_bkph'] : '-') . '</td>
</tr>

<tr>
<td class="label">RPH</td>
<td>' . (!empty($d['rph']) ? $d['rph'] : '-') . '</td>
</tr>

<tr>
<td class="label">Petak</td>
<td>' . (!empty($d['petak']) ? $d['petak'] : '-') . '</td>
</tr>

<tr>
<td class="label">Jenis Tanaman</td>
<td>' . (!empty($d['jenis_tanaman']) ? $d['jenis_tanaman'] : '-') . '</td>
</tr>

<tr>
<td class="label">Tahun Tanam</td>
<td>' . (!empty($d['tahun_tanam']) ? $d['tahun_tanam'] : '-') . '</td>
</tr>

<tr>
<td class="label">Luas Baku</td>
<td>' . (!empty($d['luas_baku']) ? $d['luas_baku'].' Ha' : '-') . '</td>
</tr>

<tr>
<td class="label">Jarak Tanam</td>
<td>' . (!empty($d['jarak_tanam']) ? $d['jarak_tanam'] : '-') . '</td>
</tr>

<tr>
<td class="label">Target Pohon</td>
<td>' . (!empty($d['target_pohon']) ? $d['target_pohon'].' pohon' : '-') . '</td>
</tr>

</table>



<div class="section">MANDOR LAPANGAN</div>

<table class="table">

<tr>
<td class="label">Nama Mandor</td>
<td>' . (!empty($d['nama_mandor']) ? $d['nama_mandor'] : '-') . '</td>
</tr>

<tr>
<td class="label">No HP</td>
<td>' . (!empty($d['no_hp']) ? $d['no_hp'] : '-') . '</td>
</tr>

<tr>
<td class="label">Alamat</td>
<td>' . (!empty($d['alamat']) ? $d['alamat'] : '-') . '</td>
</tr>
<tr>
<td class="label">Tenaga Kerja</td>
<td>' . (!empty($d['tenaga_kerja']) ? $d['tenaga_kerja'] : '-') . ' orang</td>
</tr>
</table>



<div class="section">PROGRES PENANAMAN</div>

<div class="stat-wrapper">

<div class="stat">
<div class="stat-title">Jumlah Lubang</div>
<div class="stat-value">' . number_format($d['jumlah_lubang'] ?? 0) . '</div>
</div>

<div class="stat">
<div class="stat-title">Jumlah Ajir</div>
<div class="stat-value">' . number_format($d['jumlah_ajir'] ?? 0) . '</div>
</div>

<div class="stat">
<div class="stat-title">Jumlah Tanam</div>
<div class="stat-value">' . number_format($d['jumlah_tanam'] ?? 0) . '</div>
</div>

</div>



<div class="section">HASIL MONITORING</div>

<div class="stat-wrapper">

<div class="stat">
<div class="stat-title">Tanaman Hidup</div>
<div class="stat-value">' . number_format($d['jumlah_hidup'] ?? 0) . '</div>
</div>

<div class="stat">
<div class="stat-title">Tanaman Mati</div>
<div class="stat-value">' . number_format($d['jumlah_mati'] ?? 0) . '</div>
</div>

<div class="stat">
<div class="stat-title">Persentase Hidup</div>
<div class="stat-value">' . ($persen ?? 0) . '%</div>
</div>

</div>

<br><br><br><br>

<div class="section">DOKUMENTASI LAPANGAN</div>
<table width="700px" border="0" cellspacing="1" cellpadding="10" style="margin-top:10px; border-collapse:collapse; text-align:center;">

<tr>

<td>
<div class="foto-box">
<div class="foto-title"><b>Lubang</b></div>
<img src="'.$fotoLubang.'" style="width:270px; margin:auto; display:block;">
</div>
</td>

<td>
<div class="foto-box">
<div class="foto-title"><b>Ajir</b></div>
<img src="'.$fotoAjir.'" style="width:270px; margin:auto; display:block;">
</div>
</td>

</tr>

<tr>

<td>
<div class="foto-box">
<div class="foto-title"><b>Penanaman</b></div>
<img src="'.$fotoTanam.'" style="width:270px; margin:auto; display:block;">
</div>
</td>

<td>
<div class="foto-box">
<div class="foto-title"><b>Monitoring</b></div>
<img src="'.$fotoMonitor.'" style="width:270px; margin:auto; display:block;">
</div>
</td>

</tr>

</table>


<div class="section">EVALUASI</div>

<table class="table">

<tr>
<td width="50%">
<b>Catatan</b><br><br>
' . (!empty($d['catatan']) ? $d['catatan'] : '-') . '
</td>

<td width="50%">
<b>Evaluasi</b><br><br>
' . (!empty($d['evaluasi']) ? $d['evaluasi'] : '-') . '
</td>
</tr>

</table>


<br><br>

<table width="100%">

<tr>

<td width="60%"></td>

<td style="text-align:center">
Dicetak pada ' . date('d-m-Y H:i') . ' WIB<br>
Mengetahui,<br>
  <img src="'.$qrCode.'" width="140"><br>
<span style="font-size:11px; color:#555;">Scan untuk verifikasi dokumen</span>

</td>

</tr>

</table>

';


/* =========================
GENERATE PDF
========================= */

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$dompdf->stream(
    "Data_Penanaman_" . $data_awal_id . ".pdf",
    ["Attachment" => 0]
);

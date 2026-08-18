<?php
function upload()
{

    $namafile = $_FILES['foto']['name'];
    $ukuranfile = $_FILES['foto']['size'];
    $tmpname = $_FILES['foto']['tmp_name'];

    $ekstensifoto = explode('.', $namafile);
    $ekstensifoto = strtolower(end($ekstensifoto));
    $maxsize = 2 * 1024 * 1024;

    if ($ukuranfile > $maxsize) {
        echo "<script>alert('Ukuran gambar terlalu besar, Silahkan ulangi lagi!')</script>";
        return false;
    }

    $namafilebaru = uniqid() . '.' . $ekstensifoto;

    move_uploaded_file($tmpname, '../image/' . $namafilebaru);

    return $namafilebaru;
}

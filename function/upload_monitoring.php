<?php

function upload_hidup()
{
    $file = $_FILES['foto_hidup'];

    $namaFile   = $file['name'];
    $ukuranFile = $file['size'];
    $tmpName    = $file['tmp_name'];
    $error      = $file['error'];

    if ($error === 4) {
        return null;
    }

    $ekstensiValid = ['jpg','jpeg','png'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiValid)) {
        echo "<script>alert('File harus berupa gambar (jpg, jpeg, png)!')</script>";
        return false;
    }

    if ($ukuranFile > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran gambar maksimal 2MB!')</script>";
        return false;
    }

    $namaBaru = uniqid('hidup_') . '.' . $ekstensi;
    move_uploaded_file($tmpName, '../gambar_monitoring/' . $namaBaru);

    return $namaBaru;
}


function upload_mati()
{
    $file = $_FILES['foto_mati'];

    $namaFile   = $file['name'];
    $ukuranFile = $file['size'];
    $tmpName    = $file['tmp_name'];
    $error      = $file['error'];

    if ($error === 4) {
        return null;
    }

    $ekstensiValid = ['jpg','jpeg','png'];
    $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

    if (!in_array($ekstensi, $ekstensiValid)) {
        echo "<script>alert('File harus berupa gambar (jpg, jpeg, png)!')</script>";
        return false;
    }

    if ($ukuranFile > 2 * 1024 * 1024) {
        echo "<script>alert('Ukuran gambar maksimal 2MB!')</script>";
        return false;
    }

    $namaBaru = uniqid('mati_') . '.' . $ekstensi;
    move_uploaded_file($tmpName, '../gambar_monitoring/' . $namaBaru);

    return $namaBaru;
}

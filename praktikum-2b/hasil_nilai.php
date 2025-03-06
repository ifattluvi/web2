<?php

// Mengecek apakah tombol submit sudah ditekan atau belum, jika belum maka akan diarahkan kembali ke form_nilai.php
if (!isset($_POST['submit'])) {
    header("Location: form_nilai.php");
    exit; // Menghentikan proses
}

// Mengambil data dari file proses_nilai.php
require_once 'proses_nilai.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Registrasi Nilai Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class="mx-auto mt-5 border border-2 border-dark p-3 bg-success text-white rounded" style="width: 45%;">
        <h3 class="text-center">Hasil Registrasi Nilai Mahasiswa</h3>
        <table class="table border border-2 border-success mt-3 text-white w-75 mx-auto">
            <tbody>
                <tr>
                    <td>NIM</td>
                    <td>:</td>
                    <td><?= $nim; ?></td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><?= $nama_siswa; ?></td>
                </tr>
                <tr>
                    <td>Mata Kuliah</td>
                    <td>:</td>
                    <td><?= $mata_kuliah; ?></td>
                </tr>
                <tr>
                    <td>Nilai UTS</td>
                    <td>:</td>
                    <td><?= $nilai_uts; ?></td>
                </tr>
                <tr>
                    <td>Nilai UAS</td>
                    <td>:</td>
                    <td><?= $nilai_uas; ?></td>
                </tr>
                <tr>
                    <td>Nilai Tugas</td>
                    <td>:</td>
                    <td><?= $nilai_tugas; ?></td>
                </tr>
                <tr>
                    <td>Total Nilai</td>
                    <td>:</td>
                    <td><?= $total_nilai; ?></td>
                </tr>
                <tr>
                    <td>Grade</td>
                    <td>:</td>
                    <td><b><?= $grade; ?></b></td>
                </tr>
                <tr>
                    <td>Predikat</td>
                    <td>:</td>
                    <td><b><?= $predikat; ?></b></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>:</td>
                    <td><b><?= ($total_nilai > 55) ? "LULUS" : "TIDAK LULUS"; ?></b></td>
                </tr>
            </tbody>
        </table>
        <div class="text-center my-3">
            <a href="form_nilai.php" class="btn btn-warning">Kembali</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>

</html>

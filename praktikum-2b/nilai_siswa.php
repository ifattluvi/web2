<?php
if (isset($_POST['proses'])) {
    $proses = $_POST['proses'];
    $nama_siswa = $_POST['nama'];
    $mata_kuliah = $_POST['mata_kuliah']; 
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];
    $nilai_tugas = $_POST['nilai_tugas'];

    
    $total_nilai = ($nilai_uts * 0.3) + ($nilai_uas * 0.35) + ($nilai_tugas * 0.35);

    
    echo "Proses : " . $proses;
    echo "<br>Nama : " . $nama_siswa;
    echo "<br>Mata Kuliah : " . $mata_kuliah;
    echo "<br>Nilai UTS : " . $nilai_uts;
    echo "<br>Nilai UAS : " . $nilai_uas;
    echo "<br>Nilai Tugas Praktikum : " . $nilai_tugas;
    echo "<br>Total Nilai : " . $total_nilai;

    
    if ($total_nilai > 55) {
        echo "<br>Status: <b>LULUS</b>";
    } else {
        echo "<br>Status: <b>TIDAK LULUS</b>";
    }

    
    if ($total_nilai >= 85 && $total_nilai <= 100) {
        $grade = "A";
    } elseif ($total_nilai >= 70 && $total_nilai < 85) {
        $grade = "B";
    } elseif ($total_nilai >= 56 && $total_nilai < 70) {
        $grade = "C";
    } elseif ($total_nilai >= 36 && $total_nilai < 56) {
        $grade = "D";
    } elseif ($total_nilai >= 0 && $total_nilai < 36) {
        $grade = "E";
    } else {
        $grade = "I";
    }

    echo "<br>Grade : <b>" . $grade . "</b>";

    
    switch ($grade) {
        case "A":
            $predikat = "Sangat Memuaskan";
            break;
        case "B":
            $predikat = "Memuaskan";
            break;
        case "C":
            $predikat = "Cukup";
            break;
        case "D":
            $predikat = "Kurang";
            break;
        case "E":
            $predikat = "Sangat Kurang";
            break;
        default:
            $predikat = "Tidak Ada";
    }

    echo "<br>Predikat : <b>" . $predikat . "</b>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Nilai Siswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body style="font-size: 18px; background-color: #f8f9fa;">
    <div class="container mt-5">
        <form method="POST" action="nilai_siswa.php">
            <fieldset class="p-4 bg-white">
                <legend class="w-auto px-3 h3 font-weight-bold">Form Nilai</legend>

                <div class="form-group row">
                    <label for="nama" class="col-sm-4 col-form-label">Nama</label> 
                    <div class="col-sm-8">
                        <input id="nama" name="nama" placeholder="Nama Lengkap" type="text" required class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="mata_kuliah" class="col-sm-4 col-form-label">Mata Kuliah</label> 
                    <div class="col-sm-8">
                        <select id="mata_kuliah" name="mata_kuliah" required class="custom-select">
                            <option value="dasar dasar pemrograman">Dasar Dasar Pemrograman</option>
                            <option value="basis data i">Basis Data I</option>
                            <option value="pemrograman web">Pemrograman Web</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="nilai_uts" class="col-sm-4 col-form-label">Nilai UTS</label> 
                    <div class="col-sm-8">
                        <input id="nilai_uts" name="nilai_uts" placeholder="Nilai UTS" type="text" required class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="nilai_uas" class="col-sm-4 col-form-label">Nilai UAS</label> 
                    <div class="col-sm-8">
                        <input id="nilai_uas" name="nilai_uas" placeholder="Nilai UAS" type="text" required class="form-control">
                    </div>
                </div>

                <div class="form-group row">
                    <label for="nilai_tugas" class="col-sm-4 col-form-label">Nilai Tugas/Praktikum</label> 
                    <div class="col-sm-8">
                        <input id="nilai_tugas" name="nilai_tugas" placeholder="Nilai Tugas" type="text" required class="form-control" value="" size="6"/><br/ >
                    </div>
                </div> 

                <div class="form-group row">
                    <div class="col-sm-8 offset-sm-4">
                        <button name="proses" value="Simpan" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </fieldset>
        </form>

        <?php
        if (isset($_GET['submit'])) {
            $nama_siswa = $_GET['nama'];
            $mata_kuliah = $_GET['mata_kuliah'];
            $nilai_uts = $_GET['nilai_uts'];
            $nilai_uas = $_GET['nilai_uas'];
            $nilai_tugas = $_GET['nilai_tugas'];

            echo 'proses : ' . $_GET['proses'];
            echo '<br/>Nama : ' . $nama_siswa;
            echo '<br/>Mata Kuliah : ' . $mata_kuliah;
            echo '<br/>Nilai UTS : ' . $nilai_uts;
            echo '<br/>Nilai UAS : ' . $nilai_uas;
            echo '<br/>Nilai Tugas : ' . $nilai_tugas;
        }
        ?>
    </div>
</body>
</html>

<?php
include 'config/database.php';

// Tambah data
if (isset($_POST['tambah'])) {
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jabatan = $_POST['jabatan'];

    $stmt = $conn->prepare("INSERT INTO pegawai (nip, nama, jenis_kelamin, jabatan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nip, $nama, $jenis_kelamin, $jabatan);
    $stmt->execute();
    $stmt->close();
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM pegawai WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: pegawai.php");
}

// Ambil data
$data = mysqli_query($conn, "SELECT * FROM pegawai");

// Update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nip = $_POST['nip'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jabatan = $_POST['jabatan'];

    $stmt = $conn->prepare("UPDATE pegawai SET nip = ?, nama = ?, jenis_kelamin = ?, jabatan = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nip, $nama, $jenis_kelamin, $jabatan, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: pegawai.php");
}

// Ambil data untuk edit
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM pegawai WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!-- HTML -->
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div id="layoutSidenav_content">
    <div class="container">
        <link href="css/styles2.css" rel="stylesheet" />

        <div class="container-fluid px-4">
            <h2>Data Pegawai</h2>

            <!-- Tombol untuk membuka modal tambah -->
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Pegawai</button>

            <!-- Konten Pegawai (diubah ke tabel) -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['nip']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                <td>
                                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id'] ?>">Edit</a>
                                    <a href="?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                                </td>
                            </tr>
                            <!-- Modal untuk Edit -->
                            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Data Pegawai</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="nip<?= $row['id'] ?>" class="form-label">NIP</label>
                                                    <input type="text" class="form-control" id="nip<?= $row['id'] ?>" name="nip" value="<?= htmlspecialchars($row['nip']) ?>" maxlength="10" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="nama<?= $row['id'] ?>" class="form-label">Nama</label>
                                                    <input type="text" class="form-control" id="nama<?= $row['id'] ?>" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" maxlength="45" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="jenis_kelamin<?= $row['id'] ?>" class="form-label">Jenis Kelamin</label>
                                                    <select class="form-control" id="jenis_kelamin<?= $row['id'] ?>" name="jenis_kelamin" required>
                                                        <option value="L" <?= $row['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                                        <option value="P" <?= $row['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="jabatan<?= $row['id'] ?>" class="form-label">Jabatan</label>
                                                    <input type="text" class="form-control" id="jabatan<?= $row['id'] ?>" name="jabatan" value="<?= htmlspecialchars($row['jabatan']) ?>" maxlength="45" required>
                                                </div>
                                                <button type="submit" name="update" class="btn btn-primary">Edit</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal untuk Tambah -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Data Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text" class="form-control" id="nip" name="nip" placeholder="NIP" maxlength="10" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama" maxlength="45" required>
                        </div>
                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" placeholder="Jabatan" maxlength="45" required>
                        </div>
                        <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layout/footer.php'; ?>
<?php
include 'config/database.php';

// Tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $stmt = $conn->prepare("INSERT INTO jenis_produk (nama, deskripsi) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $deskripsi);
    $stmt->execute();
    $stmt->close();
    header("Location: jenis_produk.php");
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM jenis_produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: jenis_produk.php");
}

// Ambil data
$data = mysqli_query($conn, "SELECT * FROM jenis_produk");

// Tambahan: Logika untuk Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $stmt = $conn->prepare("UPDATE jenis_produk SET nama=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("ssi", $nama, $deskripsi, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: jenis_produk.php");
}

// Tambahan: Ambil data untuk edit
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM jenis_produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div id="layoutSidenav_content">
<div class="container">
    <link href="css/styles2.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />

    <div class="container-fluid px-4">
        <h2>Data Jenis Produk</h2>

        <!-- Tombol untuk membuka modal tambah -->
        <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Jenis Produk</button>

        <!-- Modal untuk Tambah -->
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Data Jenis Produk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Jenis Produk</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Jenis Produk" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Deskripsi"></textarea>
                            </div>
                            <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tampilan data jenis produk (diubah ke tabel) -->
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($data)) { ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
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
                                    <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Data Jenis Produk</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <div class="mb-3">
                                            <label for="nama<?= $row['id'] ?>" class="form-label">Nama Jenis Produk</label>
                                            <input type="text" class="form-control" id="nama<?= $row['id'] ?>" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="deskripsi<?= $row['id'] ?>" class="form-label">Deskripsi</label>
                                            <textarea class="form-control" id="deskripsi<?= $row['id'] ?>" name="deskripsi"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
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

<?php include 'layout/footer.php'; ?>
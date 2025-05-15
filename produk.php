<?php
include 'config/database.php';

// Pastikan koneksi dari config/database.php sudah menyediakan $conn
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Logika untuk Tambah Data
if (isset($_POST['tambah'])) {
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $jenis_produk_id = $_POST['jenis_produk_id'];

    // Validasi jenis_produk_id
    $stmt = $conn->prepare("SELECT id FROM jenis_produk WHERE id = ?");
    $stmt->bind_param("i", $jenis_produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        die("Error: jenis_produk_id $jenis_produk_id tidak ditemukan.");
    }
    $stmt->close();

    // Persiapkan query INSERT
    $stmt = $conn->prepare("INSERT INTO produk (kode, nama, deskripsi, harga, stok, jenis_produk_id) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sssdii", $kode, $nama, $deskripsi, $harga, $stok, $jenis_produk_id);
    try {
        $stmt->execute();
        header("Location: produk.php?status=tambah_sukses");
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
    $stmt->close();
}

// Logika untuk Hapus Data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    try {
        $stmt->execute();
        header("Location: produk.php?status=hapus_sukses");
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
    $stmt->close();
}

// Logika untuk Update Data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $jenis_produk_id = $_POST['jenis_produk_id'];

    // Validasi jenis_produk_id
    $stmt = $conn->prepare("SELECT id FROM jenis_produk WHERE id = ?");
    $stmt->bind_param("i", $jenis_produk_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        die("Error: jenis_produk_id $jenis_produk_id tidak ditemukan.");
    }
    $stmt->close();

    $stmt = $conn->prepare("UPDATE produk SET kode=?, nama=?, deskripsi=?, harga=?, stok=?, jenis_produk_id=? WHERE id=?");
    $stmt->bind_param("sssdiii", $kode, $nama, $deskripsi, $harga, $stok, $jenis_produk_id, $id); // Perbaikan: sssdi -> sssdiii
    try {
        $stmt->execute();
        header("Location: produk.php?status=update_sukses");
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
    $stmt->close();
}

// Ambil data untuk Edit
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Ambil data produk
$data = $conn->query("SELECT produk.*, jenis_produk.nama AS nama_jenis_produk FROM produk LEFT JOIN jenis_produk ON produk.jenis_produk_id = jenis_produk.id");
if (!$data) {
    die("Query gagal: " . $conn->error);
}

// Ambil data jenis_produk untuk dropdown
$jenis_produk = $conn->query("SELECT * FROM jenis_produk");
if (!$jenis_produk) {
    die("Query gagal: " . $conn->error);
}
?>

<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div id="layoutSidenav_content">
    <div class="container">
        <link href="css/styles2.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />

        <div class="container-fluid px-4">
            <h2>Data Produk</h2>

            <!-- Notifikasi Status -->
            <?php if (isset($_GET['status'])) { ?>
                <div class="alert alert-success">
                    <?php
                    if ($_GET['status'] == 'tambah_sukses') echo "Data produk berhasil ditambahkan!";
                    elseif ($_GET['status'] == 'hapus_sukses') echo "Data produk berhasil dihapus!";
                    elseif ($_GET['status'] == 'update_sukses') echo "Data produk berhasil diperbarui!";
                    ?>
                </div>
            <?php } ?>

            <!-- Tombol untuk membuka modal tambah -->
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Produk</button>

            <!-- Modal untuk Tambah -->
            <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tambahModalLabel">Tambah Data Produk</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="kode" class="form-label">Kode</label>
                                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Kode" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Produk</label>
                                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama Produk" required>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Deskripsi"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="harga" class="form-label">Harga</label>
                                    <input type="number" step="0.01" class="form-control" id="harga" name="harga" placeholder="Harga" required>
                                </div>
                                <div class="mb-3">
                                    <label for="stok" class="form-label">Stok</label>
                                    <input type="number" class="form-control" id="stok" name="stok" placeholder="Stok" required>
                                </div>
                                <div class="mb-3">
                                    <label for="jenis_produk_id" class="form-label">Jenis Produk</label>
                                    <select class="form-control" id="jenis_produk_id" name="jenis_produk_id" required>
                                        <option value="">Pilih Jenis Produk</option>
                                        <?php while ($jp = $jenis_produk->fetch_assoc()) { ?>
                                            <option value="<?= $jp['id'] ?>"><?= htmlspecialchars($jp['nama']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tampilan data produk -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Nama Produk</th>
                            <th>Deskripsi</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Jenis Produk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $data->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['kode']) ?></td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                                <td><?= number_format($row['harga'], 2) ?></td>
                                <td><?= htmlspecialchars($row['stok']) ?></td>
                                <td><?= htmlspecialchars($row['nama_jenis_produk']) ?></td>
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
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Data Produk</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="kode<?= $row['id'] ?>" class="form-label">Kode</label>
                                                    <input type="text" class="form-control" id="kode<?= $row['id'] ?>" name="kode" value="<?= htmlspecialchars($row['kode']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="nama<?= $row['id'] ?>" class="form-label">Nama Produk</label>
                                                    <input type="text" class="form-control" id="nama<?= $row['id'] ?>" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="deskripsi<?= $row['id'] ?>" class="form-label">Deskripsi</label>
                                                    <textarea class="form-control" id="deskripsi<?= $row['id'] ?>" name="deskripsi"><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="harga<?= $row['id'] ?>" class="form-label">Harga</label>
                                                    <input type="number" step="0.01" class="form-control" id="harga<?= $row['id'] ?>" name="harga" value="<?= htmlspecialchars($row['harga']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="stok<?= $row['id'] ?>" class="form-label">Stok</label>
                                                    <input type="number" class="form-control" id="stok<?= $row['id'] ?>" name="stok" value="<?= htmlspecialchars($row['stok']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="jenis_produk_id<?= $row['id'] ?>" class="form-label">Jenis Produk</label>
                                                    <select class="form-control" id="jenis_produk_id<?= $row['id'] ?>" name="jenis_produk_id" required>
                                                        <?php
                                                        $jenis_produk = $conn->query("SELECT * FROM jenis_produk");
                                                        while ($jp = $jenis_produk->fetch_assoc()) { ?>
                                                            <option value="<?= $jp['id'] ?>" <?= $row['jenis_produk_id'] == $jp['id'] ? 'selected' : '' ?>><?= htmlspecialchars($jp['nama']) ?></option>
                                                        <?php } ?>
                                                    </select>
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
    <?php $conn->close(); // Tutup koneksi di akhir 
    ?>
</div>
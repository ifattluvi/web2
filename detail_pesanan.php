<?php
include 'config/database.php';
session_start(); // Start session for flash messages

// Initialize message variables
$success_message = '';
$error_message = '';

// Tambah data
if (isset($_POST['tambah'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Validation
    if (!is_numeric($pesanan_id) || $pesanan_id <= 0) {
        $error_message = "Pesanan ID tidak valid.";
    } elseif (!is_numeric($produk_id) || $produk_id <= 0) {
        $error_message = "Produk ID tidak valid.";
    } elseif (!is_numeric($jumlah) || $jumlah <= 0) {
        $error_message = "Jumlah harus berupa angka positif.";
    } else {
        // Check if pesanan_id exists
        $stmt = $conn->prepare("SELECT id FROM pesanan WHERE id = ?");
        $stmt->bind_param("i", $pesanan_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $error_message = "Pesanan ID tidak ditemukan.";
        } else {
            // Check if produk_id exists
            $stmt = $conn->prepare("SELECT id FROM produk WHERE id = ?");
            $stmt->bind_param("i", $produk_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $error_message = "Produk ID tidak ditemukan.";
            } else {
                // Insert data
                $stmt = $conn->prepare("INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $pesanan_id, $produk_id, $jumlah);
                try {
                    if ($stmt->execute()) {
                        $success_message = "Data detail pesanan berhasil ditambahkan.";
                        header("Location: detail_pesanan.php?status=tambah_sukses");
                        exit();
                    } else {
                        $error_message = "Gagal menambahkan data: " . $conn->error;
                    }
                } catch (mysqli_sql_exception $e) {
                    $error_message = "Gagal menambahkan data: " . $e->getMessage();
                }
                $stmt->close();
            }
        }
    }
}

// Update data
if (isset($_POST['update'])) {
    $pesanan_id = $_POST['pesanan_id'];
    $produk_id = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];

    // Validation
    if (!is_numeric($pesanan_id) || $pesanan_id <= 0) {
        $error_message = "Pesanan ID tidak valid.";
    } elseif (!is_numeric($produk_id) || $produk_id <= 0) {
        $error_message = "Produk ID tidak valid.";
    } elseif (!is_numeric($jumlah) || $jumlah <= 0) {
        $error_message = "Jumlah harus berupa angka positif.";
    } else {
        // Check if pesanan_id exists
        $stmt = $conn->prepare("SELECT id FROM pesanan WHERE id = ?");
        $stmt->bind_param("i", $pesanan_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $error_message = "Pesanan ID tidak ditemukan.";
        } else {
            // Check if produk_id exists
            $stmt = $conn->prepare("SELECT id FROM produk WHERE id = ?");
            $stmt->bind_param("i", $produk_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $error_message = "Produk ID tidak ditemukan.";
            } else {
                // Update data
                $stmt = $conn->prepare("UPDATE detail_pesanan SET jumlah = ? WHERE pesanan_id = ? AND produk_id = ?");
                $stmt->bind_param("iii", $jumlah, $pesanan_id, $produk_id);
                try {
                    if ($stmt->execute()) {
                        $success_message = "Data detail pesanan berhasil diperbarui.";
                        header("Location: detail_pesanan.php?status=update_sukses");
                        exit();
                    } else {
                        $error_message = "Gagal memperbarui data: " . $conn->error;
                    }
                } catch (mysqli_sql_exception $e) {
                    $error_message = "Gagal memperbarui data: " . $e->getMessage();
                }
                $stmt->close();
            }
        }
    }
}

// Hapus data
if (isset($_GET['hapus_pesanan_id']) && isset($_GET['hapus_produk_id'])) {
    $pesanan_id = $_GET['hapus_pesanan_id'];
    $produk_id = $_GET['hapus_produk_id'];
    if (!is_numeric($pesanan_id) || $pesanan_id <= 0 || !is_numeric($produk_id) || $produk_id <= 0) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("DELETE FROM detail_pesanan WHERE pesanan_id = ? AND produk_id = ?");
        $stmt->bind_param("ii", $pesanan_id, $produk_id);
        try {
            if ($stmt->execute()) {
                $success_message = "Data detail pesanan berhasil dihapus.";
                header("Location: detail_pesanan.php?status=hapus_sukses");
                exit();
            } else {
                $error_message = "Gagal menghapus data: " . $conn->error;
            }
        } catch (mysqli_sql_exception $e) {
            $error_message = "Gagal menghapus data: " . $e->getMessage();
        }
        $stmt->close();
    }
}

// Ambil data untuk edit
$edit_row = null;
if (isset($_GET['edit_pesanan_id']) && isset($_GET['edit_produk_id'])) {
    $pesanan_id = $_GET['edit_pesanan_id'];
    $produk_id = $_GET['edit_produk_id'];
    if (!is_numeric($pesanan_id) || $pesanan_id <= 0 || !is_numeric($produk_id) || $produk_id <= 0) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM detail_pesanan WHERE pesanan_id = ? AND produk_id = ?");
        $stmt->bind_param("ii", $pesanan_id, $produk_id);
        $stmt->execute();
        $edit_row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

// Ambil semua data detail pesanan
$data = mysqli_query($conn, "SELECT dp.*, p.id AS pesanan_kode, pr.nama AS produk_nama 
                             FROM detail_pesanan dp 
                             LEFT JOIN pesanan p ON dp.pesanan_id = p.id 
                             LEFT JOIN produk pr ON dp.produk_id = pr.id");

if (!$data) {
    $error_message = "Gagal mengambil data: " . $conn->error;
}
?>

<!-- HTML -->
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div id="layoutSidenav_content">
    <div class="container">
        <link href="css/styles2.css" rel="stylesheet" />

        <div class="container-fluid px-4">
            <h2>Data Detail Pesanan</h2>

            <!-- Display Messages -->
            <?php if ($success_message) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>
            <?php if ($error_message) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error_message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php } ?>

            <!-- Tombol untuk membuka modal tambah -->
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Detail Pesanan</button>

            <!-- Konten Detail Pesanan -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Pesanan</th>
                            <th>Nama Produk</th>
                            <th>Jumlah</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($data)) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['pesanan_kode'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['produk_nama'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['jumlah']) ?></td>
                                <td>
                                    <a href="?edit_pesanan_id=<?= $row['pesanan_id'] ?>&edit_produk_id=<?= $row['produk_id'] ?>" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['pesanan_id'] ?>_<?= $row['produk_id'] ?>">Edit</a>
                                    <a href="?hapus_pesanan_id=<?= $row['pesanan_id'] ?>&hapus_produk_id=<?= $row['produk_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                                </td>
                            </tr>
                            <!-- Modal untuk Edit -->
                            <div class="modal fade" id="editModal<?= $row['pesanan_id'] ?>_<?= $row['produk_id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $row['pesanan_id'] ?>_<?= $row['produk_id'] ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel<?= $row['pesanan_id'] ?>_<?= $row['produk_id'] ?>">Edit Data Detail Pesanan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <div class="mb-3">
                                                    <label for="pesanan_id<?= $row['pesanan_id'] ?>" class="form-label">Pesanan</label>
                                                    <select class="form-control" id="pesanan_id<?= $row['pesanan_id'] ?>" name="pesanan_id" required>
                                                        <?php
                                                        $pesanan_data = $conn->query("SELECT id, kode FROM pesanan");
                                                        while ($p = $pesanan_data->fetch_assoc()) {
                                                            echo "<option value='{$p['id']}' " . ($p['id'] == $row['pesanan_id'] ? 'selected' : '') . ">" . htmlspecialchars($p['kode']) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="produk_id<?= $row['produk_id'] ?>" class="form-label">Produk</label>
                                                    <select class="form-control" id="produk_id<?= $row['produk_id'] ?>" name="produk_id" required>
                                                        <?php
                                                        $produk_data = $conn->query("SELECT id, nama FROM produk");
                                                        while ($pr = $produk_data->fetch_assoc()) {
                                                            echo "<option value='{$pr['id']}' " . ($pr['id'] == $row['produk_id'] ? 'selected' : '') . ">" . htmlspecialchars($pr['nama']) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="jumlah<?= $row['pesanan_id'] ?>_<?= $row['produk_id'] ?>" class="form-label">Jumlah</label>
                                                    <input type="number" class="form-control" id="jumlah<?= $row['pesanan_id'] ?>_<?= $row['produk_id'] ?>" name="jumlah" value="<?= htmlspecialchars($row['jumlah']) ?>" min="1" required>
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
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Data Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="pesanan_id" class="form-label">Pesanan</label>
                            <select class="form-control" id="pesanan_id" name="pesanan_id" required>
                                <?php
                                $pesanan_data = $conn->query("SELECT id, kode FROM pesanan");
                                while ($p = $pesanan_data->fetch_assoc()) {
                                    echo "<option value='{$p['id']}'>" . htmlspecialchars($p['kode']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="produk_id" class="form-label">Produk</label>
                            <select class="form-control" id="produk_id" name="produk_id" required>
                                <?php
                                $produk_data = $conn->query("SELECT id, nama FROM produk");
                                while ($pr = $produk_data->fetch_assoc()) {
                                    echo "<option value='{$pr['id']}'>" . htmlspecialchars($pr['nama']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Jumlah" min="1" required>
                        </div>
                        <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'layout/footer.php'; ?>
</div>

<?php $conn->close(); ?>
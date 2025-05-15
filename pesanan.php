<?php
include 'config/database.php';

// Initialize message variables
$success_message = '';
$error_message = '';

// Tambah data
if (isset($_POST['tambah'])) {
    $tanggal = filter_input(INPUT_POST, 'tanggal', FILTER_SANITIZE_STRING);
    $diskon = filter_input(INPUT_POST, 'diskon', FILTER_VALIDATE_INT);
    $status_bayar = filter_input(INPUT_POST, 'status_bayar', FILTER_VALIDATE_INT);
    $anggota_id = filter_input(INPUT_POST, 'anggota_id', FILTER_VALIDATE_INT);

    // Validasi input
    if (!$tanggal || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error_message = "Tanggal tidak valid. Gunakan format YYYY-MM-DD.";
    } elseif ($diskon === false || $diskon < 0 || $diskon > 100) {
        $error_message = "Diskon harus berupa angka antara 0 dan 100.";
    } elseif ($status_bayar === false || !in_array($status_bayar, [0, 1])) {
        $error_message = "Status bayar tidak valid.";
    } elseif ($anggota_id === false) {
        $error_message = "Anggota ID tidak valid.";
    } else {
        // Validasi anggota_id
        $stmt = $conn->prepare("SELECT id FROM anggota WHERE id = ?");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $error_message = "Anggota ID $anggota_id tidak ditemukan.";
        } else {
            // Insert data
            $stmt = $conn->prepare("INSERT INTO pesanan (tanggal, diskon, status_bayar, anggota_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siii", $tanggal, $diskon, $status_bayar, $anggota_id);
            if ($stmt->execute()) {
                $success_message = "Pesanan berhasil ditambahkan.";
                header("Location: pesanan.php?status=tambah_sukses");
                exit();
            } else {
                $error_message = "Gagal menambahkan pesanan: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

// Update data
if (isset($_POST['update'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $tanggal = filter_input(INPUT_POST, 'tanggal', FILTER_SANITIZE_STRING);
    $diskon = filter_input(INPUT_POST, 'diskon', FILTER_VALIDATE_INT);
    $status_bayar = filter_input(INPUT_POST, 'status_bayar', FILTER_VALIDATE_INT);
    $anggota_id = filter_input(INPUT_POST, 'anggota_id', FILTER_VALIDATE_INT);

    // Validasi input
    if ($id === false) {
        $error_message = "ID tidak valid.";
    } elseif (!$tanggal || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error_message = "Tanggal tidak valid. Gunakan format YYYY-MM-DD.";
    } elseif ($diskon === false || $diskon < 0 || $diskon > 100) {
        $error_message = "Diskon harus berupa angka antara 0 dan 100.";
    } elseif ($status_bayar === false || !in_array($status_bayar, [0, 1])) {
        $error_message = "Status bayar tidak valid.";
    } elseif ($anggota_id === false) {
        $error_message = "Anggota ID tidak valid.";
    } else {
        // Validasi anggota_id
        $stmt = $conn->prepare("SELECT id FROM anggota WHERE id = ?");
        $stmt->bind_param("i", $anggota_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $error_message = "Anggota ID $anggota_id tidak ditemukan.";
        } else {
            // Update data
            $stmt = $conn->prepare("UPDATE pesanan SET tanggal=?, diskon=?, status_bayar=?, anggota_id=? WHERE id=?");
            $stmt->bind_param("siiii", $tanggal, $diskon, $status_bayar, $anggota_id, $id);
            if ($stmt->execute()) {
                $success_message = "Pesanan berhasil diperbarui.";
                header("Location: pesanan.php?status=update_sukses");
                exit();
            } else {
                $error_message = "Gagal memperbarui pesanan: " . $conn->error;
            }
        }
        $stmt->close();
    }
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = filter_input(INPUT_GET, 'hapus', FILTER_VALIDATE_INT);
    if ($id === false) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("DELETE FROM pesanan WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $success_message = "Pesanan berhasil dihapus.";
            header("Location: pesanan.php?status=hapus_sukses");
            exit();
        } else {
            $error_message = "Gagal menghapus pesanan: " . $conn->error;
        }
        $stmt->close();
    }
}

// Ambil data untuk edit
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($id === false) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM pesanan WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $edit_row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

// Ambil semua data pesanan dengan join ke anggota dan pegawai
$data = $conn->query("SELECT p.*, a.pegawai_id, pg.nama AS pegawai_nama 
                      FROM pesanan p 
                      LEFT JOIN anggota a ON p.anggota_id = a.id 
                      LEFT JOIN pegawai pg ON a.pegawai_id = pg.id");
if (!$data) {
    $error_message = "Query gagal: " . $conn->error;
}
?>

<!-- HTML -->
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div id="layoutSidenav_content">
    <div class="container">
        <link href="css/styles2.css" rel="stylesheet" />

        <div class="container-fluid px-4">
            <h2>Data Pesanan</h2>

            <!-- Notifikasi Status -->
            <?php if (isset($_GET['status'])) { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    if ($_GET['status'] == 'tambah_sukses') echo "Pesanan berhasil ditambahkan!";
                    elseif ($_GET['status'] == 'update_sukses') echo "Pesanan berhasil diperbarui!";
                    elseif ($_GET['status'] == 'hapus_sukses') echo "Pesanan berhasil dihapus!";
                    ?>
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
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Pesanan</button>

            <!-- Tabel Pesanan -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Diskon (%)</th>
                            <th>Status Bayar</th>
                            <th>Nama Anggota</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $data->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars($row['diskon']) ?></td>
                                <td><?= $row['status_bayar'] ? 'Lunas' : 'Belum Lunas' ?></td>
                                <td><?= htmlspecialchars($row['pegawai_nama'] ?? '-') ?></td>
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
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Data Pesanan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="tanggal<?= $row['id'] ?>" class="form-label">Tanggal</label>
                                                    <input type="date" class="form-control" id="tanggal<?= $row['id'] ?>" name="tanggal" value="<?= htmlspecialchars($row['tanggal']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="diskon<?= $row['id'] ?>" class="form-label">Diskon (%)</label>
                                                    <input type="number" class="form-control" id="diskon<?= $row['id'] ?>" name="diskon" value="<?= htmlspecialchars($row['diskon']) ?>" min="0" max="100" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="status_bayar<?= $row['id'] ?>" class="form-label">Status Bayar</label>
                                                    <select class="form-control" id="status_bayar<?= $row['id'] ?>" name="status_bayar" required>
                                                        <option value="1" <?= $row['status_bayar'] == 1 ? 'selected' : '' ?>>Lunas</option>
                                                        <option value="0" <?= $row['status_bayar'] == 0 ? 'selected' : '' ?>>Belum Lunas</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="anggota_id<?= $row['id'] ?>" class="form-label">Anggota</label>
                                                    <select class="form-control" id="anggota_id<?= $row['id'] ?>" name="anggota_id" required>
                                                        <?php
                                                        $anggota_data = $conn->query("SELECT a.id, p.nama FROM anggota a JOIN pegawai p ON a.pegawai_id = p.id");
                                                        while ($a = $anggota_data->fetch_assoc()) {
                                                            echo "<option value='{$a['id']}' " . ($a['id'] == $row['anggota_id'] ? 'selected' : '') . ">" . htmlspecialchars($a['nama']) . "</option>";
                                                        }
                                                        ?>
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

    <!-- Modal untuk Tambah -->
    <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Data Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="diskon" class="form-label">Diskon (%)</label>
                            <input type="number" class="form-control" id="diskon" name="diskon" min="0" max="100" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="status_bayar" class="form-label">Status Bayar</label>
                            <select class="form-control" id="status_bayar" name="status_bayar" required>
                                <option value="1">Lunas</option>
                                <option value="0" selected>Belum Lunas</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="anggota_id" class="form-label">Anggota</label>
                            <select class="form-control" id="anggota_id" name="anggota_id" required>
                                <?php
                                $anggota_data = $conn->query("SELECT a.id, p.nama FROM anggota a JOIN pegawai p ON a.pegawai_id = p.id");
                                while ($a = $anggota_data->fetch_assoc()) {
                                    echo "<option value='{$a['id']}'>" . htmlspecialchars($a['nama']) . "</option>";
                                }
                                ?>
                            </select>
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
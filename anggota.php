<?php
include 'config/database.php';

// Tambah data
if (isset($_POST['tambah'])) {
    $status_aktif = filter_input(INPUT_POST, 'status_aktif', FILTER_VALIDATE_INT);
    $pegawai_id = filter_input(INPUT_POST, 'pegawai_id', FILTER_VALIDATE_INT);
    $kartu_diskon_id = !empty($_POST['kartu_diskon_id']) ? filter_input(INPUT_POST, 'kartu_diskon_id', FILTER_VALIDATE_INT) : null;

    // Validasi input
    if ($status_aktif === false || $pegawai_id === false || ($kartu_diskon_id !== null && $kartu_diskon_id === false)) {
        die("Error: Input tidak valid.");
    }

    // Validasi pegawai_id
    $stmt = $conn->prepare("SELECT id FROM pegawai WHERE id = ?");
    $stmt->bind_param("i", $pegawai_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        die("Error: pegawai_id $pegawai_id tidak ditemukan.");
    }
    $stmt->close();

    // Validasi kartu_diskon_id (jika tidak null)
    if ($kartu_diskon_id !== null) {
        $stmt = $conn->prepare("SELECT id FROM kartu_diskon WHERE id = ?");
        $stmt->bind_param("i", $kartu_diskon_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            die("Error: kartu_diskon_id $kartu_diskon_id tidak ditemukan.");
        }
        $stmt->close();
    }

    // Persiapkan query INSERT
    $stmt = $conn->prepare("INSERT INTO anggota (status_aktif, pegawai_id, kartu_diskon_id) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameter (status_aktif sebagai integer)
    $stmt->bind_param("iii", $status_aktif, $pegawai_id, $kartu_diskon_id);
    try {
        $stmt->execute();
        header("Location: anggota.php?status=tambah_sukses");
        exit();
    } catch (mysqli_sql_exception $e) {
        die("Error: " . $e->getMessage());
    }
    $stmt->close();
}

// Update data
if (isset($_POST['update'])) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $status_aktif = filter_input(INPUT_POST, 'status_aktif', FILTER_VALIDATE_INT);
    $pegawai_id = filter_input(INPUT_POST, 'pegawai_id', FILTER_VALIDATE_INT);
    $kartu_diskon_id = !empty($_POST['kartu_diskon_id']) ? filter_input(INPUT_POST, 'kartu_diskon_id', FILTER_VALIDATE_INT) : null;

    // Validasi input
    if ($id === false || $status_aktif === false || $pegawai_id === false || ($kartu_diskon_id !== null && $kartu_diskon_id === false)) {
        die("Error: Input tidak valid.");
    }

    // Validasi pegawai_id
    $stmt = $conn->prepare("SELECT id FROM pegawai WHERE id = ?");
    $stmt->bind_param("i", $pegawai_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        die("Error: pegawai_id $pegawai_id tidak ditemukan.");
    }
    $stmt->close();

    // Validasi kartu_diskon_id (jika tidak null)
    if ($kartu_diskon_id !== null) {
        $stmt = $conn->prepare("SELECT id FROM kartu_diskon WHERE id = ?");
        $stmt->bind_param("i", $kartu_diskon_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            die("Error: kartu_diskon_id $kartu_diskon_id tidak ditemukan.");
        }
        $stmt->close();
    }

    // Persiapkan query UPDATE
    $stmt = $conn->prepare("UPDATE anggota SET status_aktif=?, pegawai_id=?, kartu_diskon_id=? WHERE id=?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iiii", $status_aktif, $pegawai_id, $kartu_diskon_id, $id);
    try {
        $stmt->execute();
        header("Location: anggota.php?status=update_sukses");
        exit();
    } catch (mysqli_sql_exception $e) {
        die("Error: " . $e->getMessage());
    }
    $stmt->close();
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = filter_input(INPUT_GET, 'hapus', FILTER_VALIDATE_INT);
    if ($id === false) {
        die("Error: ID tidak valid.");
    }

    $stmt = $conn->prepare("DELETE FROM anggota WHERE id=?");
    $stmt->bind_param("i", $id);
    try {
        $stmt->execute();
        header("Location: anggota.php?status=hapus_sukses");
        exit();
    } catch (mysqli_sql_exception $e) {
        die("Error: " . $e->getMessage());
    }
    $stmt->close();
}

// Ambil data untuk edit
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);
    if ($id === false) {
        die("Error: ID tidak valid.");
    }

    $stmt = $conn->prepare("SELECT * FROM anggota WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Ambil semua data anggota dengan join ke pegawai dan kartu_diskon
$data = $conn->query("SELECT a.*, p.nama AS pegawai_nama, k.nama AS kartu_diskon_nama 
                      FROM anggota a 
                      LEFT JOIN pegawai p ON a.pegawai_id = p.id 
                      LEFT JOIN kartu_diskon k ON a.kartu_diskon_id = k.id");
if (!$data) {
    die("Query gagal: " . $conn->error);
}
?>

<!-- HTML -->
<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<div id="layoutSidenav_content">
    <div class="container">
        <link href="css/styles2.css" rel="stylesheet" />

        <div class="container-fluid px-4">
            <h2>Data Anggota</h2>

            <!-- Notifikasi Status -->
            <?php if (isset($_GET['status'])) { ?>
                <div class="alert alert-success">
                    <?php
                    if ($_GET['status'] == 'tambah_sukses') echo "Anggota berhasil ditambahkan!";
                    elseif ($_GET['status'] == 'update_sukses') echo "Anggota berhasil diperbarui!";
                    elseif ($_GET['status'] == 'hapus_sukses') echo "Anggota berhasil dihapus!";
                    ?>
                </div>
            <?php } ?>

            <!-- Tombol untuk membuka modal tambah -->
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Anggota</button>

            <!-- Tabel Anggota -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Status Aktif</th>
                            <th>Kartu Diskon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        while ($row = $data->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['pegawai_nama'] ?? '-') ?></td>
                                <td><?= $row['status_aktif'] ? 'Aktif' : 'Non-Aktif' ?></td>
                                <td><?= htmlspecialchars($row['kartu_diskon_nama'] ?? '-') ?></td>
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
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Data Anggota</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="status_aktif<?= $row['id'] ?>" class="form-label">Status Aktif</label>
                                                    <select class="form-control" id="status_aktif<?= $row['id'] ?>" name="status_aktif" required>
                                                        <option value="1" <?= $row['status_aktif'] == 1 ? 'selected' : '' ?>>Aktif</option>
                                                        <option value="0" <?= $row['status_aktif'] == 0 ? 'selected' : '' ?>>Non-Aktif</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="pegawai_id<?= $row['id'] ?>" class="form-label">Pegawai</label>
                                                    <select class="form-control" id="pegawai_id<?= $row['id'] ?>" name="pegawai_id" required>
                                                        <?php
                                                        $pegawai_data = $conn->query("SELECT id, nama FROM pegawai");
                                                        while ($p = $pegawai_data->fetch_assoc()) {
                                                            echo "<option value='{$p['id']}' " . ($p['id'] == $row['pegawai_id'] ? 'selected' : '') . ">" . htmlspecialchars($p['nama']) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kartu_diskon_id<?= $row['id'] ?>" class="form-label">Kartu Diskon</label>
                                                    <select class="form-control" id="kartu_diskon_id<?= $row['id'] ?>" name="kartu_diskon_id">
                                                        <option value="">Tidak Ada</option>
                                                        <?php
                                                        $kartu_diskon_data = $conn->query("SELECT id, nama FROM kartu_diskon");
                                                        while ($k = $kartu_diskon_data->fetch_assoc()) {
                                                            echo "<option value='{$k['id']}' " . ($k['id'] == $row['kartu_diskon_id'] ? 'selected' : '') . ">" . htmlspecialchars($k['nama']) . "</option>";
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
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Data Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="status_aktif" class="form-label">Status Aktif</label>
                            <select class="form-control" id="status_aktif" name="status_aktif" required>
                                <option value="1">Aktif</option>
                                <option value="0">Non-Aktif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="pegawai_id" class="form-label">Pegawai</label>
                            <select class="form-control" id="pegawai_id" name="pegawai_id" required>
                                <?php
                                $pegawai_data = $conn->query("SELECT id, nama FROM pegawai");
                                while ($p = $pegawai_data->fetch_assoc()) {
                                    echo "<option value='{$p['id']}'>" . htmlspecialchars($p['nama']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="kartu_diskon_id" class="form-label">Kartu Diskon</label>
                            <select class="form_control" id="kartu_diskon_id" name="kartu_diskon_id">
                                <option value="">Ada</option>
                                <?php
                                $kartu_diskon_data = $conn->query("SELECT id, nama FROM kartu_diskon");
                                while ($k = $kartu_diskon_data->fetch_assoc()) {
                                    echo "<option value='{$k['id']}'>" . htmlspecialchars($k['nama']) . "</option>";
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
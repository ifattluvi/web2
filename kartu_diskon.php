<?php
include 'config/database.php';
session_start(); // Start session for flash messages

// Initialize message variables
$success_message = '';
$error_message = '';

// Tambah data
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $diskon = $_POST['diskon'];

    // Validation
    if (empty($nama)) {
        $error_message = "Nama tidak boleh kosong.";
    } elseif (empty($deskripsi)) {
        $error_message = "Deskripsi tidak boleh kosong.";
    } elseif (!is_numeric($diskon) || $diskon <= 0 || $diskon > 100) {
        $error_message = "Diskon harus berupa angka antara 1 dan 100.";
    } else {
        $stmt = $conn->prepare("INSERT INTO kartu_diskon (nama, deskripsi, diskon) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $nama, $deskripsi, $diskon);
        try {
            if ($stmt->execute()) {
                $success_message = "Data kartu diskon berhasil ditambahkan.";
                header("Location: kartu_diskon.php?status=tambah_sukses");
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

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (!is_numeric($id) || $id <= 0) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("DELETE FROM kartu_diskon WHERE id = ?");
        $stmt->bind_param("i", $id);
        try {
            if ($stmt->execute()) {
                $success_message = "Data kartu diskon berhasil dihapus.";
                header("Location: kartu_diskon.php?status=hapus_sukses");
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

// Ambil data
$data = mysqli_query($conn, "SELECT * FROM kartu_diskon");
if (!$data) {
    $error_message = "Gagal mengambil data: " . $conn->error;
}

// Update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $diskon = $_POST['diskon'];

    // Validation
    if (empty($nama)) {
        $error_message = "Nama tidak boleh kosong.";
    } elseif (empty($deskripsi)) {
        $error_message = "Deskripsi tidak boleh kosong.";
    } elseif (!is_numeric($diskon) || $diskon <= 0 || $diskon > 100) {
        $error_message = "Diskon harus berupa angka antara 1 dan 100.";
    } elseif (!is_numeric($id) || $id <= 0) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("UPDATE kartu_diskon SET nama = ?, deskripsi = ?, diskon = ? WHERE id = ?");
        $stmt->bind_param("ssii", $nama, $deskripsi, $diskon, $id);
        try {
            if ($stmt->execute()) {
                $success_message = "Data kartu diskon berhasil diperbarui.";
                header("Location: kartu_diskon.php?status=update_sukses");
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

// Ambil data untuk edit
$edit_row = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    if (!is_numeric($id) || $id <= 0) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM kartu_diskon WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $edit_row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kartu Diskon</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/styles2.css" rel="stylesheet">
</head>

<body>
    <?php include 'layout/header.php'; ?>
    <?php include 'layout/sidebar.php'; ?>

    <div id="layoutSidenav_content">
        <div class="container-fluid px-4">
            <h2>Data Kartu Diskon</h2>

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
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal" onclick="console.log('Tambah button clicked')">Tambah Kartu Diskon</button>

            <!-- Konten Kartu Diskon -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Diskon (%)</th>
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
                                <td><?= htmlspecialchars($row['diskon']) ?></td>
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
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Data Kartu Diskon</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="nama<?= $row['id'] ?>" class="form-label">Nama</label>
                                                    <input type="text" class="form-control" id="nama<?= $row['id'] ?>" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" maxlength="45" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="deskripsi<?= $row['id'] ?>" class="form-label">Deskripsi</label>
                                                    <textarea class="form-control" id="deskripsi<?= $row['id'] ?>" name="deskripsi" rows="4" required><?= htmlspecialchars($row['deskripsi']) ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="diskon<?= $row['id'] ?>" class="form-label">Diskon (%)</label>
                                                    <input type="number" class="form-control" id="diskon<?= $row['id'] ?>" name="diskon" value="<?= htmlspecialchars($row['diskon']) ?>" min="1" max="100" required>
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

        <!-- Modal untuk Tambah -->
        <div class="modal fade" id="tambahModal" tabindex="-1" aria-labelledby="tambahModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="tambahModalLabel">Tambah Data Kartu Diskon</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukkan nama kartu" maxlength="45" required>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi kartu" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="diskon" class="form-label">Diskon (%)</label>
                                <input type="number" class="form-control" id="diskon" name="diskon" placeholder="Masukkan persentase diskon" min="1" max="100" required>
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

    <!-- Bootstrap JS and Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Debugging Script -->
    <script>
        document.querySelector('[data-bs-target="#tambahModal"]').addEventListener('click', function() {
            console.log('Tambah Kartu Diskon button clicked');
        });
    </script>
</body>

</html>

<?php $conn->close(); ?>
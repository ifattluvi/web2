<?php
include 'config/database.php';
session_start(); // Start session for flash messages

// Handle AJAX request for pegawai data
if (isset($_GET['get_pegawai_data']) && isset($_GET['nip'])) {
    $nip = $_GET['nip'];
    $stmt = $conn->prepare("SELECT nip, nama, jenis_kelamin, jabatan FROM pegawai WHERE nip = ?");
    $stmt->bind_param("s", $nip);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    header('Content-Type: application/json');
    echo json_encode($data ? $data : []);
    $stmt->close();
    $conn->close();
    exit();
}

// Initialize message variables
$success_message = '';
$error_message = '';

// Tambah data
if (isset($_POST['tambah'])) {
    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jabatan = trim($_POST['jabatan']);
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $tanggal = $_POST['tanggal'];
    $pesanan_id = $_POST['pesanan_id'];

    // Validation
    if (!preg_match('/^[0-9]{1,10}$/', $nip)) {
        $error_message = "NIP harus berupa angka maksimal 10 digit.";
    } elseif (strlen($nama) > 45 || empty($nama)) {
        $error_message = "Nama harus diisi dan maksimal 45 karakter.";
    } elseif (!in_array($jenis_kelamin, ['L', 'P'])) {
        $error_message = "Jenis kelamin tidak valid.";
    } elseif (strlen($jabatan) > 45 || empty($jabatan)) {
        $error_message = "Jabatan harus diisi dan maksimal 45 karakter.";
    } elseif (!is_numeric($jumlah_bayar) || $jumlah_bayar <= 0) {
        $error_message = "Jumlah bayar harus berupa angka positif.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error_message = "Tanggal tidak valid. Gunakan format YYYY-MM-DD.";
    } elseif (!is_numeric($pesanan_id) || $pesanan_id <= 0) {
        $error_message = "Pesanan ID tidak valid.";
    } else {
        // Check if NIP exists in pegawai
        $stmt = $conn->prepare("SELECT id FROM pegawai WHERE nip = ?");
        $stmt->bind_param("s", $nip);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $error_message = "NIP tidak ditemukan di tabel pegawai.";
        } else {
            // Check if pesanan_id exists
            $stmt = $conn->prepare("SELECT id FROM pesanan WHERE id = ?");
            $stmt->bind_param("i", $pesanan_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $error_message = "Pesanan ID tidak ditemukan.";
            } else {
                // Insert data (removed nip from INSERT as it doesn't exist in pembayaran table)
                $stmt = $conn->prepare("INSERT INTO pembayaran (nama, jenis_kelamin, jabatan, jumlah_bayar, tanggal, pesanan_id) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("sssds", $nama, $jenis_kelamin, $jabatan, $jumlah_bayar, $tanggal, $pesanan_id);
                try {
                    if ($stmt->execute()) {
                        $success_message = "Data pembayaran berhasil ditambahkan.";
                        header("Location: pembayaran.php?status=tambah_sukses");
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
    $id = $_POST['id'];
    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama']);
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $jabatan = trim($_POST['jabatan']);
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $tanggal = $_POST['tanggal'];
    $pesanan_id = $_POST['pesanan_id'];

    // Validation
    if (!is_numeric($id) || $id <= 0) {
        $error_message = "ID tidak valid.";
    } elseif (!preg_match('/^[0-9]{1,10}$/', $nip)) {
        $error_message = "NIP harus berupa angka maksimal 10 digit.";
    } elseif (strlen($nama) > 45 || empty($nama)) {
        $error_message = "Nama harus diisi dan maksimal 45 karakter.";
    } elseif (!in_array($jenis_kelamin, ['L', 'P'])) {
        $error_message = "Jenis kelamin tidak valid.";
    } elseif (strlen($jabatan) > 45 || empty($jabatan)) {
        $error_message = "Jabatan harus diisi dan maksimal 45 karakter.";
    } elseif (!is_numeric($jumlah_bayar) || $jumlah_bayar <= 0) {
        $error_message = "Jumlah bayar harus berupa angka positif.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        $error_message = "Tanggal tidak valid. Gunakan format YYYY-MM-DD.";
    } elseif (!is_numeric($pesanan_id) || $pesanan_id <= 0) {
        $error_message = "Pesanan ID tidak valid.";
    } else {
        // Check if NIP exists in pegawai
        $stmt = $conn->prepare("SELECT id FROM pegawai WHERE nip = ?");
        $stmt->bind_param("s", $nip);
        $stmt->execute();
        if ($stmt->get_result()->num_rows === 0) {
            $error_message = "NIP tidak ditemukan di tabel pegawai.";
        } else {
            // Check if pesanan_id exists
            $stmt = $conn->prepare("SELECT id FROM pesanan WHERE id = ?");
            $stmt->bind_param("i", $pesanan_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $error_message = "Pesanan ID tidak ditemukan.";
            } else {
                // Update data (removed nip from UPDATE)
                $stmt = $conn->prepare("UPDATE pembayaran SET nama = ?, jenis_kelamin = ?, jabatan = ?, jumlah_bayar = ?, tanggal = ?, pesanan_id = ? WHERE id = ?");
                $stmt->bind_param("sssdsi", $nama, $jenis_kelamin, $jabatan, $jumlah_bayar, $tanggal, $pesanan_id, $id);
                try {
                    if ($stmt->execute()) {
                        $success_message = "Data pembayaran berhasil diperbarui.";
                        header("Location: pembayaran.php?status=update_sukses");
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
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    if (!is_numeric($id) || $id <= 0) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("DELETE FROM pembayaran WHERE id = ?");
        $stmt->bind_param("i", $id);
        try {
            if ($stmt->execute()) {
                $success_message = "Data pembayaran berhasil dihapus.";
                header("Location: pembayaran.php?status=hapus_sukses");
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
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    if (!is_numeric($id) || $id <= 0) {
        $error_message = "ID tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM pembayaran WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $edit_row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}

// Ambil semua data pembayaran
$data = mysqli_query($conn, "SELECT p.*, ps.tanggal AS pesanan_tanggal 
                             FROM pembayaran p 
                             LEFT JOIN pesanan ps ON p.pesanan_id = ps.id");
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
            <h2>Data Pembayaran</h2>

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
            <button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#tambahModal">Tambah Pembayaran</button>

            <!-- Konten Pembayaran -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Jabatan</th>
                            <th>Jumlah Bayar</th>
                            <th>Tanggal</th>
                            <th>Pesanan (Tanggal)</th>
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
                                <td><?= $row['jenis_kelamin'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                <td><?= number_format($row['jumlah_bayar'], 2) ?></td>
                                <td><?= htmlspecialchars($row['tanggal']) ?></td>
                                <td><?= htmlspecialchars($row['pesanan_tanggal'] ?? '-') ?></td>
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
                                            <h5 class="modal-title" id="editModalLabel<?= $row['id'] ?>">Edit Data Pembayaran</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <div class="mb-3">
                                                    <label for="nip<?= $row['id'] ?>" class="form-label">NIP</label>
                                                    <select class="form-control" id="nip<?= $row['id'] ?>" name="nip" required>
                                                        <option value="">Pilih NIP</option>
                                                        <?php
                                                        $pegawai_data = $conn->query("SELECT nip, nama FROM pegawai ORDER BY nip");
                                                        while ($p = $pegawai_data->fetch_assoc()) {
                                                            $selected = ($p['nama'] == $row['nama']) ? 'selected' : '';
                                                            echo "<option value='{$p['nip']}' $selected>" . htmlspecialchars($p['nip']) . " - " . htmlspecialchars($p['nama']) . "</option>";
                                                        }
                                                        ?>
                                                    </select>
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
                                                <div class="mb-3">
                                                    <label for="jumlah_bayar<?= $row['id'] ?>" class="form-label">Jumlah Bayar</label>
                                                    <input type="number" step="0.01" class="form-control" id="jumlah_bayar<?= $row['id'] ?>" name="jumlah_bayar" value="<?= htmlspecialchars($row['jumlah_bayar']) ?>" min="0" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="tanggal<?= $row['id'] ?>" class="form-label">Tanggal</label>
                                                    <input type="date" class="form-control" id="tanggal<?= $row['id'] ?>" name="tanggal" value="<?= htmlspecialchars($row['tanggal']) ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="pesanan_id<?= $row['id'] ?>" class="form-label">Pesanan</label>
                                                    <select class="form-control" id="pesanan_id<?= $row['id'] ?>" name="pesanan_id" required>
                                                        <?php
                                                        $pesanan_data = $conn->query("SELECT id, tanggal FROM pesanan");
                                                        while ($p = $pesanan_data->fetch_assoc()) {
                                                            echo "<option value='{$p['id']}' " . ($p['id'] == $row['pesanan_id'] ? 'selected' : '') . ">" . htmlspecialchars($p['tanggal']) . "</option>";
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
                    <h5 class="modal-title" id="tambahModalLabel">Tambah Data Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <select class="form-control" id="nip" name="nip" required>
                                <option value="">Pilih NIP</option>
                                <?php
                                $pegawai_data = $conn->query("SELECT nip, nama FROM pegawai ORDER BY nip");
                                while ($p = $pegawai_data->fetch_assoc()) {
                                    echo "<option value='{$p['nip']}'>" . htmlspecialchars($p['nip']) . " - " . htmlspecialchars($p['nama']) . "</option>";
                                }
                                ?>
                            </select>
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
                        <div class="mb-3">
                            <label for="jumlah_bayar" class="form-label">Jumlah Bayar</label>
                            <input type="number" step="0.01" class="form-control" id="jumlah_bayar" name="jumlah_bayar" placeholder="Jumlah Bayar" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal" class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                        <div class="mb-3">
                            <label for="pesanan_id" class="form-label">Pesanan</label>
                            <select class="form-control" id="pesanan_id" name="pesanan_id" required>
                                <?php
                                $pesanan_data = $conn->query("SELECT id, tanggal FROM pesanan");
                                while ($p = $pesanan_data->fetch_assoc()) {
                                    echo "<option value='{$p['id']}'>" . htmlspecialchars($p['tanggal']) . "</option>";
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

<!-- JavaScript untuk auto-populate fields -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Untuk modal tambah
    const nipSelect = document.getElementById('nip');
    if (nipSelect) {
        nipSelect.addEventListener('change', function() {
            if (this.value) {
                fetch('?get_pegawai_data=1&nip=' + this.value)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('nama').value = data.nama || '';
                            document.getElementById('jenis_kelamin').value = data.jenis_kelamin || '';
                            document.getElementById('jabatan').value = data.jabatan || '';
                        }
                    });
            }
        });
    }

    // Untuk modal edit
    document.querySelectorAll('select[id^="nip"]').forEach(select => {
        select.addEventListener('change', function() {
            if (this.value) {
                const id = this.id.replace('nip', '');
                fetch('?get_pegawai_data=1&nip=' + this.value)
                    .then(response => response.json())
                    .then(data => {
                        if (data) {
                            document.getElementById('nama' + id).value = data.nama || '';
                            document.getElementById('jenis_kelamin' + id).value = data.jenis_kelamin || '';
                            document.getElementById('jabatan' + id).value = data.jabatan || '';
                        }
                    });
            }
        });
    });
});
</script>

<?php $conn->close(); ?>
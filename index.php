<?php
// Aktifkan error reporting untuk debugging (hapus setelah selesai)
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/database.php';

// Periksa apakah koneksi database berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Ambil jumlah data dari masing-masing tabel dengan pengecekan error
$jml_anggota = 0;
$result = mysqli_query($conn, "SELECT * FROM anggota");
if ($result) {
    $jml_anggota = mysqli_num_rows($result);
    mysqli_free_result($result);
} else {
    echo "Error pada query anggota: " . mysqli_error($conn);
}

$jml_produk = 0;
$result = mysqli_query($conn, "SELECT * FROM produk");
if ($result) {
    $jml_produk = mysqli_num_rows($result);
    mysqli_free_result($result);
} else {
    echo "Error pada query produk: " . mysqli_error($conn);
}

$jml_pesanan = 0;
$result = mysqli_query($conn, "SELECT * FROM pesanan");
if ($result) {
    $jml_pesanan = mysqli_num_rows($result);
    mysqli_free_result($result);
} else {
    echo "Error pada query pesanan: " . mysqli_error($conn);
}

$jml_pembayaran = 0;
$result = mysqli_query($conn, "SELECT * FROM pembayaran");
if ($result) {
    $jml_pembayaran = mysqli_num_rows($result);
    mysqli_free_result($result);
} else {
    echo "Error pada query pembayaran: " . mysqli_error($conn);
}

$jml_kartu_diskon = 0;
$result = mysqli_query($conn, "SELECT * FROM kartu_diskon");
if ($result) {
    $jml_kartu_diskon = mysqli_num_rows($result);
    mysqli_free_result($result);
} else {
    echo "Error pada query kartu_diskon: " . mysqli_error($conn);
}

// Query jumlah anggota berdasarkan status_aktif
$statusData = [];
$jumlahAnggota = [];
$query = "SELECT status_aktif, COUNT(*) as total FROM anggota GROUP BY status_aktif ORDER BY status_aktif";
$result = mysqli_query($conn, $query);

// Periksa apakah query berhasil
if ($result === false) {
    die("Error pada query status_aktif: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($result)) {
    $statusData[] = $row['status_aktif'] ? 'Aktif' : 'Tidak Aktif';
    $jumlahAnggota[] = $row['total'];
}
mysqli_free_result($result);
?>

<?php include 'layout/header.php'; ?>
<?php include 'layout/sidebar.php'; ?>

<link href="css/table.css" rel="stylesheet" />
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Dashboard Koperasi</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body"><?= $jml_anggota ?> Anggota</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="#">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body"><?= $jml_produk ?> Produk</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="#">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body"><?= $jml_pesanan ?> Pesanan</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="#">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4">
                        <div class="card-body"><?= $jml_pembayaran ?> Pembayaran</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="#">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-area me-1"></i>
                            Anggota berdasarkan Status
                        </div>
                        <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Bar: Anggota berdasarkan Status
                        </div>
                        <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Data Pesanan
                </div>
                <div class="card-body">
                    <table id="datatablesSimple">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Anggota</th> <!-- Kembali ke header "Anggota" karena kita akan ambil nama dari tabel pegawai -->
                                <th>Produk</th>
                                <th>Tanggal</th>
                                <th>Status Bayar</th>
                                <th>Jumlah Bayar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "
                                SELECT 
                                    p.id,
                                    pg.nama AS nama_anggota,  -- Ambil nama dari tabel pegawai
                                    pr.nama AS nama_produk,
                                    p.tanggal,
                                    p.status_bayar,
                                    pb.jumlah_bayar
                                FROM pesanan p
                                LEFT JOIN anggota a ON p.anggota_id = a.id
                                LEFT JOIN pegawai pg ON a.pegawai_id = pg.id
                                LEFT JOIN detail_pesanan dp ON p.id = dp.pesanan_id
                                LEFT JOIN produk pr ON dp.produk_id = pr.id
                                LEFT JOIN pembayaran pb ON p.id = pb.pesanan_id
                            ";
                            $result = mysqli_query($conn, $query);

                            if ($result === false) {
                                die("Error pada query pesanan: " . mysqli_error($conn));
                            }

                            while ($d = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>{$d['id']}</td>
                                    <td>" . ($d['nama_anggota'] ?? 'Tidak Diketahui') . "</td>  <!-- Tampilkan nama anggota dari pegawai -->
                                    <td>" . ($d['nama_produk'] ?? 'Tidak Ada Produk') . "</td>
                                    <td>{$d['tanggal']}</td>
                                    <td>" . ($d['status_bayar'] ? 'Lunas' : 'Belum Lunas') . "</td>
                                    <td>" . ($d['jumlah_bayar'] ?? '0') . "</td>
                                </tr>";
                            }
                            mysqli_free_result($result);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <footer class="py-4 bg-light mt-auto">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center justify-content-between small">
                <div class="text-muted">Copyright © Your Website 2023</div>
                <div>
                    <a href="#">Privacy Policy</a>
                    ·
                    <a href="#">Terms & Conditions</a>
                </div>
            </div>
        </div>
    </footer>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const statusLabels = <?= json_encode($statusData); ?>;
    const jumlahData = <?= json_encode($jumlahAnggota); ?>;

    // Area Chart
    new Chart(document.getElementById("myAreaChart"), {
        type: 'line',
        data: {
            labels: statusLabels,
            datasets: [{
                label: "Jumlah Anggota",
                data: jumlahData,
                fill: true,
                borderColor: "rgba(75,192,192,1)",
                backgroundColor: "rgba(75,192,192,0.2)",
                tension: 0.3
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Bar Chart
    new Chart(document.getElementById("myBarChart"), {
        type: 'bar',
        data: {
            labels: statusLabels,
            datasets: [{
                label: "Jumlah Anggota",
                data: jumlahData,
                backgroundColor: "rgba(54, 162, 235, 0.7)",
                borderColor: "rgba(54, 162, 235, 1)",
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?php
mysqli_close($conn);
?>

<?php include 'layout/footer.php'; ?>
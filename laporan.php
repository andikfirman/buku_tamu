<?php
require_once('koneksi.php');
require_once('function.php');

$buku_tamu = [];
$p_awal = '';
$p_akhir = '';
$pesan_error = '';
$link = 'export-laporan.php';

if (isset($_POST['tampilkan'])) {
    $p_awal = $_POST['p_awal'] ?? '';
    $p_akhir = $_POST['p_akhir'] ?? '';

    if ($p_awal !== '' && $p_akhir !== '' && $p_awal <= $p_akhir) {
        $link = 'export-laporan.php?p_awal=' . urlencode($p_awal) . '&p_akhir=' . urlencode($p_akhir);

        $stmt = mysqli_prepare(
            $koneksi,
            "SELECT id_tamu, tanggal, nama_tamu, alamat, no_hp,
                    bertemu, kepentingan
             FROM bukutamu
             WHERE tanggal BETWEEN ? AND ?
             ORDER BY tanggal ASC"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ss", $p_awal, $p_akhir);
            mysqli_stmt_execute($stmt);
            $hasil = mysqli_stmt_get_result($stmt);
            while ($data = mysqli_fetch_assoc($hasil)) {
                $buku_tamu[] = $data;
            }
            mysqli_stmt_close($stmt);
        } else {
            $pesan_error = 'Gagal menyiapkan query laporan.';
        }
    } else {
        $pesan_error = 'Periode tanggal tidak valid. Periksa tanggal awal dan akhir.';
    }
}

include_once('templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Laporan Tamu</h1>

    <!-- Form Periode -->
    <div class="row mx-auto d-flex justify-content-center">
        <div class="col-xl-8 col-md-10 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <label for="p_awal" class="font-weight-bold text-primary">Periode</label>
                            </div>
                            <div class="col-auto mb-2">
                                <input type="date" class="form-control" id="p_awal" name="p_awal" value="<?= htmlspecialchars($p_awal) ?>" required>
                            </div>
                            <div class="col-auto">
                                <span class="font-weight-bold text-primary">s.d.</span>
                            </div>
                            <div class="col-auto mb-2">
                                <input type="date" class="form-control" id="p_akhir" name="p_akhir" value="<?= htmlspecialchars($p_akhir) ?>" required>
                            </div>
                            <div class="col-auto mb-2">
                                <button type="submit" name="tampilkan" class="btn btn-primary">Tampilkan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pesan Error -->
    <?php if ($pesan_error !== '') : ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($pesan_error) ?>
        </div>
    <?php endif; ?>

    <!-- Tabel Histori Tamu -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?= $link ?>" target="_blank" class="btn btn-success btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-file-excel"></i>
                </span>
                <span class="text">Export Laporan</span>
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Nama Tamu</th>
                            <th>Alamat</th>
                            <th>No. Telp/HP</th>
                            <th>Bertemu Dengan</th>
                            <th>Kepentingan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($_POST['tampilkan']) && empty($pesan_error)) : ?>
                            <?php if (!empty($buku_tamu)) : ?>
                                <?php $no = 1; ?>
                                <?php foreach ($buku_tamu as $tamu) : ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($tamu['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($tamu['nama_tamu']) ?></td>
                                        <td><?= htmlspecialchars($tamu['alamat']) ?></td>
                                        <td><?= htmlspecialchars($tamu['no_hp']) ?></td>
                                        <td><?= htmlspecialchars($tamu['bertemu']) ?></td>
                                        <td><?= htmlspecialchars($tamu['kepentingan']) ?></td>
                                        <td>
                                            <a class="btn btn-success btn-sm" href="edit-tamu.php?id=<?= urlencode($tamu['id_tamu']) ?>">Ubah</a>
                                            <a class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" href="hapus-tamu.php?id=<?= urlencode($tamu['id_tamu']) ?>">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data tamu pada periode tersebut.</td>
                                </tr>
                            <?php endif; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center">Pilih periode tanggal, lalu klik Tampilkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php include_once('templates/footer.php'); ?>
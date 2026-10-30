<?php
// Memulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once('koneksi.php');
require_once('function.php');

// Cek apakah user sudah login
if (!isset($_SESSION['login'])) {
    header('Location: login.php');
    exit;
}

// Pengecekan role
// Hanya admin yang boleh mengakses halaman Buku Tamu
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('Anda tidak memiliki akses!');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// Proses tambah data tamu
if (isset($_POST['simpan'])) {
    if (tambah_tamu($_POST) > 0) {
        echo "<script>
            alert('Data berhasil disimpan!');
            window.location.href = 'buku-tamu.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Data gagal disimpan!');
            window.location.href = 'buku-tamu.php';
        </script>";
        exit;
    }
}

// Membuat kode tamu otomatis
$query = mysqli_query(
    $koneksi,
    "SELECT MAX(id_tamu) AS kodeTerbesar FROM bukutamu"
);

if (!$query) {
    die("Query kode tamu gagal: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);
$kodeTamu = $data['kodeTerbesar'];

if (empty($kodeTamu)) {
    $urutan = 1;
} else {
    $urutan = (int) substr($kodeTamu, 2, 3);
    $urutan++;
}

$huruf = 'zt';
$kodeTamu = $huruf . sprintf('%03d', $urutan);

// Memanggil header
include_once('templates/header.php');
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Buku Tamu</h1>

    <!-- DataTables Example -->
    <div class="card shadow mb-4">

        <div class="card-header py-3">

            <button type="button"
                class="btn btn-primary btn-icon-split"
                data-toggle="modal"
                data-target="#tambahModal">

                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>

                <span class="text">Data tamu</span>
            </button>

        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered"
                    id="dataTable"
                    width="100%"
                    cellspacing="0">

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
                        <?php
                        $no = 1;
                        $buku_tamu = query("SELECT * FROM bukutamu");

                        foreach ($buku_tamu as $tamu) :
                        ?>

                            <tr>
                                <td><?= $no++; ?></td>

                                <td>
                                    <?= htmlspecialchars($tamu['tanggal'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($tamu['nama_tamu'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($tamu['alamat'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($tamu['no_hp'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($tamu['bertemu'] ?? ''); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($tamu['kepentingan'] ?? ''); ?>
                                </td>

                                <td>
                                    <a class="btn btn-success btn-sm"
                                        href="edit_tamu.php?id=<?= urlencode($tamu['id_tamu']); ?>">
                                        Ubah
                                    </a>

                                    <a href="hapus_tamu.php?id=<?= urlencode($tamu['id_tamu']); ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Apakah yakin ingin menghapus data ini?');">
                                        Hapus
                                    </a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>

                </table>

            </div>
        </div>
    </div>

</div>

<!-- Modal Tambah Data Tamu -->
<div class="modal fade"
    id="tambahModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="tambahModalLabel"
    aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="tambahModalLabel">
                    Tambah Data Tamu
                </h5>

                <button type="button"
                    class="close"
                    data-dismiss="modal"
                    aria-label="Close">

                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

            <form method="post" action="">

                <div class="modal-body">

                    <input type="hidden"
                        name="id_tamu"
                        id="id_tamu"
                        value="<?= htmlspecialchars($kodeTamu); ?>">

                    <div class="form-group row">

                        <label for="nama_tamu"
                            class="col-sm-3 col-form-label">
                            Nama Tamu
                        </label>

                        <div class="col-sm-8">
                            <input type="text"
                                class="form-control"
                                id="nama_tamu"
                                name="nama_tamu"
                                required>
                        </div>

                    </div>

                    <div class="form-group row">

                        <label for="alamat"
                            class="col-sm-3 col-form-label">
                            Alamat
                        </label>

                        <div class="col-sm-8">
                            <textarea
                                class="form-control"
                                id="alamat"
                                name="alamat"
                                required></textarea>
                        </div>

                    </div>

                    <div class="form-group row">

                        <label for="no_hp"
                            class="col-sm-3 col-form-label">
                            No. Telepon
                        </label>

                        <div class="col-sm-8">
                            <input type="text"
                                class="form-control"
                                id="no_hp"
                                name="no_hp"
                                required>
                        </div>

                    </div>

                    <div class="form-group row">

                        <label for="bertemu"
                            class="col-sm-3 col-form-label">
                            Bertemu Dengan
                        </label>

                        <div class="col-sm-8">
                            <input type="text"
                                class="form-control"
                                id="bertemu"
                                name="bertemu"
                                required>
                        </div>

                    </div>

                    <div class="form-group row">

                        <label for="kepentingan"
                            class="col-sm-3 col-form-label">
                            Kepentingan
                        </label>

                        <div class="col-sm-8">
                            <input type="text"
                                class="form-control"
                                id="kepentingan"
                                name="kepentingan"
                                required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                        Keluar
                    </button>

                    <button type="submit"
                        name="simpan"
                        class="btn btn-primary">
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<?php
include_once('templates/footer.php');
?>
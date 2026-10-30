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

// Hanya admin yang boleh mengakses halaman Data User
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
        alert('Anda tidak memiliki akses!');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// Proses tambah user
if (isset($_POST['simpan'])) {
    if (tambah_user($_POST) > 0) {
        echo "<script>
            alert('Data user berhasil disimpan!');
            window.location.href = 'users.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Data user gagal disimpan!');
            window.location.href = 'users.php';
        </script>";
        exit;
    }
}

// Proses ganti password
if (isset($_POST['ganti_password'])) {
    if (ganti_password($_POST) > 0) {
        echo "<script>
            alert('Password berhasil diubah!');
            window.location.href = 'users.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Password gagal diubah!');
            window.location.href = 'users.php';
        </script>";
        exit;
    }
}

// Membuat kode user otomatis
$query = mysqli_query(
    $koneksi,
    "SELECT MAX(id_user) AS kodeTerbesar FROM users"
);

if (!$query) {
    die("Query kode user gagal: " . mysqli_error($koneksi));
}

$data = mysqli_fetch_assoc($query);
$kodeuser = $data['kodeTerbesar'];

if (empty($kodeuser)) {
    $urutan = 1;
} else {
    $urutan = (int) substr($kodeuser, 3, 2);
    $urutan++;
}

$kodeuser = "usr" . sprintf("%02d", $urutan);

// Memanggil header
include_once('templates/header.php');
?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Data User</h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <button type="button" class="btn btn-primary"
                data-toggle="modal" data-target="#tambahModal">
                <i class="fas fa-plus"></i> Tambah User
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="dataTable"
                    width="100%" cellspacing="0">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>User Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;
                        $users = query("SELECT * FROM users");

                        foreach ($users as $user) :
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>

                                <td>
                                    <?= htmlspecialchars($user['username']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($user['user_role']); ?>
                                </td>

                                <td>
                                    <button type="button"
                                        class="btn btn-info btn-sm"
                                        data-toggle="modal"
                                        data-target="#gantiPassword"
                                        data-id="<?= htmlspecialchars($user['id_user'], ENT_QUOTES, 'UTF-8'); ?>">
                                        Ganti Password
                                    </button>

                                    <a class="btn btn-success btn-sm"
                                        href="edit-user.php?id=<?= urlencode($user['id_user']); ?>">
                                        Ubah
                                    </a>

                                    <a class="btn btn-danger btn-sm"
                                        href="hapus-user.php?id=<?= urlencode($user['id_user']); ?>"
                                        onclick="return confirm('Yakin ingin menghapus user ini?')">
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

<!-- Modal Tambah User -->
<div class="modal fade" id="tambahModal" tabindex="-1"
    role="dialog" aria-labelledby="tambahModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="tambahModalLabel">
                    Tambah User
                </h5>

                <button type="button" class="close"
                    data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="post" action="">
                <div class="modal-body">

                    <input type="hidden" name="id_user"
                        value="<?= htmlspecialchars($kodeuser, ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="form-group row">
                        <label for="username"
                            class="col-sm-3 col-form-label">
                            Username
                        </label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control"
                                id="username" name="username" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="password"
                            class="col-sm-3 col-form-label">
                            Password
                        </label>

                        <div class="col-sm-8">
                            <input type="password" class="form-control"
                                id="password" name="password" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="user_role"
                            class="col-sm-3 col-form-label">
                            User Role
                        </label>

                        <div class="col-sm-8">
                            <select class="form-control"
                                id="user_role" name="user_role" required>
                                <option value="admin">Administrator</option>
                                <option value="operator">Operator</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" name="simpan"
                        class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal Ganti Password -->
<div class="modal fade" id="gantiPassword" tabindex="-1"
    role="dialog" aria-labelledby="gantiPasswordLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="gantiPasswordLabel">
                    Ganti Password
                </h5>

                <button type="button" class="close"
                    data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form method="post" action="">
                <div class="modal-body">

                    <input type="hidden"
                        name="id_user"
                        id="id_user">

                    <div class="form-group">
                        <label for="password_baru">
                            Password Baru
                        </label>

                        <input type="password"
                            class="form-control"
                            id="password_baru"
                            name="password"
                            required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">
                        Keluar
                    </button>

                    <button type="submit"
                        name="ganti_password"
                        class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<?php include_once('templates/footer.php'); ?>

<script>
    $(document).ready(function() {
        $('#gantiPassword').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            $(this).find('#id_user').val(id);
        });
    });
</script>
<?php

require_once('koneksi.php');
require_once('function.php');

$id_user = $_GET['id'] ?? '';

if ($id_user === '') {
    echo "ID user tidak ditemukan.";
    exit;
}

$id_user_sql = mysqli_real_escape_string($koneksi, $id_user);

$data_user = query(
    "SELECT * FROM users WHERE id_user = '$id_user_sql'"
);

if (count($data_user) == 0) {
    echo "Data user tidak ditemukan.";
    exit;
}

$data = $data_user[0];

if (isset($_POST['simpan'])) {
    if (ubah_user($_POST) >= 0) {
        echo "<script>
            alert('Data user berhasil diubah!');
            window.location.href = 'users.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('Data user gagal diubah!');
        </script>";
    }
}

include_once('templates/header.php');

?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Ubah Data User
    </h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Data User
            </h6>
        </div>

        <div class="card-body">

            <form method="post" action="">

                <input type="hidden"
                    name="id_user"
                    id="id_user"
                    value="<?= htmlspecialchars($id_user); ?>">

                <div class="form-group row">
                    <label for="username"
                        class="col-sm-3 col-form-label">
                        Username
                    </label>

                    <div class="col-sm-8">
                        <input type="text"
                            class="form-control"
                            id="username"
                            name="username"
                            value="<?= htmlspecialchars($data['username']); ?>"
                            required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="user_role"
                        class="col-sm-3 col-form-label">
                        User Role
                    </label>

                    <div class="col-sm-8">
                        <select class="form-control"
                            id="user_role"
                            name="user_role"
                            required>

                            <option value="admin"
                                <?= $data['user_role'] == 'admin' ? 'selected' : ''; ?>>
                                Administrator
                            </option>

                            <option value="operator"
                                <?= $data['user_role'] == 'operator' ? 'selected' : ''; ?>>
                                Operator
                            </option>

                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-8 offset-sm-3">

                        <a class="btn btn-danger btn-icon-split"
                            href="users.php">
                            <span class="icon text-white-50">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                            <span class="text">Kembali</span>
                        </a>

                        <button type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan
                        </button>

                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

<?php include_once('templates/footer.php'); ?>
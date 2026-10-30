<?php

require_once('function.php');

$id = $_GET['id'];

$data = query("SELECT * FROM bukutamu WHERE id_tamu = '$id'");

if (count($data) == 0) {
    echo "Data tamu tidak ditemukan.";
    exit;
}

$tamu = $data[0];

if (isset($_POST['ubah'])) {

    if (ubah_tamu($_POST) > 0) {
        echo "<script>
                alert('Data berhasil diubah!');
                window.location.href='buku_tamu.php';
              </script>";
        exit;
    } else {
        echo "<script>
                alert('Data gagal diubah!');
            </script>";
    }
}

include_once('templates/header.php');

?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        Ubah Data Tamu
    </h1>

    <div class="card shadow mb-4">

        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Data Tamu
            </h6>
        </div>

        <div class="card-body">

            <form method="post">

                <input type="hidden"
                       name="id_tamu"
                       value="<?= $tamu['id_tamu']; ?>">

                <div class="form-group">
                    <label>Nama Tamu</label>
                    <input type="text"
                           name="nama_tamu"
                           class="form-control"
                           value="<?= $tamu['nama_tamu']; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat"
                              class="form-control"
                              rows="3"
                              required><?= $tamu['alamat']; ?></textarea>
                </div>

                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text"
                           name="no_hp"
                           class="form-control"
                           value="<?= $tamu['no_hp']; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Bertemu Dengan</label>
                    <input type="text"
                           name="bertemu"
                           class="form-control"
                           value="<?= $tamu['bertemu dengan']; ?>"
                           required>
                </div>

                <div class="form-group">
                    <label>Kepentingan</label>
                    <textarea name="kepentingan"
                              class="form-control"
                              rows="3"
                              required><?= $tamu['kepentingan']; ?></textarea>
                </div>

                <button type="submit"
                        name="ubah"
                        class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="buku_tamu.php"
                   class="btn btn-secondary">
                    Kembali
                </a>

            </form>

        </div>

    </div>

</div>

<?php

include_once('templates/footer.php');

?>
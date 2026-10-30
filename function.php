<?php

require_once('koneksi.php');

function query(string $query): array
{
    global $koneksi;

    $result = mysqli_query($koneksi, $query);
    $rows = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }

    return $rows;
}

// function tambah data tamu
function tambah_tamu(array $data): int
{
    global $koneksi;

    $kode = htmlspecialchars($data["id_tamu"]);
    $tanggal = date('Y-m-d');
    $nama_tamu = htmlspecialchars($data["nama_tamu"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $no_hp = htmlspecialchars($data["no_hp"]);
    $bertemu = htmlspecialchars($data["bertemu"]);
    $kepentingan = htmlspecialchars($data["kepentingan"]);

    $query = "INSERT INTO bukutamu (
        id_tamu,
        tanggal,
        nama_tamu,
        alamat,
        no_hp,
        `bertemu dengan`,
        kepentingan
    ) VALUES (
        '$kode',
        '$tanggal',
        '$nama_tamu',
        '$alamat',
        '$no_hp',
        '$bertemu',
        '$kepentingan'
    )";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

// function ubah data tamu
function ubah_tamu(array $data): int
{
    global $koneksi;

    $id = htmlspecialchars($data["id_tamu"]);
    $nama_tamu = htmlspecialchars($data["nama_tamu"]);
    $alamat = htmlspecialchars($data["alamat"]);
    $no_hp = htmlspecialchars($data["no_hp"]);
    $bertemu = htmlspecialchars($data["bertemu"]);
    $kepentingan = htmlspecialchars($data["kepentingan"]);

    $query = "UPDATE bukutamu SET
        nama_tamu = '$nama_tamu',
        alamat = '$alamat',
        no_hp = '$no_hp',
        `bertemu dengan` = '$bertemu',
        kepentingan = '$kepentingan'
        WHERE id_tamu = '$id'";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

// function hapus data tamu
function hapus_tamu(string $id): int
{
    global $koneksi;

    $query = "DELETE FROM bukutamu
              WHERE id_tamu = '$id'";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

function tambah_user($data)
{
    global $koneksi;

    $kode       = htmlspecialchars($data["id_user"]);
    $username   = htmlspecialchars($data["username"]);
    $password   = htmlspecialchars($data["password"]);
    $user_role  = htmlspecialchars($data["user_role"]);

    // Enkripsi password dengan password hash
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users VALUES ('$kode','$username','$password_hash','$user_role')";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

// function ubah data tamu
function ubah_user($data)
{
    global $koneksi;

    $kode       = htmlspecialchars($data["id_user"]);
    $username   = htmlspecialchars($data["username"]);
    $user_role  = htmlspecialchars($data["user_role"]);

    $query = "UPDATE users SET
                username = '$username',
                user_role = '$user_role'
              WHERE id_user = '$kode'";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

// function hapus data user 
function hapus_user($id)
{
    global $koneksi;

    $query = "DELETE FROM users WHERE id_user = '$id' ";

    mysqli_query($koneksi, $query);

    return mysqli_affected_rows($koneksi);
}

// function ganti password user
function ganti_password($data)
{
    global $koneksi;

    $kode = trim($data['id_user'] ?? '');
    $password = $data['password'] ?? '';

    if ($kode === '' || $password === '') {
        return 0;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = ? WHERE id_user = ?";
    $stmt = mysqli_prepare($koneksi, $query);

    if (!$stmt) {
        error_log(mysqli_error($koneksi));
        return 0;
    }

    mysqli_stmt_bind_param($stmt, "ss", $password_hash, $kode);

    if (!mysqli_stmt_execute($stmt)) {
        error_log(mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return 0;
    }

    $hasil = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    return $hasil > 0 ? 1 : 0;
}
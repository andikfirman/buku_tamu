
<?php
include 'koneksi.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header Excel
$sheet->setCellValue('A1', 'id_tamu');
$sheet->setCellValue('B1', 'tanggal');
$sheet->setCellValue('C1', 'nama');
$sheet->setCellValue('D1', 'alamat');
$sheet->setCellValue('E1', 'no_hp');
$sheet->setCellValue('F1', 'bertemu dengan');
$sheet->setCellValue('G1', 'kepentingan');

// Mengambil data dari tabel bukutamu
if (isset($_GET['cari'])) {
    $p_awal = $_GET['p_awal'] ?? '';
    $p_akhir = $_GET['p_akhir'] ?? '';

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT * FROM bukutamu WHERE tanggal BETWEEN ? AND ?"
    );

    if (!$stmt) {
        die("Error query: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($stmt, "ss", $p_awal, $p_akhir);
    mysqli_stmt_execute($stmt);
    $data = mysqli_stmt_get_result($stmt);
} else {
    $data = mysqli_query($koneksi, "SELECT * FROM bukutamu");
}

if (!$data) {
    die("Error database: " . mysqli_error($koneksi));
}

// Memasukkan data ke Excel
$i = 2;

while ($d = mysqli_fetch_assoc($data)) {
    $sheet->setCellValue('A' . $i, $d['id_tamu']);
    $sheet->setCellValue('B' . $i, $d['tanggal']);
    $sheet->setCellValue('C' . $i, $d['nama_tamu']);
    $sheet->setCellValue('D' . $i, $d['alamat']);

    $sheet->setCellValueExplicit(
        'E' . $i,
        (string) $d['no_hp'],
        DataType::TYPE_STRING
    );

    $sheet->setCellValue('F' . $i, $d['bertemu']);
    $sheet->setCellValue('G' . $i, $d['kepentingan']);

    $i++;
}

// Mengatur lebar kolom otomatis
foreach (range('A', 'G') as $kolom) {
    $sheet->getColumnDimension($kolom)->setAutoSize(true);
}

// Download file Excel
$nama_file = 'Laporan Buku Tamu.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $nama_file . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');

exit;
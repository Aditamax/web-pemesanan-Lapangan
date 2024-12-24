<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_lapangan = $_GET['id'];
    $sql = "SELECT * FROM fields WHERE id = $id_lapangan";
    $result = $koneksi->query($sql);
    $field = $result->fetch_assoc();
}

if (isset($_POST['book'])) {
    $id_user = $_SESSION['user_id'];
    $tanggal_pemesanan = $_POST['tanggal_pemesanan'];
    $waktu_mulai = $_POST['waktu_mulai'];
    $waktu_selesai = $_POST['waktu_selesai'];
    $total_harga = $_POST['total_harga'];
    $status_pemesanan = 'pending';

    $sql_booking = "INSERT INTO bookings (id_user, id_lapangan, tanggal_pemesanan, waktu_mulai, waktu_selesai, status_pemesanan, total_harga) 
                    VALUES ('$id_user', '$id_lapangan', '$tanggal_pemesanan', '$waktu_mulai', '$waktu_selesai', '$status_pemesanan', '$total_harga')";
    if ($koneksi->query($sql_booking) === TRUE) {
        echo "Pemesanan berhasil!";
    } else {
        echo "Error: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Pesan Lapangan: <?php echo $field['nama_lapangan']; ?></h2>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="tanggal_pemesanan" class="form-label">Tanggal Pemesanan</label>
                <input type="date" class="form-control" id="tanggal_pemesanan" name="tanggal_pemesanan" required>
            </div>
            <div class="mb-3">
                <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" required>
            </div>
            <div class="mb-3">
                <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                <input type="time" class="form-control" id="waktu_selesai" name="waktu_selesai" required>
            </div>
            <div class="mb-3">
                <label for="total_harga" class="form-label">Total Harga</label>
                <input type="number" class="form-control" id="total_harga" name="total_harga" value="<?php echo $field['harga_per_jam']; ?>" readonly>
            </div>
            <button type="submit" name="book" class="btn btn-primary">Pesan</button>
        </form>
    </div>
</body>
</html>

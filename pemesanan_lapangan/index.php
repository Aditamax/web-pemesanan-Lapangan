<?php
session_start();
include('database.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$sql = "SELECT * FROM fields";
$result = $koneksi->query($sql);
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
        <h2>Daftar Lapangan</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Lapangan</th>
                    <th>Jenis Olahraga</th>
                    <th>Lokasi</th>
                    <th>Harga/Jam</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($field = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $field['nama_lapangan']; ?></td>
                        <td><?php echo $field['jenis_olahraga']; ?></td>
                        <td><?php echo $field['lokasi']; ?></td>
                        <td><?php echo $field['harga_per_jam']; ?></td>
                        <td><a href="booking.php?id=<?php echo $field['id']; ?>" class="btn btn-success">Pesan</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

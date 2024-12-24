<?php
session_start();
include('database.php');

// Cek apakah user sudah login dan memiliki peran admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Proses untuk menambah, mengedit, dan menghapus data
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    // Menambah pengguna
    if ($action == 'add_user' && isset($_POST['submit'])) {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $peran = $_POST['peran'];
        $sql = "INSERT INTO users (nama, email, password, peran) VALUES ('$nama', '$email', '$password', '$peran')";
        if ($koneksi->query($sql) === TRUE) {
            echo "Pengguna berhasil ditambahkan!";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }

    // Mengedit pengguna
    if ($action == 'edit_user' && isset($_POST['submit'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $peran = $_POST['peran'];
        $sql = "UPDATE users SET nama='$nama', email='$email', peran='$peran' WHERE id=$id";
        if ($koneksi->query($sql) === TRUE) {
            echo "Pengguna berhasil diperbarui!";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }

    // Menghapus pengguna
    if ($action == 'delete_user') {
        $id = $_GET['id'];
        $sql = "DELETE FROM users WHERE id=$id";
        if ($koneksi->query($sql) === TRUE) {
            echo "Pengguna berhasil dihapus!";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }

    // Menambah lapangan
    if ($action == 'add_field' && isset($_POST['submit'])) {
        $nama_lapangan = $_POST['nama_lapangan'];
        $jenis_olahraga = $_POST['jenis_olahraga'];
        $lokasi = $_POST['lokasi'];
        $harga_per_jam = $_POST['harga_per_jam'];
        $sql = "INSERT INTO fields (nama_lapangan, jenis_olahraga, lokasi, harga_per_jam) 
                VALUES ('$nama_lapangan', '$jenis_olahraga', '$lokasi', '$harga_per_jam')";
        if ($koneksi->query($sql) === TRUE) {
            echo "Lapangan berhasil ditambahkan!";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }

    // Mengedit lapangan
    if ($action == 'edit_field' && isset($_POST['submit'])) {
        $id = $_POST['id'];
        $nama_lapangan = $_POST['nama_lapangan'];
        $jenis_olahraga = $_POST['jenis_olahraga'];
        $lokasi = $_POST['lokasi'];
        $harga_per_jam = $_POST['harga_per_jam'];
        $sql = "UPDATE fields SET nama_lapangan='$nama_lapangan', jenis_olahraga='$jenis_olahraga', 
                lokasi='$lokasi', harga_per_jam='$harga_per_jam' WHERE id=$id";
        if ($koneksi->query($sql) === TRUE) {
            echo "Lapangan berhasil diperbarui!";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }

    // Menghapus lapangan
    if ($action == 'delete_field') {
        $id = $_GET['id'];
        $sql = "DELETE FROM fields WHERE id=$id";
        if ($koneksi->query($sql) === TRUE) {
            echo "Lapangan berhasil dihapus!";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }

    
    // Menghapus pemesanan
    if ($action == 'delete_booking') {
        $id = $_GET['id'];
        $sql = "DELETE FROM bookings WHERE id=$id";
        if ($koneksi->query($sql) === TRUE) {
            echo "Pemesanan berhasil dihapus!";
        } else {
            echo "Error: " . $koneksi->error;
        }
    }
}

// Menampilkan daftar pengguna
$sql_users = "SELECT * FROM users";
$users = $koneksi->query($sql_users);

// Menampilkan daftar lapangan
$sql_fields = "SELECT * FROM fields";
$fields = $koneksi->query($sql_fields);

// Menampilkan daftar pemesanan
$sql_bookings = "SELECT bookings.*, users.nama AS nama_user, fields.nama_lapangan 
                 FROM bookings 
                 JOIN users ON bookings.id_user = users.id
                 JOIN fields ON bookings.id_lapangan = fields.id";
$bookings = $koneksi->query($sql_bookings);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pemesanan Lapangan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Panel</h2>

        <!-- Form untuk menambah pengguna -->
        <h3>Tambah Pengguna</h3>
        <form method="POST" action="?action=add_user">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="peran" class="form-label">Peran</label>
                <select class="form-control" id="peran" name="peran">
                    <option value="admin">Admin</option>
                    <option value="penyewa">Penyewa</option>
                </select>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Tambah Pengguna</button>
        </form>

        <hr>

        <!-- Daftar Pengguna -->
        <h3>Daftar Pengguna</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Peran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['nama']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['peran']; ?></td>
                        <td>
                            <a href="?action=edit_user&id=<?php echo $user['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?action=delete_user&id=<?php echo $user['id']; ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <hr>

        <!-- Daftar Lapangan -->
        <h3>Tambah Lapangan</h3>
        <form method="POST" action="?action=add_field">
            <div class="mb-3">
                <label for="nama_lapangan" class="form-label">Nama Lapangan</label>
                <input type="text" class="form-control" id="nama_lapangan" name="nama_lapangan" required>
            </div>
            <div class="mb-3">
                <label for="jenis_olahraga" class="form-label">Jenis Olahraga</label>
                <input type="text" class="form-control" id="jenis_olahraga" name="jenis_olahraga" required>
            </div>
            <div class="mb-3">
                <label for="lokasi" class="form-label">Lokasi</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" required>
            </div>
            <div class="mb-3">
                <label for="harga_per_jam" class="form-label">Harga per Jam</label>
                <input type="number" class="form-control" id="harga_per_jam" name="harga_per_jam" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Tambah Lapangan</button>
        </form>

        <hr>

        <!-- Daftar Lapangan -->
        <h3>Daftar Lapangan</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Lapangan</th>
                    <th>Jenis Olahraga</th>
                    <th>Lokasi</th>
                    <th>Harga/Jam</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($field = $fields->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $field['id']; ?></td>
                        <td><?php echo $field['nama_lapangan']; ?></td>
                        <td><?php echo $field['jenis_olahraga']; ?></td>
                        <td><?php echo $field['lokasi']; ?></td>
                        <td><?php echo $field['harga_per_jam']; ?></td>
                        <td>
                            <a href="?action=edit_field&id=<?php echo $field['id']; ?>" class="btn btn-warning">Edit</a>
                            <a href="?action=delete_field&id=<?php echo $field['id']; ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <hr>

        <!-- Daftar Pemesanan -->
        <h3>Daftar Pemesanan</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Penyewa</th>
                    <th>Nama Lapangan</th>
                    <th>Tanggal Pemesanan</th>
                    <th>Waktu Mulai</th>
                    <th>Waktu Selesai</th>
                    <th>Status Pemesanan</th>
                    <th>Total Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $booking['id']; ?></td>
                        <td><?php echo $booking['nama_user']; ?></td>
                        <td><?php echo $booking['nama_lapangan']; ?></td>
                        <td><?php echo $booking['tanggal_pemesanan']; ?></td>
                        <td><?php echo $booking['waktu_mulai']; ?></td>
                        <td><?php echo $booking['waktu_selesai']; ?></td>
                        <td><?php echo $booking['status_pemesanan']; ?></td>
                        <td><?php echo $booking['total_harga']; ?></td>
                        <td>
                            <a href="?action=delete_booking&id=<?php echo $booking['id']; ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

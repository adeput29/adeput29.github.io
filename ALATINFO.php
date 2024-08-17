<?php
session_start(); // Mulai sesi

$message = "";
$data_file = 'Notif.json'; // File untuk menyimpan data JSON
$allowed_tokens = array("Gampedia22", "Gampedia23", "Gampedia24");

// Fungsi untuk memuat data dari file JSON
function loadData($file) {
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $data;
        }
    }
    return [];
}

// Fungsi untuk menyimpan data ke file JSON
function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT) . "\n"); // Hapus opsi JSON_UNESCAPED_SLASHES dan JSON_UNESCAPED_UNICODE
}

$data_array = loadData($data_file);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title'])) {
    // Mendapatkan nilai Token dari input form
    $token = $_POST['token'];

    // Menyimpan data hanya jika token valid
    if (in_array($token, $allowed_tokens)) {
        // Mendapatkan data dari form
        $title = $_POST['title'];
        $deskripsi_singkat = $_POST['deskripsi_singkat'];
        $deskripsi = str_replace(array("\r\n", "\r", "\n"), "\n", $_POST['deskripsi']); // Menggantikan newline dengan \n
        $post_date = $_POST['post_date'];
        $link_tujuan = $_POST['link_tujuan'];

        // Membuat array baru berdasarkan input form
        $new_data = [
            "title" => $title,
            "deskripsi_singkat" => $deskripsi_singkat,
            "deskripsi" => $deskripsi,
            "post_date" => $post_date,
            "link_tujuan" => $link_tujuan
        ];

        // Menambahkan data baru ke dalam array
        $data_array[] = $new_data;

        // Menyimpan data ke dalam file JSON
        saveData($data_file, $data_array);

        $message = "Pesan Berhasil Dikirim.";
    } else {
        $message = "Token tidak valid.";
    }
}

if (isset($_GET['delete'])) {
    // Menghapus data berdasarkan indeks yang diberikan
    $index = $_GET['delete'];
    if (isset($data_array[$index])) {
        unset($data_array[$index]);
        $data_array = array_values($data_array); // Mengatur ulang indeks array
        saveData($data_file, $data_array); // Simpan perubahan ke file
        $message = "Pesan yang Anda pilih telah dihapus.";
    } else {
        $message = "Pesan tidak ditemukan.";
    }
}

if (isset($_GET['edit'])) {
    // Mendapatkan data yang akan diedit berdasarkan indeks
    $editIndex = $_GET['edit'];
    if (isset($data_array[$editIndex])) {
        $editData = $data_array[$editIndex];
    } else {
        $message = "Pesan tidak ditemukan.";
    }
}

// Jika token valid, kirimkan data JSON
if (isset($_GET['token']) && in_array($_GET['token'], $allowed_tokens)) {
    header('Content-Type: application/json');
    echo json_encode($data_array, JSON_PRETTY_PRINT);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Notifikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;0,700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: "Ubuntu", sans-serif;
        }
        
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            font-size: 15px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table th {
            background-color: #007bff;
            color: #fff;
        }

        table td a {
            color: #007bff;
            text-decoration: none;
        }

        table td a:hover {
            text-decoration: underline;
        }
        
        img[alt="www.000webhost.com"] {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                Form Input Notifikasi
            </div>
            <div class="card-body">
                <?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="token" class="form-label">Token:</label>
                        <input type="text" id="token" name="token" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title:</label>
                        <input type="text" id="title" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi_singkat" class="form-label">Deskripsi Singkat:</label>
                        <input type="text" id="deskripsi_singkat" name="deskripsi_singkat" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi:</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="post_date" class="form-label">Post Date:</label>
                        <input type="datetime-local" id="post_date" name="post_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="link_tujuan" class="form-label">Link Tujuan (Opsional):</label>
                        <input type="url" id="link_tujuan" name="link_tujuan" class="form-control">
                    </div>
                    <div class="d-grid">
                        <input type="submit" value="Kirim" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                Daftar Notifikasi
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Deskripsi Singkat</th>
                            <th>Deskripsi</th>
                            <th>Post Date</th>
                            <th>Link Tujuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_array as $index => $data) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['title']); ?></td>
                                <td><?php echo htmlspecialchars($data['deskripsi_singkat']); ?></td>
                                <td><?php echo htmlspecialchars($data['deskripsi']); ?></td>
                                <td><?php echo isset($data['post_date']) ? date('Y-m-d H:i', strtotime($data['post_date'])) : ''; ?></td>
                                <td><?php echo isset($data['link_tujuan']) ? htmlspecialchars($data['link_tujuan']) : ''; ?></td>
                                <td>
                                    <a href="?edit=<?php echo $index; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?delete=<?php echo $index; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

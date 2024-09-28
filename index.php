<?php
$mysqli = new mysqli("localhost", "root", "", "asah_otak");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$query = "SELECT * FROM master_kata ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();
$kata = $row['kata'];
$clue = $row['clue'];

$huruf_ke_3 = isset($kata[2]) ? $kata[2] : '';
$huruf_ke_7 = isset($kata[6]) ? $kata[6] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jawaban = $_POST['jawaban'];
    $poin = 0;

    for ($i = 0; $i < strlen($kata); $i++) {
        if ($i == 2 || $i == 6) {
            $poin += 10;
        } elseif (isset($jawaban[$i]) && $jawaban[$i] === $kata[$i]) {
            $poin += 10;
        } else {
            $poin -= 2;
        }
    }

    if (isset($_POST['simpan'])) {
        $nama_user = $_POST['nama_user'];
        $insert_query = "INSERT INTO point_game (nama_user, total_point) VALUES ('$nama_user', '$poin')";
        $mysqli->query($insert_query);
        echo "Poin Anda telah disimpan!";
    }

    echo "<h2>Poin yang Anda dapatkan adalah: $poin</h2>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asah Otak Game</title>
</head>
<body>
    <h1>Permainan Asah Otak</h1>
    <p>Clue: <?php echo $clue; ?></p>
    <form method="POST">
        <?php for ($i = 0; $i < strlen($kata); $i++): ?>
            <input 
                type="text" 
                name="jawaban[]" 
                maxlength="1" 
                value="<?php echo ($i == 2 || $i == 6) ? $kata[$i] : ''; ?>" 
                <?php echo ($i == 2 || $i == 6) ? 'readonly' : ''; ?>
                style="width: 30px; text-align: center;">
        <?php endfor; ?>
        <br><br>
        <button type="submit" name="submit">Kirim Jawaban</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <form method="POST">
            <label for="nama_user">Nama Anda:</label>
            <input type="text" name="nama_user" required>
            <input type="hidden" name="jawaban" value="<?php echo implode("", $_POST['jawaban']); ?>">
            <button type="submit" name="simpan">Simpan Poin</button>
        </form>
        <form method="POST">
            <button type="submit" name="ulangi">Ulangi</button>
        </form>
    <?php endif; ?>
</body>
</html>

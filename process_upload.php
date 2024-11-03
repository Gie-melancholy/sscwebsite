<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Upload File</h2>

        <!-- File Upload Form -->
        <form method="post" action="upload.php" enctype="multipart/form-data">
            <label for="file">Select file (CSV, JPG, JPEG, PNG, GIF, PDF, DOCX, XLSX):</label>
            <input type="file" id="file" name="file" accept=".csv, .jpg, .jpeg, .png, .gif, .pdf, .docx, .xlsx" required>
            <button type="submit" name="upload">Upload</button>
        </form>
    </div>
</body>
</html>

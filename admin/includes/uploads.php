<?php
function upload_image(string $fieldName, string $currentImage = ''): string {
    // Check if a file was uploaded without errors
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return $currentImage; // Keep existing image if no new file was uploaded
    }

    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('File upload failed with error code: ' . $_FILES[$fieldName]['error']);
    }

    $fileTmpPath = $_FILES[$fieldName]['tmp_name'];
    $fileSize = $_FILES[$fieldName]['size'];
    $fileExtension = strtolower(pathinfo($_FILES[$fieldName]['name'], PATHINFO_EXTENSION));

    // Allowed image types
    $allowedTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'webp' => 'image/webp'
    ];

    // Validate mime type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($fileTmpPath);

    if (!in_array($mimeType, $allowedTypes, true)) {
        throw new RuntimeException('Only JPG, PNG, GIF, and WEBP images are allowed.');
    }

    // Validate file size (max 5 MB)
    if ($fileSize > 5 * 1024 * 1024) {
        throw new RuntimeException('The image must be smaller than 5 MB.');
    }

    // Define upload directory relative to this file (admin/includes/ -> admin/uploads/ or root/uploads/)
    // Adjust __DIR__ levels depending on where your uploads folder actually lives.
    $uploadDirectory = dirname(__DIR__, 2) . '/uploads';

    if (!is_dir($uploadDirectory)) {
        if (!mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
            throw new RuntimeException('Could not create the uploads folder.');
        }
    }

    // Generate unique filename and destination
    $filename = bin2hex(random_bytes(16)) . '.' . $fileExtension;
    $destination = $uploadDirectory . '/' . $filename;

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        throw new RuntimeException('Could not save the uploaded image.');
    }

    // Return relative path to be stored in the database
    return 'uploads/' . $filename;
}
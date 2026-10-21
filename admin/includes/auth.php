<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../includes/db.php';

/* Base URLs, correct even when the URL has extra path parts */
function admin_base() {
    return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
}
function site_base() {
    return rtrim(str_replace('\\', '/', dirname(rtrim(admin_base(), '/'))), '/') . '/';
}

function require_login() {
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . admin_base() . 'login.php');
        exit;
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

/* One-time message: flash('Saved!') to set, flash() to read and clear */
function flash($msg = null, $type = 'success') {
    if ($msg !== null) {
        $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/* Form security token */
function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function check_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        die('Invalid request. Go back and try again.');
    }
}

/* Image upload. Returns the new path (e.g. uploads/abc.jpg) or $current if no new file */
function upload_image($field, $current = '') {
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return $current;
    }
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
        flash('Only JPG, PNG, WEBP or GIF images are allowed.', 'danger');
        return $current;
    }
    if (!@getimagesize($_FILES[$field]['tmp_name'])) {
        flash('That file is not a valid image.', 'danger');
        return $current;
    }
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = __DIR__ . '/../../uploads/' . $name;
    if (move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
        return 'uploads/' . $name;
    }
    flash('Could not save the uploaded image.', 'danger');
    return $current;
}
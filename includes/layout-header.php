<?php
require_once __DIR__ . '/bootstrap.php';

$user = current_user();
$isAdminRequest = is_admin_request();
$prefix = asset_prefix();
$categories = get_categories();
$flashMessages = get_flash_messages();
$currentSection = get_current_section();
$pageTitle = get_page_title();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Digital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= htmlspecialchars($prefix . 'assets/css/style.css', ENT_QUOTES) ?>">
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="navbar navbar-expand-lg <?= $user && !empty($user['is_admin']) ? 'navbar-dark bg-danger' : 'navbar-dark bg-dark' ?>">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= htmlspecialchars($isAdminRequest ? '../index.php' : 'index.php', ENT_QUOTES) ?>">Perpustakaan Digital<?php if ($user && !empty($user['is_admin'])): ?> <span class="badge bg-warning text-dark ms-2">Admin</span><?php endif; ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?= $currentSection === 'browsing' ? 'active' : '' ?>" href="<?= htmlspecialchars($isAdminRequest ? '../index.php' : 'index.php', ENT_QUOTES) ?>">Jelajahi</a></li>
                <?php if ($user && empty($user['is_admin'])): ?>
                    <li class="nav-item"><a class="nav-link <?= $currentSection === 'favorites' ? 'active' : '' ?>" href="<?= htmlspecialchars($isAdminRequest ? '../favorites.php' : 'favorites.php', ENT_QUOTES) ?>">Favorit</a></li>
                    <li class="nav-item"><a class="nav-link <?= $currentSection === 'borrowed' ? 'active' : '' ?>" href="<?= htmlspecialchars($isAdminRequest ? '../borrowed.php' : 'borrowed.php', ENT_QUOTES) ?>">Peminjaman</a></li>
                <?php endif; ?>
                <?php if ($user && !empty($user['is_admin'])): ?>
                    <li class="nav-item"><a class="nav-link <?= $currentSection === 'admin' ? 'active' : '' ?>" href="<?= htmlspecialchars($isAdminRequest ? 'dashboard.php' : 'admin/dashboard.php', ENT_QUOTES) ?>">Admin</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav mb-2 mb-lg-0">
                <?php if ($user): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?= htmlspecialchars($user['name'], ENT_QUOTES) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($isAdminRequest ? '../profile.php' : 'profile.php', ENT_QUOTES) ?>">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= htmlspecialchars($isAdminRequest ? '../logout.php' : 'logout.php', ENT_QUOTES) ?>">Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($isAdminRequest ? '../login.php' : 'login.php', ENT_QUOTES) ?>">Masuk</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= htmlspecialchars($isAdminRequest ? '../register.php' : 'register.php', ENT_QUOTES) ?>">Daftar</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php if (!empty($pageTitle)): ?>
<div class="page-header bg-primary bg-opacity-10 border-bottom">
    <div class="container py-3">
        <h1 class="h4 mb-0 text-primary"><?= htmlspecialchars($pageTitle, ENT_QUOTES) ?></h1>
    </div>
</div>
<?php endif; ?>
<main class="flex-grow-1 py-4">
    <div class="container">
        <?php foreach ($flashMessages as $msg): ?>
            <div class="alert alert-<?= htmlspecialchars($msg['type'], ENT_QUOTES) ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($msg['message'], ENT_QUOTES) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endforeach; ?>

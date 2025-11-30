<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (current_user()) {
    logout_user();
    set_flash('success', 'Anda telah keluar.');
}

redirect('index.php');

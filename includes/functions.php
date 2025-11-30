<?php

// Generic helpers

function now_iso(): string
{
    return date('c'); // ISO 8601
}

function format_date(string $dateStr): string
{
    if (empty($dateStr)) {
        return '-';
    }
    try {
        $date = new DateTime($dateStr);
        $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
        return $date->format('d M Y H:i') . ' WIB'; // contoh: 27 Nov 2025 14:30 WIB
    } catch (Exception $e) {
        return htmlspecialchars($dateStr, ENT_QUOTES);
    }
}

function format_date_long(string $dateStr): string
{
    if (empty($dateStr)) {
        return '-';
    }
    try {
        $date = new DateTime($dateStr);
        $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
        return $date->format('d F Y H:i:s') . ' WIB'; // contoh: 27 November 2025 14:30:45 WIB
    } catch (Exception $e) {
        return htmlspecialchars($dateStr, ENT_QUOTES);
    }
}

function read_json(string $filename, $default = [])
{
    $path = DATA_PATH . '/' . $filename;
    if (!file_exists($path)) {
        return $default;
    }

    $contents = file_get_contents($path);
    if ($contents === false || trim($contents) === '') {
        return $default;
    }

    $data = json_decode($contents, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        return $default;
    }

    return $data;
}

function write_json(string $filename, $data): bool
{
    $path = DATA_PATH . '/' . $filename;
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($json === false) {
        return false;
    }

    return (bool) file_put_contents($path, $json, LOCK_EX);
}

function next_id(array $items): int
{
    $max = 0;
    foreach ($items as $item) {
        if (isset($item['id']) && (int) $item['id'] > $max) {
            $max = (int) $item['id'];
        }
    }

    return $max + 1;
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

// Flash messaging

function set_flash(string $type, string $message): void
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash_messages(): array
{
    if (!isset($_SESSION['flash'])) {
        return [];
    }

    $messages = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $messages;
}

// Simple detection for admin area based on script path
function is_admin_request(): bool
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    return strpos($script, '/admin/') !== false;
}

function asset_prefix(): string
{
    return is_admin_request() ? '../' : '';
}

// Get current section name for menu indicator
function get_current_section(): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $basename = basename($script, '.php');
    
    if (is_admin_request()) {
        if (strpos($script, '/admin/') !== false) {
            if ($basename === 'dashboard' || $basename === 'books' || $basename === 'categories' || 
                $basename === 'add-book' || $basename === 'edit-book' || $basename === 'delete-book' || 
                $basename === 'users' || $basename === 'logs' || $basename === 'reset-password') {
                return 'admin';
            }
        }
    }
    
    if ($basename === 'favorites') return 'favorites';
    if ($basename === 'borrowed') return 'borrowed';
    if ($basename === 'profile') return 'profile';
    if ($basename === 'book') return 'browsing';
    if ($basename === 'search') return 'browsing';
    if ($basename === 'read') return 'browsing';
    if ($basename === 'index') return 'browsing';
    
    return '';
}

// Get page title for context band
function get_page_title(): string
{
    $section = get_current_section();
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $basename = basename($script, '.php');
    
    if ($section === 'admin') {
        if ($basename === 'dashboard') return 'Dashboard Admin';
        if ($basename === 'books' || $basename === 'add-book' || $basename === 'edit-book' || $basename === 'delete-book') return 'Kelola Buku';
        if ($basename === 'categories') return 'Kelola Kategori';
        if ($basename === 'users') return 'Lihat Pengguna';
        if ($basename === 'logs') return 'Lihat Log';
        if ($basename === 'reset-password') return 'Reset Password';
        return 'Admin';
    }
    
    if ($basename === 'favorites') return 'Buku Favorit';
    if ($basename === 'borrowed') return 'Riwayat Peminjaman';
    if ($basename === 'profile') return 'Profil Saya';
    if ($basename === 'book') return 'Detail Buku';
    if ($basename === 'search') return 'Hasil Pencarian';
    if ($basename === 'read') return 'Membaca';
    if ($basename === 'index') return 'Jelajahi Buku';
    
    return '';
}

// Pagination helper
function paginate_array(array $items, int $page, int $perPage): array
{
    $total = count($items);
    $page = max($page, 1);
    $perPage = max($perPage, 1);

    $offset = ($page - 1) * $perPage;
    $pagedItems = array_slice($items, $offset, $perPage);

    return [
        'items'      => $pagedItems,
        'total'      => $total,
        'page'       => $page,
        'per_page'   => $perPage,
        'total_page' => (int) ceil($total / $perPage),
    ];
}

// BOOKS

function get_books(): array
{
    return read_json('books.json', []);
}

function get_book(int $id): ?array
{
    foreach (get_books() as $book) {
        if ((int) $book['id'] === $id) {
            return $book;
        }
    }

    return null;
}

function save_books(array $books): bool
{
    return write_json('books.json', $books);
}

function search_filter_books(?string $q, ?string $category, ?string $year): array
{
    $q = $q !== null ? trim($q) : '';
    $category = $category !== null ? trim($category) : '';
    $year = $year !== null ? trim($year) : '';

    $books = get_books();

    $books = array_filter($books, function ($book) use ($q, $category, $year) {
        if ($q !== '') {
            $haystack = strtolower(($book['title'] ?? '') . ' ' . ($book['author'] ?? ''));
            if (strpos($haystack, strtolower($q)) === false) {
                return false;
            }
        }

        if ($category !== '' && isset($book['category']) && (string) $book['category'] !== $category) {
            return false;
        }

        if ($year !== '' && isset($book['year']) && (string) $book['year'] !== $year) {
            return false;
        }

        return true;
    });

    // sort by created_at desc if available
    usort($books, function ($a, $b) {
        return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
    });

    return array_values($books);
}

function get_recommended_books(array $book, int $limit = 4): array
{
    $books = get_books();
    $category = $book['category'] ?? null;

    $filtered = array_filter($books, function ($b) use ($book, $category) {
        if ($b['id'] === $book['id']) {
            return false;
        }
        if ($category && ($b['category'] ?? null) === $category) {
            return true;
        }
        return false;
    });

    return array_slice(array_values($filtered), 0, $limit);
}

function update_book_rating(int $bookId): void
{
    $books = get_books();
    $reviews = get_reviews();

    $sum = 0;
    $count = 0;
    foreach ($reviews as $review) {
        if ((int) $review['book_id'] === $bookId) {
            $sum += (int) $review['rating'];
            $count++;
        }
    }

    foreach ($books as &$book) {
        if ((int) $book['id'] === $bookId) {
            $book['rating'] = $count > 0 ? round($sum / $count, 1) : 0.0;
            $book['updated_at'] = now_iso();
            break;
        }
    }

    save_books($books);
}

// CATEGORIES

function get_categories(): array
{
    return read_json('categories.json', []);
}

function save_categories(array $categories): bool
{
    return write_json('categories.json', $categories);
}

// USERS

function get_users(): array
{
    return read_json('users.json', []);
}

function save_users(array $users): bool
{
    return write_json('users.json', $users);
}

function find_user_by_email(string $email): ?array
{
    $email = strtolower(trim($email));
    foreach (get_users() as $user) {
        if (strtolower($user['email'] ?? '') === $email) {
            return $user;
        }
    }

    return null;
}

function find_user_by_id(int $id): ?array
{
    foreach (get_users() as $user) {
        if ((int) $user['id'] === $id) {
            return $user;
        }
    }

    return null;
}

// FAVORITES

function get_favorites(): array
{
    return read_json('favorites.json', []);
}

function save_favorites(array $favorites): bool
{
    return write_json('favorites.json', $favorites);
}

function get_user_favorites(int $userId): array
{
    return array_values(array_filter(get_favorites(), function ($fav) use ($userId) {
        return (int) $fav['user_id'] === $userId;
    }));
}

function is_favorite(int $userId, int $bookId): bool
{
    foreach (get_favorites() as $fav) {
        if ((int) $fav['user_id'] === $userId && (int) $fav['book_id'] === $bookId) {
            return true;
        }
    }

    return false;
}

function add_favorite(int $userId, int $bookId): void
{
    if (is_favorite($userId, $bookId)) {
        return;
    }

    $favorites = get_favorites();
    $favorites[] = [
        'id'        => next_id($favorites),
        'user_id'   => $userId,
        'book_id'   => $bookId,
        'created_at'=> now_iso(),
    ];

    save_favorites($favorites);
}

function remove_favorite(int $userId, int $bookId): void
{
    $favorites = array_filter(get_favorites(), function ($fav) use ($userId, $bookId) {
        return !((int) $fav['user_id'] === $userId && (int) $fav['book_id'] === $bookId);
    });

    save_favorites(array_values($favorites));
}

// BORROW

function get_borrow_records(): array
{
    return read_json('borrow.json', []);
}

function save_borrow_records(array $records): bool
{
    return write_json('borrow.json', $records);
}

function get_user_borrow_records(int $userId): array
{
    return array_values(array_filter(get_borrow_records(), function ($row) use ($userId) {
        return (int) $row['user_id'] === $userId;
    }));
}

function get_active_borrow(int $userId, int $bookId): ?array
{
    foreach (get_borrow_records() as $row) {
        if ((int) $row['user_id'] === $userId && (int) $row['book_id'] === $bookId && empty($row['returned_at'])) {
            return $row;
        }
    }
    return null;
}

function borrow_book(int $userId, int $bookId): void
{
    if (get_active_borrow($userId, $bookId)) {
        return;
    }

    $records = get_borrow_records();
    $records[] = [
        'id'          => next_id($records),
        'user_id'     => $userId,
        'book_id'     => $bookId,
        'borrowed_at' => now_iso(),
        'returned_at' => null,
    ];

    save_borrow_records($records);
}

function return_book(int $userId, int $bookId): void
{
    $records = get_borrow_records();
    foreach ($records as &$row) {
        if ((int) $row['user_id'] === $userId && (int) $row['book_id'] === $bookId && empty($row['returned_at'])) {
            $row['returned_at'] = now_iso();
        }
    }

    save_borrow_records($records);
}

// REVIEWS

function get_reviews(): array
{
    return read_json('reviews.json', []);
}

function save_reviews(array $reviews): bool
{
    return write_json('reviews.json', $reviews);
}

function get_book_reviews(int $bookId): array
{
    return array_values(array_filter(get_reviews(), function ($row) use ($bookId) {
        return (int) $row['book_id'] === $bookId;
    }));
}

function add_review(int $userId, int $bookId, int $rating, string $comment): void
{
    $reviews = get_reviews();
    $reviews[] = [
        'id'        => next_id($reviews),
        'user_id'   => $userId,
        'book_id'   => $bookId,
        'rating'    => max(1, min(5, $rating)),
        'comment'   => $comment,
        'created_at'=> now_iso(),
    ];

    save_reviews($reviews);
    update_book_rating($bookId);
}

// LOGS

function get_logs(): array
{
    return read_json('logs.json', []);
}

function save_logs(array $logs): bool
{
    return write_json('logs.json', $logs);
}

function add_log(?int $userId, string $action, string $entityType, ?int $entityId, string $message): void
{
    $logs = get_logs();
    $logs[] = [
        'id'          => next_id($logs),
        'user_id'     => $userId,
        'action'      => $action,
        'entity_type' => $entityType,
        'entity_id'   => $entityId,
        'message'     => $message,
        'created_at'  => now_iso(),
    ];

    save_logs($logs);
}

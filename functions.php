<?php
/**
 * functions.php — Lapisan penyimpanan, autentikasi, dan utilitas template.
 *
 * ALUR PENYIMPANAN (default, tanpa database — siap jalan di XAMPP/Laragon):
 *   1. Pendaftar PPDB -> data/ppdb.json   (ditulis oleh proses_ppdb.php)
 *   2. Artikel berita -> data/berita.json  (ditulis/dihapus oleh admin_dashboard.php)
 *   3. Akun admin     -> data/admin.json   (password disimpan dalam bentuk hash)
 *   4. Berkas upload  -> uploads/berkas/   (nama acak + validasi ekstensi & MIME)
 *   5. Status login   -> PHP session        (admin_login.php / admin_dashboard.php)
 *
 * ALTERNATIF MySQL: aktifkan fungsi db() (PDO) di bagian paling bawah, lalu
 * ganti pemanggilan json_baca()/json_tulis() dengan query INSERT/SELECT/DELETE.
 */
require_once __DIR__ . '/data.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('DATA_DIR', __DIR__ . '/data');
define('UPLOAD_DIR', __DIR__ . '/uploads/berkas');
define('UPLOAD_MAKS', 2097152); // 2 MB

const UPLOAD_DIPERBOLEHKAN = ['jpg', 'jpeg', 'png', 'pdf'];
const STATUS_PPDB = ['Baru', 'Diverifikasi', 'Diterima', 'Ditolak'];
const KATEGORI_BERITA = ['Pengumuman', 'Kegiatan', 'Prestasi', 'Akademik'];

/* =========================================================
 * Utilitas
 * ======================================================= */
function e(?string $teks): string
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

function slugify(string $teks): string
{
    $slug = preg_replace('/[^a-z0-9]+/', '-', strtolower(trim($teks)));
    return trim($slug, '-') ?: 'artikel';
}

function tanggal_id(string $tanggal): string
{
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $ts = strtotime($tanggal);
    return $ts
        ? (int)date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts)
        : $tanggal;
}

/* =========================================================
 * CSRF (pelindung form sederhana)
 * ======================================================= */
function csrf_token(): string
{
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(16));
}

function csrf_valid(): bool
{
    return isset($_POST['csrf']) && hash_equals(csrf_token(), (string)$_POST['csrf']);
}

/* =========================================================
 * Flash message (pesan sekali tampil lewat session)
 * ======================================================= */
function flash_set(string $tipe, string $pesan): void
{
    $_SESSION['flash'][] = ['tipe' => $tipe, 'pesan' => $pesan];
}

function flash_get(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

/* =========================================================
 * Baca / tulis file JSON
 * ======================================================= */
function json_baca(string $file, array $default = []): array
{
    $path = DATA_DIR . '/' . $file;
    if (!is_file($path)) {
        return $default;
    }
    $data = json_decode((string)file_get_contents($path), true);
    return is_array($data) ? $data : $default;
}

function json_tulis(string $file, array $data): bool
{
    if (!is_dir(DATA_DIR)) {
        @mkdir(DATA_DIR, 0775, true);
    }
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents(DATA_DIR . '/' . $file, $json, LOCK_EX) !== false;
}

/* =========================================================
 * PPDB
 * ======================================================= */
function ppdb_semua(): array
{
    return json_baca('ppdb.json');
}

function ppdb_cari(string $nomor): ?array
{
    foreach (ppdb_semua() as $row) {
        if (($row['nomor'] ?? '') === $nomor) {
            return $row;
        }
    }
    return null;
}

function ppdb_simpan(array $d): string
{
    $rows = ppdb_semua();

    // Nomor urut naik meski data pernah dihapus.
    $maks = 0;
    foreach ($rows as $row) {
        if (preg_match('/(\d+)$/', (string)($row['nomor'] ?? ''), $m)) {
            $maks = max($maks, (int)$m[1]);
        }
    }
    $nomor = 'PPDB-' . date('Y') . '-' . str_pad((string)($maks + 1), 4, '0', STR_PAD_LEFT);

    $rows[] = [
        'nomor'  => $nomor,
        'tgl'    => date('Y-m-d H:i:s'),
        'nama'   => $d['nama'],
        'nik'    => $d['nik'],
        'unit'   => $d['unit'],
        'asal'   => $d['asal'],
        'ortu'   => $d['ortu'],
        'wa'     => $d['wa'],
        'berkas' => $d['berkas'],
        'status' => 'Baru',
    ];
    json_tulis('ppdb.json', $rows);
    return $nomor;
}

function ppdb_status(string $nomor, string $status): bool
{
    if (!in_array($status, STATUS_PPDB, true)) {
        return false;
    }
    $rows = ppdb_semua();
    foreach ($rows as $i => $row) {
        if (($row['nomor'] ?? '') === $nomor) {
            $rows[$i]['status'] = $status;
            return json_tulis('ppdb.json', $rows);
        }
    }
    return false;
}

function ppdb_hapus(string $nomor): bool
{
    $rows = array_filter(ppdb_semua(), static fn ($r) => ($r['nomor'] ?? '') !== $nomor);
    return json_tulis('ppdb.json', array_values($rows));
}

/* =========================================================
 * Berita
 * ======================================================= */
function berita_semua(): array
{
    $rows = json_baca('berita.json');
    // Terbaru di atas: tanggal menurun, lalu id menurun.
    usort($rows, static fn ($a, $b) =>
        [($b['tanggal'] ?? ''), ($b['id'] ?? 0)] <=> [($a['tanggal'] ?? ''), ($a['id'] ?? 0)]);
    return $rows;
}

function berita_cari(string $slug): ?array
{
    foreach (berita_semua() as $row) {
        if (($row['slug'] ?? '') === $slug) {
            return $row;
        }
    }
    return null;
}

function berita_simpan(array $d): int
{
    $rows = json_baca('berita.json');
    $id = $rows ? (int)max(array_column($rows, 'id')) + 1 : 1;

    $rows[] = [
        'id'        => $id,
        'slug'      => slugify($d['judul']) . '-' . $id,
        'judul'     => $d['judul'],
        'kategori'  => $d['kategori'],
        'tanggal'   => $d['tanggal'],
        'penulis'   => $d['penulis'] !== '' ? $d['penulis'] : 'Admin Yayasan',
        'gambar'    => $d['gambar'] !== ''
            ? $d['gambar']
            : 'https://picsum.photos/seed/berita' . $id . '/1200/630',
        'ringkasan' => $d['ringkasan'],
        'isi'       => $d['isi'],
    ];
    json_tulis('berita.json', $rows);
    return $id;
}

function berita_hapus(int $id): bool
{
    $rows = array_filter(json_baca('berita.json'), static fn ($b) => (int)($b['id'] ?? 0) !== $id);
    return json_tulis('berita.json', array_values($rows));
}

/* =========================================================
 * Admin (login sederhana berbasis session)
 * ======================================================= */
function admin_akun(): array
{
    return json_baca('admin.json', ['username' => 'admin', 'password' => '']);
}

function admin_login(string $username, string $password): bool
{
    $akun = admin_akun();
    return hash_equals((string)$akun['username'], $username)
        && password_verify($password, (string)$akun['password']);
}

function admin_logged(): bool
{
    return !empty($_SESSION['admin']);
}

function require_admin(): void
{
    if (!admin_logged()) {
        header('Location: admin_login.php');
        exit;
    }
}

/** Kunci login 5 menit setelah 5 kali salah password. */
function login_terkunci(): bool
{
    return (int)($_SESSION['login_gagal'] ?? 0) >= 5
        && time() < (int)($_SESSION['login_locked'] ?? 0);
}

function login_gagal(): void
{
    $_SESSION['login_gagal'] = (int)($_SESSION['login_gagal'] ?? 0) + 1;
    if ($_SESSION['login_gagal'] >= 5) {
        $_SESSION['login_locked'] = time() + 300;
    }
}

/* =========================================================
 * ALTERNATIF MySQL (opsional) — aktifkan bila diperlukan:
 *
 *   function db(): PDO
 *   {
 *       static $pdo = null;
 *       if ($pdo === null) {
 *           $pdo = new PDO(
 *               'mysql:host=localhost;dbname=yayasan;charset=utf8mb4',
 *               'root', '',
 *               [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
 *                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
 *           );
 *       }
 *       return $pdo;
 *   }
 *
 *   Contoh penyimpanan PPDB dengan MySQL:
 *   $st = db()->prepare('INSERT INTO ppdb
 *       (nomor, nama, nik, unit, asal, ortu, wa, berkas, status, tgl)
 *       VALUES (?, ?, ?, ?, ?, ?, ?, ?, "Baru", NOW())');
 *   $st->execute([$nomor, $nama, $nik, $unit, $asal, $ortu, $wa, $berkas]);
 * ========================================================= */

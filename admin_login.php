<?php
require_once __DIR__ . '/functions.php';
if (admin_logged()) {
    header('Location: admin_dashboard.php');
    exit;
}
$pageTitle = 'Login Admin';
$active = 'admin';

$err = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if (login_terkunci()) {
        $err['_umum'] = 'Terlalu banyak percobaan gagal. Akun dikunci sementara — coba lagi dalam 5 menit.';
    } elseif (!csrf_valid()) {
        $err['_umum'] = 'Sesi berakhir, silakan coba lagi.';
    } elseif ($username === '' || $password === '') {
        $err['_umum'] = 'Username dan password wajib diisi.';
    } elseif (admin_login($username, $password)) {
        session_regenerate_id(true);        // cegah session fixation
        $_SESSION['admin'] = $username;
        unset($_SESSION['login_gagal'], $_SESSION['login_locked']);
        header('Location: admin_dashboard.php');
        exit;
    } else {
        login_gagal();
        $sisa = 5 - (int)($_SESSION['login_gagal']);
        $err['_umum'] = $sisa > 0
            ? 'Username atau password salah. Sisa percobaan: ' . $sisa . '.'
            : 'Terlalu banyak percobaan. Coba lagi dalam 5 menit.';
    }
}

include 'header.php';
?>
<section class="bg-navy text-white py-14">
  <div class="max-w-6xl mx-auto px-4">
    <h1 class="text-4xl font-bold">Login pengurus</h1>
    <p class="mt-3 text-slate-200 max-w-2xl">Area khusus admin yayasan untuk memantau pendaftar PPDB dan mengelola berita.</p>
  </div>
</section>

<section class="max-w-md mx-auto px-4 py-16">
  <form method="post" action="admin_login.php" class="p-6 md:p-8 rounded-2xl bg-mist space-y-5" novalidate>
    <div class="flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-lg bg-navy text-emerald">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 11h14v10H5z"/></svg>
      </span>
      <h2 class="text-2xl font-bold text-navy">Masuk ke dashboard</h2>
    </div>

    <?php if (isset($err['_umum'])): ?>
      <p role="alert" class="p-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm"><?= e($err['_umum']) ?></p>
    <?php endif; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div>
      <label for="username" class="block text-sm font-medium text-navy">Username</label>
      <input id="username" name="username" type="text" autocomplete="username" required
        class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-navy">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required
        class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
    </div>

    <button class="w-full px-6 py-3 rounded-md bg-emerald-dark text-white font-semibold hover:bg-navy focus:outline-2 focus:outline-offset-2 focus:outline-emerald">Masuk</button>

    <div class="p-3 rounded-md bg-white border border-dashed border-emerald text-xs text-slate-600">
      <strong class="text-navy">Akun demo template:</strong> username <code class="font-mono text-emerald-dark">admin</code> &middot; password <code class="font-mono text-emerald-dark">admin123</code>.
      Ubah di <code class="font-mono">data/admin.json</code> sebelum dipakai produksi.
    </div>
  </form>
</section>
<?php include 'footer.php'; ?>

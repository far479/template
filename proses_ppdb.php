<?php
/**
 * proses_ppdb.php — Backend PPDB.
 * 1. Validasi data calon siswa (server-side).
 * 2. Jika gagal  -> kembali ke ppdb.php dengan pesan error + isi form lama.
 * 3. Jika sukses -> simpan berkas ke uploads/berkas/ dan data ke data/ppdb.json,
 *                   lalu tampilkan halaman sukses beserta nomor pendaftaran.
 */
require_once __DIR__ . '/functions.php';
$pageTitle = 'Hasil Pendaftaran PPDB';
$active = 'ppdb';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ppdb.php');
    exit;
}

$err = [];
if (!csrf_valid()) {
    $err['_umum'] = 'Sesi formulir telah berakhir. Silakan isi ulang formulir.';
}

/* ---------- Ambil & bersihkan input ---------- */
$nama = trim((string)($_POST['nama'] ?? ''));
$nikRaw = trim((string)($_POST['nik'] ?? ''));
$nik = preg_replace('/\D/', '', $nikRaw);
$unit = trim((string)($_POST['unit'] ?? ''));
$asal = trim((string)($_POST['asal_sekolah'] ?? ''));
$ortu = trim((string)($_POST['nama_ortu'] ?? ''));
$wa = trim((string)($_POST['wa'] ?? ''));
$waNorm = str_replace([' ', '-', '(', ')'], '', $wa);

$unitValid = array_values(array_unique(array_map(static fn ($u) => $u[1], $units)));

/* ---------- Validasi dasar ---------- */
if (mb_strlen($nama) < 3) {
    $err['nama'] = 'Nama lengkap minimal 3 karakter.';
}
if (!preg_match('/^\d{10,16}$/', $nik)) {
    $err['nik'] = 'NIK/NISN harus berupa 10–16 digit angka.';
}
if (!in_array($unit, $unitValid, true)) {
    $err['unit'] = 'Pilih salah satu unit pendidikan yang tersedia.';
}
if (mb_strlen($asal) < 2) {
    $err['asal_sekolah'] = 'Asal sekolah wajib diisi.';
}
if (mb_strlen($ortu) < 3) {
    $err['nama_ortu'] = 'Nama orang tua/wali wajib diisi.';
}
if (!preg_match('/^(08|628)[0-9]{7,12}$/', $waNorm)) {
    $err['wa'] = 'Nomor WhatsApp tidak valid (contoh: 081234567890).';
}

/* ---------- Validasi berkas ---------- */
$f = $_FILES['berkas'] ?? null;
$ext = '';
if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    $err['berkas'] = 'Berkas pendukung wajib diunggah.';
} elseif ($f['error'] !== UPLOAD_ERR_OK) {
    $err['berkas'] = 'Gagal mengunggah berkas (kode error ' . (int)$f['error'] . ').';
} elseif ($f['size'] > UPLOAD_MAKS) {
    $err['berkas'] = 'Ukuran berkas maksimal 2 MB.';
} else {
    $ext = strtolower(pathinfo((string)$f['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, UPLOAD_DIPERBOLEHKAN, true)) {
        $err['berkas'] = 'Format berkas harus JPG, PNG, atau PDF.';
    } elseif (function_exists('finfo_open')) {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png', 'application/pdf'], true)) {
            $err['berkas'] = 'Isi berkas tidak sesuai dengan formatnya.';
        }
    }
}

/* ---------- Gagal validasi: kembalikan ke form ---------- */
if ($err) {
    $_SESSION['ppdb_err'] = $err;
    $_SESSION['ppdb_old'] = [
        'nama' => $nama,
        'nik' => $nikRaw,
        'unit' => $unit,
        'asal_sekolah' => $asal,
        'nama_ortu' => $ortu,
        'wa' => $wa,
    ];
    header('Location: ppdb.php#form');
    exit;
}

/* ---------- Simpan berkas (nama acak, tanpa eksekusi script) ---------- */
if (!is_dir(UPLOAD_DIR)) {
    @mkdir(UPLOAD_DIR, 0775, true);
}
$namaBerkas = bin2hex(random_bytes(16)) . '.' . $ext;
if (!move_uploaded_file($f['tmp_name'], UPLOAD_DIR . '/' . $namaBerkas)) {
    $_SESSION['ppdb_err'] = ['_umum' => 'Berkas gagal disimpan. Coba lagi atau hubungi panitia.'];
    header('Location: ppdb.php#form');
    exit;
}

/* ---------- Simpan data pendaftar ke data/ppdb.json ---------- */
$nomor = ppdb_simpan([
    'nama' => $nama,
    'nik' => $nik,
    'unit' => $unit,
    'asal' => $asal,
    'ortu' => $ortu,
    'wa' => $waNorm,
    'berkas' => 'uploads/berkas/' . $namaBerkas,
]);

include 'header.php';
?>
<section class="bg-navy text-white py-14">
  <div class="max-w-6xl mx-auto px-4">
    <h1 class="text-4xl font-bold">Pendaftaran terkirim</h1>
    <p class="mt-3 text-slate-200 max-w-2xl">Terima kasih, formulir Anda telah kami terima dan sedang menunggu verifikasi panitia.</p>
  </div>
</section>

<section class="max-w-3xl mx-auto px-4 py-16">
  <div class="p-6 md:p-8 rounded-2xl bg-mist border-t-4 border-emerald">
    <div class="flex items-center gap-4">
      <span class="grid place-items-center w-12 h-12 rounded-full bg-emerald text-white text-2xl font-bold" aria-hidden="true">&#10003;</span>
      <div>
        <h2 class="text-2xl font-bold text-navy">Berhasil! Simpan nomor pendaftaran Anda</h2>
        <p class="text-sm text-slate-500">Nomor ini digunakan saat cek status dan wawancara.</p>
      </div>
    </div>

    <p class="mt-6 text-center text-3xl md:text-4xl font-bold tracking-wider text-navy bg-white rounded-xl border-2 border-dashed border-emerald py-6"><?= e($nomor) ?></p>

    <dl class="mt-6 grid sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
      <div><dt class="text-slate-500">Nama calon siswa</dt><dd class="font-semibold text-navy"><?= e($nama) ?></dd></div>
      <div><dt class="text-slate-500">NIK / NISN</dt><dd class="font-semibold text-navy"><?= e($nik) ?></dd></div>
      <div><dt class="text-slate-500">Unit pilihan</dt><dd class="font-semibold text-navy"><?= e($unit) ?></dd></div>
      <div><dt class="text-slate-500">Asal sekolah</dt><dd class="font-semibold text-navy"><?= e($asal) ?></dd></div>
      <div><dt class="text-slate-500">Orang tua / wali</dt><dd class="font-semibold text-navy"><?= e($ortu) ?></dd></div>
      <div><dt class="text-slate-500">WhatsApp</dt><dd class="font-semibold text-navy"><?= e($waNorm) ?></dd></div>
      <div class="sm:col-span-2"><dt class="text-slate-500">Berkas terunggah</dt>
        <dd><a class="font-semibold text-emerald-dark hover:underline" href="<?= e('uploads/berkas/' . $namaBerkas) ?>" target="_blank" rel="noopener"><?= e($namaBerkas) ?></a></dd>
      </div>
    </dl>

    <p role="status" class="mt-6 p-3 rounded-md bg-emerald/15 text-emerald-dark text-sm font-medium">
      Data tersimpan pada <code class="font-mono">data/ppdb.json</code> dengan status <strong>Baru</strong>. Panitia akan menghubungi Anda via WhatsApp.
    </p>

    <div class="mt-6 flex flex-wrap gap-3">
      <button onclick="window.print()" class="px-5 py-2.5 rounded-md bg-navy text-white font-semibold hover:bg-emerald-dark">Cetak bukti</button>
      <a href="ppdb.php" class="px-5 py-2.5 rounded-md border border-slate-300 font-semibold text-slate-600 hover:bg-white">Daftar lagi</a>
      <a href="index.php" class="px-5 py-2.5 rounded-md border border-slate-300 font-semibold text-slate-600 hover:bg-white">Kembali ke beranda</a>
    </div>
  </div>
</section>
<?php include 'footer.php'; ?>

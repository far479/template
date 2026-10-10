<?php
require_once __DIR__ . '/functions.php';
$slug = trim((string)($_GET['slug'] ?? ''));
$artikel = $slug !== '' ? berita_cari($slug) : null;
$pageTitle = $artikel ? $artikel['judul'] : 'Berita Tidak Ditemukan';
$active = 'berita';

include 'header.php';

if (!$artikel): ?>
  <section class="max-w-3xl mx-auto px-4 py-24 text-center">
    <p class="text-6xl font-bold text-emerald-dark">404</p>
    <h1 class="mt-4 text-3xl font-bold text-navy">Berita tidak ditemukan</h1>
    <p class="mt-3 text-slate-500">Artikel yang Anda cari mungkin sudah dihapus atau tautannya salah.</p>
    <div class="mt-6 flex justify-center gap-3">
      <a href="berita.php" class="px-5 py-2.5 rounded-md bg-navy text-white font-semibold hover:bg-emerald-dark">Lihat semua berita</a>
      <a href="index.php" class="px-5 py-2.5 rounded-md border border-slate-300 font-semibold text-slate-600 hover:bg-mist">Beranda</a>
    </div>
  </section>
<?php
  include 'footer.php';
  return;
endif;

$paragraf = preg_split('/\R\s*\R/', trim((string)$artikel['isi']));
$terkait = array_slice(
    array_values(array_filter(berita_semua(), static fn ($b) => ($b['slug'] ?? '') !== $slug)),
    0,
    3
);
?>
<section class="bg-navy text-white py-10">
  <div class="max-w-4xl mx-auto px-4">
    <nav class="text-sm text-slate-300" aria-label="Breadcrumb">
      <a href="index.php" class="hover:text-emerald">Beranda</a> <span aria-hidden="true">/</span>
      <a href="berita.php" class="hover:text-emerald">Berita</a> <span aria-hidden="true">/</span>
      <span class="text-slate-100"><?= e($artikel['kategori'] ?? 'Info') ?></span>
    </nav>
    <span class="mt-4 inline-block px-3 py-1 rounded-full bg-emerald text-navy text-xs font-bold uppercase tracking-wide"><?= e($artikel['kategori'] ?? 'Info') ?></span>
    <h1 class="mt-3 text-3xl md:text-4xl font-bold leading-tight"><?= e($artikel['judul']) ?></h1>
    <p class="mt-4 text-sm text-slate-300">
      Ditulis oleh <span class="font-semibold text-emerald"><?= e($artikel['penulis'] ?? 'Admin Yayasan') ?></span>
      &middot; <time datetime="<?= e($artikel['tanggal'] ?? '') ?>"><?= e(tanggal_id($artikel['tanggal'] ?? '')) ?></time>
      &middot; <?= e($site['nama']) ?>
    </p>
  </div>
</section>

<article class="max-w-4xl mx-auto px-4 py-12">
  <img src="<?= e($artikel['gambar']) ?>" alt="Ilustrasi: <?= e($artikel['judul']) ?>" class="w-full h-64 md:h-96 object-cover rounded-2xl" loading="lazy">

  <div class="mt-8 prose-custom">
    <p class="text-lg font-medium text-navy leading-relaxed"><?= e($artikel['ringkasan'] ?? '') ?></p>
    <?php foreach ($paragraf as $p): ?>
      <p class="mt-5 text-slate-700 leading-relaxed"><?= e($p) ?></p>
    <?php endforeach; ?>
  </div>

  <div class="mt-10 p-5 rounded-xl bg-mist flex flex-wrap items-center justify-between gap-4">
    <p class="text-sm text-slate-600">Informasi resmi lainnya tersedia di halaman berita.</p>
    <div class="flex gap-3">
      <a href="berita.php" class="px-4 py-2 rounded-md bg-navy text-white text-sm font-semibold hover:bg-emerald-dark">&larr; Semua berita</a>
      <a href="kontak.php" class="px-4 py-2 rounded-md border border-slate-300 text-sm font-semibold text-slate-600 hover:bg-white">Hubungi kami</a>
    </div>
  </div>
</article>

<?php if ($terkait): ?>
<section class="bg-mist py-14">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-navy">Berita lainnya</h2>
    <div class="mt-6 grid gap-6 md:grid-cols-3">
      <?php foreach ($terkait as $b): ?>
        <article class="bg-white rounded-xl overflow-hidden border border-slate-200 hover:shadow-md transition-shadow">
          <a href="detail_berita.php?slug=<?= e($b['slug']) ?>">
            <img src="<?= e($b['gambar']) ?>" alt="<?= e($b['judul']) ?>" class="h-36 w-full object-cover" loading="lazy">
          </a>
          <div class="p-4">
            <p class="text-xs text-slate-500"><?= e($b['kategori'] ?? 'Info') ?> &middot; <?= e(tanggal_id($b['tanggal'] ?? '')) ?></p>
            <h3 class="mt-1 font-semibold text-navy leading-snug">
              <a href="detail_berita.php?slug=<?= e($b['slug']) ?>" class="hover:text-emerald-dark"><?= e($b['judul']) ?></a>
            </h3>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php include 'footer.php'; ?>

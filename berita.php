<?php
require_once __DIR__ . '/functions.php';
$pageTitle = 'Berita & Pengumuman';
$active = 'berita';

$perHalaman = 6;
$semua = berita_semua();
$totalData = count($semua);
$totalHalaman = max(1, (int)ceil($totalData / $perHalaman));
$halaman = min(max(1, (int)($_GET['page'] ?? 1)), $totalHalaman);
$mulai = ($halaman - 1) * $perHalaman;
$daftar = array_slice($semua, $mulai, $perHalaman);

include 'header.php';

$warnaKategori = [
    'Pengumuman' => 'bg-emerald/10 text-emerald-dark',
    'Kegiatan' => 'bg-amber-100 text-amber-700',
    'Prestasi' => 'bg-navy/10 text-navy',
    'Akademik' => 'bg-sky-100 text-sky-700',
];
?>
<section class="bg-navy text-white py-14">
  <div class="max-w-6xl mx-auto px-4">
    <h1 class="text-4xl font-bold">Berita &amp; pengumuman</h1>
    <p class="mt-3 text-slate-200 max-w-2xl">Kegiatan, prestasi, dan informasi resmi terbaru dari <?= e($site['nama']) ?>. Total <?= $totalData ?> artikel.</p>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-16">
  <?php if (!$daftar): ?>
    <div class="p-10 rounded-2xl bg-mist text-center">
      <p class="font-semibold text-navy">Belum ada artikel.</p>
      <p class="mt-1 text-sm text-slate-500">Artikel baru dapat ditambahkan melalui panel admin.</p>
      <a href="admin_login.php" class="mt-4 inline-block px-5 py-2 rounded-md bg-navy text-white text-sm font-semibold hover:bg-emerald-dark">Masuk admin</a>
    </div>
  <?php else: ?>
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($daftar as $b): ?>
        <article class="bg-white rounded-xl overflow-hidden border border-slate-200 flex flex-col hover:shadow-md transition-shadow">
          <a href="detail_berita.php?slug=<?= e($b['slug']) ?>" class="block">
            <img src="<?= e($b['gambar']) ?>" alt="<?= e($b['judul']) ?>" class="h-44 w-full object-cover" loading="lazy">
          </a>
          <div class="p-5 flex flex-col flex-1">
            <p class="text-xs text-slate-500">
              <span class="px-2 py-0.5 rounded-full font-semibold <?= $warnaKategori[$b['kategori'] ?? ''] ?? 'bg-slate-100 text-slate-600' ?>"><?= e($b['kategori'] ?? 'Info') ?></span>
              &nbsp;&middot;&nbsp; <time datetime="<?= e($b['tanggal'] ?? '') ?>"><?= e(tanggal_id($b['tanggal'] ?? '')) ?></time>
            </p>
            <h2 class="mt-2 font-semibold text-navy leading-snug">
              <a href="detail_berita.php?slug=<?= e($b['slug']) ?>" class="hover:text-emerald-dark"><?= e($b['judul']) ?></a>
            </h2>
            <p class="mt-2 text-sm flex-1"><?= e($b['ringkasan'] ?? '') ?></p>
            <a href="detail_berita.php?slug=<?= e($b['slug']) ?>" class="mt-4 text-sm font-semibold text-emerald-dark hover:underline">Baca selengkapnya &rarr;</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if ($totalHalaman > 1): ?>
      <nav class="mt-10 flex justify-center items-center gap-2" aria-label="Navigasi halaman berita">
        <?php if ($halaman > 1): ?>
          <a href="?page=<?= $halaman - 1 ?>" class="px-3 h-9 grid place-items-center rounded-md text-sm font-medium bg-white border border-slate-300 text-slate-600 hover:border-emerald-dark">&larr; Sebelumnya</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $totalHalaman; $p++): ?>
          <a href="?page=<?= $p ?>" aria-current="<?= $p === $halaman ? 'page' : 'false' ?>"
            class="w-9 h-9 grid place-items-center rounded-md text-sm font-medium <?= $p === $halaman ? 'bg-navy text-white' : 'bg-white border border-slate-300 text-slate-600 hover:border-emerald-dark' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($halaman < $totalHalaman): ?>
          <a href="?page=<?= $halaman + 1 ?>" class="px-3 h-9 grid place-items-center rounded-md text-sm font-medium bg-white border border-slate-300 text-slate-600 hover:border-emerald-dark">Berikutnya &rarr;</a>
        <?php endif; ?>
      </nav>
      <p class="mt-3 text-center text-xs text-slate-500">Halaman <?= $halaman ?> dari <?= $totalHalaman ?></p>
    <?php endif; ?>
  <?php endif; ?>
</section>
<?php include 'footer.php'; ?>

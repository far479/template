<?php require_once __DIR__ . '/functions.php';
$pageTitle = 'Beranda'; $active = 'index'; include 'header.php';
$keunggulan = [['Pengajar Berpengalaman', '120+ guru tersertifikasi dan terus dilatih.', '120+'], ['Kurikulum Terpadu', 'Kurikulum nasional dipadu pendidikan karakter.', '4 jenjang'], ['Fasilitas Modern', 'Lab, perpustakaan digital, dan area olahraga.', '30+ ruang'], ['Prestasi Siswa', 'Juara olimpiade dan lomba tingkat provinsi.', '250+ medali']];
$berita = array_slice(berita_semua(), 0, 3); // 3 artikel terbaru dari data/berita.json
$testimoni = [['Ibu Rina', 'Orang tua siswa SD', 'Anak saya jadi percaya diri dan rajin berdoa. Guru-gurunya sabar dan komunikatif.'], ['Pak Dodi', 'Orang tua siswa SMP', 'Laporan perkembangan jelas, kegiatan pembelajarannya juga menyenangkan.'], ['Salsa', 'Alumni SMA 2024', 'Bimbingan karier di sekolah membantu saya lolos kuliah di PTN pilihan.']];
?>
<section class="bg-navy text-white relative overflow-hidden">
  <img src="https://picsum.photos/seed/sekolah/1600/900" alt="" class="absolute inset-0 w-full h-full object-cover opacity-20">
  <div class="relative max-w-6xl mx-auto px-4 py-20 md:py-28 grid md:grid-cols-5 gap-10 items-center">
    <div class="md:col-span-3">
      <h1 class="text-4xl md:text-5xl font-bold leading-tight">Tempat anak bertumbuh cerdas, berakhlak, dan siap menghadapi masa depan.</h1>
      <p class="mt-5 text-lg text-slate-200 max-w-xl">Dari TK sampai SMA/SMK, <?= $site['nama'] ?> mendampingi setiap jenjang dengan guru berpengalaman dan lingkungan belajar yang hangat.</p>
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="ppdb.php" class="px-6 py-3 rounded-md bg-emerald text-navy font-semibold hover:bg-white">Daftar Sekarang</a>
        <a href="unit.php" class="px-6 py-3 rounded-md border border-white/60 font-semibold hover:bg-white hover:text-navy">Jelajahi Unit</a>
      </div>
    </div>
    <div class="md:col-span-2 rounded-2xl overflow-hidden border-4 border-emerald shadow-xl">
      <img src="https://picsum.photos/seed/siswa/700/600" alt="Siswa belajar bersama di kelas" class="w-full h-72 md:h-96 object-cover">
    </div>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-16">
  <h2 class="text-3xl font-bold text-navy">Mengapa memilih kami</h2>
  <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($keunggulan as [$t, $d, $n]): ?>
      <div class="p-6 rounded-xl bg-mist border-t-4 border-emerald">
        <p class="text-3xl font-bold text-emerald-dark"><?= $n ?></p>
        <h3 class="mt-2 font-semibold text-navy"><?= $t ?></h3>
        <p class="mt-1 text-sm"><?= $d ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="bg-mist py-16">
  <div class="max-w-6xl mx-auto px-4">
    <div class="flex items-end justify-between flex-wrap gap-3">
      <h2 class="text-3xl font-bold text-navy">Unit pendidikan</h2>
      <a href="unit.php" class="text-emerald-dark font-semibold hover:underline">Lihat semua unit</a>
    </div>
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <?php foreach ($units as $i => [$nama, $kode, $desc, $usia]): ?>
        <article class="bg-white rounded-xl overflow-hidden shadow-sm flex flex-col">
          <img src="https://picsum.photos/seed/unit<?= $i ?>/500/320" alt="Kegiatan belajar <?= $nama ?>" class="h-40 w-full object-cover" loading="lazy">
          <div class="p-5 flex flex-col flex-1">
            <h3 class="font-semibold text-navy"><?= $nama ?></h3>
            <p class="text-xs text-emerald-dark font-medium mt-1"><?= $usia ?></p>
            <p class="text-sm mt-2 flex-1"><?= $desc ?></p>
            <a href="unit.php#<?= strtolower(str_replace('/', '-', $kode)) ?>" class="mt-4 inline-block text-center px-4 py-2 rounded-md bg-navy text-white text-sm font-semibold hover:bg-emerald-dark">Lihat detail</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-16">
  <div class="flex items-end justify-between flex-wrap gap-3">
    <h2 class="text-3xl font-bold text-navy">Berita dan pengumuman</h2>
    <a href="berita.php" class="text-emerald-dark font-semibold hover:underline">Lihat semua berita</a>
  </div>
  <div class="mt-8 grid gap-6 md:grid-cols-3">
    <?php foreach ($berita as $b): ?>
      <article class="rounded-xl border border-slate-200 overflow-hidden hover:shadow-md transition-shadow bg-white">
        <a href="detail_berita.php?slug=<?= e($b['slug']) ?>">
          <img src="<?= e($b['gambar']) ?>" alt="<?= e($b['judul']) ?>" class="h-44 w-full object-cover" loading="lazy">
        </a>
        <div class="p-5">
          <p class="text-xs text-slate-500"><span class="text-emerald-dark font-semibold"><?= e($b['kategori']) ?></span> &middot; <time datetime="<?= e($b['tanggal']) ?>"><?= e(tanggal_id($b['tanggal'])) ?></time></p>
          <h3 class="mt-2 font-semibold text-navy"><?= e($b['judul']) ?></h3>
          <p class="mt-2 text-sm"><?= e($b['ringkasan']) ?></p>
          <a href="detail_berita.php?slug=<?= e($b['slug']) ?>" class="mt-3 inline-block text-sm font-semibold text-emerald-dark hover:underline">Baca selengkapnya</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="bg-navy py-16 text-white">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-bold">Kata orang tua dan alumni</h2>
    <div class="mt-8 grid gap-6 md:grid-cols-3">
      <?php foreach ($testimoni as [$nama, $peran, $isi]): ?>
        <figure class="p-6 rounded-xl bg-navy-soft">
          <blockquote class="text-slate-200">&ldquo;<?= $isi ?>&rdquo;</blockquote>
          <figcaption class="mt-4 text-sm"><span class="font-semibold text-emerald"><?= $nama ?></span>, <?= $peran ?></figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
    <div class="mt-10 grid grid-cols-2 md:grid-cols-4 gap-3">
      <?php for ($g = 0; $g < 4; $g++): ?>
        <img src="https://picsum.photos/seed/galeri<?= $g ?>/400/300" alt="Dokumentasi kegiatan sekolah <?= $g + 1 ?>" class="rounded-lg w-full h-32 sm:h-40 object-cover" loading="lazy">
      <?php endfor; ?>
    </div>
  </div>
</section>
<?php include 'footer.php'; ?>

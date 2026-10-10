<?php $pageTitle = 'Unit Pendidikan'; $active = 'unit'; include 'header.php';
$fasilitas = ['TK' => ['Area bermain aman', 'Kelas sensori', 'Kegiatan seni'], 'SD' => ['Perpustakaan', 'Lab komputer', 'Ekstrakurikuler pramuka'], 'SMP' => ['Lab IPA', 'Klub robotik', 'Program tahfidz'], 'SMA/SMK' => ['Lab komputer dan jaringan', 'Bimbingan kuliah', 'Magang industri']];
?>
<section class="bg-navy text-white py-14"><div class="max-w-6xl mx-auto px-4">
  <h1 class="text-4xl font-bold">Unit pendidikan</h1>
  <p class="mt-3 text-slate-200 max-w-2xl">Empat jenjang dalam satu naungan, dengan kurikulum yang saling menyambung.</p>
</div></section>

<section class="max-w-6xl mx-auto px-4 py-16 space-y-10">
  <?php foreach ($units as $i => [$nama, $kode, $desc, $usia]): ?>
    <article id="<?= strtolower(str_replace('/', '-', $kode)) ?>" class="scroll-mt-24 grid md:grid-cols-5 gap-6 rounded-2xl border border-slate-200 overflow-hidden">
      <img src="https://picsum.photos/seed/unitdetail<?= $i ?>/700/500" alt="Gedung <?= $nama ?>" class="md:col-span-2 w-full h-56 md:h-full object-cover" loading="lazy">
      <div class="md:col-span-3 p-6">
        <h2 class="text-2xl font-bold text-navy"><?= $nama ?></h2>
        <p class="text-sm font-medium text-emerald-dark mt-1"><?= $usia ?></p>
        <p class="mt-3"><?= $desc ?></p>
        <ul class="mt-4 flex flex-wrap gap-2">
          <?php foreach ($fasilitas[$kode] as $f): ?><li class="px-3 py-1 rounded-full bg-emerald/10 text-emerald-dark text-sm"><?= $f ?></li><?php endforeach; ?>
        </ul>
        <a href="ppdb.php?unit=<?= urlencode($kode) ?>#form" class="mt-6 inline-block px-5 py-2 rounded-md bg-navy text-white font-semibold hover:bg-emerald-dark">Daftar ke <?= $kode ?></a>
      </div>
    </article>
  <?php endforeach; ?>
</section>
<?php include 'footer.php'; ?>

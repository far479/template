<?php $pageTitle = 'Profil Yayasan'; $active = 'profil'; include 'header.php';
$sejarah = [['1998', 'Yayasan didirikan dan membuka TK pertama dengan 40 siswa.'], ['2005', 'SD dan SMP dibuka seiring kebutuhan masyarakat sekitar.'], ['2012', 'SMA/SMK diresmikan dengan jurusan IPA, IPS, dan TKJ.'], ['2024', 'Perpustakaan digital dan laboratorium komputer baru beroperasi.']];
$misi = ['Menyelenggarakan pendidikan bermutu yang berlandaskan akhlak mulia.', 'Mengembangkan guru melalui pelatihan berkelanjutan.', 'Menyediakan fasilitas belajar yang aman dan modern.', 'Menjalin kemitraan erat dengan orang tua dan masyarakat.'];
$struktur = [['H. Ahmad Fauzi, M.Pd.', 'Ketua Yayasan'], ['Dra. Siti Aminah', 'Sekretaris'], ['Budi Santoso, S.E.', 'Bendahara'], ['Dr. Hendra Wijaya', 'Direktur Pendidikan']];
?>
<section class="bg-navy text-white py-14"><div class="max-w-6xl mx-auto px-4">
  <h1 class="text-4xl font-bold">Profil yayasan</h1>
  <p class="mt-3 text-slate-200 max-w-2xl">Lebih dari dua dekade mengabdi untuk pendidikan anak bangsa.</p>
</div></section>

<section class="max-w-6xl mx-auto px-4 py-16 grid lg:grid-cols-2 gap-12">
  <div>
    <h2 class="text-2xl font-bold text-navy">Sejarah</h2>
    <ol class="mt-6 border-l-2 border-emerald space-y-6 pl-6">
      <?php foreach ($sejarah as [$thn, $ket]): ?>
        <li><p class="font-bold text-emerald-dark"><?= $thn ?></p><p><?= $ket ?></p></li>
      <?php endforeach; ?>
    </ol>
  </div>
  <div class="space-y-8">
    <div class="p-6 rounded-xl bg-mist">
      <h2 class="text-2xl font-bold text-navy">Visi</h2>
      <p class="mt-3">Menjadi yayasan pendidikan terpercaya yang melahirkan generasi cerdas, berakhlak, dan berdaya saing global.</p>
    </div>
    <div>
      <h2 class="text-2xl font-bold text-navy">Misi</h2>
      <ul class="mt-3 space-y-2">
        <?php foreach ($misi as $m): ?>
          <li class="flex gap-3"><span class="mt-2 w-2 h-2 rounded-full bg-emerald shrink-0"></span><span><?= $m ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="bg-mist py-16"><div class="max-w-6xl mx-auto px-4">
  <h2 class="text-2xl font-bold text-navy">Struktur yayasan</h2>
  <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
    <?php foreach ($struktur as $i => [$nama, $jabatan]): ?>
      <div class="bg-white rounded-xl p-6 text-center shadow-sm">
        <img src="https://picsum.photos/seed/pengurus<?= $i ?>/200/200" alt="Foto <?= $nama ?>" class="w-24 h-24 rounded-full object-cover mx-auto" loading="lazy">
        <p class="mt-4 font-semibold text-navy"><?= $nama ?></p>
        <p class="text-sm text-emerald-dark"><?= $jabatan ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div></section>
<?php include 'footer.php'; ?>

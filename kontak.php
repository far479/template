<?php $pageTitle = 'Kontak'; $active = 'kontak';
$status = null; $err = []; $v = ['nama' => '', 'email' => '', 'pesan' => ''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
  if ($v['nama'] === '') $err['nama'] = 'Nama wajib diisi.';
  if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $err['email'] = 'Masukkan alamat email yang valid.';
  if (mb_strlen($v['pesan']) < 10) $err['pesan'] = 'Pesan minimal 10 karakter.';
  if (!$err) { // Aktifkan mail() atau simpan ke database di sini.
    $status = 'ok'; $v = ['nama' => '', 'email' => '', 'pesan' => ''];
  }
}
include 'header.php';
$h = fn($s) => htmlspecialchars($s, ENT_QUOTES);
?>
<section class="bg-navy text-white py-14"><div class="max-w-6xl mx-auto px-4">
  <h1 class="text-4xl font-bold">Hubungi kami</h1>
  <p class="mt-3 text-slate-200 max-w-2xl">Tanyakan pendaftaran, biaya, atau kunjungan sekolah. Kami balas di hari kerja.</p>
</div></section>

<section class="max-w-6xl mx-auto px-4 py-16 grid lg:grid-cols-2 gap-12">
  <div>
    <ul class="space-y-4">
      <li><p class="font-semibold text-navy">Alamat</p><p><?= $site['alamat'] ?></p></li>
      <li><p class="font-semibold text-navy">Telepon</p><p><?= $site['telp'] ?></p></li>
      <li><p class="font-semibold text-navy">Email</p><p><?= $site['email'] ?></p></li>
      <li><p class="font-semibold text-navy">Jam layanan</p><p>Senin–Jumat 07.30–15.00, Sabtu 08.00–12.00</p></li>
    </ul>
    <!-- Ganti src dengan embed Google Maps lokasi yayasan Anda -->
    <iframe class="mt-8 w-full h-72 rounded-xl border-0" loading="lazy" title="Lokasi yayasan di Google Maps"
      src="https://www.google.com/maps?q=Garut,Jawa+Barat&output=embed" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
  </div>

  <form id="form" method="post" action="kontak.php#form" class="scroll-mt-24 p-6 rounded-xl bg-mist space-y-5" novalidate>
    <h2 class="text-2xl font-bold text-navy">Kirim pesan</h2>
    <?php if ($status === 'ok'): ?>
      <p role="status" class="p-3 rounded-md bg-emerald/15 text-emerald-dark font-medium">Pesan terkirim. Kami akan menghubungi Anda segera.</p>
    <?php endif; ?>
    <?php foreach (['nama' => ['Nama lengkap', 'text'], 'email' => ['Email', 'email']] as $k => [$label, $type]): ?>
      <div>
        <label for="<?= $k ?>" class="block text-sm font-medium text-navy"><?= $label ?></label>
        <input id="<?= $k ?>" name="<?= $k ?>" type="<?= $type ?>" value="<?= $h($v[$k]) ?>" required
          <?= isset($err[$k]) ? 'aria-invalid="true" aria-describedby="e-' . $k . '"' : '' ?>
          class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
        <?php if (isset($err[$k])): ?><p id="e-<?= $k ?>" class="mt-1 text-sm text-red-700"><?= $err[$k] ?></p><?php endif; ?>
      </div>
    <?php endforeach; ?>
    <div>
      <label for="pesan" class="block text-sm font-medium text-navy">Pesan</label>
      <textarea id="pesan" name="pesan" rows="5" required <?= isset($err['pesan']) ? 'aria-invalid="true" aria-describedby="e-pesan"' : '' ?>
        class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald"><?= $h($v['pesan']) ?></textarea>
      <?php if (isset($err['pesan'])): ?><p id="e-pesan" class="mt-1 text-sm text-red-700"><?= $err['pesan'] ?></p><?php endif; ?>
    </div>
    <button class="px-6 py-3 rounded-md bg-emerald-dark text-white font-semibold hover:bg-navy focus:outline-2 focus:outline-offset-2 focus:outline-emerald">Kirim pesan</button>
  </form>
</section>
<?php include 'footer.php'; ?>

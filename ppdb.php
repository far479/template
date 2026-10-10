<?php
require_once __DIR__ . '/functions.php';
$pageTitle = 'PPDB Online';
$active = 'ppdb';

$unitTersedia = array_values(array_unique(array_map(static fn ($u) => $u[1], $units)));
$err = $_SESSION['ppdb_err'] ?? [];
$old = $_SESSION['ppdb_old'] ?? [];
unset($_SESSION['ppdb_err'], $_SESSION['ppdb_old']);
$pilihan = $old['unit'] ?? ($_GET['unit'] ?? ($unitTersedia[0] ?? ''));

include 'header.php';

/** Render satu baris input + pesan error agar markup tetap ringkas. */
function input_form(string $key, string $label, string $type, string $placeholder, array $old, array $err, string $extra = ''): string
{
    $hasErr = isset($err[$key]);
    $html = '<div>'
        . '<label for="' . $key . '" class="block text-sm font-medium text-navy">' . e($label) . ' <span class="text-red-600">*</span></label>'
        . '<input id="' . $key . '" name="' . $key . '" type="' . $type . '" value="' . e($old[$key] ?? '') . '"'
        . ' placeholder="' . e($placeholder) . '" required ' . $extra
        . ($hasErr ? ' aria-invalid="true" aria-describedby="err-' . $key . '"' : '')
        . ' class="mt-1 w-full rounded-md border ' . ($hasErr ? 'border-red-500' : 'border-slate-300')
        . ' bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">'
        . ($hasErr ? '<p id="err-' . $key . '" class="mt-1 text-sm text-red-700">' . e($err[$key]) . '</p>' : '')
        . '</div>';
    return $html;
}
?>
<section class="bg-navy text-white py-14">
  <div class="max-w-6xl mx-auto px-4">
    <p class="text-emerald font-semibold uppercase tracking-wide text-sm">Tahun Ajaran 2027/2028</p>
    <h1 class="mt-2 text-4xl font-bold">Pendaftaran Peserta Didik Baru</h1>
    <p class="mt-3 text-slate-200 max-w-2xl">Isi formulir dengan data yang benar sesuai dokumen resmi. Panitia memverifikasi berkas maksimal 2 hari kerja.</p>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-16 grid lg:grid-cols-3 gap-8">
  <!-- ============ FORMULIR ============ -->
  <form id="form" method="post" action="proses_ppdb.php" enctype="multipart/form-data"
        class="scroll-mt-24 lg:col-span-2 p-6 md:p-8 rounded-2xl bg-mist space-y-5" novalidate>
    <div class="flex items-center gap-3">
      <span class="grid place-items-center w-10 h-10 rounded-lg bg-navy text-emerald font-bold">1</span>
      <div>
        <h2 class="text-2xl font-bold text-navy">Formulir calon siswa</h2>
        <p class="text-sm text-slate-500">Tanda <span class="text-red-600">*</span> wajib diisi.</p>
      </div>
    </div>

    <?php if (isset($err['_umum'])): ?>
      <p role="alert" class="p-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm"><?= e($err['_umum']) ?></p>
    <?php endif; ?>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <?= input_form('nama', 'Nama lengkap calon siswa', 'text', 'Sesuai akta kelahiran', $old, $err) ?>

    <div class="grid sm:grid-cols-2 gap-5">
      <?= input_form('nik', 'NIK / NISN', 'text', '10–16 digit angka', $old, $err, 'inputmode="numeric" maxlength="16"') ?>
      <div>
        <label for="unit" class="block text-sm font-medium text-navy">Pilihan unit <span class="text-red-600">*</span></label>
        <select id="unit" name="unit" required
          <?= isset($err['unit']) ? 'aria-invalid="true" aria-describedby="err-unit"' : '' ?>
          class="mt-1 w-full rounded-md border <?= isset($err['unit']) ? 'border-red-500' : 'border-slate-300' ?> bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
          <option value="">— Pilih unit —</option>
          <?php foreach ($unitTersedia as $kode): ?>
            <option value="<?= e($kode) ?>" <?= $pilihan === $kode ? 'selected' : '' ?>><?= e($kode) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($err['unit'])): ?><p id="err-unit" class="mt-1 text-sm text-red-700"><?= e($err['unit']) ?></p><?php endif; ?>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-5">
      <?= input_form('asal_sekolah', 'Asal sekolah', 'text', 'Nama sekolah asal / "Belum bersekolah"', $old, $err) ?>
      <?= input_form('nama_ortu', 'Nama orang tua / wali', 'text', 'Ayah atau ibu kandung', $old, $err) ?>
    </div>

    <?= input_form('wa', 'Nomor WhatsApp', 'tel', 'Contoh: 081234567890', $old, $err, 'inputmode="tel"') ?>

    <div>
      <label for="berkas" class="block text-sm font-medium text-navy">Berkas pendukung <span class="text-red-600">*</span></label>
      <input id="berkas" name="berkas" type="file" accept=".jpg,.jpeg,.png,.pdf" required
        <?= isset($err['berkas']) ? 'aria-invalid="true" aria-describedby="err-berkas"' : '' ?>
        class="mt-1 w-full text-sm rounded-md border <?= isset($err['berkas']) ? 'border-red-500' : 'border-slate-300' ?> bg-white px-3 py-2 file:mr-3 file:rounded-md file:border-0 file:bg-navy file:px-4 file:py-2 file:text-white file:font-semibold hover:file:bg-emerald-dark focus:outline-2 focus:outline-emerald">
      <p class="mt-1 text-xs text-slate-500">Scan akta kelahiran / kartu keluarga / ijazah — format JPG, PNG, atau PDF, maksimal 2 MB.</p>
      <?php if (isset($err['berkas'])): ?><p id="err-berkas" class="mt-1 text-sm text-red-700"><?= e($err['berkas']) ?></p><?php endif; ?>
    </div>

    <div class="flex flex-wrap gap-3 pt-2">
      <button class="px-6 py-3 rounded-md bg-emerald-dark text-white font-semibold hover:bg-navy focus:outline-2 focus:outline-offset-2 focus:outline-emerald">Kirim pendaftaran</button>
      <button type="reset" class="px-6 py-3 rounded-md border border-slate-300 font-semibold text-slate-600 hover:bg-white">Isi ulang</button>
    </div>
  </form>

  <!-- ============ INFO PENDAMPING ============ -->
  <aside class="space-y-6">
    <div class="p-6 rounded-2xl border border-slate-200">
      <h3 class="font-bold text-navy">Jadwal PPDB</h3>
      <ul class="mt-3 space-y-3 text-sm">
        <li class="flex justify-between gap-3"><span>Gelombang 1</span><span class="font-semibold text-emerald-dark">1 Okt – 15 Nov 2026</span></li>
        <li class="flex justify-between gap-3"><span>Gelombang 2</span><span class="font-semibold text-emerald-dark">16 Nov – 20 Des 2026</span></li>
        <li class="flex justify-between gap-3"><span>Tes &amp; wawancara</span><span class="font-semibold text-emerald-dark">2–6 Jan 2027</span></li>
        <li class="flex justify-between gap-3"><span>Pengumuman</span><span class="font-semibold text-emerald-dark">10 Jan 2027</span></li>
        <li class="flex justify-between gap-3"><span>Hari pertama masuk</span><span class="font-semibold text-emerald-dark">12 Jan 2027</span></li>
      </ul>
    </div>

    <div class="p-6 rounded-2xl bg-navy text-white">
      <h3 class="font-bold">Alur pendaftaran</h3>
      <ol class="mt-3 space-y-3 text-sm list-decimal list-inside marker:text-emerald marker:font-bold">
        <li>Isi formulir di halaman ini.</li>
        <li>Unggah berkas pendukung.</li>
        <li>Verifikasi berkas oleh panitia.</li>
        <li>Ikuti tes &amp; wawancara.</li>
        <li>Pengumuman hasil seleksi.</li>
      </ol>
    </div>

    <div class="p-6 rounded-2xl bg-mist">
      <h3 class="font-bold text-navy">Butuh bantuan?</h3>
      <p class="mt-2 text-sm">Hubungi panitia PPDB di <a class="font-semibold text-emerald-dark hover:underline" href="tel:<?= e(preg_replace('/\s+/', '', $site['telp'])) ?>"><?= e($site['telp']) ?></a> atau <a class="font-semibold text-emerald-dark hover:underline" href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a>.</p>
    </div>
  </aside>
</section>
<?php include 'footer.php'; ?>

<?php
/**
 * admin_dashboard.php — Panel admin.
 * Bagian atas memproses aksi (POST-Redirect-GET), bagian bawah menampilkan data:
 *   - Tab PPDB   : tabel pendaftar + detail calon siswa + ubah status/hapus
 *   - Tab Berita : tambah & hapus artikel
 */
require_once __DIR__ . '/functions.php';
require_admin();

/* ================= Aksi (semua lewat POST + CSRF) ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        flash_set('err', 'Sesi berakhir, silakan coba lagi.');
        header('Location: admin_dashboard.php');
        exit;
    }

    switch ($_POST['act'] ?? '') {
        case 'logout':
            unset($_SESSION['admin']);
            session_regenerate_id(true);
            header('Location: admin_login.php');
            exit;

        case 'status':
            $nomor = (string)($_POST['nomor'] ?? '');
            $status = (string)($_POST['status'] ?? '');
            if (ppdb_status($nomor, $status)) {
                flash_set('ok', 'Status ' . $nomor . ' diperbarui menjadi "' . $status . '".');
            } else {
                flash_set('err', 'Gagal memperbarui status.');
            }
            header('Location: admin_dashboard.php' . (isset($_POST['dari_detail']) ? '?detail=' . urlencode($nomor) : ''));
            exit;

        case 'hapus_ppdb':
            $nomor = (string)($_POST['nomor'] ?? '');
            $ok = ppdb_hapus($nomor);
            flash_set($ok ? 'ok' : 'err', $ok
                ? 'Data pendaftar ' . $nomor . ' berhasil dihapus.'
                : 'Data pendaftar gagal dihapus.');
            header('Location: admin_dashboard.php');
            exit;

        case 'tambah_berita':
            $judul = trim((string)($_POST['judul'] ?? ''));
            $kategori = (string)($_POST['kategori'] ?? '');
            $tanggal = (string)($_POST['tanggal'] ?? '');
            $ringkasan = trim((string)($_POST['ringkasan'] ?? ''));
            $isi = trim((string)($_POST['isi'] ?? ''));
            $penulis = trim((string)($_POST['penulis'] ?? ''));
            $gambar = trim((string)($_POST['gambar'] ?? ''));

            $bErr = [];
            if (mb_strlen($judul) < 5) {
                $bErr[] = 'Judul minimal 5 karakter.';
            }
            if (!in_array($kategori, KATEGORI_BERITA, true)) {
                $bErr[] = 'Pilih kategori yang valid.';
            }
            if (!strtotime($tanggal)) {
                $bErr[] = 'Tanggal tidak valid.';
            }
            if (mb_strlen($ringkasan) < 10) {
                $bErr[] = 'Ringkasan minimal 10 karakter.';
            }
            if (mb_strlen($isi) < 30) {
                $bErr[] = 'Isi artikel minimal 30 karakter (pisahkan paragraf dengan baris kosong).';
            }

            if ($bErr) {
                flash_set('err', implode(' ', $bErr));
                header('Location: admin_dashboard.php?tab=berita');
                exit;
            }

            $idBaru = berita_simpan([
                'judul' => $judul,
                'kategori' => $kategori,
                'tanggal' => $tanggal,
                'ringkasan' => $ringkasan,
                'isi' => $isi,
                'penulis' => $penulis,
                'gambar' => $gambar,
            ]);
            flash_set('ok', 'Artikel "' . $judul . '" terbit (ID #' . $idBaru . ').');
            header('Location: admin_dashboard.php?tab=berita');
            exit;

        case 'hapus_berita':
            $id = (int)($_POST['id'] ?? 0);
            $ok = berita_hapus($id);
            flash_set($ok ? 'ok' : 'err', $ok
                ? 'Artikel #' . $id . ' berhasil dihapus.'
                : 'Artikel gagal dihapus.');
            header('Location: admin_dashboard.php?tab=berita');
            exit;

        default:
            flash_set('err', 'Aksi tidak dikenal.');
            header('Location: admin_dashboard.php');
            exit;
    }
}

/* ================= Data untuk tampilan ================= */
$pageTitle = 'Dashboard Admin';
$active = 'admin';

$pendaftar = ppdb_semua();
usort($pendaftar, static fn ($a, $b) => strcmp($b['tgl'] ?? '', $a['tgl'] ?? ''));
$beritaList = berita_semua();

$statusCount = array_count_values(array_column($pendaftar, 'status'));
$totalBaru = $statusCount['Baru'] ?? 0;
$totalDiterima = $statusCount['Diterima'] ?? 0;

$detail = null;
if (!empty($_GET['detail'])) {
    $detail = ppdb_cari((string)$_GET['detail']);
    if (!$detail) {
        flash_set('err', 'Data pendaftar tidak ditemukan.');
        unset($_GET['detail']);
    }
}
$tab = ($_GET['tab'] ?? '') === 'berita' ? 'berita' : 'ppdb';
$warnaStatus = [
    'Baru' => 'bg-sky-100 text-sky-700',
    'Diverifikasi' => 'bg-amber-100 text-amber-700',
    'Diterima' => 'bg-emerald/15 text-emerald-dark',
    'Ditolak' => 'bg-red-100 text-red-700',
];

include 'header.php';
?>
<section class="bg-navy text-white py-10">
  <div class="max-w-6xl mx-auto px-4 flex flex-wrap items-center justify-between gap-4">
    <div>
      <p class="text-emerald font-semibold text-sm uppercase tracking-wide">Area pengurus</p>
      <h1 class="mt-1 text-3xl font-bold">Dashboard admin</h1>
      <p class="text-sm text-slate-300 mt-1">Masuk sebagai <span class="font-semibold text-emerald"><?= e((string)$_SESSION['admin']) ?></span></p>
    </div>
    <div class="flex gap-3">
      <a href="index.php" class="px-4 py-2 rounded-md border border-white/50 text-sm font-semibold hover:bg-white hover:text-navy">Lihat situs</a>
      <form method="post" action="admin_dashboard.php">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="act" value="logout">
        <button class="px-4 py-2 rounded-md bg-emerald text-navy text-sm font-bold hover:bg-white">Keluar</button>
      </form>
    </div>
  </div>
</section>

<section class="max-w-6xl mx-auto px-4 py-10">
  <?php foreach (flash_get() as $f): ?>
    <p role="status" class="mb-4 p-3 rounded-md text-sm font-medium <?= $f['tipe'] === 'ok' ? 'bg-emerald/15 text-emerald-dark border border-emerald/30' : 'bg-red-50 text-red-700 border border-red-200' ?>"><?= e($f['pesan']) ?></p>
  <?php endforeach; ?>

  <!-- Statistik -->
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <?php
    $statistik = [
        ['Total pendaftar', count($pendaftar), 'bg-navy text-white'],
        ['Menunggu verifikasi', $totalBaru, 'bg-mist text-navy'],
        ['Diterima', $totalDiterima, 'bg-emerald/15 text-emerald-dark'],
        ['Artikel berita', count($beritaList), 'bg-mist text-navy'],
    ];
    foreach ($statistik as [$label, $angka, $warna]): ?>
      <div class="p-5 rounded-xl <?= $warna ?> border border-slate-200">
        <p class="text-3xl font-bold"><?= $angka ?></p>
        <p class="mt-1 text-sm font-medium opacity-80"><?= $label ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Tab -->
  <div class="mt-8 flex gap-2 p-1 rounded-lg bg-mist w-fit" role="tablist" aria-label="Menu dashboard">
    <button id="btn-ppdb" role="tab" aria-selected="<?= $tab === 'ppdb' ? 'true' : 'false' ?>" aria-controls="tab-ppdb"
      class="px-5 py-2 rounded-md text-sm font-semibold focus:outline-2 focus:outline-emerald"
      onclick="bukaTab('ppdb')">Pendaftar PPDB</button>
    <button id="btn-berita" role="tab" aria-selected="<?= $tab === 'berita' ? 'true' : 'false' ?>" aria-controls="tab-berita"
      class="px-5 py-2 rounded-md text-sm font-semibold focus:outline-2 focus:outline-emerald"
      onclick="bukaTab('berita')">Kelola berita</button>
  </div>

  <!-- =================== PANEL PPDB =================== -->
  <div id="tab-ppdb" role="tabpanel" class="mt-6 <?= $tab === 'ppdb' ? '' : 'hidden' ?>">
    <?php if ($detail): ?>
      <!-- Detail calon siswa -->
      <div class="mb-6 p-6 rounded-2xl border-2 border-emerald bg-white">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-dark">Detail calon siswa</p>
            <h2 class="text-xl font-bold text-navy"><?= e($detail['nama']) ?></h2>
            <p class="text-sm text-slate-500">Nomor pendaftaran <span class="font-mono font-semibold text-navy"><?= e($detail['nomor']) ?></span></p>
          </div>
          <a href="admin_dashboard.php" class="px-4 py-2 rounded-md border border-slate-300 text-sm font-semibold text-slate-600 hover:bg-mist">&larr; Tutup detail</a>
        </div>

        <dl class="mt-5 grid sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-4 text-sm">
          <div><dt class="text-slate-500">Tanggal daftar</dt><dd class="font-semibold text-navy"><?= e($detail['tgl']) ?></dd></div>
          <div><dt class="text-slate-500">NIK / NISN</dt><dd class="font-mono font-semibold text-navy"><?= e($detail['nik']) ?></dd></div>
          <div><dt class="text-slate-500">Unit pilihan</dt><dd class="font-semibold text-navy"><?= e($detail['unit']) ?></dd></div>
          <div><dt class="text-slate-500">Asal sekolah</dt><dd class="font-semibold text-navy"><?= e($detail['asal']) ?></dd></div>
          <div><dt class="text-slate-500">Orang tua / wali</dt><dd class="font-semibold text-navy"><?= e($detail['ortu']) ?></dd></div>
          <div><dt class="text-slate-500">WhatsApp</dt>
            <dd><a class="font-semibold text-emerald-dark hover:underline" target="_blank" rel="noopener" href="https://wa.me/<?= e(preg_replace('/^0/', '62', (string)$detail['wa'])) ?>"><?= e($detail['wa']) ?></a></dd>
          </div>
          <div class="sm:col-span-2 lg:col-span-3"><dt class="text-slate-500">Berkas</dt>
            <dd><?php if (!empty($detail['berkas'])): ?>
                <a class="font-semibold text-emerald-dark hover:underline" href="<?= e($detail['berkas']) ?>" target="_blank" rel="noopener">Lihat / unduh berkas</a>
              <?php else: ?>
                <span class="text-slate-400">Tidak ada berkas terunggah (data contoh).</span>
              <?php endif; ?>
            </dd>
          </div>
        </dl>

        <form method="post" action="admin_dashboard.php" class="mt-6 flex flex-wrap items-end gap-3 p-4 rounded-xl bg-mist">
          <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="act" value="status">
          <input type="hidden" name="nomor" value="<?= e($detail['nomor']) ?>">
          <input type="hidden" name="dari_detail" value="1">
          <div>
            <label for="status" class="block text-xs font-semibold text-navy">Ubah status</label>
            <select id="status" name="status" class="mt-1 rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
              <?php foreach (STATUS_PPDB as $s): ?>
                <option value="<?= e($s) ?>" <?= ($detail['status'] ?? '') === $s ? 'selected' : '' ?>><?= e($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="px-5 py-2 rounded-md bg-navy text-white text-sm font-semibold hover:bg-emerald-dark">Simpan status</button>
        </form>
      </div>
    <?php endif; ?>

    <div class="rounded-xl border border-slate-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
          <thead class="bg-mist text-navy text-xs uppercase">
            <tr>
              <th class="px-4 py-3">Nomor</th>
              <th class="px-4 py-3">Nama calon siswa</th>
              <th class="px-4 py-3">Unit</th>
              <th class="px-4 py-3">Asal sekolah</th>
              <th class="px-4 py-3">WhatsApp</th>
              <th class="px-4 py-3">Tanggal</th>
              <th class="px-4 py-3">Status</th>
              <th class="px-4 py-3 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 bg-white">
            <?php if (!$pendaftar): ?>
              <tr><td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada pendaftar. Data akan muncul otomatis setelah formulir PPDB dikirim.</td></tr>
            <?php endif; ?>
            <?php foreach ($pendaftar as $p): ?>
              <tr class="hover:bg-mist/60">
                <td class="px-4 py-3 font-mono text-xs"><?= e($p['nomor']) ?></td>
                <td class="px-4 py-3 font-semibold text-navy"><?= e($p['nama']) ?></td>
                <td class="px-4 py-3"><?= e($p['unit']) ?></td>
                <td class="px-4 py-3"><?= e($p['asal']) ?></td>
                <td class="px-4 py-3"><?= e($p['wa']) ?></td>
                <td class="px-4 py-3 text-xs text-slate-500"><?= e(substr($p['tgl'], 0, 10)) ?></td>
                <td class="px-4 py-3">
                  <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $warnaStatus[$p['status'] ?? 'Baru'] ?? 'bg-slate-100 text-slate-600' ?>"><?= e($p['status'] ?? 'Baru') ?></span>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <a href="?detail=<?= e($p['nomor']) ?>" class="px-3 py-1.5 rounded-md bg-navy text-white text-xs font-semibold hover:bg-emerald-dark">Detail</a>
                  <form method="post" action="admin_dashboard.php" class="inline" onsubmit="return confirm('Hapus data <?= e($p['nomor']) ?>?')">
                    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="act" value="hapus_ppdb">
                    <input type="hidden" name="nomor" value="<?= e($p['nomor']) ?>">
                    <button class="px-3 py-1.5 rounded-md border border-red-200 text-red-700 text-xs font-semibold hover:bg-red-50">Hapus</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <p class="mt-2 text-xs text-slate-500">Data tersimpan pada <code class="font-mono">data/ppdb.json</code> &middot; total <?= count($pendaftar) ?> pendaftar.</p>
  </div>

  <!-- =================== PANEL BERITA =================== -->
  <div id="tab-berita" role="tabpanel" class="mt-6 <?= $tab === 'berita' ? '' : 'hidden' ?>">
    <div class="grid lg:grid-cols-2 gap-6">
      <!-- Form tambah berita -->
      <form method="post" action="admin_dashboard.php?tab=berita" class="p-6 rounded-2xl bg-mist space-y-4">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="act" value="tambah_berita">
        <h2 class="text-lg font-bold text-navy">Tambah artikel baru</h2>

        <div>
          <label for="judul" class="block text-sm font-medium text-navy">Judul</label>
          <input id="judul" name="judul" type="text" required class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <div>
            <label for="kategori" class="block text-sm font-medium text-navy">Kategori</label>
            <select id="kategori" name="kategori" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
              <?php foreach (KATEGORI_BERITA as $k): ?><option value="<?= e($k) ?>"><?= e($k) ?></option><?php endforeach; ?>
            </select>
          </div>
          <div>
            <label for="tanggal" class="block text-sm font-medium text-navy">Tanggal</label>
            <input id="tanggal" name="tanggal" type="date" value="<?= date('Y-m-d') ?>" required class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
          </div>
          <div>
            <label for="penulis" class="block text-sm font-medium text-navy">Penulis</label>
            <input id="penulis" name="penulis" type="text" placeholder="Admin Yayasan" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
          </div>
        </div>

        <div>
          <label for="gambar" class="block text-sm font-medium text-navy">URL gambar (opsional)</label>
          <input id="gambar" name="gambar" type="url" placeholder="https://... (kosongkan untuk gambar otomatis)" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald">
        </div>

        <div>
          <label for="ringkasan" class="block text-sm font-medium text-navy">Ringkasan</label>
          <textarea id="ringkasan" name="ringkasan" rows="2" required class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald"></textarea>
        </div>

        <div>
          <label for="isi" class="block text-sm font-medium text-navy">Isi artikel <span class="text-xs text-slate-500">(paragraf dipisah baris kosong)</span></label>
          <textarea id="isi" name="isi" rows="6" required class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 focus:outline-2 focus:outline-emerald"></textarea>
        </div>

        <button class="px-5 py-2.5 rounded-md bg-emerald-dark text-white font-semibold hover:bg-navy">Terbitkan artikel</button>
      </form>

      <!-- Daftar artikel -->
      <div class="rounded-xl border border-slate-200 overflow-hidden self-start">
        <div class="overflow-x-auto">
          <table class="w-full text-sm text-left">
            <thead class="bg-mist text-navy text-xs uppercase">
              <tr>
                <th class="px-4 py-3">Judul</th>
                <th class="px-4 py-3">Kategori</th>
                <th class="px-4 py-3">Tanggal</th>
                <th class="px-4 py-3 text-right">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
              <?php if (!$beritaList): ?>
                <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada artikel.</td></tr>
              <?php endif; ?>
              <?php foreach ($beritaList as $b): ?>
                <tr class="hover:bg-mist/60">
                  <td class="px-4 py-3">
                    <a class="font-semibold text-navy hover:text-emerald-dark" target="_blank" rel="noopener" href="detail_berita.php?slug=<?= e($b['slug']) ?>"><?= e($b['judul']) ?></a>
                  </td>
                  <td class="px-4 py-3"><?= e($b['kategori']) ?></td>
                  <td class="px-4 py-3 text-xs text-slate-500"><?= e($b['tanggal']) ?></td>
                  <td class="px-4 py-3 text-right">
                    <form method="post" action="admin_dashboard.php?tab=berita" class="inline" onsubmit="return confirm('Hapus artikel &quot;<?= e($b['judul']) ?>&quot;?')">
                      <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="act" value="hapus_berita">
                      <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
                      <button class="px-3 py-1.5 rounded-md border border-red-200 text-red-700 text-xs font-semibold hover:bg-red-50">Hapus</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <p class="mt-2 text-xs text-slate-500">Data tersimpan pada <code class="font-mono">data/berita.json</code> &middot; total <?= count($beritaList) ?> artikel.</p>
  </div>
</section>

<script>
  function bukaTab(nama) {
    const ppdb = nama === 'ppdb';
    document.getElementById('tab-ppdb').classList.toggle('hidden', !ppdb);
    document.getElementById('tab-berita').classList.toggle('hidden', ppdb);
    [['btn-ppdb', ppdb], ['btn-berita', !ppdb]].forEach(([id, aktif]) => {
      const el = document.getElementById(id);
      el.setAttribute('aria-selected', aktif ? 'true' : 'false');
      el.classList.toggle('bg-white', aktif);
      el.classList.toggle('shadow-sm', aktif);
      el.classList.toggle('text-navy', aktif);
      el.classList.toggle('text-slate-500', !aktif);
    });
  }
  bukaTab(<?= json_encode($tab) ?>);
</script>
<?php include 'footer.php'; ?>

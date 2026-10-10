</main>
<footer class="bg-navy text-slate-300 mt-0">
  <div class="max-w-6xl mx-auto px-4 py-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
    <div class="lg:col-span-2">
      <p class="text-white font-bold text-lg"><?= $site['nama'] ?></p>
      <p class="mt-3 text-sm max-w-sm leading-relaxed">Mendidik dengan ilmu, membentuk dengan akhlak. Satu yayasan, empat jenjang, satu visi untuk generasi unggul.</p>
      <div class="flex gap-3 mt-5">
        <?php foreach (['Facebook' => 'M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z', 'Instagram' => 'M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm0 2a3 3 0 1 1 0 6 3 3 0 0 1 0-6zm5.5-3.5a1 1 0 1 0 0 2 1 1 0 0 0 0-2z', 'YouTube' => 'M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5 3-5 3z'] as $n => $d): ?>
          <a href="#" aria-label="<?= $n ?>" class="w-9 h-9 grid place-items-center rounded-full bg-navy-soft hover:bg-emerald-dark"><svg class="w-4 h-4 fill-white" viewBox="0 0 24 24"><path d="<?= $d ?>"/></svg></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div>
      <p class="text-white font-semibold mb-3">Tautan Cepat</p>
      <ul class="space-y-2 text-sm">
        <?php foreach ($menu as [$label, $href]): ?><li><a class="hover:text-emerald" href="<?= $href ?>"><?= $label ?></a></li><?php endforeach; ?>
      </ul>
    </div>
    <div>
      <p class="text-white font-semibold mb-3">Hubungi Kami</p>
      <ul class="space-y-2 text-sm">
        <li><?= $site['alamat'] ?></li><li><?= $site['telp'] ?></li><li><?= $site['email'] ?></li>
      </ul>
    </div>
  </div>
  <div class="border-t border-navy-soft py-4 text-center text-xs">&copy; <?= date('Y') ?> <?= $site['nama'] ?>. Hak cipta dilindungi.</div>
</footer>
<script>
  const btn = document.getElementById('menu-btn'), panel = document.getElementById('menu-mobile');
  btn.addEventListener('click', () => {
    const open = panel.classList.toggle('hidden') === false;
    btn.setAttribute('aria-expanded', open);
    btn.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
  });
</script>
</body>
</html>

<?php require_once __DIR__ . '/data.php';
$pageTitle = $pageTitle ?? 'Beranda'; $active = $active ?? 'index';
$menu = ['index' => ['Beranda', 'index.php'], 'profil' => ['Profil', 'profil.php'], 'unit' => ['Unit Pendidikan', 'unit.php'], 'berita' => ['Berita', 'berita.php'], 'kontak' => ['Kontak', 'kontak.php']];
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?> | <?= $site['nama'] ?></title>
  <meta name="description" content="<?= $site['nama'] ?> mengelola TK, SD, SMP, dan SMA/SMK dengan pendidikan berkarakter.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <style type="text/tailwindcss">
    @theme {
      --font-sans: "Poppins", ui-sans-serif, system-ui, sans-serif;
      --color-navy: #0b2545;
      --color-navy-soft: #13315c;
      --color-emerald: #10b981;
      --color-emerald-dark: #047857;
      --color-mist: #f4f8fb;
    }
  </style>
</head>
<body class="font-sans text-slate-700 bg-white antialiased">
<a href="#konten" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 bg-white px-4 py-2 rounded">Lewati ke konten</a>
<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-slate-200">
  <nav class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between" aria-label="Navigasi utama">
    <a href="index.php" class="flex items-center gap-2 font-bold text-navy">
      <span class="grid place-items-center w-9 h-9 rounded-lg bg-navy text-emerald">CB</span>
      <span><?= $site['nama'] ?></span>
    </a>
    <ul class="hidden md:flex items-center gap-1">
      <?php foreach ($menu as $key => [$label, $href]): ?>
        <li><a href="<?= $href ?>" <?= $active === $key ? 'aria-current="page"' : '' ?>
          class="px-3 py-2 rounded-md text-sm font-medium <?= $active === $key ? 'bg-emerald/10 text-emerald-dark' : 'text-slate-600 hover:text-navy hover:bg-mist' ?>"><?= $label ?></a></li>
      <?php endforeach; ?>
      <li><a href="ppdb.php" <?= $active === 'ppdb' ? 'aria-current="page"' : '' ?>
        class="ml-2 px-4 py-2 rounded-md <?= $active === 'ppdb' ? 'bg-navy text-white' : 'bg-emerald-dark text-white hover:bg-navy' ?> text-sm font-semibold">Daftar Sekarang</a></li>
    </ul>
    <button id="menu-btn" class="md:hidden p-2 rounded-md text-navy focus:outline-2 focus:outline-emerald" aria-expanded="false" aria-controls="menu-mobile" aria-label="Buka menu">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
  </nav>
  <div id="menu-mobile" class="hidden md:hidden border-t border-slate-200 bg-white px-4 py-3 space-y-1">
    <?php foreach ($menu as $key => [$label, $href]): ?>
      <a href="<?= $href ?>" class="block px-3 py-2 rounded-md font-medium <?= $active === $key ? 'bg-emerald/10 text-emerald-dark' : 'text-slate-700 hover:bg-mist' ?>"><?= $label ?></a>
    <?php endforeach; ?>
    <a href="ppdb.php" class="block text-center px-3 py-2 rounded-md <?= $active === 'ppdb' ? 'bg-navy text-white' : 'bg-emerald-dark text-white' ?> font-semibold">Daftar Sekarang</a>
  </div>
</header>
<main id="konten">

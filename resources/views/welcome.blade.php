<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIM Inventaris') }} - Sistem Inventaris Sekolah</title>
    <meta name="description" content="Kelola inventaris, peminjaman, dan pengembalian barang sekolah dalam satu sistem.">
    <style>
        :root {
            color-scheme: light;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --ink: #1e293b;
            --muted: #64748b;
            --line: #e2e8f0;
            --surface: #fff;
            --page: #f8fafc;
        }

        * { box-sizing: border-box; }
        body {
            min-height: 100vh;
            margin: 0;
            color: var(--ink);
            background: var(--page);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        a { color: inherit; }
        .site-header {
            background: var(--surface);
            border-bottom: 1px solid var(--line);
        }
        .header-inner, .page {
            width: min(1080px, calc(100% - 40px));
            margin: 0 auto;
        }
        .header-inner {
            min-height: 72px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            color: var(--ink);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 700;
        }
        .brand-mark {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 7px;
            background: var(--primary);
            color: white;
            font-size: 0.8rem;
            letter-spacing: 0.04em;
        }
        .brand small {
            display: block;
            margin-top: 2px;
            color: var(--muted);
            font-size: 0.72rem;
            font-weight: 400;
        }
        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .button {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            padding: 0 15px;
            border: 1px solid var(--line);
            border-radius: 6px;
            background: var(--surface);
            color: #334155;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
        }
        .button:hover { background: #f8fafc; }
        .button-primary {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }
        .button-primary:hover {
            border-color: var(--primary-dark);
            background: var(--primary-dark);
            color: white;
        }
        .button:focus-visible {
            outline: 2px solid #93c5fd;
            outline-offset: 2px;
        }
        .page { padding: 76px 0 40px; }
        .intro { max-width: 720px; }
        .eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            color: var(--primary-dark);
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        h1 {
            max-width: 700px;
            margin: 0;
            font-size: clamp(2.2rem, 5vw, 3.55rem);
            line-height: 1.12;
            letter-spacing: -0.04em;
        }
        .lead {
            max-width: 650px;
            margin: 20px 0 26px;
            color: var(--muted);
            font-size: 1.08rem;
            line-height: 1.75;
        }
        .intro-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 64px;
        }
        .feature {
            min-height: 166px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: var(--surface);
        }
        .feature-number {
            display: block;
            margin-bottom: 20px;
            color: var(--primary);
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.08em;
        }
        .feature h2 {
            margin: 0 0 8px;
            font-size: 1rem;
        }
        .feature p {
            margin: 0;
            color: var(--muted);
            font-size: 0.88rem;
            line-height: 1.6;
        }
        .site-footer {
            margin-top: 72px;
            padding-top: 20px;
            border-top: 1px solid var(--line);
            color: var(--muted);
            font-size: 0.8rem;
        }

        @media (max-width: 700px) {
            .header-inner, .page { width: min(100% - 32px, 540px); }
            .header-inner { min-height: 64px; gap: 12px; }
            .brand { gap: 9px; font-size: 0.87rem; }
            .brand-mark { width: 34px; height: 34px; }
            .header-actions { gap: 6px; }
            .button { min-height: 38px; padding: 0 11px; font-size: 0.8rem; }
            .page { padding-top: 54px; }
            .lead { font-size: 0.98rem; }
            .features { grid-template-columns: 1fr; gap: 10px; margin-top: 42px; }
            .feature { min-height: auto; padding: 18px; }
            .feature-number { margin-bottom: 12px; }
            .site-footer { margin-top: 48px; }
        }

        @media (max-width: 380px) {
            .brand small { display: none; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-mark" aria-hidden="true">SI</span>
                <span>SIM Inventaris<small>Sistem Inventaris Sekolah</small></span>
            </a>
            <nav class="header-actions" aria-label="Navigasi utama">
                @auth
                    <a class="button button-primary" href="{{ route('dashboard') }}">Dashboard</a>
                @else
                    @if (Route::has('login'))
                        <a class="button" href="{{ route('login') }}">Masuk</a>
                    @endif
                    @if (Route::has('register'))
                        <a class="button button-primary" href="{{ route('register') }}">Daftar</a>
                    @endif
                @endauth
            </nav>
        </div>
    </header>

    <main class="page">
        <section class="intro">
            <span class="eyebrow">Inventaris sekolah</span>
            <h1>Data barang dan peminjaman, tertata dalam satu tempat.</h1>
            <p class="lead">
                Pantau persediaan, catat peminjaman, dan kelola pengembalian barang sekolah dengan alur yang jelas untuk admin maupun peminjam.
            </p>
            <div class="intro-actions">
                @auth
                    <a class="button button-primary" href="{{ route('dashboard') }}">Buka dashboard</a>
                @else
                    @if (Route::has('login'))
                        <a class="button button-primary" href="{{ route('login') }}">Masuk ke sistem</a>
                    @endif
                    @if (Route::has('register'))
                        <a class="button" href="{{ route('register') }}">Buat akun</a>
                    @endif
                @endauth
            </div>
        </section>

        <section class="features" aria-label="Fitur utama">
            <article class="feature">
                <span class="feature-number">01</span>
                <h2>Data inventaris</h2>
                <p>Kelola barang, kategori, kondisi, dan stok yang tersedia.</p>
            </article>
            <article class="feature">
                <span class="feature-number">02</span>
                <h2>Peminjaman tercatat</h2>
                <p>Lihat siapa yang meminjam barang dan kapan barang harus kembali.</p>
            </article>
            <article class="feature">
                <span class="feature-number">03</span>
                <h2>Pengembalian terpantau</h2>
                <p>Catat tanggal kembali, kondisi barang, dan catatan pemeriksaan.</p>
            </article>
        </section>

        <footer class="site-footer">
            Sistem Inventaris Sekolah
        </footer>
    </main>
</body>
</html>
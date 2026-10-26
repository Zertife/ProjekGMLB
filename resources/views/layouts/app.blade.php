<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Sistem Inventaris') }} - Sistem Inventaris Sekolah</title>
        <meta name="description" content="Sistem Inventaris Sekolah - Kelola barang, peminjaman, dan pengembalian secara digital">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <!-- Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { font-family: 'Inter', sans-serif; }

            :root {
                --primary: #4f46e5;
                --primary-dark: #3730a3;
                --secondary: #06b6d4;
                --success: #10b981;
                --warning: #f59e0b;
                --danger: #ef4444;
                --sidebar-width: 260px;
            }

            body { background: #f1f5f9; color: #1e293b; }

            /* ===== SIDEBAR ===== */
            .sidebar {
                position: fixed;
                top: 0; left: 0;
                width: var(--sidebar-width);
                height: 100vh;
                background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #1e1b4b 100%);
                z-index: 50;
                overflow-y: auto;
                transition: transform 0.3s ease;
                scrollbar-width: thin;
                scrollbar-color: rgba(255,255,255,0.2) transparent;
            }

            .sidebar::-webkit-scrollbar { width: 4px; }
            .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }

            .sidebar-brand {
                padding: 1.5rem 1.25rem;
                border-bottom: 1px solid rgba(255,255,255,0.1);
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .sidebar-brand .brand-icon {
                width: 40px; height: 40px;
                background: linear-gradient(135deg, #818cf8, #6366f1);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.1rem;
                color: white;
                box-shadow: 0 4px 15px rgba(99,102,241,0.4);
            }

            .sidebar-brand .brand-text h1 {
                font-size: 0.9rem;
                font-weight: 700;
                color: white;
                line-height: 1.2;
            }

            .sidebar-brand .brand-text p {
                font-size: 0.7rem;
                color: rgba(255,255,255,0.5);
            }

            .sidebar-section {
                padding: 0.5rem 0;
            }

            .sidebar-section-label {
                padding: 0.75rem 1.25rem 0.25rem;
                font-size: 0.65rem;
                font-weight: 600;
                color: rgba(255,255,255,0.35);
                text-transform: uppercase;
                letter-spacing: 0.08em;
            }

            .sidebar-link {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.65rem 1.25rem;
                color: rgba(255,255,255,0.65);
                font-size: 0.85rem;
                font-weight: 500;
                text-decoration: none;
                border-radius: 0;
                margin: 0 0.5rem;
                border-radius: 8px;
                transition: all 0.2s ease;
                position: relative;
            }

            .sidebar-link:hover {
                color: white;
                background: rgba(255,255,255,0.1);
            }

            .sidebar-link.active {
                color: white;
                background: linear-gradient(135deg, rgba(99,102,241,0.8), rgba(139,92,246,0.6));
                box-shadow: 0 4px 12px rgba(99,102,241,0.3);
            }

            .sidebar-link .icon {
                width: 20px;
                text-align: center;
                font-size: 0.9rem;
                flex-shrink: 0;
            }

            .sidebar-user {
                position: sticky;
                bottom: 0;
                background: rgba(0,0,0,0.3);
                backdrop-filter: blur(10px);
                padding: 1rem 1.25rem;
                border-top: 1px solid rgba(255,255,255,0.1);
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .user-avatar {
                width: 36px; height: 36px;
                border-radius: 50%;
                background: linear-gradient(135deg, #818cf8, #6366f1);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.85rem;
                font-weight: 700;
                color: white;
                flex-shrink: 0;
            }

            /* ===== MAIN CONTENT ===== */
            .main-content {
                margin-left: var(--sidebar-width);
                min-height: 100vh;
                transition: margin-left 0.3s ease;
            }

            /* ===== TOPBAR ===== */
            .topbar {
                background: white;
                border-bottom: 1px solid #e2e8f0;
                padding: 0 1.5rem;
                height: 64px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                position: sticky;
                top: 0;
                z-index: 40;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            }

            .topbar-breadcrumb {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.85rem;
            }

            .topbar-breadcrumb a { color: #64748b; text-decoration: none; }
            .topbar-breadcrumb a:hover { color: var(--primary); }
            .topbar-breadcrumb .separator { color: #cbd5e1; }
            .topbar-breadcrumb .current { color: #1e293b; font-weight: 500; }

            .page-content {
                padding: 1.5rem;
            }

            /* ===== CARDS ===== */
            .card {
                background: white;
                border-radius: 12px;
                border: 1px solid #e2e8f0;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }

            .card-header {
                padding: 1.25rem 1.5rem;
                border-bottom: 1px solid #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .card-header h2 {
                font-size: 0.95rem;
                font-weight: 600;
                color: #1e293b;
            }

            .card-body {
                padding: 1.5rem;
            }

            /* ===== STAT CARDS ===== */
            .stat-card {
                background: white;
                border-radius: 12px;
                padding: 1.25rem;
                border: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                gap: 1rem;
                transition: transform 0.2s, box-shadow 0.2s;
            }

            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            }

            .stat-icon {
                width: 52px; height: 52px;
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.3rem;
                flex-shrink: 0;
            }

            .stat-icon.indigo { background: #eef2ff; color: #4f46e5; }
            .stat-icon.cyan { background: #ecfeff; color: #06b6d4; }
            .stat-icon.green { background: #f0fdf4; color: #10b981; }
            .stat-icon.amber { background: #fffbeb; color: #f59e0b; }
            .stat-icon.red { background: #fef2f2; color: #ef4444; }
            .stat-icon.violet { background: #f5f3ff; color: #7c3aed; }

            .stat-info p { font-size: 0.78rem; color: #64748b; font-weight: 500; }
            .stat-info h3 { font-size: 1.6rem; font-weight: 700; color: #1e293b; line-height: 1.2; }
            .stat-info small { font-size: 0.72rem; color: #94a3b8; }

            /* ===== TABLES ===== */
            .table-wrapper { overflow-x: auto; border-radius: 8px; }

            table.inv-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 0.85rem;
            }

            table.inv-table thead th {
                background: #f8fafc;
                padding: 0.75rem 1rem;
                text-align: left;
                font-weight: 600;
                color: #475569;
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                border-bottom: 1px solid #e2e8f0;
                white-space: nowrap;
            }

            table.inv-table tbody td {
                padding: 0.85rem 1rem;
                border-bottom: 1px solid #f1f5f9;
                color: #374151;
                vertical-align: middle;
            }

            table.inv-table tbody tr:last-child td { border-bottom: none; }

            table.inv-table tbody tr:hover td { background: #f8fafc; }

            /* ===== BADGES ===== */
            .badge {
                display: inline-flex;
                align-items: center;
                gap: 0.3rem;
                padding: 0.2rem 0.65rem;
                border-radius: 20px;
                font-size: 0.72rem;
                font-weight: 600;
            }

            .badge-success { background: #d1fae5; color: #065f46; }
            .badge-warning { background: #fef3c7; color: #92400e; }
            .badge-danger { background: #fee2e2; color: #991b1b; }
            .badge-info { background: #dbeafe; color: #1e40af; }
            .badge-secondary { background: #f1f5f9; color: #475569; }
            .badge-violet { background: #ede9fe; color: #5b21b6; }

            /* ===== BUTTONS ===== */
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 0.4rem;
                padding: 0.5rem 1rem;
                border-radius: 8px;
                font-size: 0.85rem;
                font-weight: 600;
                border: none;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .btn-primary {
                background: linear-gradient(135deg, #4f46e5, #7c3aed);
                color: white;
                box-shadow: 0 2px 8px rgba(79,70,229,0.3);
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #3730a3, #6d28d9);
                box-shadow: 0 4px 15px rgba(79,70,229,0.4);
                transform: translateY(-1px);
                color: white;
            }

            .btn-secondary {
                background: white;
                color: #475569;
                border: 1px solid #e2e8f0;
            }

            .btn-secondary:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
                color: #1e293b;
            }

            .btn-success {
                background: linear-gradient(135deg, #10b981, #059669);
                color: white;
                box-shadow: 0 2px 8px rgba(16,185,129,0.3);
            }

            .btn-success:hover {
                background: linear-gradient(135deg, #059669, #047857);
                transform: translateY(-1px);
                color: white;
            }

            .btn-warning {
                background: linear-gradient(135deg, #f59e0b, #d97706);
                color: white;
                box-shadow: 0 2px 8px rgba(245,158,11,0.3);
            }

            .btn-warning:hover {
                transform: translateY(-1px);
                color: white;
            }

            .btn-danger {
                background: linear-gradient(135deg, #ef4444, #dc2626);
                color: white;
                box-shadow: 0 2px 8px rgba(239,68,68,0.3);
            }

            .btn-danger:hover {
                transform: translateY(-1px);
                color: white;
            }

            .btn-sm {
                padding: 0.3rem 0.65rem;
                font-size: 0.78rem;
                border-radius: 6px;
            }

            /* ===== FORMS ===== */
            .form-group { margin-bottom: 1.25rem; }

            .form-label {
                display: block;
                font-size: 0.82rem;
                font-weight: 600;
                color: #374151;
                margin-bottom: 0.4rem;
            }

            .form-label .required {
                color: #ef4444;
                margin-left: 2px;
            }
            .required { color: #ef4444; margin-left: 0.15rem; }

            .form-control {
                width: 100%;
                padding: 0.6rem 0.9rem;
                border: 1.5px solid #e2e8f0;
                border-radius: 8px;
                font-size: 0.85rem;
                color: #1e293b;
                background: white;
                transition: border-color 0.2s, box-shadow 0.2s;
                outline: none;
            }

            .form-control:focus {
                border-color: #4f46e5;
                box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
            }

            .form-control.is-invalid {
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239,68,68,0.1);
            }

            .form-error {
                font-size: 0.78rem;
                color: #ef4444;
                margin-top: 0.3rem;
            }

            /* ===== ALERTS ===== */
            .alert {
                padding: 0.85rem 1rem;
                border-radius: 8px;
                font-size: 0.85rem;
                display: flex;
                align-items: flex-start;
                gap: 0.65rem;
                margin-bottom: 1rem;
            }

            .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
            .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
            .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
            .alert-info { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }

            /* ===== RESPONSIVE ===== */
            .sidebar-toggle { display: none; }
            @media (max-width: 1024px) {
                .page-content { padding: 1.25rem; }
                .dashboard-grid { grid-template-columns: minmax(0, 1fr); }
            }

            @media (max-width: 768px) {
                .sidebar { transform: translateX(-100%); }
                .sidebar.open { transform: translateX(0); }
                .main-content { margin-left: 0; }
                .sidebar-toggle { display: flex; }
                .topbar { min-height: 64px; height: auto; padding: 0.75rem 1rem; gap: 0.5rem; flex-wrap: wrap; }
                .topbar > div { flex-wrap: wrap; }
                .topbar-breadcrumb { flex-wrap: wrap; }
                .page-content { padding: 1rem; }
                .card-header { flex-wrap: wrap; gap: 0.75rem; padding: 1rem; }
                .form-grid-2 { grid-template-columns: minmax(0, 1fr); gap: 0; }
                .dashboard-grid { grid-template-columns: minmax(0, 1fr); }
                .sidebar-overlay {
                    display: none;
                    position: fixed;
                    inset: 0;
                    background: rgba(0,0,0,0.5);
                    z-index: 45;
                }
                .sidebar-overlay.show { display: block; }
                .filter-form { width: 100%; }
                .filter-form .form-control { flex: 1 1 100%; min-width: 0; }
                .pagination-footer { align-items: flex-start; }
                .pagination-footer nav { width: 100%; overflow-x: auto; }
            }

            @media (max-width: 480px) {
                .page-content { padding: 0.75rem; }
                .topbar { align-items: flex-start; }
                .topbar > div:last-child { width: 100%; padding-left: 2.5rem; }
                .page-heading > .btn { justify-content: center; width: 100%; }
                .card-body { padding: 1rem; }
                .empty-state { padding: 2rem 1rem; }
            }

            /* ===== ANIMATIONS ===== */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .fade-in { animation: fadeIn 0.3s ease; }

            /* ===== MISC ===== */
            .page-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #1e293b;
            }

            .page-subtitle {
                font-size: 0.82rem;
                color: #64748b;
            }

            .empty-state {
                text-align: center;
                padding: 3rem;
                color: #94a3b8;
            }

            .empty-state i { font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.5; }
            .empty-state p { font-size: 0.9rem; }

            .page-heading { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
            .filter-form { display: flex; align-items: center; flex-wrap: wrap; gap: 0.5rem; }
            .filter-form .form-control { width: auto; min-width: 150px; }
            .form-card { max-width: 720px; }
            .form-actions { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1.5rem; }
            .dashboard-grid { display: grid; gap: 1.25rem; margin-bottom: 1.25rem; }
            .admin-dashboard-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .user-dashboard-grid { grid-template-columns: minmax(0, 3fr) minmax(0, 2fr); }
            .form-grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
            @media (max-width: 1024px) {
                .dashboard-grid { grid-template-columns: minmax(0, 1fr); }
            }
            @media (max-width: 768px) {
                .form-grid-2 { grid-template-columns: minmax(0, 1fr); gap: 0; }
            }
            .pagination-footer { padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; }
            .pagination-footer > p { font-size: 0.8rem; color: #64748b; margin: 0; }

            /* Pagination */
            .pagination { display: flex; gap: 0.3rem; align-items: center; list-style: none; padding: 0; margin: 0; }
            .pagination .page-item a, .pagination .page-item span, .pagination .page-link {
                padding: 0.4rem 0.75rem;
                border-radius: 6px;
                font-size: 0.82rem;
                font-weight: 500;
                color: #475569;
                background: white;
                border: 1px solid #e2e8f0;
                text-decoration: none;
                transition: all 0.2s;
            }
            .pagination .page-item a:hover, .pagination .page-link:hover { background: #f8fafc; color: #1e293b; }
            .pagination .page-item.active span {
                background: #4f46e5;
                color: white;
                border-color: #4f46e5;
            }
            .pagination .page-item.disabled span { opacity: 0.5; }
        </style>
    </head>
    <body>
        <!-- Sidebar Overlay (mobile) -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
                <div class="brand-text">
                    <h1>SIM Inventaris</h1>
                    <p>Sistem Inventaris Sekolah</p>
                </div>
            </div>

            <nav class="sidebar-section">
                <p class="sidebar-section-label">Utama</p>
                <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-chart-pie"></i></span>
                    Dashboard
                </a>

                @if(auth()->user()->isAdmin())
                    <p class="sidebar-section-label">Manajemen</p>
                    <a href="{{ route('users.index') }}" class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-users"></i></span>
                        Manajemen User
                    </a>
                    <a href="{{ route('categories.index') }}" class="sidebar-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-tags"></i></span>
                        Kategori
                    </a>
                    <a href="{{ route('items.index') }}" class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-boxes-stacked"></i></span>
                        Barang
                    </a>
                    <a href="{{ route('loans.index') }}" class="sidebar-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-handshake"></i></span>
                        Peminjaman
                    </a>
                    <a href="{{ route('return-logs.index') }}" class="sidebar-link {{ request()->routeIs('return-logs.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-rotate-left"></i></span>
                        Pengembalian
                    </a>
                    <p class="sidebar-section-label">Laporan</p>
                    <a href="{{ route('activity-logs.index') }}" class="sidebar-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-scroll"></i></span>
                        Activity Log
                    </a>
                @else
                    <p class="sidebar-section-label">Barang</p>
                    <a href="{{ route('items.index') }}" class="sidebar-link {{ request()->routeIs('items.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-boxes-stacked"></i></span>
                        Daftar Barang
                    </a>
                    <p class="sidebar-section-label">Peminjaman</p>
                    <a href="{{ route('loans.index') }}" class="sidebar-link {{ request()->routeIs('loans.*') ? 'active' : '' }}">
                        <span class="icon"><i class="fas fa-handshake"></i></span>
                        Peminjaman Saya
                    </a>
                @endif
                <p class="sidebar-section-label">Akun</p>
                <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <span class="icon"><i class="fas fa-user-gear"></i></span>
                    Profil
                </a>
            </nav>

            <div class="sidebar-user">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div style="flex:1; min-width:0;">
                    <p style="font-size:0.8rem; font-weight:600; color:white; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ auth()->user()->name }}</p>
                    <p style="font-size:0.7rem; color:rgba(255,255,255,0.5);">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.5);font-size:1rem;" title="Logout">
                        <i class="fas fa-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            <!-- TOPBAR -->
            <header class="topbar">
                <div style="display:flex;align-items:center;gap:1rem;">
                    <button class="sidebar-toggle btn btn-secondary btn-sm" onclick="toggleSidebar()" style="padding:0.4rem 0.6rem;">
                        <i class="fas fa-bars"></i>
                    </button>
                    <nav class="topbar-breadcrumb">
                        {{ $breadcrumb ?? '' }}
                    </nav>
                </div>
                <div style="display:flex;align-items:center;gap:1rem;">
                    <span style="font-size:0.82rem;color:#64748b;">
                        <i class="fas fa-calendar-day" style="margin-right:4px;"></i>
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <main class="page-content fade-in">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-circle-check"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">
                        <i class="fas fa-circle-xmark"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="alert alert-warning">
                        <i class="fas fa-triangle-exclamation"></i>
                        <span>{{ session('warning') }}</span>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.toggle('open');
                overlay.classList.toggle('show');
            }
        </script>
    </body>
</html>

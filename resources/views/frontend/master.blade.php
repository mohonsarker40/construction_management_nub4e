<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4a62a">
    <meta name="description" content="A clearer way to plan construction projects, coordinate teams, and track materials.">
    <title>@yield('title', 'Buildwise | Construction Project Management')</title>
    <style>
        :root { color-scheme: light; --ink:#17221d; --muted:#67736d; --paper:#f7f8f5; --white:#fff; --line:#e5e9e3; --green:#183d32; --lime:#c8e66b; --orange:#f4a62a; }
        * { box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body { margin:0; color:var(--ink); background:var(--paper); font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif; line-height:1.6; }
        a { color:inherit; }
        .site-header { position:relative; z-index:2; background:var(--paper); }
        .nav-wrap { width:min(1160px, calc(100% - 48px)); min-height:82px; margin:auto; display:flex; align-items:center; justify-content:space-between; gap:28px; }
        .brand { display:inline-flex; align-items:center; gap:11px; text-decoration:none; font-weight:800; letter-spacing:-.04em; font-size:1.25rem; }
        .brand-mark { display:grid; width:38px; height:38px; place-items:center; border-radius:12px; color:var(--green); background:var(--lime); font-size:1.15rem; }
        .brand-name span { color:var(--muted); font-weight:500; }
        .nav-links { display:flex; align-items:center; gap:30px; }
        .nav-links a { text-decoration:none; color:#536059; font-size:.92rem; font-weight:600; }
        .nav-links a:hover { color:var(--green); }
        .nav-cta,.button { display:inline-flex; min-height:46px; align-items:center; justify-content:center; gap:9px; padding:0 20px; border:0; border-radius:8px; color:#15241d; background:var(--lime); text-decoration:none; font-size:.92rem; font-weight:750; transition:transform .18s ease,background .18s ease; }
        .nav-cta:hover,.button:hover { transform:translateY(-2px); background:#d5f17b; }
        .menu-toggle { display:none; width:42px; height:42px; border:1px solid var(--line); border-radius:8px; color:var(--ink); background:var(--white); font-size:1.25rem; }
        main { min-height:62vh; }
        .site-footer { margin-top:70px; color:#d8e0da; background:#142820; }
        .footer-inner { width:min(1160px, calc(100% - 48px)); margin:auto; padding:38px 0; display:flex; align-items:center; justify-content:space-between; gap:24px; }
        .footer-inner .brand { color:#fff; }
        .footer-inner p { margin:0; color:#aebbb2; font-size:.88rem; }
        .footer-links { display:flex; gap:20px; }
        .footer-links a { color:#d8e0da; text-decoration:none; font-size:.88rem; }
        @media (max-width:700px) {
            .nav-wrap { width:calc(100% - 36px); min-height:72px; flex-wrap:wrap; gap:12px; }
            .menu-toggle { display:inline-grid; place-items:center; margin-left:auto; }
            .nav-cta { display:none; }
            .nav-links { display:none; order:3; width:100%; padding:4px 0 18px; flex-direction:column; align-items:flex-start; gap:14px; }
            .nav-links.is-open { display:flex; }
            .footer-inner { width:calc(100% - 36px); align-items:flex-start; flex-direction:column; }
            .footer-links { flex-wrap:wrap; }
        }
        @media (prefers-reduced-motion:reduce) { html { scroll-behavior:auto; } *,*::before,*::after { transition:none !important; } }
        @yield('styles')
    </style>
</head>
<body>
    @include('frontend.header')
    <main id="main-content">
        @yield('content')
    </main>
    @include('frontend.footer')
    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');
        if (menuToggle && navLinks) {
            menuToggle.addEventListener('click', () => {
                const isOpen = navLinks.classList.toggle('is-open');
                menuToggle.setAttribute('aria-expanded', String(isOpen));
            });
        }
    </script>
    @yield('script')
</body>
</html>

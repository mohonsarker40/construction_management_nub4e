<header class="site-header">
    <nav class="nav-wrap" aria-label="Main navigation">
        <a href="{{ url('/') }}" class="brand" aria-label="Buildwise home">
            <span class="brand-mark" aria-hidden="true">⌂</span>
            <span class="brand-name">BUILDWISE <span>CONSTRUCTION</span></span>
        </a>

        <button class="menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false" aria-controls="primary-navigation">☰</button>

        <div class="nav-links" id="primary-navigation">
            <a href="{{ url('/') }}#solutions">Solutions</a>
            <a href="{{ url('/') }}#workflow">How it works</a>
            <a href="{{ url('/') }}#contact">Contact</a>
        </div>

        <a class="nav-cta" href="{{ route('login') }}">Sign in <span aria-hidden="true">↗</span></a>
    </nav>
</header>

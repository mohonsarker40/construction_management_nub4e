<footer class="site-footer" id="contact">
    <div class="footer-inner">
        <div>
            <a href="{{ url('/') }}" class="brand">
                <span class="brand-mark" aria-hidden="true">⌂</span>
                <span>BUILDWISE</span>
            </a>
            <p style="margin-top:12px">Plan well. Build with confidence.</p>
        </div>
        <div class="footer-links">
            <a href="{{ url('/') }}#solutions">Project planning</a>
            <a href="{{ url('/') }}#workflow">Team workflow</a>
            <a href="{{ route('login') }}">Team sign in</a>
        </div>
        <p>© {{ date('Y') }} Buildwise Construction Management</p>
    </div>
</footer>

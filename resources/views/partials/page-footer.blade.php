<footer class="page-footer{{ isset($footerClass) ? ' ' . $footerClass : '' }}">
    <div class="footer-section">
        <p>&copy; {{ date('Y') }} PAL. All rights reserved.</p>
    </div>
</footer>

@auth
    @once
        @include('partials.chatbot')
    @endonce
@endauth

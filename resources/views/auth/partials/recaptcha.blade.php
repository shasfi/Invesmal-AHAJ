@if(filled(config('services.recaptcha.site_key')) && config('services.recaptcha.enabled'))
<input type="hidden" name="recaptcha_token" id="recaptcha-token">
<div id="recaptcha-badge" style="text-align:center;font-size:0.7rem;color:var(--muted);margin:0.75rem 0 0;min-height:1rem;"></div>
@push('scripts')
<script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('recaptcha-token')?.closest('form');
    const tokenInput = document.getElementById('recaptcha-token');
    const badge = document.getElementById('recaptcha-badge');
    const siteKey = @json(config('services.recaptcha.site_key'));
    let submitting = false;

    if (!form || !tokenInput) return;

    if (typeof grecaptcha === 'undefined') {
        if (badge) badge.textContent = 'reCAPTCHA could not load. Refresh the page or check your connection.';
        return;
    }

    if (badge) badge.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Protected by reCAPTCHA';

    form.addEventListener('submit', function (e) {
        if (submitting) return;
        e.preventDefault();

        grecaptcha.enterprise.ready(async () => {
            try {
                tokenInput.value = await grecaptcha.enterprise.execute(siteKey, { action: 'REGISTER' });
                submitting = true;
                form.submit();
            } catch (err) {
                console.error(err);
                if (badge) badge.textContent = 'Security check failed. Please refresh and try again.';
            }
        });
    });
});
</script>
@endpush
@endif

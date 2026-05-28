@php
    $socialReady = filled(config('services.firebase.api_key'))
        && filled(config('services.firebase.auth_domain'))
        && filled(config('services.firebase.project_id'));
@endphp

@if($socialReady)
<form id="firebase-auth-form" method="POST" action="{{ route('firebase.authenticate') }}" style="display:none;">
    @csrf
    <input type="hidden" name="id_token" id="firebase-id-token">
    <input type="hidden" name="role" id="firebase-role" value="">
</form>

<div class="auth-social" style="margin-bottom:1.25rem;">
    <p style="text-align:center;font-size:0.8rem;color:var(--text-secondary);margin-bottom:0.75rem;">Or continue with</p>
    <div style="display:flex;flex-direction:column;gap:0.5rem;">
        <button type="button" id="firebase-google-btn" class="auth-social-btn auth-social-btn--google" style="cursor:pointer;border:none;width:100%;">
            <i class="fa-brands fa-google"></i> Continue with Google
        </button>
        <button type="button" id="firebase-facebook-btn" class="auth-social-btn auth-social-btn--facebook" style="cursor:pointer;border:none;width:100%;">
            <i class="fa-brands fa-facebook-f"></i> Continue with Facebook
        </button>
    </div>
</div>

<style>
.auth-social-btn{display:flex;align-items:center;justify-content:center;gap:0.5rem;width:100%;padding:0.75rem 1rem;border-radius:var(--radius-md);font-weight:600;font-size:0.9rem;text-decoration:none;border:1px solid var(--border);transition:all 0.2s}
.auth-social-btn--facebook{background:#1877f2;color:#fff;border-color:#1877f2}
.auth-social-btn--facebook:hover{filter:brightness(1.08)}
.auth-social-btn--google{background:#fff;color:#333;border-color:#ddd}
.auth-social-btn--google:hover{background:#f5f5f5}
</style>

@push('scripts')
<script type="module">
import { initializeApp } from 'https://www.gstatic.com/firebasejs/10.14.1/firebase-app.js';
import { getAuth, signInWithPopup, GoogleAuthProvider, FacebookAuthProvider } from 'https://www.gstatic.com/firebasejs/10.14.1/firebase-auth.js';

const app = initializeApp({
    apiKey: @json(config('services.firebase.api_key')),
    authDomain: @json(config('services.firebase.auth_domain')),
    projectId: @json(config('services.firebase.project_id')),
    appId: @json(config('services.firebase.app_id')),
});

const auth = getAuth(app);
const form = document.getElementById('firebase-auth-form');
const tokenInput = document.getElementById('firebase-id-token');
const roleInput = document.getElementById('firebase-role');
const registerRole = document.getElementById('role');

async function socialLogin(provider) {
    if (registerRole && !registerRole.value) {
        alert('Please select your role (Student Founder / Investor / Mentor) first.');
        registerRole.focus();
        return;
    }
    if (roleInput && registerRole) {
        roleInput.value = registerRole.value;
    }
    try {
        const result = await signInWithPopup(auth, provider);
        tokenInput.value = await result.user.getIdToken();
        form.submit();
    } catch (e) {
        console.error(e);
        alert('Sign-in cancelled or failed. Try again or use email/password below.');
    }
}

document.getElementById('firebase-google-btn')?.addEventListener('click', () => socialLogin(new GoogleAuthProvider()));
document.getElementById('firebase-facebook-btn')?.addEventListener('click', () => socialLogin(new FacebookAuthProvider()));
</script>
@endpush
@endif

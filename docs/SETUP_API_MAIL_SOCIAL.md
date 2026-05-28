# Invesmal — OpenAI, Email, Facebook & Google Setup

Roman Urdu + English steps for FYP demo.

---

## 1. Pitch Deck AI (OpenAI) — optional but recommended

**Ab bina key ke bhi kaam chalega:** demo pitch deck auto ban jayega. Real AI ke liye key chahiye.

### Steps (free trial / paid credits)

1. Browser mein jao: https://platform.openai.com/signup  
2. Login → **API keys** → **Create new secret key**  
3. Project folder mein file kholo: `invesmal/.env`  
4. Line update karo:
   ```env
   OPENAI_API_KEY=sk-proj-xxxxxxxx
   ```
5. Terminal:
   ```bash
   cd invesmal
   php artisan config:clear
   ```
6. Dubara **Pitch Decks → Generate** try karo.

**Note:** OpenAI ab mostly billing card mangta hai. Agar card nahi hai, demo mode use karo (already enabled).

---

## 2. Email kyun nahi jati? (Forgot password / verification)

Tumhari `.env` mein abhi ye hai:

```env
MAIL_MAILER=log
```

Iska matlab email **inbox mein nahi** — sirf `storage/logs/laravel.log` mein save hoti hai.

### Option A — Development (reset link screen par)

1. **Forgot password** par email daalo → submit  
2. Page par **Dev reset link** dikhega (click karke password reset karo)

### Option B — Real email (Mailtrap Email Sending / Live SMTP)

1. https://mailtrap.io → **Email Sending** → **SMTP**  
2. **Email Sending → API Tokens → Create Token** (sirf ye token — sandbox inbox ka password NAHI)  
3. `.env` mein (tumhari live settings):
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=live.smtp.mailtrap.io
   MAIL_PORT=587
   MAIL_USERNAME=apismtp@mailtrap.io
   MAIL_PASSWORD=paste_your_api_token_here
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS=your@verified-domain.com
   MAIL_FROM_NAME=Invesmal
   ```
4. Mailtrap mein **Sending Domains** verify karo — `MAIL_FROM_ADDRESS` wahi domain honi chahiye  
5. `php artisan config:clear`  
6. Forgot password try karo — email recipient inbox mein jayegi (sandbox nahi)

**Error `535 Authentication failed`?**
- `MAIL_PASSWORD` galat hai ya sandbox password live host par use ho raha hai  
- Naya API token banao, purana revoke ho sakta hai  
- Ya testing ke liye **Sandbox SMTP** (alag host):
  ```env
  MAIL_HOST=sandbox.smtp.mailtrap.io
  MAIL_PORT=2525
  MAIL_USERNAME=<from Email Testing inbox SMTP>
  MAIL_PASSWORD=<from Email Testing inbox SMTP>
  ```
  (username `apismtp@mailtrap.io` sirf **live** SMTP ke liye hai)

### Option C — Resend (real Gmail — limit on free plan)

1. https://resend.com → sign up  
2. **Free plan:** ~**100 emails/day**, **3000/month** (plan change hota hai — dashboard par dekho)  
3. **Important:** `onboarding@resend.dev` se sirf **jis Gmail se Resend account banaya** us par email jati hai — **baqi addresses par error**  
4. **Sab users ko email** chahiye → Resend mein **domain verify** karo (`invesmal.app`) ya FYP ke liye **Mailtrap sandbox** use karo (Option B sandbox)  
5. `.env`:
   ```env
   MAIL_MAILER=resend
   RESEND_API_KEY=re_xxxxxxxx
   MAIL_FROM_ADDRESS=onboarding@resend.dev
   ```

### Option D — FYP multi-user testing (recommended abhi)

Mailtrap **sandbox** — har user email "send" ho jati hai, sab emails **mailtrap.io → Email Testing → inbox** mein dikhti hain (Gmail par nahi, lekin demo ke liye best):

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=2514402
MAIL_PASSWORD=your_sandbox_password
```

### Email verification vs Admin verify

- **Email verify** (`/email/verify`) = Laravel email link (SMTP chahiye)  
- **Admin verify** (`/admin/verification`) = platform par account approve — FYP demo ke liye ye zyada use hota hai

---

## 3. Continue with Facebook

1. https://developers.facebook.com → **My Apps** → Create App (type: Consumer)  
2. Add product **Facebook Login**  
3. **Settings → Basic**: App ID + App Secret copy  
4. **Facebook Login → Settings**: Valid OAuth Redirect URIs:
   ```
   http://127.0.0.1:8000/auth/facebook/callback
   ```
   (agar port alag hai to `APP_URL` ke mutabiq change karo)

5. `.env`:
   ```env
   FACEBOOK_CLIENT_ID=your_app_id
   FACEBOOK_CLIENT_SECRET=your_app_secret
   APP_URL=http://127.0.0.1:8000
   ```

6. `php artisan config:clear`  
7. Login page par **Continue with Facebook** active ho jayega

---

## 4. Firebase — Google & Facebook (login page)

1. https://console.firebase.google.com → Create project  
2. **Authentication** → Sign-in method → enable **Google** + **Facebook**  
3. Project settings → Your apps → Web app → copy config  
4. `.env`:
   ```env
   FIREBASE_API_KEY=AIza...
   FIREBASE_AUTH_DOMAIN=invesmal-37db0.firebaseapp.com
   FIREBASE_PROJECT_ID=invesmal-37db0
   FIREBASE_APP_ID=1:119535514921:web:8eede90e72c66a1e17b338
   ```
5. **API Key kahan hai?** (tum sirf App ID set kiye ho — API Key alag hai)  
   - Firebase Console → ⚙️ **Project settings** → tab **General**  
   - Neeche **Your apps** → Web app → `firebaseConfig` mein **`apiKey`** copy karo  
   - Ya page ke upar **Web API Key** field  
6. Authentication → Settings → Authorized domains → add `127.0.0.1`  
7. Login page → **Continue with Google (Firebase)**  

Facebook ke liye Firebase console mein Facebook App ID/Secret bhi lagana hota hai.

---

## 5. Continue with Google (Laravel Socialite — optional)

1. https://console.cloud.google.com → New Project  
2. **APIs & Services → OAuth consent screen** (External, test users add karo)  
3. **Credentials → Create OAuth client ID** → Web application  
4. Authorized redirect URI:
   ```
   http://127.0.0.1:8000/auth/google/callback
   ```
5. `.env`:
   ```env
   GOOGLE_CLIENT_ID=xxxx.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=GOCSPX-xxxx
   ```
6. `php artisan config:clear`

---

## 5. reCAPTCHA + email verification (current behaviour)

| Page | reCAPTCHA | Email verification |
|------|-----------|-------------------|
| **Register** | Yes (invisible Enterprise) | Email sent; dashboard after verify link |
| **Login** | No | No extra step — demo accounts work |
| **Forgot password** | No | Reset link only |

reCAPTCHA uses invisible Enterprise (not checkbox) — same as most Firebase setups.

### Full server verify (Enterprise API enabled)

1. https://console.cloud.google.com → project **invesmal-37db0**
2. **APIs & Services → Library** → search **reCAPTCHA Enterprise API** → **Enable**
3. **APIs & Services → Credentials** → **Create API key** (restrict: reCAPTCHA Enterprise API only)
4. `.env`:
   ```env
   RECAPTCHA_API_KEY=your-server-api-key
   RECAPTCHA_STRICT=true
   ```
5. `php artisan config:clear`

Firebase Console → Authentication → reCAPTCHA mein bhi same site key honi chahiye jo `.env` ki `RECAPTCHA_SITE_KEY` hai.

---

## 6. Quick checklist

| Feature | `.env` keys | Works without? |
|---------|-------------|----------------|
| Pitch deck (demo) | — | ✅ Yes |
| Pitch deck (real AI) | `OPENAI_API_KEY` | Demo only |
| Forgot password inbox | Mailtrap SMTP | Dev link on page |
| Facebook login | `FACEBOOK_*` | Buttons show "setup required" |
| Google login | `GOOGLE_*` | Buttons show "setup required" |
| reCAPTCHA badge | `RECAPTCHA_SITE_KEY` | Optional |
| reCAPTCHA server verify | `RECAPTCHA_API_KEY` + Enterprise API enabled | Use `RECAPTCHA_STRICT=false` locally |

---

## 7. API key mujhe dena ho to

Chat mein **sirf** ye bhejo (password mat bhejna):

```
OPENAI_API_KEY=sk-...
```

Main `.env` line bata dunga — tum khud paste karna (security).

**Kabhi GitHub par API key commit mat karna.**

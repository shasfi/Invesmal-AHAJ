# Free Email — Asli Gmail / Inbox Par (FYP)

Mailtrap **sandbox** emails **Mailtrap website** par dikhti hain — **Gmail par nahi**.  
Resend free sirf **apni** email par bhejta hai (domain verify ke bina).

## Best free option: Gmail SMTP (recommended)

**Free**, **kisi bhi email** par bhej sakte ho (faasfi7222@gmail.com se), ~500/day.

### Step 1 — Google App Password

1. Gmail account par **2-Step Verification** ON karo:  
   https://myaccount.google.com/security  
2. **App passwords** kholo:  
   https://myaccount.google.com/apppasswords  
3. App name: `Invesmal` → **Create**  
4. **16 character password** copy karo (spaces hata kar paste karo)

### Step 2 — `.env` update

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=faasfi7222@gmail.com
MAIL_PASSWORD=abcdefghijklmnop
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=faasfi7222@gmail.com
MAIL_FROM_NAME=Invesmal
```

`MAIL_PASSWORD` = App Password (normal Gmail password NAHI)

### Step 3

```bash
php artisan config:clear
```

### Step 4 — Test

1. Pehle **Register** se account banao (email + password)  
2. **Forgot password** → wahi email  
3. **Gmail inbox** + **Spam** check karo  

---

## Option 2 — Brevo (free, 300 emails/day)

Agar Gmail app password na mile:

1. https://www.brevo.com → free account  
2. **SMTP & API** → SMTP key copy  
3. `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your-brevo-login-email
MAIL_PASSWORD=your-smtp-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-brevo-login-email
MAIL_FROM_NAME=Invesmal
```

---

## Kyun Mailtrap "nahi chalti" feel hoti hai?

| Service | Email kahan jati hai |
|---------|---------------------|
| Mailtrap **sandbox** | mailtrap.io inbox (fake testing) |
| Mailtrap **live** | Domain verify chahiye |
| Resend free | Sirf signup wali email |
| **Gmail SMTP** | **Har real email par** ✅ |

---

## Google login users

Forgot password sirf **email/password register** wale accounts ke liye.  
Google se login → **Continue with Google** use karo.

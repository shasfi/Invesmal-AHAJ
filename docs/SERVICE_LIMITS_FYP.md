# Service Limits — 2–3 Month FYP Project

Approximate free-tier limits for Invesmal (check each provider’s dashboard for current numbers).

## Forgot password (Gmail SMTP)

| Item | Limit |
|------|--------|
| Cost | Free (personal Gmail) |
| Sending | ~**500 emails per day** |
| 2–3 months FYP | Enough for demos, testing, small class (~hundreds of resets total) |
| Risk | Heavy spam/abuse can trigger Google security review |
| App Password | Works until you revoke it; no monthly expiry |

**Invesmal app limit:** 1 reset request per email every **30 seconds**; **10 requests per minute** per IP on the form.

---

## Google login (Firebase Authentication)

| Item | Limit |
|------|--------|
| Cost | **Spark (free)** plan |
| Phone auth | Limited free SMS |
| Google / Facebook sign-in | **Free** for typical FYP traffic |
| Monthly active users | High cap on free tier (thousands+) — fine for FYP |
| 2–3 months | No automatic shutdown; project stays active if you use it |

**Note:** Firebase Console project stays free unless you enable paid Blaze features.

---

## Facebook login (Firebase)

| Item | Limit |
|------|--------|
| Cost | Free via Firebase (same as Google) |
| Requirement | Facebook Developer app + Firebase Facebook provider configured |
| 2–3 months | Same as Google — suitable for FYP |

---

## OpenAI (pitch deck AI)

| Item | Limit |
|------|--------|
| Cost | Pay-as-you-go after free credits |
| FYP | Monitor usage in OpenAI dashboard |
| Fallback | Demo mode works without key |

---

## Summary for viva

- **Forgot password:** Gmail SMTP, free, real inbox, ~500/day.  
- **Google/Facebook login:** Firebase free tier, fine for 2–3 months of university demo.  
- **Not unlimited:** Avoid automated bulk email tests; respect 30s reset throttle.

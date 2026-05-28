# Invesmal CSS Architecture

Modular CSS loaded via Laravel Vite. **No monolithic `dashboard.css`** — removed May 2026.

## Layout bundles (every page)

| Layout | Vite CSS entries |
|--------|------------------|
| `layouts/dashboard` | `global/theme.css`, `components/dashboard-shared.css`, `components/forms-extended.css`, `dashboard/dashboard-partials.css` |
| `layouts/public` | `global/theme.css`, `components/public-shared.css`, `components/navbar.css`, `components/footer.css` |

Plus `@stack('styles')` for page modules.

## Page modules (`@push('styles')`)

| View(s) | CSS file(s) |
|---------|-------------|
| `auth/*` (form, forgot, reset, oauth) | `auth/login.css` |
| `admin/dashboard` | `admin/admin-dashboard.css` |
| `admin/activity-logs` | `admin/activity-logs.css` |
| `admin/verification` | `admin/verification.css` |
| `users/index` | `users/users-list.css` |
| `conversations/index` | `conversations/conversations.css` |
| `conversations/show` | `conversations/chat.css` |
| `investments/*` | `investments/investments.css` |
| `documents/index` | `documents/documents.css` |
| `pitch_decks/*` | `pitch_decks/pitch-decks.css` (imports `components/pitch-buttons.css`) |
| `meetings/*` | `meetings/meetings.css` |
| `startups/index` | `startups/discovery.css`, `startups/startup-cards.css` |
| `startups/show` | `startups/show.css`, `startups/startup-cards.css` |
| `startups/landing` | `public/landing.css` |
| `ai/sentiment`, `ai/insights` | `components/pitch-buttons.css` |
| `ai/sentiment-index` | `components/pitch-buttons.css`, `documents/documents.css` |

## Dashboard-only pages (layout bundle only)

`dashboard/index`, `users/profile`, `users/form`, `users/founders`, `users/investors`, `startups/form`, `reports/*`, `notifications/*`, `notification-preferences`, `conversations/create`

## Directory tree

```
resources/css/
├── global/theme.css           # Tokens, reset, light mode, app chrome, animations
├── components/
│   ├── dashboard-shared.css   # Bento, tables, cards structure, forms base
│   ├── forms-extended.css     # auth-form-group, profile-card
│   ├── public-shared.css      # Public startup-card v1, pagination
│   ├── navbar.css
│   ├── footer.css
│   └── pitch-buttons.css      # .pp-btn (AI + pitch decks)
├── dashboard/dashboard-partials.css
├── auth/login.css
├── admin/
├── users/
├── conversations/
├── investments/
├── documents/
├── pitch_decks/pitch-decks.css
├── meetings/
├── startups/
└── public/landing.css
```

## Production fallback

`partials/vite-assets.blade.php` loads layout CSS from `build/manifest.json` when Vite dev server is off. Pass `layout` => `dashboard` or `public`.

## Build

```bash
npm run build
```

## Adding a new page

1. Create `resources/css/{module}/{page}.css`
2. Add entry to `vite.config.js` `input` array
3. In Blade: `@push('styles')` + `@include('partials.styles-module', ['entries' => [...]])`

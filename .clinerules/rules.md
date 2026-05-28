
---

# ✅ CLINE RULES — INVESMAL (FINAL PRODUCTION VERSION)

````md
# 🏷️ PROJECT IDENTITY

**Project Name**: Invesmal  
**Core Purpose**: A premium startup-investor ecosystem connecting university founders, investors, and mentors with AI-powered matching, analytics, and funding workflows.

The platform must feel:
- Silicon Valley SaaS-grade
- investor-ready
- production-level architecture
- visually premium and modern
- highly scalable and modular

Reference inspiration:
- Stripe
- Linear
- Vercel
- Framer
- Arc
- Notion (modern UI parts only)

---

# 🛠️ TECH STACK — STRICT CONSTRAINTS

| Layer | Technology |
|-------|------------|
| Backend | PHP (Laravel) |
| Templates | Blade |
| Frontend | Vanilla HTML, CSS, JavaScript (optional Alpine.js only) |
| AI / ML | Python microservices (FastAPI / Flask) |
| Database | MySQL |
| Storage | AWS S3 |
| Hosting | AWS / Azure |

## HARD RULES
- NO MERN stack
- NO React / Vue / Angular / Node backend
- NO CSS frameworks (Tailwind, Bootstrap, etc.)
- NO UI libraries or design systems
- Business logic ONLY in Laravel Services
- AI logic ONLY in Python microservices
- Keep architecture modular and production-grade
- Prioritize maintainability and scalability

---

# 🎨 FRONTEND RULES (ABSOLUTE)

## Styling System
- ONLY ONE CSS FILE: `resources/css/dashboard.css`
- PURE vanilla CSS only
- CSS variables required (`:root`)
- Grid + Flexbox only
- No inline styles (`style=""`)
- No utility frameworks

## Allowed Features
- CSS Variables
- Gradients
- Glassmorphism (`backdrop-filter`)
- Shadows & glow effects
- `clamp()` responsive scaling
- Smooth transitions
- Subtle keyframe animations

## FORBIDDEN
- TailwindCSS
- Bootstrap / Material UI / Bulma
- Inline styling
- Repetitive grid-only layouts
- Hardcoded pixel-only layouts

---

# 🎨 DESIGN SYSTEM (CORE PHILOSOPHY)

## RULE #1
Every section MUST feel visually unique.

DO NOT:
- reuse identical card grids
- repeat same spacing rhythm
- create uniform dashboards
- stack boring rectangles

---

## Layout Language
- Bento-grid (asymmetric)
- layered depth sections
- editorial-style UI
- floating components
- overlapping visual blocks
- variable density layouts
- cinematic spacing

---

## Section Diversity Rules

Each page must include:
- at least 3 distinct layout styles
- different visual rhythm per section
- different card behavior per section

Example:
- Hero → cinematic + gradient + large typography
- Stats → floating glass strips
- Listings → asymmetric grid
- Insights → split layout + charts
- Messaging → minimal dark UI

---

:root {
  /* 🌿 Base Backgrounds */
  --bg-primary: #0f1412;      /* deep forest-black (modern replacement of pure black) */
  --bg-secondary: #151f1b;    /* soft layered dark green-black */

  /* 🪟 Surfaces (Glass / Cards) */
  --surface: rgba(24, 67, 67, 0.12);        /* subtle teal glass */
  --surface-strong: rgba(22, 48, 44, 0.75); /* deeper premium card surface */

  /* 🎯 Brand Colors (your identity preserved) */
  --primary: #184343;   /* deep professional teal (trust / investors) */
  --secondary: #567256; /* muted financial green (growth / stability) */
  --accent: #E3D2C0;    /* warm investor beige (premium highlight) */

  /* ✨ Enhanced Accent System (modern touch) */
  --accent-warm: #D6BFA9;  /* richer beige for hover / glow */
  --accent-soft: #7FA39A;   /* soft teal-green for gradients */

  /* 📊 Status Colors (muted, non-neon) */
  --success: #5C8F6A;  /* calm green */
  --warning: #C89B5D;  /* muted gold (investor feel) */
  --danger: #B46A6A;   /* soft red (not aggressive) */

  /* 📝 Typography */
  --text: #F5F1EC;     /* warm off-white */
  --muted: #B9B2A6;    /* soft beige-gray */

  /* 🧱 Borders */
  --border: rgba(227, 210, 192, 0.12); /* beige-tinted subtle border */

  /* 🌫️ Depth */
  --shadow: 0 18px 50px rgba(0, 0, 0, 0.45);

  /* ✨ Glow (subtle premium effect) */
  --glow-primary: 0 0 25px rgba(24, 67, 67, 0.25);
  --glow-accent: 0 0 30px rgba(227, 210, 192, 0.15);
}

# ✨ TYPOGRAPHY SYSTEM

* Font: Inter or Geist
* Tight tracking: `-0.02em`
* Strong hierarchy
* Large hero typography
* Minimal paragraph width for readability

Rules:

* Headings = bold + large + spaced
* Body = clean + muted
* CTAs = strong contrast

---

# 🧠 UI QUALITY STANDARDS

UI must feel:

* premium SaaS startup
* investor-grade fintech platform
* modern AI product

NOT:

* admin panel
* university portal
* Bootstrap dashboard
* repetitive SaaS template

---

# 🧩 COMPONENT DESIGN RULES

## Cards

* Must vary in size and shape
* Must feel floating
* Must use layered glass surfaces
* Must NOT be uniform grid blocks

## Buttons

* rounded-xl or larger
* soft glow hover
* gradient accents allowed
* premium tactile feel

## Sections

Each page must mix:

* large feature blocks
* compact stat strips
* side panels
* floating elements

---

# 🤖 AI SYSTEM (OPENAI INTEGRATION)

## AI RULES

* Use OpenAI API only
* AI logic isolated in Python services or Laravel service layer
* All responses must be structured JSON
* No blocking UI flows

---

## CORE AI FEATURES

### MUST BUILD

1. Startup Idea Analyzer
2. Investor Matching Engine
3. Pitch Deck Generator
4. Startup Scoring System

### OPTIONAL HIGH VALUE

* Sentiment analysis (investor chat)
* Market trend detection
* Startup summarization
* Investor message assistant
* Document intelligence extractor

---

# 🏗️ LARAVEL ARCHITECTURE RULES

## Structure

* Controllers → thin only
* Services → business logic
* Requests → validation layer
* Policies → authorization logic
* Models → relationships + scopes only

## Naming

* Classes: PascalCase
* Methods: snake_case
* Tables: plural snake_case

---

# 🐍 PYTHON AI SERVICES

Structure:
ai-services/
sentiment/
pitch_assist/
matching/

Rules:

* FastAPI preferred
* REST APIs only
* Stateless services
* Docker-ready microservices
* Communication via HTTP or queue (SQS/Redis)

---

# ⚡ PERFORMANCE RULES

* Lazy load AWS S3 assets
* Paginate all lists
* Minimize DOM size
* Avoid heavy JS frameworks
* Optimize Blade rendering
* Keep animations lightweight

---

# 🔄 DEVELOPMENT WORKFLOW

1. Build Laravel core system first
2. Validate authentication + CRUD
3. Add AI services after stability
4. Design UI last (polish phase)
5. Add animations only in final phase

---

# 🚫 ANTI-PATTERNS (STRICT)

NEVER:

* build repetitive dashboards
* use boxy grid layouts everywhere
* copy Bootstrap UI patterns
* overuse cards without variation
* create template-looking SaaS UI
* introduce frontend frameworks
* mix inconsistent design systems

---

# 🧠 FINAL GOAL

The final product must feel like:

"A funded Silicon Valley startup platform with AI intelligence, premium UX, and investor-grade UI systems."

```

---

# 🚀 What you now have

This is now:
- production-grade
- AI-agent optimized
- non-conflicting
- UI-restrictive (prevents boxy design)
- architecture-safe
- cost-efficient for your $15 budget

---


```

# Invesmal — Complete Technical Documentation

## Final Year Project (FYP) — Evaluation & Viva Preparation

---

## 1. PROJECT OVERVIEW

**Invesmal** is a **multi-role startup investment platform** built with Laravel 12. It connects **Student Founders**, **Investors**, and **Mentors** in a unified ecosystem where students can pitch their startup ideas, investors can discover and fund promising ventures, and mentors can guide early-stage startups.

### Core Workflow
```
Student Founder → Creates Startup → Uploads Pitch Deck → Receives Investments
Investor        → Discovers Startups → Expresses Interest → Invests
Mentor          → Browses Startups → Schedules Meetings → Mentors Founders
Admin           → Verifies Users/Startups → Moderates Content → Monitors Activity
```

### Key Purpose
- Bridge the gap between university students with startup ideas and real investors
- Provide AI-powered pitch deck generation and analysis
- Enable secure messaging and meeting scheduling between stakeholders
- Track investments from interest to approval

---

## 2. TECH STACK

### Frontend
| Technology | Purpose |
|------------|---------|
| **Laravel Blade** | Server-side templating engine for all views |
| **CSS3 Custom Properties** | Dark-theme design system with 25+ CSS variables (design tokens) |
| **Inter Font** | Google Fonts — primary typeface |
| **Font Awesome 6.5.2** | Icons across all modules |
| **Alpine.js 3.x** | Lightweight JavaScript framework for interactivity |
| **Vite** | Asset bundling and hot module replacement |

### Backend (Laravel 12)
| Component | Details |
|-----------|---------|
| **PHP 8.2** | Runtime environment |
| **Laravel 12.58** | Framework version |
| **16 Controllers** | 12 root + 4 admin sub-controllers |
| **10 Models** | User, Startup, Investment, Meeting, Conversation, Message, Document, Notification, PitchDeck, ActivityLog |
| **9 Services** | Business logic layer (Auth, Meeting, Investment, Document, Notification, PitchDeck, AI, etc.) |
| **7 Middleware** | Auth, Guest, CheckRole, Verified, Throttle, CSRF, etc. |
| **6 Form Requests** | Login, Register, ForgotPassword, ResetPassword, StoreUser, UpdateUser, StoreStartup, etc. |

### Database
| Detail | Description |
|--------|-------------|
| **Engine** | SQLite (development) / MySQL (production-ready) |
| **Tables** | 14 tables (users, startups, investments, meetings, conversations, messages, documents, notifications, pitch_decks, activity_logs, and pivot tables) |
| **Migrations** | 15 migration files |
| **Relationships** | Full Eloquent ORM with hasMany, belongsTo, belongsToMany |

### Services / APIs
| Service | Purpose |
|---------|---------|
| **AIService** | AI-powered pitch deck generation and analysis |
| **PitchDeckService** | Pitch deck CRUD, AI integration, public data extraction |
| **NotificationService** | In-app notifications + email layer |
| **MeetingService** | Meeting scheduling, accept/decline/cancel |
| **InvestmentService** | Investment workflow (interest → approval/rejection) |
| **ConversationService** | Messaging between users |
| **DocumentService** | File upload, versioning, download |

---

## 3. MODULES BREAKDOWN

### 3.1 Authentication Module

**Controller:** `AuthController.php` (10 methods)  
**Requests:** `LoginRequest`, `RegisterRequest`, `ForgotPasswordRequest`, `ResetPasswordRequest`  
**Views:** `auth/form.blade.php`, `auth/forgot-password.blade.php`, `auth/reset-password.blade.php`, `auth/verify-email.blade.php`

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **Registration** | ✅ Working | Name, email, password (min 8 chars, confirmed), role selection (Student/Investor/Mentor) |
| **Login** | ✅ Working | Email + password, "Remember me" checkbox, session regeneration |
| **Logout** | ✅ Working | Session invalidation + CSRF token regeneration |
| **Password Reset** | ✅ Working | Forgot password → email link → set new password flow using Laravel's Password facade |
| **Throttling** | ✅ Route-level | Login: 5/min, Register: 3/min, Forgot Password: 1/min |
| **Email Verification** | ⚠️ Removed | Disabled (no SMTP server). Users go directly to dashboard after signup |

#### Security:
- Session regeneration on login
- Session invalidation on logout
- Route-level rate limiting
- CSRF protection on all forms

---

### 3.2 User Roles

**Model:** `User.php` (implements Authenticatable, uses Notifiable, HasFactory)

| Role | Database Value | Description |
|------|---------------|-------------|
| **Student Founder** | `student_founder` | Creates startups, uploads pitch decks, receives investments, schedules meetings |
| **Investor** | `investor` | Discovers startups, expresses interest, invests, views reports |
| **Mentor** | `mentor` | Browses startups, schedules mentoring meetings, guides founders |
| **Admin** | `admin` | Verifies users/startups, moderates content, monitors platform activity |

#### Role-Based Authorization:
- **Route-level:** `role:admin` middleware applied to `/admin/*` routes
- **Controller-level:** `UserController::authorizeEdit()` — only owner or admin can edit profiles
- **View-level:** Dashboard sidebar and navbar show role-specific navigation links

#### User Model Fields:
```
name, email, password (hashed), role, avatar, is_verified, bio, university
```

#### User Relationships:
```
startups(), notifications(), activityLogs(), investments(), documents(),
scheduledMeetings(), invitedMeetings(), sentMessages(), pitchDecks()
```

#### User Scopes:
```
studentFounders(), investors(), mentors(), verified()
```

---

### 3.3 Startup Module

**Controller:** `StartupController.php` (9 methods)  
**Model:** `Startup.php` (50+ fillable fields, 23 methods)  
**Views:** `startups/landing.blade.php`, `startups/index.blade.php`, `startups/show.blade.php`, `startups/form.blade.php`

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **Public Landing Page** | ✅ Working | Hero section, platform stats (total startups, funded, total raised, active investors), featured/trending/recently funded sections |
| **Discover Page** | ✅ Working | Search, filter by stage (idea/mvp/funded), filter by industry, paginated (12 per page) |
| **Startup Detail** | ✅ Working | Full startup profile with funding progress bar, investor count, team size, pitch deck integration |
| **Create Startup** | ✅ Working | Name, description, stage, industry, funding goal, team size, logo upload |
| **Edit Startup** | ✅ Working | Update all fields, replace logo |
| **Delete Startup** | ✅ Working | Soft delete with redirect |
| **Search** | ✅ Working | Full-text search across name, description, industry, mission |
| **Stage Filtering** | ✅ Working | idea, mvp, funded |
| **Industry Filtering** | ✅ Working | Dynamic industry list from database |

#### Startup Model Key Features:
- **Funding tracking:** `funding_goal`, `amount_raised`, `funding_percent` accessor
- **Progress scoring:** `calculateProgressScore()` — scores completeness (name, description, logo, website, team, etc.)
- **Money formatting:** `formatted_funding_goal`, `formatted_amount_raised` — auto-converts to K/M format
- **Trending algorithm:** `scopeTrending()` — orders by distinct investor count
- **Relationships:** founder, verifier, pitchDeck, investments, approvedInvestments

---

### 3.4 Investment Module

**Controller:** `InvestmentController.php` (6 methods)  
**Model:** `Investment.php`  
**Service:** `InvestmentService.php`  
**Views:** `investments/index.blade.php`, `investments/show.blade.php`, `investments/create.blade.php`

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **Express Interest** | ✅ Working | Investor submits amount + message for a startup |
| **Investment List** | ✅ Working | Role-aware: investors see their investments, founders see investments on their startups, admin sees all pending |
| **Approve Investment** | ✅ Working | Admin approval with remarks |
| **Reject Investment** | ✅ Working | Admin rejection with remarks |
| **Status Tracking** | ✅ Working | pending → approved/rejected |
| **Notifications** | ✅ Wired | Founder notified on new interest, investor/founder notified on approval/rejection |

#### Investment Model:
```
investor_id, startup_id, amount, status (pending/approved/rejected), message, admin_remarks, reviewed_by, reviewed_at
```

---

### 3.5 Meeting Module

**Controller:** `MeetingController.php` (7 methods)  
**Model:** `Meeting.php`  
**Service:** `MeetingService.php`  
**Migration:** `2026_05_18_000001_create_meetings_table.php`  
**Views:** `meetings/index.blade.php` (3 tabs), `meetings/show.blade.php`, `meetings/create.blade.php`

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **Schedule Meeting** | ✅ Working | Title, notes, location, date/time, invitee selection |
| **Meeting List** | ✅ Working | Three tabs: All, Upcoming, Past |
| **Accept Meeting** | ✅ Working | Invitee accepts → status changes to accepted |
| **Decline Meeting** | ✅ Working | Either party can decline |
| **Cancel Meeting** | ✅ Working | Either party can cancel |
| **Authorization** | ✅ Working | Only scheduler or invitee can view/act on a meeting |
| **Notifications** | ✅ Wired | All parties notified on create/accept/decline/cancel with email support |

#### Meeting Model:
```
scheduler_id, invitee_id, startup_id (nullable), title, notes, location,
scheduled_at, status (pending/accepted/declined/cancelled)
```

---

### 3.6 Messaging Module

**Controller:** `ConversationController.php` (5 methods)  
**Models:** `Conversation.php`, `Message.php`  
**Service:** `ConversationService.php`  
**Views:** `conversations/index.blade.php`, `conversations/show.blade.php`

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **Conversation List** | ✅ Working | Shows all conversations with last message preview and unread count |
| **Start Conversation** | ✅ Working | Start from user profile with optional subject |
| **Send Message** | ✅ Working | Text messages with sender/receiver distinction |
| **Real-time-like Polling** | ✅ Working | 5-second AJAX polling for new messages |
| **Mark as Read** | ✅ Working | Messages marked read when conversation viewed |
| **Authorization** | ✅ Working | Only participants can view conversations |
| **Notifications** | ✅ Wired | In-app notification when new message received |

---

### 3.7 Pitch Deck Module (AI-Powered)

**Controller:** `PitchDeckController.php` (10 methods)  
**Model:** `PitchDeck.php`  
**Service:** `PitchDeckService.php` + `AIService.php`  
**Policy:** `PitchDeckPolicy.php` (authorize view/update/delete)  
**Views:** 6 files (index, create, edit, show, analysis, upload)

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **AI Generation** | ✅ Working | Describe startup → AI generates full pitch deck with sections, tagline, executive summary |
| **Upload & Analyze** | ✅ Working | Upload PDF/PPTX → AI analyzes and scores |
| **Edit Pitch Deck** | ✅ Working | Edit sections, tagline, executive summary |
| **AI Analysis** | ✅ Working | Overall score, investor readiness, strengths/weaknesses, category breakdown (9 categories), key improvements |
| **Public Summary API** | ✅ Working | JSON endpoint for startup profile pages |
| **Delete** | ✅ Working | With policy authorization |
| **Error Handling** | ✅ Protected | Try/catch around AI generation with user-friendly fallback |

#### Analysis Categories:
```
Clarity, Problem Statement, Solution Fit, Market Opportunity, Business Model,
Competitive Advantage, Team Strength, Financial Projections, Ask Clarity
```

---

### 3.8 Document Module

**Controller:** `DocumentController.php` (5 methods)  
**Model:** `Document.php`  
**Service:** `DocumentService.php`  
**Views:** `documents/index.blade.php`

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **Upload** | ✅ Working | Inline upload form on documents page, file type selection, startup association |
| **Download** | ✅ Working | Secure download with ownership check + file-exists validation |
| **Delete** | ✅ Working | With ownership authorization |
| **Versioning** | ✅ Working | Auto-increments version for same startup + type |
| **Supported Types** | ✅ | pitch_deck, business_plan, financials, other |
| **File Types** | ✅ | PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX (max 10MB) |

---

### 3.9 Notification System

**Controller:** `NotificationController.php` (3 methods)  
**Model:** `Notification.php`  
**Service:** `NotificationService.php`  
**Mail:** `NotificationMail.php`  
**Views:** `notifications/index.blade.php`

#### Features:
| Feature | Status | Details |
|---------|--------|---------|
| **In-App Notifications** | ✅ Working | Paginated list with unread highlighting |
| **Mark as Read** | ✅ Working | Individual + mark all as read |
| **Duplicate Prevention** | ✅ Working | Same type+title within 5 minutes prevented |
| **Email Layer** | ✅ Ready | Queueable emails via `notifyWithEmail()` — sends only if user is verified |
| **Unread Badge** | ✅ Working | Badge count in dashboard navbar and sidebar |
| **Notification Types** | ✅ | meeting, investment, message, security, info, success, warning |

---

## 4. COMPLETE FEATURES LIST

### By Module
| Module | Features Count | Status |
|--------|---------------|--------|
| Authentication | 5 (Login, Register, Logout, Forgot Password, Reset Password) | ✅ Full |
| User Management | 4 (View Profile, Edit Profile, List Users, Role Authorization) | ✅ Full |
| Startup Management | 9 (Landing, Discover, Search, Filter, Create, View, Edit, Delete, Progress) | ✅ Full |
| Investment System | 5 (Express Interest, List, View, Approve, Reject) | ✅ Full |
| Meetings | 7 (Schedule, List, View, Accept, Decline, Cancel, Tabs) | ✅ Full |
| Messaging | 5 (List, Start, Send, Receive, Mark Read) | ✅ Full |
| Pitch Decks | 7 (Generate, Upload, Edit, Analyze, View, Public API, Delete) | ✅ Full |
| Documents | 4 (Upload, Download, List, Delete) | ✅ Full |
| Notifications | 4 (In-app, Mark Read, Duplicate Prevention, Email) | ✅ Full |
| Admin Panel | 4 (Verification, Moderation, Monitoring, Activity Logs) | ✅ Full |
| Dashboard | 4 (Admin, Founder, Investor, Mentor views) | ✅ Full |
| Reports | 2 (Startup Report, Investor Report) | ✅ Basic |

### Role-Based Feature Access
| Feature | Student | Investor | Mentor | Admin | Public |
|---------|---------|----------|--------|-------|--------|
| View Landing Page | ✅ | ✅ | ✅ | ✅ | ✅ |
| Discover Startups | ✅ | ✅ | ✅ | ✅ | ✅ |
| View Startup Detail | ✅ | ✅ | ✅ | ✅ | ✅ |
| Create Startup | ✅ | ❌ | ❌ | ❌ | ❌ |
| Edit Startup | ✅ (own) | ❌ | ❌ | ✅ | ❌ |
| Generate Pitch Deck | ✅ | ❌ | ❌ | ❌ | ❌ |
| Express Investment Interest | ❌ | ✅ | ❌ | ❌ | ❌ |
| View Investments | ✅ (on own) | ✅ (own) | ❌ | ✅ (all) | ❌ |
| Schedule Meetings | ✅ | ✅ | ✅ | ❌ | ❌ |
| Send Messages | ✅ | ✅ | ✅ | ✅ | ❌ |
| Upload Documents | ✅ | ✅ | ✅ | ✅ | ❌ |
| View Reports | ❌ | ✅ | ❌ | ✅ | ❌ |
| Admin Panel | ❌ | ❌ | ❌ | ✅ | ❌ |

---

## 5. SYSTEM FLOW

### User Journey: Student Founder
```
1. Visit landing page → Click "Get Started"
2. Register → Select "Student Founder" role → Auto-logged in → Redirected to Dashboard
3. Dashboard shows: "Create Startup" CTA
4. Create Startup → Fill form (name, description, stage, industry, funding goal) → Submit
5. Startup detail page shown with funding progress
6. Generate Pitch Deck → Describe startup → AI generates sections → Edit & save
7. Upload supporting documents (business plan, financials)
8. Investor discovers startup → Expresses interest → Notification received
9. Schedule meeting with investor → Discuss terms
10. Track investment status (pending → approved)
```

### User Journey: Investor
```
1. Register as Investor → Dashboard shows platform stats
2. Browse Discover page → Search/filter startups by stage/industry
3. View startup detail → Review pitch deck score
4. Express Interest → Submit amount + message
5. Message founder for discussion
6. Schedule meeting for due diligence
7. Wait for admin approval → Investment approved/rejected
8. View investment portfolio in My Investments
```

### Data Flow
```
User Registration → User Model → Auth Middleware → Role-Based Dashboard
Startup Creation → Startup Model → Public Discovery → Investor Interest
Investment Interest → Investment Model → Admin Review → Approval/Rejection → Notifications
Pitch Deck Generation → AIService → PitchDeck Model → Analysis → Public API → Startup Profile
Meeting Schedule → Meeting Model → Status Updates → Notifications → Both Parties
Message Send → Conversation Model → Polling → Real-time Display → Notification
```

---

## 6. ROUTES & STRUCTURE SUMMARY

### Key Route Groups

| Route Group | Middleware | Purpose |
|-------------|-----------|---------|
| `/` | web | Public landing page |
| `/discover`, `/startups/{startup}` | web | Public discovery |
| `/login`, `/register` | guest | Authentication |
| `/forgot-password`, `/reset-password/{token}` | guest | Password reset |
| `/dashboard` | auth | Role-based dashboard |
| `/users/*` | auth | User management |
| `/startups/create`, `/startups/{id}/edit` | auth | Startup CRUD |
| `/conversations/*` | auth | Messaging |
| `/meetings/*` | auth | Meeting management |
| `/documents/*` | auth | Document upload/download |
| `/investments/*` | auth | Investment management |
| `/pitch-decks/*` | auth | AI pitch deck suite |
| `/pitch-decks/{id}/summary` | public | Public pitch deck API |
| `/notifications/*` | auth | Notification center |
| `/admin/*` | auth + role:admin | Admin panel |
| `/reports/*` | auth | Reports for admin/investors |

### Controller Map

| Controller | Routes | Role |
|------------|--------|------|
| `AuthController` | login, register, logout, forgot/reset password | Guest |
| `DashboardController` | /dashboard (invoke) | Auth |
| `StartupController` | CRUD + landing + discover + search | Mixed (public + auth) |
| `InvestmentController` | CRUD + approve/reject | Auth |
| `MeetingController` | CRUD + accept/decline/cancel | Auth |
| `ConversationController` | index, show, store, sendMessage, markRead | Auth |
| `DocumentController` | CRUD + download | Auth |
| `PitchDeckController` | CRUD + generate + upload + analyze + publicSummary | Mixed |
| `NotificationController` | index, markRead, markAllRead | Auth |
| `UserController` | CRUD + profile | Auth |
| `ReportController` | startupReport, investorReport | Auth |
| `Admin\VerificationController` | index, verifyUser, verifyStartup | Admin |
| `Admin\ModerationController` | index, flag, unflag | Admin |
| `Admin\MonitoringController` | index | Admin |
| `Admin\ActivityLogController` | index | Admin |

### Architecture Pattern
```
Route → Controller → Service → Model → Database
                        ↓
                    NotificationService (side-effect)
                        ↓
                    View (Blade) → Response
```

---

## 7. SAMPLE DATA (For Testing)

Run: `php artisan db:seed --class=SampleDataSeeder`

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@invesmal.com | password |
| Founder | ahmed@invesmal.com | password |
| Founder | fatima@invesmal.com | password |
| Investor | zain@invesmal.com | password |
| Investor | sara@invesmal.com | password |
| Mentor | bilal@invesmal.com | password |

**Seeded data includes:** 4 startups (EcoCharge, FarmLink, MediConnect, EduBridge), 3 investments, 3 meetings, 2 documents, 2 conversations with messages.

---

## 8. SYSTEM STATISTICS

| Metric | Count |
|--------|-------|
| Total Controllers | 16 |
| Total Models | 10 |
| Total Services | 9 |
| Total Middleware | 7 |
| Total Blade Views | 40+ |
| Total Migrations | 15 |
| Total Routes | 45+ |
| Lines of PHP Code | ~3,500+ |
| Lines of CSS | ~900 |
| Database Tables | 14 |

---

*Documentation generated for FYP evaluation and viva preparation.*
*Project: Invesmal — Multi-Role Startup Investment Platform*
*Framework: Laravel 12 | PHP 8.2 | SQLite*
---
name: PTE Management System
description: Warm, trustworthy admin system for running a Malaysian tuition centre's day-to-day operations
colors:
  indigo-primary: "#3730a3"
  indigo-hover: "#4338ca"
  indigo-tint: "#e0e7ff"
  indigo-accent: "#6366f1"
  warm-accent: "#d97706"
  warm-accent-tint: "#fef3c7"
  page-bg: "#f1f5f9"
  surface: "#ffffff"
  ink: "#1e293b"
  ink-muted: "#64748b"
  border: "#e2e8f0"
  success: "#15803d"
  success-tint: "#dcfce7"
  danger: "#b91c1c"
  danger-tint: "#fee2e2"
  warning: "#a16207"
  warning-tint: "#fef9c3"
  overdue: "#c2410c"
  overdue-tint: "#ffedd5"
typography:
  title:
    fontFamily: "system-ui, -apple-system, Segoe UI, Roboto, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 600
    lineHeight: 1.3
    letterSpacing: "normal"
  section-label:
    fontFamily: "system-ui, -apple-system, Segoe UI, Roboto, sans-serif"
    fontSize: "0.75rem"
    fontWeight: 500
    lineHeight: 1.2
    letterSpacing: "0.05em"
  body:
    fontFamily: "system-ui, -apple-system, Segoe UI, Roboto, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: "normal"
  metric:
    fontFamily: "system-ui, -apple-system, Segoe UI, Roboto, sans-serif"
    fontSize: "1.5rem"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "normal"
rounded:
  sm: "6px"
  md: "8px"
  lg: "12px"
  pill: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "24px"
  2xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.indigo-primary}"
    textColor: "#ffffff"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  button-primary-hover:
    backgroundColor: "{colors.indigo-hover}"
    textColor: "#ffffff"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  button-secondary:
    backgroundColor: "{colors.indigo-tint}"
    textColor: "{colors.indigo-primary}"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  button-danger:
    backgroundColor: "#dc2626"
    textColor: "#ffffff"
    rounded: "{rounded.md}"
    padding: "8px 16px"
  card:
    backgroundColor: "{colors.surface}"
    rounded: "{rounded.lg}"
    padding: "24px"
  badge-success:
    backgroundColor: "{colors.success-tint}"
    textColor: "{colors.success}"
    rounded: "{rounded.pill}"
    padding: "2px 8px"
  badge-danger:
    backgroundColor: "{colors.danger-tint}"
    textColor: "{colors.danger}"
    rounded: "{rounded.pill}"
    padding: "2px 8px"
  badge-warning:
    backgroundColor: "{colors.warning-tint}"
    textColor: "{colors.warning}"
    rounded: "{rounded.pill}"
    padding: "2px 8px"
  input:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.ink}"
    rounded: "{rounded.md}"
    padding: "8px 12px"
---

# Design System: PTE Management System

## 1. Overview

**Creative North Star: "The Front Desk"**

Picture the front desk of a well-run neighbourhood tuition centre: organized, calm, and welcoming. The person behind it knows every parent by name, keeps the ledger straight, and never makes a family feel like a database row. That's the register this system runs in — indigo and slate carry the "organized and trustworthy" half, and every screen should still feel like it was built by people who know the students, not by a faceless SaaS vendor. It explicitly rejects the sterile gray-and-blue corporate-dashboard look and the dense, faceless data-grid feel that most admin tools default to — this is software for people managing relationships (parents, kids, tutors), not just records.

The system is currently **calm and steady** in its interaction character: soft shadows, gentle hover states, no bounce or flashy motion. It reflects PRODUCT.md's "trust the numbers" principle — an OWNER glancing at the dashboard should feel confidence, not spectacle.

**Key Characteristics:**
- Indigo-primary (#3730a3) navigation and primary actions, on a soft slate-gray canvas (#f1f5f9)
- Flat white cards with a single soft shadow (`shadow-sm`) and a hairline border — never stacked or nested
- System sans-serif throughout; no display font, no custom type — clarity over branding flourish
- Semantic status color for every state (green/red/yellow/orange), consistently mapped to the same meaning everywhere
- A warm accent (amber, #d97706) is reserved for human-touch moments — welcome messages, positive milestones, empty states — to keep the system from reading as cold enterprise software, without diluting indigo as the operational primary

## 2. Colors

The palette is indigo-led and slate-neutral, with a disciplined semantic-status set and one warm accent held in reserve for human moments.

### Primary
- **Front Desk Indigo** (#3730a3): Sidebar background, primary buttons, active nav state, primary links on hover. Carries the "organized and trustworthy" half of the brand. Used deliberately, not everywhere — the sidebar and calls-to-action are its home, not body text or large surfaces.
- **Indigo Hover** (#4338ca): Hover state for indigo-800 surfaces (buttons, active nav).
- **Indigo Tint** (#e0e7ff): Light backgrounds for badges, icon containers, secondary buttons — the "soft" register of the primary.
- **Indigo Accent** (#6366f1): Reserved for small highlight moments (rarely used today; available for focus rings, chart accents).

### Secondary
- **Front Desk Amber** (#d97706): The warmth token. Not present in the current build — introduced here as the deliberate answer to "how do we avoid feeling like cold enterprise software" without touching the operational indigo. Use sparingly: a warm welcome banner, a "you're all caught up" empty state, a positive milestone callout. Never for primary actions or status meaning (those stay semantic green/red/yellow/orange).
- **Amber Tint** (#fef3c7): Background for amber-accented callouts.

### Neutral
- **Page Canvas Slate** (#f1f5f9): Main content background (`bg-slate-100`), sits behind every card.
- **Surface White** (#ffffff): Cards, tables, modals — the "paper" the content sits on.
- **Ink** (#1e293b): Primary body text, headings (`text-slate-800`).
- **Muted Ink** (#64748b): Secondary text, labels, placeholder-adjacent copy (`text-slate-500`).
- **Hairline Border** (#e2e8f0): Card borders, table dividers (`border-slate-200`).

### Named Rules
**The Ten Percent Rule.** Amber (the warmth accent) never covers more than a small, deliberate moment on any screen — a banner, an icon, an empty-state illustration tint. It is a seasoning, not a surface. Indigo remains the only color that structures navigation and primary action.

**The One Meaning Rule.** Green always means Active/Present/Paid/Completed. Red always means Inactive/Absent/Overdue-critical. Yellow always means Late/Partial/Scheduled. Orange always means Overdue. Never reuse a status color for a different meaning on a different screen.

## 3. Typography

**Body Font:** system-ui (with -apple-system, Segoe UI, Roboto, sans-serif fallback)
**Display/Label Font:** same system stack — no separate display face

**Character:** A single, honest system sans-serif carries the whole interface. Nothing about the type system tries to feel "designed" — the personality comes from color, spacing, and copy tone, not typographic flourish. This matches a front-desk tool: legible instantly, no learning curve, no font-loading delay.

### Hierarchy
- **Title** (600 weight, 1.25rem / 20px, 1.3 line-height): Page titles ("Students", "Dashboard"). One per page, top-left.
- **Section Label** (500 weight, 0.75rem / 12px, uppercase, 0.05em tracking): Card headers, table column headers, sidebar group labels ("PEOPLE", "ACADEMIC", "FINANCE"). Always muted-ink or indigo-300 on dark surfaces.
- **Body** (400 weight, 0.875rem / 14px, 1.5 line-height): Table cells, form labels, paragraph copy. The workhorse size for the entire app.
- **Metric** (700 weight, 1.5rem / 24px, 1.2 line-height): KPI numbers on the dashboard cards. The only place type gets loud, and only by weight/size, never by color gradient.

### Named Rules
**The One Voice Rule.** No second typeface is introduced for "personality." Warmth and hierarchy come from weight, size, color, and spacing — never from a display font layered on top of the system sans.

## 4. Elevation

The system is flat by default, lifted only where a surface needs to visually separate from the page canvas — cards and modals get a single soft shadow plus a hairline border; nothing else uses elevation. There is no multi-level shadow scale; depth is binary (flat page vs. lifted card), which keeps the interface calm rather than layered.

### Shadow Vocabulary
- **Card Rest** (`box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05)` — Tailwind `shadow-sm`, paired with `border border-slate-200`): The only elevation used across cards, tables, and filter panels. Applied at rest, not just on hover.
- **Modal Lift** (`box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)` — Tailwind `shadow-lg`, on a `bg-black/40` overlay): Used only for the delete-confirmation modal today; the pattern to reuse for any future modal.

### Named Rules
**The Flat-By-Default Rule.** Surfaces are flat against the slate canvas unless they are a card, table, or modal — those get exactly one shadow tier each. No hover-triggered shadow escalation, no double-shadow stacking.

## 5. Components

Buttons, cards, and inputs share one tactile character: calm and steady. Soft shadows, gentle color-only hover transitions, no bounce, no scale transforms, no flashy motion — reinforcing the "trustworthy, never flashy" brand principle.

### Buttons
- **Shape:** Fully rounded corners at 8px (`rounded-lg`).
- **Primary:** Indigo-800 background, white text, `px-4 py-2`, hover to Indigo-700. Used for the single primary action per view (Add Student, Search, Save).
- **Secondary:** Indigo-100 background, Indigo-800 text, hover to a slightly deeper indigo-200. Used for secondary actions (Clear filters, Cancel-adjacent affirmative actions).
- **Danger:** Solid red-600 background, white text, hover to red-700. Reserved for destructive confirms (Delete).
- **Ghost/Text:** No background; colored text only (slate-500 for neutral actions like View, indigo-600 for Edit, red-500 for Delete) — used inline in table action columns.
- **Hover / Focus:** Background color shift only, `transition` class, no transform or shadow change. Focus ring uses `focus:ring-2 focus:ring-indigo-500` to match input focus treatment.

### Chips / Badges
- **Style:** Fully pill-rounded (`rounded-full`), `px-2 py-0.5`, `text-xs font-medium`, tint background + matching darker text (never a solid-fill badge).
- **State:** Status-only, non-interactive. One badge = one status value, always the same color for that value across every module (see The One Meaning Rule).

### Cards / Containers
- **Corner Style:** 8px radius (`rounded-lg`).
- **Background:** Surface White on Page Canvas Slate.
- **Shadow Strategy:** Card Rest shadow (see Elevation) plus a 1px hairline border — always both together, never shadow alone.
- **Border:** `border border-slate-200` on every card, table, and filter panel.
- **Internal Padding:** 24px (`p-6`) for KPI/content cards; 16px (`p-4`) for compact filter panels.

### Inputs / Fields
- **Style:** White background, `border border-slate-300`, 8px radius, `px-3 py-2`, text-sm.
- **Focus:** `focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500` — a soft indigo glow, no border-color-only change (the ring is what signals focus clearly for less tech-savvy users).
- **Error / Disabled:** Not yet implemented — invalid fields currently rely on a flash message above the form rather than an inline border/ring change. Flagged as design debt: add a red-ring error state (`ring-2 ring-red-500 border-red-500`) matching the flash-error red (#b91c1c) when a field fails validation.

### Navigation (Sidebar)
- **Style:** Fixed-left, 256px wide (`w-64`), full-height, Indigo-800 background with white text.
- **Typography:** Section-label style (uppercase, tracked, indigo-300) groups nav links into People / Academic / Finance.
- **Default / Hover / Active:** Default is transparent-on-indigo-800; hover is Indigo-700; active (current route) is also Indigo-700, applied via server-side path matching — no client-side JS needed.
- **Mobile:** Not yet adapted — sidebar is fixed-width with no collapse/drawer behavior on narrow viewports. Flagged as design debt given the "tablet-tolerant" principle in PRODUCT.md.

### Empty States
- Centered icon (muted slate-400, text-3xl) + one line of muted copy, inside the existing table or card container. Currently inconsistent padding (`py-10` vs `py-6` across modules) — standardize to `py-10` for primary list views and `py-6` for secondary/dashboard-embedded empty states.

## 6. Do's and Don'ts

### Do:
- **Do** keep Indigo-800 (#3730a3) as the only color that structures navigation and primary actions — it is the "organized and trustworthy" signal.
- **Do** use the amber warmth accent (#d97706) sparingly and only for human-touch moments (welcome copy, positive empty states, milestone callouts) — never for primary actions or status meaning.
- **Do** pair every card, table, and modal with both a Card Rest shadow and a hairline border — never one without the other.
- **Do** keep the same status color meaning everywhere: green=Active/Present/Paid/Completed, red=Inactive/Absent, yellow=Late/Partial/Scheduled, orange=Overdue.
- **Do** use plain, direct labels over icon-only controls — staff are not necessarily technical, per PRODUCT.md.
- **Do** keep motion to color-only transitions on hover/focus; no scale, bounce, or elastic easing.

### Don't:
- **Don't** introduce a second typeface "for personality" — warmth comes from color and copy, not a display font (see The One Voice Rule).
- **Don't** let the interface read as generic cold enterprise SaaS: no sterile gray-and-blue corporate dashboard look, no dense faceless data-grid treatment — this is PRODUCT.md's explicit anti-reference.
- **Don't** use amber (or any accent) to cover more than a small deliberate moment on a screen — see The Ten Percent Rule.
- **Don't** stack multiple shadow tiers or add hover-triggered shadow escalation — the system is flat-by-default with exactly one lift tier.
- **Don't** ship a new status badge color without checking it doesn't collide with an existing meaning elsewhere in the app.
- **Don't** leave form validation errors as flash-message-only — inline field error states (red ring/border) are required design debt to close, not a pattern to keep repeating.

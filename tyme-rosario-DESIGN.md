---
version: alpha
name: tyme-rosario-design-analysis
description: >
  Essence Dubai's surfaces read as an editorial fragrance film — light, warm,
  and cinematic. The base canvas is warm cream (`#FAF9F6`) holding elegant
  serif display text in Cormorant; white editorial bands alternate with
  cream sections for visual rhythm. The single brand voltage is **Golden
  Amber** (`#CA8A04`) — used scarcely on primary CTAs, gold underlines, and
  accent details. Type runs Cormorant (serif, elegant) at generous weights
  for display roles paired with Montserrat (geometric sans, clean) for body
  — never bombastic, always refined. Spacing follows a 4px token ladder with
  generous editorial pacing throughout. The brand's strongest visual signature
  is the **full-bleed cinematic hero photograph** — extreme close-ups of
  perfume bottles refracting light, golden-hour lifestyle stills, and
  editorial product photography that fills the viewport — followed by a
  staggered editorial layout below. Every section reads as a distinct
  "scene" in a fragrance film, with fluid transitions between them.

colors:
  # Brand & Accent
  primary: "#CA8A04"
  primary-active: "#B07A03"
  primary-hover: "#9A6B02"
  on-primary: "#FFFFFF"

  # Surface
  canvas: "#FAF9F6"
  canvas-elevated: "#F5F0EB"
  canvas-light: "#FFFFFF"
  surface-card: "#FFFFFF"
  surface-soft-light: "#FCFAF7"
  surface-strong-light: "#F0EAE2"

  # Text
  ink: "#1C1917"
  body: "#5C5349"
  body-strong: "#2C2416"
  body-on-light: "#1C1917"
  muted: "#9C9287"
  muted-soft: "#C4BBB0"

  # Structure
  hairline: "#E8E2D9"
  hairline-on-light: "#D9D0C3"
  hairline-strong: "#8C8276"

  # Contrast helpers
  on-dark: "#FFFFFF"
  on-light: "#1C1917"

  # Semantic
  semantic-info: "#4A8DB7"
  semantic-success: "#4A9E6E"
  semantic-warning: "#D4A345"
  semantic-error: "#C44A4A"

  # Scoped accents
  accent-amber-deep: "#8B6914"
  accent-rose-petal: "#D4A49A"
  accent-ivory: "#F8F4EF"

typography:
  display-mega:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 80px
    fontWeight: 500
    lineHeight: 1.0
    letterSpacing: -2px
  display-xl:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 56px
    fontWeight: 600
    lineHeight: 1.08
    letterSpacing: -0.5px
  display-lg:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 40px
    fontWeight: 600
    lineHeight: 1.12
    letterSpacing: -0.3px
  display-md:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 28px
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: -0.2px
  heading-lg:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 22px
    fontWeight: 600
    lineHeight: 1.3
    letterSpacing: 0
  heading-md:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 18px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 0
  heading-sm:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 16px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 0
  subtitle:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 20px
    fontWeight: 400
    lineHeight: 1.4
    letterSpacing: 0.4px
    fontStyle: italic
  body-lg:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 17px
    fontWeight: 400
    lineHeight: 1.65
    letterSpacing: 0
  body-md:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 15px
    fontWeight: 400
    lineHeight: 1.6
    letterSpacing: 0
  body-sm:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 13px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  caption:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 12px
    fontWeight: 400
    lineHeight: 1.4
    letterSpacing: 0
  caption-uppercase:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 11px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 1px
    textTransform: uppercase
  button:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 13px
    fontWeight: 700
    lineHeight: 1.0
    letterSpacing: 1.5px
    textTransform: uppercase
  button-lg:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 14px
    fontWeight: 700
    lineHeight: 1.0
    letterSpacing: 1.5px
    textTransform: uppercase
  nav-link:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 12px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 1px
    textTransform: uppercase
  overline:
    fontFamily: "'Montserrat', system-ui, -apple-system, sans-serif"
    fontSize: 10px
    fontWeight: 700
    lineHeight: 1.4
    letterSpacing: 1.2px
    textTransform: uppercase
  number-display:
    fontFamily: "'Cormorant', 'Playfair Display', Georgia, serif"
    fontSize: 48px
    fontWeight: 300
    lineHeight: 1.0
    letterSpacing: -1px

rounded:
  none: 0px
  xs: 2px
  sm: 4px
  md: 8px
  lg: 12px
  xl: 16px
  pill: 46px
  full: 9999px

spacing:
  xxxs: 4px
  xxs: 8px
  xs: 16px
  sm: 24px
  md: 32px
  lg: 48px
  xl: 64px
  xxl: 96px
  xxxl: 128px
  section: 100px

components:
  # Navigation
  top-nav:
    backgroundColor: transparent
    backgroundColor-solid: "{colors.canvas}"
    textColor: "{colors.on-dark}"
    textColor-solid: "{colors.ink}"
    typography: "{typography.nav-link}"
    height: 72px
    transition: background 0.4s ease-in-out
  top-nav-mobile:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.muted}"
    textColor-active: "{colors.primary}"
    typography: "{typography.nav-link}"
    height: 64px

  # Hero
  hero-cinematic:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.on-dark}"
    typography-title: "{typography.display-mega}"
    typography-subtitle: "{typography.subtitle}"
    height: 100vh
    padding: 0
    overlay-gradient: linear-gradient(to top, rgba(28,25,23,0.4) 0%, transparent 50%)
  hero-scroll-indicator:
    textColor: "{colors.on-dark}"
    typography: "{typography.caption-uppercase}"
    bottom: 40px

  # Scene sections
  section-cinema-light:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography-title: "{typography.display-lg}"
    padding: "{spacing.section}"
  section-cinema-image:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.on-dark}"
    typography-title: "{typography.display-lg}"
    typography-body: "{typography.body-lg}"
    padding: "{spacing.xxl}"
    overlay-gradient: linear-gradient(to right, rgba(28,25,23,0.6) 0%, transparent 70%)
  section-editorial:
    backgroundColor: "{colors.canvas-light}"
    textColor: "{colors.ink}"
    typography-title: "{typography.display-lg}"
    padding: "{spacing.section}"

  # Buttons
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 16px 40px
    height: 48px
    transition: background 0.2s ease
  button-primary-active:
    backgroundColor: "{colors.primary-active}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 16px 40px
    height: 48px
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 16px 40px
    height: 48px
  button-outline-gold:
    backgroundColor: transparent
    textColor: "{colors.primary}"
    border: 1px solid "{colors.primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 15px 39px
    height: 48px
    transition: all 0.2s ease
  button-outline-ink:
    backgroundColor: transparent
    textColor: "{colors.ink}"
    border: 1px solid "{colors.ink}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 15px 39px
    height: 48px
    transition: all 0.2s ease
  button-ghost:
    backgroundColor: transparent
    textColor: "{colors.ink}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 16px 40px
    height: 48px

  # Category cards (film still style)
  category-card-cinema:
    backgroundColor: "{colors.canvas}"
    imageOverlay: rgba(28,25,23,0.3)
    textColor: "{colors.on-dark}"
    typography-title: "{typography.heading-lg}"
    typography-label: "{typography.button}"
    rounded: "{rounded.none}"
    height: 600px
    overlay-gradient: linear-gradient(to top, rgba(28,25,23,0.5) 0%, transparent 40%)
    hoverScale: 1.05

  # Editorial spread
  editorial-spread:
    backgroundColor: "{colors.canvas-light}"
    gap: 0
    imageWidth: 60%
    textWidth: 40%
    typography-title: "{typography.display-md}"
    typography-body: "{typography.body-lg}"
    typography-cta: "{typography.button}"

  # Product cards (editorial staggered grid)
  product-card-editorial:
    backgroundColor: "{colors.surface-card}"
    textColor-name: "{colors.ink}"
    textColor-price: "{colors.primary}"
    textColor-description: "{colors.body}"
    typography-name: "{typography.heading-sm}"
    typography-price: "{typography.number-display}"
    typography-brand: "{typography.caption-uppercase}"
    rounded: "{rounded.none}"
    padding: "{spacing.xs}"
    shadow: 0 2px 16px rgba(28,25,23,0.06)
    hoverShadow: 0 8px 32px rgba(28,25,23,0.10)
    hoverBorder: 1px solid "{colors.primary}"
    transition: all 0.3s ease

  # Forms
  text-input:
    backgroundColor: "{colors.canvas-light}"
    textColor: "{colors.ink}"
    placeholderColor: "{colors.muted-soft}"
    typography: "{typography.body-md}"
    border: 1px solid "{colors.hairline}"
    borderFocus: 1px solid "{colors.primary}"
    rounded: "{rounded.sm}"
    padding: 14px 16px
    height: 48px
    transition: border 0.2s ease
  textarea:
    backgroundColor: "{colors.canvas-light}"
    textColor: "{colors.ink}"
    placeholderColor: "{colors.muted-soft}"
    typography: "{typography.body-md}"
    border: 1px solid "{colors.hairline}"
    borderFocus: 1px solid "{colors.primary}"
    rounded: "{rounded.sm}"
    padding: 14px 16px
    transition: border 0.2s ease

  # Modals
  modal-sheet:
    backgroundColor: "{colors.canvas-light}"
    textColor: "{colors.ink}"
    typography-title: "{typography.heading-lg}"
    rounded: "{rounded.lg}"
    padding: "{spacing.lg}"
    overlay: rgba(28,25,23,0.5)
    maxWidth: 600px

  # Footer
  footer-signature:
    backgroundColor: "{colors.hairline}"
    textColor: "{colors.body}"
    textColor-accent: "{colors.primary}"
    typography: "{typography.body-sm}"
    typography-title: "{typography.caption-uppercase}"
    padding: "{spacing.xxl} {spacing.lg}"

  # Badges
  badge-gold:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.caption-uppercase}"
    rounded: "{rounded.pill}"
    padding: 6px 16px
  badge-outline:
    backgroundColor: transparent
    textColor: "{colors.primary}"
    border: 1px solid "{colors.primary}"
    typography: "{typography.caption-uppercase}"
    rounded: "{rounded.pill}"
    padding: 5px 15px
  badge-soft:
    backgroundColor: "{colors.canvas-elevated}"
    textColor: "{colors.body}"
    typography: "{typography.caption-uppercase}"
    rounded: "{rounded.pill}"
    padding: 6px 16px

  # Page banners (secondary pages)
  page-banner:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.on-dark}"
    typography-title: "{typography.display-lg}"
    typography-subtitle: "{typography.subtitle}"
    height: 50vh
    minHeight: 400px
    overlay-gradient: linear-gradient(to top, rgba(28,25,23,0.5) 0%, transparent 50%)

  # Section title with gold underline
  section-title-gold:
    typography: "{typography.display-md}"
    textColor: "{colors.ink}"
    underlineColor: "{colors.primary}"
    underlineHeight: 2px
    underlineWidth: 60px
    margin-bottom: "{spacing.lg}"

  # Mobile nav
  mobile-nav-item:
    typography: "{typography.caption-uppercase}"
    textColor: "{colors.muted}"
    textColor-active: "{colors.primary}"
    iconSize: 22px
---
# Essence Dubai — Design Analysis

## Overview

Essence Dubai's surfaces read as an editorial fragrance film — light, warm, and cinematic. The base canvas is `{colors.canvas}` (`#FAF9F6` — warm cream, never sterile white), holding elegant serif display text in Cormorant; white editorial bands (`{colors.canvas-light}`) alternate with cream sections for visual rhythm and breathing room. The single brand voltage is `{colors.primary}` (`#CA8A04` — Golden Amber) — used scarcely on primary CTAs, gold accent underlines, and scrolled-into-view details. Type runs **Cormorant** (serif, elegant, refined) at modest weights (display 500–600, never 900) paired with **Montserrat** (geometric sans, clean) for body — the contrast between the two is the typographic signature. Photography is treated as cinema: extreme close-ups of perfume bottles catching golden light, shallow depth of field, editorial still-life compositions, and golden-hour lifestyle imagery. Spacing follows a 4px token ladder with generous editorial pacing — sections breathe, elements have room, nothing feels cramped. The brand's strongest visual signature is the **cinematic hero photograph** that fills the viewport with a single powerful image — then dissolves into staggered editorial layouts below. Every section reads as a distinct "scene" in a fragrance film, connected by fluid transitions that feel like camera dissolves.

**Key Characteristics:**
- Warm cream canvas (`{colors.canvas}`) — never pure white, always soft and tactile
- Golden Amber (`{colors.primary}`) as the single brand voltage — used scarcely, never on backgrounds, always on CTAs and gold accent lines
- Cormorant serif for all display roles — elegant, refined, editorial
- Montserrat for all body text — clean, fresh, geometric, contrasting
- **Sharp corners** (`{rounded.none}`) on all buttons and CTAs — precision luxury
- Cinematic photography — extreme close-ups, shallow depth of field, gold-hour light
- Generous editorial spacing — sections at `{spacing.section}`, big negative space
- Scenes rather than sections — each major block is a visual chapter with its own identity
- Fluid dissolve transitions between scenes — morphing blurs, opacity crossfades, smooth scroll reveals
- No dark mode — this is a light-before-dark luxury, always luminous
- Product photography as objets d'art — sculptural, lit, composed

## Colors

### Brand & Accent

- **Golden Amber** (`{colors.primary}` — `#CA8A04`): The signature brand voltage. Used exclusively on primary CTAs, gold accent underlines beneath section titles, active navigation states, and badge fills. NEVER used as a page background or large surface fill — its power comes from scarcity.
- **Primary Active** (`{colors.primary-active}` — `#B07A03`): Pressed state for gold buttons. Slightly deeper for tactile feedback.
- **Primary Hover** (`{colors.primary-hover}` — `#9A6B02`): Hover state for gold buttons. Deepens the amber.
- **On Primary** (`{colors.on-primary}` — `#FFFFFF`): Pure white text on gold fills. Always white, never anything else.
- **Amber Deep** (`{colors.accent-amber-deep}` — `#8B6914`): Deeper gold for special border accents, signature cards, and exclusive badges.
- **Rose Petal** (`{colors.accent-rose-petal}` — `#D4A49A`): Soft rose accent for subtle decorative details — never CTAs. Used in backgrounds, secondary cards, or footer details.
- **Ivory** (`{colors.accent-ivory}` — `#F8F4EF`): Lightest cream, used for subtle surface differentiation behind editorial image spreads.

### Surface

- **Canvas** (`{colors.canvas}` — `#FAF9F6`): The page floor. Warm cream — never pure white, never sterile. This is the default background of every page. It reads as tactile, warm, inviting, like high-quality paper.
- **Canvas Elevated** (`{colors.canvas-elevated}` — `#F5F0EB`): Cards, panels, and elevated surfaces sitting above the canvas. A slightly deeper cream for subtle depth.
- **Canvas Light** (`{colors.canvas-light}` — `#FFFFFF`): Pure white editorial bands — used sparingly for content sections that need maximum contrast (editorial spreads, form cards, product detail).
- **Surface Card** (`{colors.surface-card}` — `#FFFFFF`): Pure white product cards. The only pure white surface used in the product grid — creates maximum contrast against the warm cream page for product photography.
- **Surface Soft Light** (`{colors.surface-soft-light}` — `#FCFAF7`): Near-cream white for soft section bands that need subtle differentiation.
- **Surface Strong Light** (`{colors.surface-strong-light}` — `#F0EAE2`): Accent bands — used sparingly for highlight sections like "About Tyme" or featured collections.

### Hairlines

- **Hairline** (`{colors.hairline}` — `#E8E2D9`): 1px dividers on the cream canvas. Warm beige, never grey. Used for section dividers, table rows, product list separators. Also serves as footer background.
- **Hairline On Light** (`{colors.hairline-on-light}` — `#D9D0C3`): 1px dividers on white editorial bands. Slightly more visible against pure white.
- **Hairline Strong** (`{colors.hairline-strong}` — `#8C8276`): Medium-contrast divider for emphasized boundaries like form sections or card borders.

### Text

- **Ink** (`{colors.ink}` — `#1C1917`): Primary text — headlines, hero titles, navigation. Warm near-black (never pure `#000`, which feels harsh against warm cream).
- **Body** (`{colors.body}` — `#5C5349`): Default running text. Warm brown-grey — soft, readable, never high-contrast harsh.
- **Body Strong** (`{colors.body-strong}` — `#2C2416`): Emphasized body text, product names, price labels. Darker than body but not as dark as ink.
- **Body On Light** (`{colors.body-on-light}` — `#1C1917`): Text on white editorial bands. Same as ink — high contrast on white.
- **Muted** (`{colors.muted}` — `#9C9287`): Secondary/subdued text, metadata, photo captions. Warm muted grey.
- **Muted Soft** (`{colors.muted-soft}` — `#C4BBB0`): Placeholder text, disabled states. Light warm grey.

### Semantic

- **Info** (`{colors.semantic-info}` — `#4A8DB7`): Informational badges and callouts — product availability, shipping info.
- **Success** (`{colors.semantic-success}` — `#4A9E6E`): Confirmation states, "in stock" indicators, order confirmed.
- **Warning** (`{colors.semantic-warning}` — `#D4A345`): Stock warnings, price change alerts.
- **Error** (`{colors.semantic-error}` — `#C44A4A`): Form validation errors, payment failures.

## Typography

### Font Family

**Cormorant** is the display typeface — an elegant serif with delicate contrast between thick and thin strokes. It carries the brand's editorial, sophisticated character. Used exclusively for display roles (hero titles, section headings, product names, prices) where its refinement can shine. Weights used: 300 (for price display), 400 (italic for subtitles), 500 (for mega display), 600 (for section and component headings). Cormorant is available via Google Fonts with full variable weight support.

**Montserrat** is the body typeface — a clean, geometric sans-serif that provides the fresh, modern counterpoint to Cormorant's elegance. Used for all running text, navigation, buttons, captions, and UI elements. Weights used: 400 (body), 600 (nav, captions), 700 (buttons). Montserrat's geometric clarity ensures readability at small sizes and provides the "clean" and "fresh" qualities briefed.

When Cormorant cannot be used, **Playfair Display** is the substitute serif (available via Google Fonts). For Montserrat, system-ui or -apple-system serve as fallback.

### Hierarchy

| Token | Size | Weight | Line Height | Letter Spacing | Use |
|-------|------|--------|-------------|----------------|-----|
| `{typography.display-mega}` | 80px | 500 | 1.0 | -2px | Hero wordmark (TYME) |
| `{typography.display-xl}` | 56px | 600 | 1.08 | -0.5px | Section hero titles, page banners |
| `{typography.display-lg}` | 40px | 600 | 1.12 | -0.3px | Major section headings |
| `{typography.display-md}` | 28px | 600 | 1.2 | -0.2px | Sub-section heads, editorial titles |
| `{typography.heading-lg}` | 22px | 600 | 1.3 | 0 | Editorial spread titles |
| `{typography.heading-md}` | 18px | 600 | 1.4 | 0 | Component titles |
| `{typography.heading-sm}` | 16px | 600 | 1.4 | 0 | Card titles (product names) |
| `{typography.subtitle}` | 20px | 400(i) | 1.4 | 0.4px | Hero subtitles, lead paragraphs |
| `{typography.body-lg}` | 17px | 400 | 1.65 | 0 | Long-form editorial body |
| `{typography.body-md}` | 15px | 400 | 1.6 | 0 | Default body text |
| `{typography.body-sm}` | 13px | 400 | 1.5 | 0 | Small text, metadata |
| `{typography.caption}` | 12px | 400 | 1.4 | 0 | Photo captions |
| `{typography.caption-uppercase}` | 11px | 600 | 1.4 | 1px | Section labels, badge text |
| `{typography.button}` | 13px | 700 | 1.0 | 1.5px (uppercase) | All CTA labels |
| `{typography.button-lg}` | 14px | 700 | 1.0 | 1.5px (uppercase) | Primary hero CTAs |
| `{typography.nav-link}` | 12px | 600 | 1.4 | 1px (uppercase) | Navigation items |
| `{typography.overline}` | 10px | 700 | 1.4 | 1.2px (uppercase) | Labels above titles |
| `{typography.number-display}` | 48px | 300 | 1.0 | -1px | Product prices, quantities |

### Principles

- Cormorant is ALWAYS used for display/heading roles. Never for body text, buttons, or navigation. The serif/sans divide is strict.
- Montserrat is ALWAYS used for body, buttons, navigation, captions, and UI. Never for display/hero roles.
- All buttons are uppercase with 1.5px tracking — this is non-negotiable. The generous tracking communicates luxury and precision.
- Navigation links are uppercase with 1px tracking — tighter than buttons, still refined.
- Body text is never uppercase (except `caption-uppercase` for labels). Running text is always sentence-case.
- Display sizes tighten letter-spacing proportionally as size increases (mega: -2px, xl: -0.5px, lg: -0.3px). This follows the editorial principle that larger type needs tighter spacing.
- Line heights are generous (1.5–1.65 for body) to ensure readability and communicate spacious luxury.
- Never use font-weight 900 or 800 — maximum weight is 700 (buttons). The brand communicates refinement, not aggression.

### Font Loading

```css
@import url('https://fonts.googleapis.com/css2?family=Cormorant:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Montserrat:wght@400;600;700&display=swap');
```

## Layout

### Spacing System

- **Base unit:** 4px.
- **Tokens:** `{spacing.xxxs}` 4px · `{spacing.xxs}` 8px · `{spacing.xs}` 16px · `{spacing.sm}` 24px · `{spacing.md}` 32px · `{spacing.lg}` 48px · `{spacing.xl}` 64px · `{spacing.xxl}` 96px · `{spacing.xxxl}` 128px · `{spacing.section}` 100px.
- **Section padding:** Every major section uses `{spacing.section}` (100px) top and bottom. This is the editorial breathing room — sections don't touch, they breathe.
- **Card padding:** `{spacing.xs}` (16px) for product cards. `{spacing.md}` (32px) for editorial cards and modal sheets.
- **Text wrapping:** Body text max-width: 540px on editorial spreads. Never full-width paragraphs.
- **Button padding:** Horizontal padding of 40px for primary, 32px for outline, 24px for ghost.

### Grid & Container

- **Max content width:** 1280px (wide editorial). Hero sections are full-bleed.
- **Grid for product listings:** 4-column grid on desktop (`.grid-4`), 2-column on tablet, 2-column on mobile with larger cards.
- **Editorial spread grid:** 60/40 split — 60% image, 40% text. Flips to full-width stacked on tablet/mobile.
- **Category grid:** Full-width cards in a row (3-up on desktop, 1-up stacked on mobile). Each card is full-bleed within its column.
- **Footer grid:** 4-column for links, collapses to 2-column on tablet, stacked on mobile.

### Whitespace Philosophy

Whitespace is the brand's most important design material. Every section is separated by generous `{spacing.section}` — sections never touch. Within sections, elements breathe with `{spacing.md}` to `{spacing.lg}` gaps. This generosity communicates luxury: when you have nothing to prove, you leave space. The whitespace also serves a cinematic purpose — it gives each "scene" room to be experienced before the next one begins. On mobile, spacing reduces by approximately 40% but the principle remains: breathing room over density.

## Elevation & Depth

Depth in Essence Dubai is created primarily through **photography and surface shifts** — never through heavy shadows or dark overlays. This is a light-first luxury.

| Level | Treatment | Use |
|-------|-----------|-----|
| Flat | Canvas surface | Body sections, footer, secondary pages |
| Card | Light surface shift (`{colors.surface-card}`) + subtle shadow | Product cards, editorial panels |
| Elevated | `{colors.canvas-elevated}` background | Category cards with photographic background |
| Overlay | Semi-transparent dark gradient + blur | Hero overlays, modal backdrops |
| Photographic | Deep field of view, shallow DOF, natural light | Hero sections, editorial spreads |

- **Surface shifts** are the primary depth mechanism. Cream (`{colors.canvas}`) → elevated cream (`{colors.canvas-elevated}`) → pure white (`{colors.surface-card}`). Each step reads as a physical layer.
- **Shadows** are minimal: only on product cards (`box-shadow: 0 2px 16px rgba(28,25,23,0.06)`). Never on buttons, never on navigation.
- **Photographic depth** is the hero mechanism. Full-bleed imagery with a subtle dark gradient overlay at the bottom creates the illusion of standing inside a photograph.
- **Glassmorphism** is used on category card overlays — `backdrop-filter: blur(8px)` with semi-transparent background — creating a lens-through-glass effect.

### Decorative Depth

- **Gold accent lines** beneath section titles — 2px tall, 60px wide, `{colors.primary}` fill. A subtle horizontal "cut" in the surface that catches the eye.
- **Hover lifts** on product cards — `translateY(-4px)` with enhanced shadow. The card separates from the page.
- **Category card zooms** — background image scales to 1.05 on hover, like a camera pulling focus.
- **Scroll-reveal opacity** — sections fade and slide into view using CSS `animation-timeline: view()` or Intersection Observer, creating the dissolve between scenes.

## Shapes

### Border Radius Scale

| Token | Value | Use |
|-------|-------|-----|
| `{rounded.none}` | 0px | ALL buttons, CTAs, navigation, category cards, editorial spreads, hero sections |
| `{rounded.xs}` | 2px | Tag-like elements |
| `{rounded.sm}` | 4px | Form inputs, textarea, select |
| `{rounded.md}` | 8px | N/A (bypassed — go from sm to lg) |
| `{rounded.lg}` | 12px | Modal sheets, product modals, card groups |
| `{rounded.xl}` | 16px | Large overlays, special containers |
| `{rounded.pill}` | 46px | Badges, pills, chips |
| `{rounded.full}` | 9999px | Instagram avatar, color swatches, brand icon |

### Photography Geometry

- Product photography: **always sharp corners** (`{rounded.none}`). Product images are rectangular, full-bleed to their container. Never rounded corners on product photos.
- Lifestyle/editorial images: full-bleed, sharp corners, edge-to-edge.
- Category cards: full-bleed images with glassmorphism overlay — image fills the entire card.
- Aspect ratios: Product thumbnails at 3:4 (portrait editorial). Editorial spreads at 4:3 or 16:9. Hero images at 16:9 or 3:2.
- No image borders — photographs sit directly on the surface without frames.

## Components

### Navigation

**`top-nav`** — The primary navigation bar. Positioned fixed at top. On page load, background is transparent with white text (hero context). After scrolling past the hero, background becomes solid `{colors.canvas}` with ink text — transition via `background-color 0.4s ease-in-out`. Height 72px. Logo on the far left, nav links centered, cart + sign-in icons on the far right. Nav links use `{typography.nav-link}` (12px / 600 / 1px tracking / uppercase). Active page is indicated by a 2px gold underline matching `{colors.primary}`.

On mobile (< 768px): desktop links collapse into a hamburger. Cart and sign-in remain visible. The hamburger opens a full-screen overlay nav.

**`top-nav-mobile`** — Bottom navigation bar for mobile devices. Fixed at bottom. Height 64px. Background `{colors.canvas}`. Contains 4–5 icon+label items. Active item uses `{colors.primary}` for the icon and label. Inactive items use `{colors.muted}`. Labels use `{typography.caption-uppercase}` (10px / 600 / 1px tracking).

### Hero — Scene 1: "The Unveiling"

**`hero-cinematic`** — Full-bleed hero section. Height 100vh, width 100vw, no padding. This is PURE CINEMA — no CTAs, no buttons, no secondary elements. Only:
1. A full-bleed photograph (extreme close-up of a perfume bottle, liquid pouring, or golden-hour editorial still — shallow depth of field, backlit with golden light)
2. The TYME wordmark in `{typography.display-mega}` (80px / 500 / -2px tracking) — centered or left-aligned, in `{colors.on-dark}` (white)
3. A subtitle in `{typography.subtitle}` — "Descubrí tu esencia" in italic Cormorant
4. A subtle gradient overlay at the bottom: `linear-gradient(to top, rgba(28,25,23,0.4) 0%, transparent 50%)`

The first CTA appears on scroll, not in the hero. The hero's job is IMPACT, not conversion.

**`hero-scroll-indicator`** — A subtle animated indicator at the bottom of the hero. A thin gold line or chevron, pulsing opacity, with "DESCUBRÍ" in `{typography.caption-uppercase}` at 11px. Hides on scroll. Position absolute, bottom 40px.

### Scene Sections

**`section-cinema-light`** — A full-width cream section. Background `{colors.canvas}`. Padding `{spacing.section}` top and bottom. Centered or left-aligned title in `{typography.display-lg}` with gold underline beneath. Content sits inside the 1280px max-width container. Used for category selection, featured collections, and informational bands.

**`section-cinema-image`** — A full-bleed image section with text overlay. Background image (lifestyle photography, golden hour, perfume editorial) covers the entire section. Overlay gradient on the left or bottom side for text readability. Title in `{typography.display-lg}`, body in `{typography.body-lg}`, both in `{colors.on-dark}` (white). Padding `{spacing.xxl}`. Used for "About Tyme" and "The Story" scene.

**`section-editorial`** — A white editorial band. Background `{colors.canvas-light}`. Used for content-heavy sections (contact form, product detail editorial spread). Regular padding `{spacing.section}`.

### Buttons

**`button-primary`** — The signature gold CTA. Background `{colors.primary}`, text `{colors.on-primary}`, type `{typography.button}` (13px / 700 / 1.5px tracking / uppercase), padding 16px × 40px, height 48px, **sharp corners `{rounded.none}` (0px)**. Hover: background `{colors.primary-hover}`. Active: `{colors.primary-active}`.

**`button-outline-gold`** — Secondary CTA on cream/white backgrounds. Transparent background, 1px `{colors.primary}` border, text `{colors.primary}`, same typography, same geometry. Used for "Ver colección" and secondary actions.

**`button-outline-ink`** — Secondary CTA on light backgrounds where gold would compete. Transparent background, 1px `{colors.ink}` border, text `{colors.ink}`. Used on white editorial bands.

**`button-ghost`** — Text-only button. No border, no background. Text `{colors.ink}`, `{typography.button}`. Used for "Leer más" in editorial spreads. Hover: gold text color.

### Category Cards — Scene 2: "La Colección"

**`category-card-cinema`** — Film-still style category card. Full-bleed background image (not product photos — conceptual imagery: amber liquid, citrus slices, oud wood, rose petals — each representing an olfactory family). Height 600px. Bottom overlay gradient `linear-gradient(to top, rgba(28,25,23,0.5) 0%, transparent 40%)` for text readability. Category title in `{typography.heading-lg}` (Cormorant, white). Button `button-outline-gold` at the bottom: "Explorar" in `{typography.button}`. On hover: background image scales 1.05 (smooth camera zoom, `transition: transform 0.5s ease`). Glassmorphism overlay for the button area: `backdrop-filter: blur(8px)`.

Three categories: **Amaderadas** (woody, warm amber tones), **Cítricas** (bright, fresh citrus), **Orientales** (rich, exotic spices). Each with a distinct color accent in the glass overlay.

### Editorial Spread — Scene 3: "The Curated"

**`editorial-spread`** — Magazine-style spread. 60/40 image-to-text split. Left column (60%): full-height product photography — one perfume bottle shot as a sculptural object, shallow depth of field, dramatic lighting against cream or gold backdrop. Right column (40%): editorial text block with generous spacing. Title in `{typography.display-md}` (Cormorant), description in `{typography.body-lg}` (Montserrat, 540px max-width), price in `{typography.number-display}` (Cormorant 300 weight, gold color), and a `button-outline-ink` CTA. On tablet/mobile: stacks vertically, image top, text below.

Below the hero product: a **staggered editorial grid** of more products. 3–4 columns, masonry-like or uneven rows. Each tile is a `product-card-editorial`.

### Product Cards

**`product-card-editorial`** — A product card designed as an editorial thumbnail. Background `{colors.surface-card}` (pure white), subtle shadow `box-shadow: 0 2px 16px rgba(28,25,23,0.06)`, sharp corners `{rounded.none}`. Padding `{spacing.xs}` (16px). Product image fills the top (3:4 portrait aspect ratio). Below: brand in `{typography.caption-uppercase}` (11px / muted), product name in `{typography.heading-sm}` (16px Cormorant / ink), price in `{typography.number-display}` (48px / 300 / gold). On hover: card lifts with `translateY(-4px)` and enhanced shadow `box-shadow: 0 8px 32px rgba(28,25,23,0.10)`. Top border reveals a 2px gold accent line.

### Forms

**`text-input`** — Default input field. Background `{colors.canvas-light}` (white), text `{colors.ink}`, placeholder `{colors.muted-soft}`, type `{typography.body-md}` (15px), border `1px solid {colors.hairline}`, rounded `{rounded.sm}` (4px — the ONLY rounded element in the system), height 48px, padding 14px × 16px. Focus state: border transitions to `1px solid {colors.primary}` (gold) with no outline (use `outline: none`). The gold focus is the only time gold appears in a form — subtle, precise.

**`textarea`** — Same styling as `text-input` but with `min-height: 120px` and `resize: vertical`.

### Modals

**`modal-sheet`** — Modal overlay sheet. Background `{colors.canvas-light}` (white), rounded `{rounded.lg}` (12px — the only large-radius component), padding `{spacing.lg}` (48px), max-width 600px centered. Backdrop: `rgba(28,25,23,0.5)` at 50% opacity. Used for product quick-view, sign-in, and cart confirmation. Title in `{typography.heading-lg}`. Close button: ghost style in top-right corner.

### Footer — Scene 6: "The Colophon"

**`footer-signature`** — Global footer. Background `{colors.hairline}` (`#E8E2D9` — the warm beige line color, now a surface), text `{colors.body}`. Padding `{spacing.xxl}` top/bottom, `{spacing.lg}` left/right. Logo or wordmark in gold. Links in `{typography.body-sm}`. Column titles in `{typography.caption-uppercase}` (gold). Copyright in `{colors.muted}` at bottom. This is the only dark-cream surface in the system — it signals the end of the journey, like the colophon of a fine book.

### Badges

**`badge-gold`** — Gold filled badge. Background `{colors.primary}`, text `{colors.on-primary}`, type `{typography.caption-uppercase}` (11px / 600 / 1px tracking), rounded `{rounded.pill}`, padding 6px × 16px. Used for "NUEVO", "EXCLUSIVO", "OFERTA".

**`badge-outline`** — Gold outline badge. Transparent, 1px `{colors.primary}` border, gold text. Same geometry. Used for secondary labels.

**`badge-soft`** — Neutral badge. Background `{colors.canvas-elevated}`, text `{colors.body}`. Used for "AGOTADO", "PROXIMAMENTE".

### Page Banners — Secondary Pages

**`page-banner`** — Full-width banner for secondary pages (Products, Fragancias, Promotions, Legals, Auth). Height 50vh, min-height 400px. Full-bleed background image (editorial photography, product still life, or texture detail). Gradient overlay same as hero. Page title in `{typography.display-lg}` (Cormorant, white). Optional subtitle in `{typography.subtitle}`. No CTAs — pure scene-setting for the page content below.

### Section Titles

**`section-title-gold`** — Section heading treatment. Text in `{typography.display-md}` or `{typography.display-lg}`, color `{colors.ink}`. Below the title: a 2px tall, 60px wide gold bar (`{colors.primary}`) — centered for centered titles, left-aligned for left titles. Margin-bottom `{spacing.lg}`. This gold underline is the brand's most consistent structural signature — it appears on every section title across all pages.

## The 5 Scenes of the Homepage

The homepage is a cinematic journey in 5 scenes. Each scene has a distinct visual identity, connected by fluid transitions.

### Scene 1: "The Unveiling" — Hero
- **Visual:** Extreme close-up perfume photography / editorial still. Full-bleed, 100vh.
- **Elements:** TYME wordmark + subtitle + scroll indicator
- **No CTAs, no navigation links in viewport.** Pure impact.
- **Transition out:** Opacity fade + background blur dissolve as user scrolls.

### Scene 2: "La Colección" — Olfactory Families
- **Visual:** Three film-still cards. Conceptual imagery for each family.
- **Elements:** Section title with gold underline + 3 `category-card-cinema` in row.
- **Background:** `{colors.canvas}`.
- **Transition in:** Cards stagger-in from bottom with opacity.
- **Transition out:** Section ends with generous `{spacing.section}` whitespace.

### Scene 3: "The Curated" — Editorial Spread + Staggered Grid
- **Visual:** Magazine spread (60/40 image/text) + masonry product grid below.
- **Elements:** One hero product editorial + 8 `product-card-editorial` in staggered grid.
- **Background:** `{colors.canvas-light}` (white editorial band).
- **Transition in:** Fade + slide from right for the editorial spread.
- **Transition out:** Section ends with whitespace.

### Scene 4: "The Story" — About Tyme
- **Visual:** Full-bleed lifestyle/golden-hour image with text overlay.
- **Elements:** Title "SOBRE TYME" + editorial body text.
- **Background:** Images fill entire section. Text reads over gradient.
- **Transition in:** Image scrolls into view with parallax effect.
- **Transition out:** Whitespace.

### Scene 5: "The Connection" — Contact
- **Visual:** Split layout — editorial image (left) + form (right).
- **Elements:** Section title "CONECTEMOS" + form inputs + gold CTA + Instagram link.
- **Background:** `{colors.canvas-light}` with image column.
- **Transition in:** Gentle fade.

## Do's and Don'ts

### Do
- Use `{colors.canvas}` (`#FAF9F6`) as the default page background — never pure white for general sections
- Apply `{colors.primary}` scarcely — CTAs, gold underlines, active states, and badge fills only
- Use Cormorant for ALL display/heading roles and Montserrat for ALL body/UI roles — never mix them
- Keep ALL buttons and CTAs at sharp `{rounded.none}` corners — this is non-negotiable
- Use generous `{spacing.section}` (100px) between major sections — sections need to breathe
- Style photos as full-bleed, edge-to-edge — no frames, no rounded corners on photography
- Use the gold underline on EVERY section title — it's the brand's structural signature
- Use `{typography.button}` (13px / 700 / 1.5px tracking / uppercase) for ALL CTA labels — consistent voice
- Use fluid transitions (opacity + translate) between scenes — the dissolve is the visual connector
- Make the homepage hero a pure cinematic statement — NO CTAs, NO secondary elements

### Don't
- NEVER use pure `#000000` for text — use `{colors.ink}` (`#1C1917`) which is warm near-black
- NEVER use dark mode — this design system is luminescent, warm, and light-first
- NEVER use gold as a background fill or large surface area — its power is in scarcity
- NEVER round the corners of buttons, CTAs, or photography — sharp edges communicate precision
- NEVER use heavy shadows — shadows are subtle (product cards only, 0.06 opacity max)
- NEVER use font-weight 800 or 900 — the brand communicates refinement, not aggression
- NEVER put CTAs in the hero — the hero is for impact, not conversion
- NEVER mix typefaces in the same line — Cormorant for display, Montserrat for body, never inline
- NEVER use emoji as icons — use SVG (Heroicons or Lucide) for all iconography
- NEVER use Bootstrap components directly without restyling — the system must feel bespoke

## Responsive Behavior

### Breakpoints

| Name | Width | Key Changes |
|-------|-------|-------------|
| Mobile | < 640px | Display sizes clamp down (mega: 48px, xl: 36px, lg: 28px), nav collapses to hamburger, grids go 2-up, section padding reduces 40% |
| Tablet | 640–1024px | Display sizes intermediate (mega: 64px, xl: 44px, lg: 32px), grids 2–3-up, editorial stacks vertically |
| Desktop | 1024–1280px | Full display sizes, 4-column grids, full nav, editorial 60/40 |
| Wide | > 1280px | Content caps at 1280px max-width, hero continues full-bleed |

### Touch Targets
- Primary CTA at 48px height — WCAG AAA compliant (44 × 44 minimum).
- All interactive elements (nav links, icons, buttons) minimum 44 × 44px tap target.
- Bottom mobile nav at 64px height for thumb-friendly navigation.
- Product cards are fully tappable (entire card surface is a link).

### Collapsing Strategy
- **Nav:** Desktop links collapse to hamburger at 768px. Full-screen overlay nav opens from right. Cart + sign-in remain visible as icons.
- **Hero:** On mobile, display-mega reduces to 48px (line-height clamped). Photography reframes — keep the most impactful 16:9 crop visible.
- **Category grid:** 3-up → 1-up (full-width film stills) on mobile.
- **Editorial spread:** 60/40 → stacked (image full-width, text below).
- **Product grid:** 4 columns → 2 columns on tablet, 2 columns on mobile (larger cards).
- **Footer:** 4-column → 2-column on tablet → stacked on mobile.
- **Type:** Display sizes clamp at each breakpoint. Body sizes remain stable.

### Image Behavior
- Hero images: `object-fit: cover` with `object-position: center 30%`. On mobile, adjust object-position to keep the focal point visible.
- Category card images: `object-fit: cover`, scales on hover.
- Product images: `object-fit: contain` within a fixed-ratio container (3:4 aspect). Padding for whitespace around the bottle.
- Editorial spread images: `object-fit: cover`, full height of the container.
- Art direction: Use `srcset` where available to serve mobile-cropped versions. Hero photography should be composed knowing it will be cropped differently at each breakpoint.

## Iteration Guide

When refining the Essence Dubai design:

1. **Apply the color palette first.** Ensure backgrounds use `{colors.canvas}` (not white) and gold is used sparingly. Check contrast ratios.
2. **Set the typography hierarchy.** Load Cormorant + Montserrat via Google Fonts. Apply type tokens strictly — no mixing roles.
3. **Build the hero (Scene 1).** Full-bleed, no CTAs. Get the photography and typography right before adding anything below.
4. **Add the category cards (Scene 2).** Source conceptual photography for each olfactory family. Glassmorphism overlay + gold outline button.
5. **Build the editorial spread (Scene 3).** 60/40 split. Hero product as sculpture. Staggered product grid below.
6. **Add the story section (Scene 4).** Full-bleed lifestyle image. Text overlay. Long-form editorial body.
7. **Complete with the contact section (Scene 5).** Split layout, form, gold primary CTA.
8. **Apply transitions between scenes.** Opacity fade + gentle translateY. Each section triggers on scroll.
9. **Test at all breakpoints.** Clamp display sizes, collapse grids, verify touch targets.
10. **Refine the photography.** The images are the most important design element — invest in composition, lighting, and art direction.

## Known Gaps

- **Licensed fonts:** Cormorant and Montserrat are both Google Fonts (open source, free). No licensing issues.
- **Animation/transition timing:** Not explicitly documented beyond "fluid dissolve." Recommended: 500–700ms ease-out for scroll reveals, 300ms ease for hover states, 400ms ease for nav background transition.
- **Loading states:** Skeleton screens and loading spinners not defined. Use subtle cream pulsing placeholders matching `{colors.canvas-elevated}`.
- **404/500 pages:** Not documented in this spec. Should follow the `page-banner` pattern with centered error message.
- **Authentication states:** Signed-in navigation (user avatar replacing sign-in icon) not explicitly covered. User avatar: `{rounded.full}`, 36 × 36px.
- **Cart states:** Empty cart, cart with items, cart dropdown/panel not explicitly documented. Panel should follow `modal-sheet` pattern.
- **Mobile nav full-screen overlay:** Not detailed — should follow the same cream/gold aesthetic with full-height menu and close icon.
- **Cookie consent:** Not covered. Should be minimal — cream background, gold link, subtle.
- **Image licensing:** All photography (product, lifestyle, category) must be original or properly licensed. The design system's entire impact depends on image quality.
- **Print stylesheet:** Not documented. Consider hiding hero, nav, and decorative elements in print.
- **Reduced motion:** All scroll-reveal animations should respect `prefers-reduced-motion: reduce` — reveal content immediately without animation.

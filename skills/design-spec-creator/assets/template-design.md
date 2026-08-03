---
# =============================================================================
# DESIGN.md TEMPLATE
# =============================================================================
# Follow the guidance in skills/design-spec-creator/SKILL.md to fill this out.
#
# Naming: replace "my-brand" with the actual brand name in the file name and
# frontmatter below.

name: my-brand-design-analysis
description: >
  One paragraph capturing the brand's visual DNA. Canvas color, accent color,
  type philosophy, spacing character, image treatment, and overall mood.
  Example: "A luxury-automotive brand whose marketing surfaces read as cinematic
  editorial. The base canvas is near-black holding pure white display type..."
version: alpha

colors:
  # Group 1: Brand & Accent
  primary: "#000000"
  primary-active: "#000000"
  on-primary: "#ffffff"

  # Group 2: Surface
  canvas: "#ffffff"
  canvas-elevated: "#f5f5f5"
  canvas-light: "#ffffff"

  # Group 3: Text
  ink: "#000000"
  body: "#666666"
  body-strong: "#000000"
  body-on-light: "#000000"
  muted: "#999999"
  muted-soft: "#cccccc"

  # Group 4: Structure
  hairline: "#e5e5e5"
  hairline-strong: "#000000"
  hairline-on-light: "#d2d2d2"

  # Group 5: Contrast helpers
  on-dark: "#ffffff"
  on-light: "#000000"

  # Group 6: Semantic (functional only, not decorative)
  semantic-info: "#337ab7"
  semantic-success: "#8dc572"
  semantic-warning: "#f0ad4e"
  semantic-error: "#be6464"

  # Group 7: Scoped accents (rename per brand)
  # accent-{name}: "#XXXXXX"

  # Group 8: Badge-specific
  # badge-{name}: "#XXXXXX"

typography:
  display-mega:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 80px
    fontWeight: 500
    lineHeight: 1.05
    letterSpacing: -1.6px
  display-xl:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 56px
    fontWeight: 500
    lineHeight: 1.1
    letterSpacing: -1.12px
  display-lg:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 36px
    fontWeight: 500
    lineHeight: 1.2
    letterSpacing: -0.36px
  display-md:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 26px
    fontWeight: 500
    lineHeight: 1.5
    letterSpacing: 0.195px
  title-md:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 18px
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: 0
  title-sm:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 16px
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: 0.08px
  body-lg:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 18px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  body-md:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 16px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  body-sm:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 14px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  caption:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 12px
    fontWeight: 400
    lineHeight: 1.4
    letterSpacing: 0
  caption-uppercase:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 11px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 1.1px
    textTransform: uppercase
  button:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 14px
    fontWeight: 700
    lineHeight: 1.0
    letterSpacing: 1.4px
    textTransform: uppercase
  nav-link:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 13px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: 0.65px
    textTransform: uppercase
  overline:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 10px
    fontWeight: 700
    lineHeight: 1.45
    letterSpacing: 0

rounded:
  none: 0px
  xs: 2px
  sm: 4px
  md: 6px
  lg: 8px
  xl: 12px
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
  section: 80px

components:
  # Navigation
  top-nav:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.nav-link}"
    height: 64px

  # Buttons
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 14px 32px
    height: 48px
  button-primary-active:
    backgroundColor: "{colors.primary-active}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
  button-outline-on-dark:
    backgroundColor: transparent
    textColor: "{colors.on-dark}"
    border: 1px solid "{colors.on-dark}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 13px 31px
    height: 48px
  button-outline-on-light:
    backgroundColor: transparent
    textColor: "{colors.ink}"
    border: 1px solid "{colors.ink}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 13px 31px
    height: 48px

  # Hero
  hero-banner:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.on-dark}"
    typography: "{typography.display-xl}"
    padding: 0

  # Cards
  feature-card:
    backgroundColor: "{colors.canvas-elevated}"
    textColor: "{colors.ink}"
    typography: "{typography.title-md}"
    rounded: "{rounded.none}"
    padding: 24px
  feature-card-light:
    backgroundColor: "{colors.canvas-light}"
    textColor: "{colors.body-on-light}"
    typography: "{typography.title-md}"
    rounded: "{rounded.none}"
    padding: 32px

  # Forms
  text-input:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body-md}"
    rounded: "{rounded.sm}"
    padding: 14px 16px
    height: 48px

  # Badges
  badge-pill:
    backgroundColor: "{colors.canvas-elevated}"
    textColor: "{colors.ink}"
    typography: "{typography.caption-uppercase}"
    rounded: "{rounded.full}"
    padding: 4px 12px

  # Footer
  footer:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.body}"
    typography: "{typography.body-sm}"
    padding: 64px 48px
---

# Template Instructions

> **To use this template:**
> 1. Copy this file: `cp skills/design-spec-creator/assets/template-design.md my-brand-DESIGN.md`
> 2. Fill the YAML frontmatter with the brand's actual tokens
> 3. Write each narrative section below (replace placeholder text)
> 4. Validate against skills/design-spec-creator/assets/schema.json
> 5. Run token generation: `node skills/design-implementer/assets/generate-tokens.js my-brand-DESIGN.md`

## Overview

One paragraph capturing the brand's visual DNA. Canvas, accent, type philosophy, spacing character, image treatment, overall mood.

**Key Characteristics:**
- [Characteristic 1: canvas, accent, primary color usage rule]
- [Characteristic 2: type family and weight philosophy]
- [Characteristic 3: corner/shape treatment — sharp vs rounded]
- [Characteristic 4: photography/image treatment]
- [Characteristic 5: spacing philosophy — dense vs generous]
- [Characteristic 6: depth approach — shadows vs surface shifts vs photography]
- [Characteristic 7: unique visual signature or differentiator]
- [Characteristic 8: layout grid/container system]

## Colors

### Brand & Accent
- **Primary** (`{colors.primary}` — #hex): Usage description. When and how to apply.
- **Primary Active** (`{colors.primary-active}` — #hex): Pressed/hover state.
- **On Primary** (`{colors.on-primary}` — #hex): Label color on primary surfaces.

### Surface
- **Canvas** (`{colors.canvas}` — #hex): Default page background. Mood/character description.
- **Canvas Elevated** (`{colors.canvas-elevated}` — #hex): Card/panel surfaces.
- **Canvas Light** (`{colors.canvas-light}` — #hex): White/light editorial bands.

### Hairlines
- **Hairline** (`{colors.hairline}` — #hex): 1px dividers on the canvas.
- **Hairline On Light** (`{colors.hairline-on-light}` — #hex): Dividers on light bands.

### Text
- **Ink** (`{colors.ink}` — #hex): Primary text color.
- **Body** (`{colors.body}` — #hex): Default running text.
- **Body On Light** (`{colors.body-on-light}` — #hex): Text on light/white bands.
- **Muted** (`{colors.muted}` — #hex): Secondary/subdued text.
- **On Dark** (`{colors.on-dark}` — #hex): Text on dark surfaces.

### Semantic
- **Info** (`{colors.semantic-info}` — #hex): Informational badges/callouts.
- **Success** (`{colors.semantic-success}` — #hex): Confirmations.
- **Warning** (`{colors.semantic-warning}` — #hex): Warnings.
- **Error** (`{colors.semantic-error}` — #hex): Form errors.

## Typography

### Font Family
[Font name] is the [single/primary] typeface. [Description of its character, where it's used, how it behaves at different sizes.]

When [Font name] cannot be licensed, suitable open-source substitutes include [substitute fonts].

### Hierarchy

| Token | Size | Weight | Line Height | Letter Spacing | Use |
|-------|------|--------|-------------|----------------|-----|
| `{typography.display-mega}` | 80px | 500 | 1.05 | -1.6px | Homepage hero h1 |
| `{typography.display-xl}` | 56px | 500 | 1.1 | -1.12px | Subsidiary heroes |
| `{typography.display-lg}` | 36px | 500 | 1.2 | -0.36px | Section heads |
| `{typography.display-md}` | 26px | 500 | 1.5 | 0.195px | Sub-section heads |
| `{typography.title-md}` | 18px | 700 | 1.2 | 0 | Component titles |
| `{typography.title-sm}` | 16px | 500 | 1.4 | 0.08px | List labels |
| `{typography.body-lg}` | 18px | 400 | 1.5 | 0 | Long-form body |
| `{typography.body-md}` | 16px | 400 | 1.5 | 0 | Default body |
| `{typography.body-sm}` | 14px | 400 | 1.5 | 0 | Captions, metadata |
| `{typography.caption}` | 12px | 400 | 1.4 | 0 | Photo captions |
| `{typography.caption-uppercase}` | 11px | 600 | 1.4 | 1.1px | Section labels, badges |
| `{typography.button}` | 14px | 700 | 1.0 | 1.4px (uppercase) | CTA labels |
| `{typography.nav-link}` | 13px | 600 | 1.4 | 0.65px (uppercase) | Nav items |
| `{typography.overline}` | 10px | 700 | 1.45 | 0 | Labels above titles |

### Principles
- [Principle 1: weight philosophy, why]
- [Principle 2: uppercase/lowercase rules]
- [Principle 3: letter-spacing approach]
- [Principle 4: any special treatment (tight line-heights at display, etc.)]

## Layout

### Spacing System
- **Base unit:** 4px.
- **Tokens:** `{spacing.xxxs}` 4px · `{spacing.xxs}` 8px · `{spacing.xs}` 16px · `{spacing.sm}` 24px · `{spacing.md}` 32px · `{spacing.lg}` 48px · `{spacing.xl}` 64px · `{spacing.xxl}` 96px · `{spacing.xxxl}` 128px · `{spacing.section}` 80px.
- **Section padding:** [description of typical section padding].
- **Card padding:** [description of card/component padding].

### Grid & Container
- **Max content width:** ~1280px (or as appropriate).
- **Grid:** [column system, layout patterns].
- **Responsive:** [how grids collapse].

### Whitespace Philosophy
[Description of how whitespace is used to communicate brand character — generous vs tight, structural vs decorative.]

## Elevation & Depth

| Level | Treatment | Use |
|-------|-----------|-----|
| Flat | [default surface] | Body bands, footer |
| Card | [elevated surface or shadow] | Cards, panels |
| Overlay | [overlay treatment] | Modals, dimming |
| Photographic | [image depth approach] | Hero sections |

[Description of how depth is expressed — shadows, surface color shifts, photography, gradients. Which is dominant and why.]

### Decorative Depth
- [Decorative treatment 1]
- [Decorative treatment 2]

## Shapes

### Border Radius Scale

| Token | Value | Use |
|-------|-------|-----|
| `{rounded.none}` | 0px | [where sharp corners apply] |
| `{rounded.xs}` | 2px | [where minimal rounding applies] |
| `{rounded.sm}` | 4px | Form inputs |
| `{rounded.md}` | 6px | [where medium rounding applies] |
| `{rounded.lg}` | 8px | [where large rounding applies] |
| `{rounded.xl}` | 12px | [where extra rounding applies] |
| `{rounded.pill}` | 46px | Sub-nav chips, badges |
| `{rounded.full}` | 9999px | Avatars, color swatches |

### Photography Geometry
- [Description of image corner treatment, aspect ratios, cropping behavior]

## Components

### Navigation

**`top-nav`** — Default top navigation. Background `{colors.canvas}`, text `{colors.ink}`, height 64px. [Layout description: logo position, menu structure, utilities.]

### Buttons

**`button-primary`** — The primary CTA. Background `{colors.primary}`, text `{colors.on-primary}`, type `{typography.button}` (14px / 700 / 1.4px tracking, uppercase), padding 14px × 32px, height 48px, rounded `{rounded.none}`.

**`button-outline-on-dark`** — Secondary CTA on dark backgrounds. Transparent background, 1px `{colors.on-dark}` border, same type geometry.

**`button-outline-on-light`** — Secondary CTA on light backgrounds. Transparent background, 1px `{colors.ink}` border.

### Hero

**`hero-banner`** — Full-bleed hero section. Background `{colors.canvas}`, display headline in `{typography.display-xl}`. [Description of image treatment, CTA placement, overlay approach.]

### Cards

**`feature-card`** — Content/feature card. Background `{colors.canvas-elevated}`, title in `{typography.title-md}`, rounded `{rounded.none}`, padding 24px. [Layout description.]

### Forms

**`text-input`** — Default input field. Background `{colors.canvas}`, text `{colors.ink}`, type `{typography.body-md}`, rounded `{rounded.sm}`, height 48px.

### Badges

**`badge-pill`** — Small uppercase pill badge. Background `{colors.canvas-elevated}`, text `{colors.ink}`, type `{typography.caption-uppercase}` (11px / 600 / 1.1px tracking, uppercase), rounded `{rounded.full}`, padding 4px × 12px.

### Footer

**`footer`** — Global footer. Background `{colors.canvas}`, text `{colors.body}`, type `{typography.body-sm}`, padding 64px × 48px. [Layout description.]

## Do's and Don'ts

### Do
- [Do item 1: primary color usage rule]
- [Do item 2: shape/corner rule]
- [Do item 3: typography rule (uppercase, tracking, weight)]
- [Do item 4: photography/image treatment rule]
- [Do item 5: spacing token adherence rule]
- [Do item 6: depth/elevation rule]
- [Do item 7: component-specific rule]
- [Do item 8: responsive behavior rule]

### Don't
- [Don't item 1: color misuse]
- [Don't item 2: shape/corner anti-pattern]
- [Don't item 3: typography anti-pattern]
- [Don't item 4: photography anti-pattern]
- [Don't item 5: spacing anti-pattern]
- [Don't item 6: depth anti-pattern]
- [Don't item 7: component anti-pattern]
- [Don't item 8: brand voice anti-pattern]

## Responsive Behavior

### Breakpoints

| Name | Width | Key Changes |
|-------|-------|-------------|
| Mobile | < 640px | [Hero h1 clamp size, nav collapses, grids 1-up, section padding reduces] |
| Tablet | 640–1024px | [Hero h1 intermediate, grids 2-up, spacing adjusts] |
| Desktop | 1024–1280px | [Full hero size, multi-column grids, full nav] |
| Wide | > 1280px | [Content caps at max-width, hero continues full-bleed] |

### Touch Targets
- Primary CTA at 48px height — WCAG AAA compliant (44 × 44).
- [Other touch target notes.]

### Collapsing Strategy
- [Nav: when it collapses to hamburger]
- [Hero: how photography reframes per breakpoint]
- [Grids: column collapse progression]
- [Type: display size clamping per breakpoint]

### Image Behavior
- [How images scale/crop across breakpoints]
- [Art direction notes for responsive images]

## Agent Prompt Guide

### Quick Color Reference
- Primary CTA: "[color name] (#hex)"
- Background: "[color name] (#hex)"
- Surface: "[color name] (#hex)"
- Heading text: "[color name] (#hex)"
- Body text: "[color name] (#hex)"
- Border: "[color name] (#hex)"

### Example Component Prompts
- "Create a hero section with [description of hero, key values, and behavior]"
- "Design a [component type] button with [specific visual properties]"
- "Build a navigation bar with [layout description and visual treatment]"
- "Create a [component type] card grid with [styling details and responsive behavior]"

### Iteration Guide
When refining existing screens generated with this design system:
1. Focus on ONE component at a time.
2. Reference specific token names from this document.
3. Use natural language descriptions alongside specific values.
4. Describe the desired "feel" alongside measurements.

## Known Gaps

- [Gap 1: Licensed font, substitute documentation]
- [Gap 2: States not captured (hover, focus, disabled, error)]
- [Gap 3: Animation/transition timings not documented]
- [Gap 4: In-product surfaces not covered]
- [Gap 5: Edge cases or missing component variants]
- [Gap 6: Authentication/personalized states out of scope]

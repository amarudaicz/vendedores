---
name: design-spec-creator
description: >
  Create structured *-DESIGN.md design specification files with YAML tokens,
  component specs, and visual guidelines for AI design agents.
  Trigger: When you need to create a new DESIGN.md, analyze a brand,
  or define a design system from brand guidelines, screenshots, or descriptions.
license: Apache-2.0
metadata:
  author: gentleman-programming
  version: "1.0"
---

## When to Use

- Need to create a `*-DESIGN.md` file for a new brand or project
- Analyzing an existing website or brand guidelines to extract design tokens
- Defining a design system from screenshots, descriptions, or mood boards
- Creating a design spec that will be consumed by `design-implementer`
- Need structured design documentation before any code is written

## Critical Patterns

### 1. Source Analysis

Extract design tokens from whatever source is available:

| Source | What to extract | Strategy |
|--------|----------------|----------|
| Screenshots / live site | Colors, type scales, spacing, component styles | Measure hex values, font sizes, paddings. Identify patterns across multiple pages |
| Brand guidelines (PDF/docs) | Official hex values, typefaces, brand voice | Transcribe exactly — these are authoritative |
| User description | Mood, intended feel, reference brands | Infer a coherent token set from described characteristics |
| ui-ux-pro-max | Color palettes, font pairings, style direction | Run first, then adopt results into the DESIGN.md |

When analyzing a live site, capture at minimum:
- **Background colors** on hero, body, cards, footer — distinguish canvas from elevated surfaces
- **Primary CTA** shape, color, padding, typography
- **Navigation** height, background, link styling
- **Three type scales**: largest display, default body, smallest label
- **Spacing rhythm**: section padding, card padding, gap between components
- **Border radius**: on buttons vs cards vs pills vs inputs

### 2. YAML Frontmatter Structure

Every DESIGN.md MUST have YAML frontmatter between `---` markers. Required sections:

```yaml
---
name: {brand}-design-analysis
description: >
  Single paragraph capturing the brand's visual DNA — canvas, accent color,
  type philosophy, spacing character, image treatment, and overall mood.

colors:
  # See Naming Conventions below

typography:
  # See Typography naming below

rounded:
  none: 0px
  sm: 4px
  md: 8px
  lg: 12px
  full: 9999px

spacing:
  # Scale from 4px up, named semantically

components:
  # Named component definitions with token references
---
```

### 3. Naming Conventions

#### Color Naming

| Pattern | Examples | When to use |
|---------|----------|-------------|
| `primary` / `primary-active` / `primary-hover` | `#da291c`, `#b01e0a` | The brand's signature accent |
| `canvas` / `canvas-elevated` / `canvas-light` | `#181818`, `#303030`, `#ffffff` | Page background and elevation steps |
| `ink` / `body` / `body-strong` | `#ffffff`, `#969696` | Text colors on dark canvas |
| `body-on-light` | `#181818` | Text on light/white bands |
| `muted` / `muted-soft` | `#666666`, `#8f8f8f` | Secondary/low-emphasis text |
| `hairline` / `hairline-on-light` / `hairline-strong` | `#303030`, `#d2d2d2` | 1px dividers on dark/light surfaces |
| `surface-card` / `surface-soft-light` | `#303030`, `#f7f7f7` | Component-specific surfaces |
| `on-primary` / `on-dark` / `on-light` | `#ffffff`, `#ffffff`, `#181818` | Text contrast colors |
| `accent-{name}` | `accent-yellow-hypersail` | Scoped sub-brand accents |
| `semantic-{type}` | `semantic-info`, `semantic-success`, `semantic-warning` | Functional/status colors |
| `badge-{name}` | `badge-new` | Badge-specific colors |

#### Typography Naming

| Pattern | Examples | Use |
|---------|----------|-----|
| `display-{size}` | `display-mega`, `display-xl`, `display-lg`, `display-md` | Hero and section headlines (mega = 80px, xl = 56px, lg = 36px) |
| `heading-{size}` | `heading-lg`, `heading-md`, `heading-sm` | Component titles, card headers |
| `title-{size}` | `title-md`, `title-sm` | Smaller titles, list labels |
| `body-{size}` | `body-lg`, `body-md`, `body-sm` | Running text (md = default 16px or 14px) |
| `subtitle` | `subtitle` | Lead paragraphs, hero subtitles |
| `caption` / `caption-uppercase` | `caption`, `caption-uppercase` | Smallest text, photo captions |
| `button` / `button-{size}` | `button`, `button-md`, `button-sm` | CTA labels (often uppercase + tracking) |
| `nav-link` | `nav-link` | Navigation items |
| `overline` | `overline` | Tiny label above titles |
| `{special-purpose}` | `number-display` | Context-specific roles |

Each typography entry MUST include:
```yaml
typography:
  display-xl:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 56px
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: -1.12px
```

Optionally: `textTransform: uppercase` for buttons and nav.

#### Component Naming

Follow `{context}-{variant}` or `{role}-{variant}-{context}`:

```
button-primary              # Main CTA
button-outline-on-dark      # Outline variant on dark bg
button-outline-on-light     # Same variant on light bg
button-primary-active       # Pressed state
top-nav-on-dark             # Navigation on dark canvas
hero-band-cinema            # Full-bleed image hero
hero-band-light             # White-canvas hero
feature-card-photo          # Image-first card
feature-card-light          # Text/content card
text-input-on-dark          # Input on dark canvas
badge-pill                  # Small chip badge
footer-dark                 # Footer on dark canvas
```

#### Rounded Naming

```yaml
rounded:
  none: 0px
  xs: 2px
  sm: 4px
  md: 6px
  lg: 8px
  xl: 12px
  pill: 46px    # Only if pill shape exists
  full: 9999px
```

#### Spacing Naming

```yaml
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
  section: 80px   # Context-specific if differs
```

### 4. Token Reference System

Components MUST reference tokens using `{section.key}` syntax — NEVER inline hex values:

```yaml
components:
  button-primary:
    backgroundColor: "{colors.primary}"   # NOT "#da291c"
    textColor: "{colors.on-primary}"      # NOT "#ffffff"
    typography: "{typography.button}"     # NOT "14px/700/1.4px uppercase"
    rounded: "{rounded.none}"             # NOT "0px"
    padding: 14px 32px                    # Fine to inline (not tokenized)
```

In narrative sections, use inline `{token.refs}` when referencing specific values:
- "The primary accent is `{colors.primary}` — used scarcely on CTAs."
- "Buttons use `{typography.button}` — 14px / 700 / 1.4px tracking, uppercase."
- "Sharp `{rounded.none}` corners on every CTA."
- "Section padding: `{spacing.xxl}` for major bands."

This lets the reader AND design-implementer resolve exact values.

### 5. Two Styles

#### Style A: Structured (RECOMMENDED)

Complete YAML frontmatter + narrative sections. Examples: Ferrari, Renault.

Use when:
- You have measurable tokens (hex values, type scales, padding)
- The design will be implemented by `design-implementer`
- The design system has explicit components

#### Style B: Narrative-Only

No YAML frontmatter. Pure markdown with inline values. Example: Lamborghini.

Use when:
- Tokens are approximate (described, not measured)
- The design is concept-level, not production-ready
- The agent needs creative freedom in interpretation

### 6. Required Narrative Sections

After the YAML frontmatter `---`, include these sections in order:

#### Overview
One-paragraph elevator pitch capturing the brand's visual DNA. Then a bullet list of **Key Characteristics** (7-10 items).

Template: "`{Brand}`'s [surface type] reads as [mood/atmosphere]. The base canvas is `{token}` holding [description of main text treatment]; [secondary canvas description]. The single brand voltage is `{token}` — used [how/when]. Type runs `{font}` at [weights] — [description]. [Signature visual element] is the brand's strongest signature. Spacing follows the [base unit] token ladder."

#### Colors
Expand every color from the frontmatter with context. Group into:
- **Brand & Accent**: primary, accent colors with usage rules
- **Surface**: canvas, elevated, card, soft surfaces
- **Hairlines**: divider colors and their usage
- **Text**: ink, body, muted, on-dark/on-light
- **Semantic**: info, success, warning, error

Each entry: `**Color Name** (\`{token}\` — #hex): Usage description.`

#### Typography
- Declare the font family and why
- Hierarchy table: Token, Size, Weight, Line Height, Letter Spacing, Use
- Principles (3-5 rules about typography behavior)
- Font substitute note if the font is licensed

#### Layout
- **Spacing System**: base unit, key tokens, section padding, card padding
- **Grid & Container**: max-width, column system, grid patterns
- **Whitespace Philosophy**: how spacing communicates brand character

#### Elevation & Depth
Table: Level | Treatment | Use

Describe how depth is created (shadows, surface shifts, photography, gradients).

#### Shapes
- Border radius table with token → value → usage
- Photography geometry (aspect ratios, corner treatment)

#### Components
For each component in the YAML, write a paragraph describing:
- When to use it
- Visual structure (background, text, typography, padding, radius)
- Layout details (positioning, inner structure)
- Variants and states

#### Do's and Don'ts
- **Do** (7-10 items): What to ALWAYS do
- **Don't** (7-10 items): What to NEVER do

#### Responsive Behavior
- Breakpoints table
- Touch targets
- Collapsing strategy (nav, grids, type)
- Image behavior per breakpoint

#### Iteration Guide
Numbered steps for common workflows. Reference component names and tokens directly.

#### Known Gaps
Honest list of what isn't documented. Covers edge cases, missing variants, licensed fonts, animation, states.

### 7. Validation Checklist

Before publishing, verify:

- [ ] YAML frontmatter has `name`, `description`, `colors`, `typography`, `rounded`, `spacing`, `components`
- [ ] Every color has a semantic name
- [ ] Every typography entry has all 4 required fields (fontSize, fontWeight, lineHeight, letterSpacing)
- [ ] Components use `{section.key}` references — no inline hex values
- [ ] All `{section.key}` references resolve to existing tokens
- [ ] Overview paragraph exists + Key Characteristics bullet list
- [ ] Colors section documents every color with usage context
- [ ] Typography hierarchy table exists
- [ ] Do's and Don'ts exist (7-10 each)
- [ ] Responsive breakpoints defined
- [ ] Iteration Guide present
- [ ] Known Gaps section present
- [ ] File named `{brand}-DESIGN.md`
- [ ] Can be parsed by `design-implementer`'s token generator

## Code Examples

### Minimal Complete Frontmatter

```yaml
---
name: example-design-analysis
description: >
  Brief description of the design system. What makes it unique,
  what mood/atmosphere it conveys, and key visual characteristics.

colors:
  primary: "#FF0000"
  canvas: "#ffffff"
  canvas-elevated: "#f5f5f5"
  ink: "#000000"
  body: "#666666"
  muted: "#999999"
  hairline: "#e5e5e5"
  on-primary: "#ffffff"
  on-dark: "#ffffff"

typography:
  display-xl:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 56px
    fontWeight: 700
    lineHeight: 1.05
    letterSpacing: -0.5px
  body-md:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 16px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  button:
    fontFamily: "'BrandFont', sans-serif"
    fontSize: 14px
    fontWeight: 700
    lineHeight: 1.0
    letterSpacing: 1px
    textTransform: uppercase

rounded:
  none: 0px
  sm: 4px
  full: 9999px

spacing:
  xs: 16px
  md: 32px
  lg: 48px
  xl: 64px

components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 14px 32px
    height: 48px
  hero-banner:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.on-dark}"
    padding: 0
---
```

### Complete Component with Narrative Description

```yaml
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 14px 32px
    height: 48px
```

Then in narrative:
```markdown
**`button-primary`** — The signature primary CTA. Background `{colors.primary}`,
text `{colors.on-primary}`, type `{typography.button}` (14px / 700 / 1.4px tracking,
uppercase), padding 14px × 32px, height 48px, **rounded `{rounded.none}` (0px —
sharp corners)**.
```

### Color Narrative Expansion

```markdown
### Brand & Accent
- **Rosso Corsa** (`{colors.primary}` — #da291c): The iconic brand red.
  Primary CTA fill. Used scarcely.
- **On Primary** (`{colors.on-primary}` — #ffffff): Label color on primary.
  Always pairs primary with white text.

### Surface
- **Canvas** (`{colors.canvas}` — #181818): The page floor — near-black,
  never pure black, slight warmth.
- **Canvas Elevated** (`{colors.canvas-elevated}` — #303030): Cards and panels
  sitting above the canvas.
```

## Commands

```bash
# Create a new DESIGN.md from template
cp skills/design-spec-creator/assets/template-design.md my-brand-DESIGN.md

# Validate DESIGN.md against schema
# (requires ajv-cli or similar JSON Schema validator)
npx ajv validate -s skills/design-spec-creator/assets/schema.json -d my-brand-DESIGN.md

# Generate tokens for implementation (from design-implementer)
node skills/design-implementer/assets/generate-tokens.js my-brand-DESIGN.md
```

## Verification Checklist

- [ ] YAML frontmatter complete with all 7 sections
- [ ] Colors use semantic naming conventions
- [ ] Typography entries complete (all 4 properties)
- [ ] Components use `{section.key}` token references
- [ ] All narrative sections present (Overview → Known Gaps)
- [ ] Do's and Don'ts have 7-10 items each
- [ ] Responsive breakpoints specified
- [ ] Token references resolve correctly
- [ ] File follows `{brand}-DESIGN.md` naming

## Resources

- **Template**: See [assets/template-design.md](assets/template-design.md) for creating new DESIGN.md files
- **Schema**: See [assets/schema.json](assets/schema.json) for validating DESIGN.md structure
- **Integration**: See [references/design-implementer-integration.md](references/design-implementer-integration.md) for the full design-to-code workflow
- **Examples**: See project root `*-DESIGN.md` files (ferrari-DESIGN.md, renault-DESIGN.md) for reference implementations

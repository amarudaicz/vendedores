---
name: design-implementer
description: >
  Implement professional UI designs from DESIGN.md files.
  Parses YAML frontmatter (colors, typography, spacing, components),
  generates Tailwind config + CSS custom properties automatically,
  handles local images, and applies responsive behavior per spec.
  Trigger: When implementing UI from a *-DESIGN.md design system file.
license: Apache-2.0
metadata:
  author: gentleman-programming
  version: "1.0"
---

## When to Use

- User provides (or references) a `*-DESIGN.md` file with design system tokens
- Need to implement a page/component matching an existing DESIGN.md spec
- Generating Tailwind config + CSS tokens from a structured design system
- Converting brand design specs (colors, typography, spacing, components) to code

## Critical Patterns

### 1. Token Extraction (REQUIRED first step)

Always extract tokens from the YAML frontmatter between `---` markers:

```
---
colors:
  primary: "#da291c"
  ...
typography:
  display-mega:
    fontSize: 80px
    ...
---
```

Parse these sections: `colors`, `typography`, `spacing`, `rounded`, `components`.

### 2. Resolve Token References

DESIGN.md files use `{section.key}` references:
- `backgroundColor: "{colors.primary}"` → resolve to `colors.primary` hex value
- `typography: "{typography.button}"` → expand to the full typography object
- `spacing: "{spacing.xl}"` → resolve to the spacing value

### 3. Token Generation

Run the token generator:
```bash
node skills/design-implementer/assets/generate-tokens.js path/to/DESIGN.md
```

This creates:
- `tailwind.config.js` — colors, spacing, borderRadius, fontFamily, screens
- `tokens.css` — CSS custom properties for typography and component defaults

If the project already has a `tailwind.config.js`, merge the generated colors/spacing.

### 4. Component Implementation

Translate `components:` definitions to HTML+Tailwind:

| Component YAML | HTML+Tailwind equivalent |
|----------------|--------------------------|
| `backgroundColor` | `bg-{color}` or `bg-[#hex]` |
| `textColor` | `text-{color}` or `text-[#hex]` |
| `rounded` | `rounded-none`, `rounded-sm`, etc. |
| `padding: 14px 32px` | `px-8 py-3.5` |
| `height: 48px` | `h-12` |
| `typography.fontSize` | `text-[size]` or `text-{token}` |

For component variants (e.g. `button-primary`, `button-outline-on-dark`), create **Tailwind component classes** via `@apply` in CSS or utility classes directly in HTML.

### 5. Image Handling

- Use `src="assets/images/{project}/{filename}"` for all local images
- Hero images: `w-full h-screen object-cover` (full-bleed) or `w-full h-[80vh] object-cover`
- Product cards: image on top, `w-full aspect-[16/9] object-cover`
- Vehicle photography: maintain aspect ratio, `object-cover` with centered crop
- Background overlays for text legibility: `bg-gradient-to-t from-black/60 to-transparent`
- **Never use emoji as icons** — use SVG icons (Heroicons, Lucide)

### 6. Responsive Behavior

Apply the DESIGN.md breakpoints and collapse rules:

- Hero video/image: `object-cover`, full viewport height
- Grid columns: responsive with Tailwind (`grid-cols-1 md:grid-cols-2 lg:grid-cols-3`)
- Display type: clamp sizes per breakpoint
- Navigation: hamburger below tablet breakpoint
- Section padding: reduce on mobile (`py-16 md:py-24`)

## Code Examples

### Parsing a DESIGN.md token to Tailwind config

```javascript
// DESIGN.md has:
// colors:
//   primary: "#FFC000"
//   canvas: "#000000"
// spacing:
//   sm: 24px
//   md: 32px

// Generated tailwind.config.js:
module.exports = {
  theme: {
    extend: {
      colors: {
        primary: '#FFC000',
        canvas: '#000000',
      },
      spacing: {
        sm: '24px',
        md: '32px',
      }
    }
  }
}
```

### Component: Button Primary (Ferrari Rosso Corsa)

```html
<!-- From components.button-primary -->
<button class="bg-[#da291c] text-white px-8 py-3.5 h-12
               uppercase tracking-[1.4px] text-sm font-bold
               rounded-none transition-colors duration-200
               hover:bg-[#b01e0a] cursor-pointer">
  Scopri di più
</button>
```

### Hero Section with Full-Bleed Image

```html
<section class="relative w-full h-screen bg-[#181818] overflow-hidden">
  <img src="assets/images/ferrari/hero-model.jpg"
       alt="Ferrari model name"
       class="absolute inset-0 w-full h-full object-cover" />
  <div class="absolute inset-0 bg-gradient-to-t from-[#181818] via-transparent to-black/20"></div>
  <div class="absolute bottom-16 left-8 md:left-16 z-10">
    <h1 class="text-[80px] font-[500] leading-[1.05] tracking-[-1.6px] text-white">
      MODEL NAME
    </h1>
    <button class="...">Discover</button>
  </div>
</section>
```

### Responsive Grid Pattern

```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
  <!-- cards -->
</div>
```

## Commands

```bash
# Generate tokens from a DESIGN.md file
node skills/design-implementer/assets/generate-tokens.js path/to/DESIGN.md

# Generate tokens and output to specific directory
node skills/design-implementer/assets/generate-tokens.js path/to/DESIGN.md --out ./src/styles

# Generate tokens with Tailwind config merge (existing project)
node skills/design-implementer/assets/generate-tokens.js path/to/DESIGN.md --merge ./tailwind.config.js

# Create a new DESIGN.md from template
cp skills/design-implementer/assets/template-design.md my-brand-DESIGN.md
```

## Verification Checklist

Before delivering, verify:

- [ ] All colors from DESIGN.md map to Tailwind classes or `bg-[#hex]`
- [ ] Typography hierarchy matches (size, weight, line-height, letter-spacing)
- [ ] Button/card border-radius matches spec (check rounded.* tokens)
- [ ] Spacing uses the token scale, not ad-hoc values
- [ ] Images use local `assets/images/` paths with proper `object-fit`
- [ ] Responsive breakpoints applied per design spec
- [ ] Hover/focus states implemented per design
- [ ] No emojis used as UI icons (use SVGs)
- [ ] `cursor-pointer` on all clickable elements
- [ ] Do's/Don'ts from DESIGN.md respected

## Resources

- **Template**: See [assets/template-design.md](assets/template-design.md) for creating new DESIGN.md files
- **Token Generator**: See [assets/generate-tokens.js](assets/generate-tokens.js) for automated token extraction
- **ui-ux-pro-max**: See [references/ui-ux-pro-max-integration.md](references/ui-ux-pro-max-integration.md) for complementary design searches

# design-spec-creator + design-implementer Integration

Use `design-spec-creator` **before** `design-implementer`. The `design-spec-creator`
produces the `*-DESIGN.md` spec that `design-implementer` consumes.

## Full Workflow

```
              ┌──────────────────────────────────┐
              │   ui-ux-pro-max (optional)       │
              │   Design discovery & research    │
              └──────────┬───────────────────────┘
                         │  color palettes, font pairings, style direction
                         ▼
              ┌──────────────────────────────────┐
              │   design-spec-creator  ◄── YOU ARE HERE │
              │   Creates *-DESIGN.md from       │
              │   brand analysis or guidelines    │
              └──────────┬───────────────────────┘
                         │  ferrari-DESIGN.md (etc.)
                         ▼
              ┌──────────────────────────────────┐
              │   design-implementer             │
              │   Parses DESIGN.md tokens →      │
              │   Generates Tailwind config +    │
              │   CSS tokens + implements code   │
              └──────────┬───────────────────────┘
                         │  tailwind.config.js, tokens.css, HTML
                         ▼
              ┌──────────────────────────────────┐
              │   Verificación contra DESIGN.md  │
              │   Do's/Don'ts, token fidelity    │
              └──────────────────────────────────┘
```

## When to Use Each

| Situation | Action |
|-----------|--------|
| No DESIGN.md exists | Run `design-spec-creator` to analyze brand/site and create one |
| DESIGN.md exists, needs code | Run `design-implementer` to generate tokens + implement |
| Need design discovery | Run `ui-ux-pro-max` FIRST, then feed results into `design-spec-creator` |
| DESIGN.md has new components | Update DESIGN.md via `design-spec-creator` patterns, then run `design-implementer` |
| Design iteration (new brand variant) | Copy DESIGN.md template, fill new tokens, then implement |

## Quick Reference

```bash
# 1. Create DESIGN.md from template
cp skills/design-spec-creator/assets/template-design.md my-brand-DESIGN.md

# 2. Validate structure
npx ajv validate -s skills/design-spec-creator/assets/schema.json -d my-brand-DESIGN.md

# 3. Generate implementation tokens
node skills/design-implementer/assets/generate-tokens.js my-brand-DESIGN.md

# 4. Generate tokens to specific output directory
node skills/design-implementer/assets/generate-tokens.js my-brand-DESIGN.md --out ./src/styles

# 5. Merge with existing Tailwind config
node skills/design-implementer/assets/generate-tokens.js my-brand-DESIGN.md --merge ./tailwind.config.js
```

## Notes

- `design-spec-creator` produces the **blueprint** (DESIGN.md with tokens + narrative)
- `design-implementer` produces the **implementation** (Tailwind config, CSS, HTML)
- `ui-ux-pro-max` produces the **discovery** (palettes, pairings, styles)
- All three complement each other — use them in sequence for a complete design-to-code pipeline
- The schema at `skills/design-spec-creator/assets/schema.json` validates DESIGN.md structure before implementation

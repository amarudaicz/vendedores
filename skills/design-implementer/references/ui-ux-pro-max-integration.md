# ui-ux-pro-max Integration

Use `ui-ux-pro-max` **before** `design-implementer` when you need design
decisions that aren't covered by the existing DESIGN.md file.

## When to Use ui-ux-pro-max

| Situation | Action |
|-----------|--------|
| No DESIGN.md exists yet | Run `ui-ux-pro-max` with `--design-system` to generate one, then migrate tokens to a DESIGN.md file |
| Need additional color palettes | `python3 skills/ui-ux-pro-max/scripts/search.py "<keywords>" --domain color` |
| Need font pairings | `python3 skills/ui-ux-pro-max/scripts/search.py "<keywords>" --domain typography` |
| UX pattern decisions | `python3 skills/ui-ux-pro-max/scripts/search.py "<keywords>" --domain ux` |
| Chart/graph recommendations | `python3 skills/ui-ux-pro-max/scripts/search.py "<keywords>" --domain chart` |
| Landing page structure | `python3 skills/ui-ux-pro-max/scripts/search.py "<keywords>" --domain landing` |
| Style exploration (glassmorphism, dark mode, etc.) | `python3 skills/ui-ux-pro-max/scripts/search.py "<keywords>" --domain style` |

## Workflow

```
1. ui-ux-pro-max search (design discovery)
         │
         ▼
2. Create/update DESIGN.md with chosen tokens
         │
         ▼
3. design-implementer generate-tokens.js
         │
         ▼
4. Implement components from DESIGN.md spec
         │
         ▼
5. Verify against DESIGN.md Do's/Don'ts
```

## Example

```bash
# 1. Discover design direction
python3 skills/ui-ux-pro-max/scripts/search.py "automotive luxury brand" --design-system

# 2. Create DESIGN.md from template
cp skills/design-implementer/assets/template-design.md my-brand-DESIGN.md
# (edit with chosen tokens from step 1)

# 3. Generate tokens
node skills/design-implementer/assets/generate-tokens.js my-brand-DESIGN.md

# 4. Implement using the generated tokens + DESIGN.md spec
```

## Notes

- `design-implementer` handles **implementation** (generating tokens, writing HTML+Tailwind, handling images)
- `ui-ux-pro-max` handles **discovery** (color palettes, font pairings, UX patterns, style recommendations)
- They complement each other — use both for a complete design-to-code workflow

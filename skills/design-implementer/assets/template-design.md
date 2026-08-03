---
name: my-brand-design
description: >
  Brief description of the design system. What makes it unique,
  what mood/atmosphere it conveys, and key visual characteristics.
version: alpha

colors:
  primary: "#000000"
  primary-active: "#000000"
  on-primary: "#ffffff"
  ink: "#000000"
  body: "#666666"
  body-on-light: "#000000"
  muted: "#999999"
  canvas: "#ffffff"
  canvas-elevated: "#f5f5f5"
  canvas-light: "#ffffff"
  hairline: "#e5e5e5"
  hairline-strong: "#000000"
  on-dark: "#ffffff"

typography:
  display-xl:
    fontFamily: "'FontName', sans-serif"
    fontSize: 56px
    fontWeight: 700
    lineHeight: 1.05
    letterSpacing: -0.5px
  display-lg:
    fontFamily: "'FontName', sans-serif"
    fontSize: 40px
    fontWeight: 700
    lineHeight: 1.1
    letterSpacing: -0.3px
  display-md:
    fontFamily: "'FontName', sans-serif"
    fontSize: 32px
    fontWeight: 700
    lineHeight: 1.15
    letterSpacing: 0
  heading-lg:
    fontFamily: "'FontName', sans-serif"
    fontSize: 24px
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: 0
  heading-md:
    fontFamily: "'FontName', sans-serif"
    fontSize: 20px
    fontWeight: 600
    lineHeight: 1.25
    letterSpacing: 0
  body-lg:
    fontFamily: "'FontName', sans-serif"
    fontSize: 18px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  body-md:
    fontFamily: "'FontName', sans-serif"
    fontSize: 16px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  body-sm:
    fontFamily: "'FontName', sans-serif"
    fontSize: 14px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  button:
    fontFamily: "'FontName', sans-serif"
    fontSize: 14px
    fontWeight: 700
    lineHeight: 1.0
    letterSpacing: 1px
    textTransform: uppercase
  caption:
    fontFamily: "'FontName', sans-serif"
    fontSize: 12px
    fontWeight: 400
    lineHeight: 1.4
    letterSpacing: 0

rounded:
  none: 0px
  xs: 2px
  sm: 4px
  md: 6px
  lg: 8px
  xl: 12px
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
  super: 128px

components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 14px 32px
    height: 48px
  button-outline:
    backgroundColor: transparent
    textColor: "{colors.ink}"
    typography: "{typography.button}"
    rounded: "{rounded.none}"
    padding: 13px 31px
    height: 48px
  hero-banner:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.on-dark}"
    typography: "{typography.display-xl}"
    padding: 0
  feature-card:
    backgroundColor: "{colors.canvas-elevated}"
    textColor: "{colors.ink}"
    typography: "{typography.heading-md}"
    rounded: "{rounded.none}"
    padding: 24px
  text-input:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body-md}"
    rounded: "{rounded.sm}"
    padding: 14px 16px
    height: 48px
  footer:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.body}"
    typography: "{typography.body-sm}"
    padding: 64px 48px
---

## Overview

[Describe the design system: canvas color, accent color, typography approach,
spacing philosophy, image treatment, and overall mood.]

**Key Characteristics:**
- [Characteristic 1]
- [Characteristic 2]
- [Characteristic 3]

## Do's and Don'ts

### Do
- [Do item 1]
- [Do item 2]

### Don't
- [Don't item 1]
- [Don't item 2]

## Responsive Behavior

| Breakpoint | Width | Changes |
|------------|-------|---------|
| Mobile | < 640px | [Changes] |
| Tablet | 640-1024px | [Changes] |
| Desktop | 1024-1280px | [Changes] |
| Wide | > 1280px | [Changes] |

## Image Treatment

[Describe how images should be handled: aspect ratios, full-bleed behavior,
overlays, responsive cropping.]

---
name: Elite Performance Framework
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#44474d'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#75777e'
  outline-variant: '#c5c6ce'
  surface-tint: '#505f7a'
  primary: '#000000'
  on-primary: '#ffffff'
  primary-container: '#0b1c34'
  on-primary-container: '#7584a2'
  inverse-primary: '#b7c7e7'
  secondary: '#006d32'
  on-secondary: '#ffffff'
  secondary-container: '#64ff92'
  on-secondary-container: '#007436'
  tertiary: '#000000'
  on-tertiary: '#ffffff'
  tertiary-container: '#241a00'
  on-tertiary-container: '#a47f00'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d6e3ff'
  primary-fixed-dim: '#b7c7e7'
  on-primary-fixed: '#0b1c34'
  on-primary-fixed-variant: '#384761'
  secondary-fixed: '#64ff92'
  secondary-fixed-dim: '#41e279'
  on-secondary-fixed: '#00210b'
  on-secondary-fixed-variant: '#005224'
  tertiary-fixed: '#ffdf94'
  tertiary-fixed-dim: '#efc13e'
  on-tertiary-fixed: '#241a00'
  on-tertiary-fixed-variant: '#594400'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  display-hero:
    fontFamily: Montserrat
    fontSize: 64px
    fontWeight: '800'
    lineHeight: 72px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Montserrat
    fontSize: 40px
    fontWeight: '800'
    lineHeight: 48px
  headline-lg-mobile:
    fontFamily: Montserrat
    fontSize: 32px
    fontWeight: '800'
    lineHeight: 40px
  headline-md:
    fontFamily: Montserrat
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  stat-xl:
    fontFamily: Bebas Neue
    fontSize: 56px
    fontWeight: '400'
    lineHeight: 56px
    letterSpacing: 0.05em
  stat-md:
    fontFamily: Bebas Neue
    fontSize: 32px
    fontWeight: '400'
    lineHeight: 32px
    letterSpacing: 0.03em
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
    letterSpacing: 0.1em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 48px
  section-gap: 80px
  element-gap: 24px
---

## Brand & Style

This design system embodies the "Elite Performance" ethos of a world-class football academy. The brand personality is **authoritative, aspirational, and high-energy**, bridging the gap between grassroots passion and professional discipline. 

The aesthetic is **Corporate Modern with a Sports Editorial edge**. It utilizes high-contrast typography and cinematic spacing to evoke the feeling of a premium Nike or Adidas digital experience. The UI must feel "fast" and "precise," reflecting the athletic excellence expected at Ibeju Lekki. Large, immersive photography featuring authentic African youth talent serves as the emotional backbone, while the technical UI remains grounded in professional stability.

## Colors

The palette is anchored by **Primary Navy**, providing a deep, institutional foundation that commands respect. 

- **Primary Navy (#04152D):** Used for headers, footers, and primary navigation to establish authority.
- **Deep Academy Blue (#082A5E):** Used for subtle layering, hover states, and secondary containers.
- **Performance Green (#17C964):** The "action" color. Reserved for CTAs, success states, and live match indicators. It represents growth and the pitch itself.
- **Victory Gold (#F4C542):** Used sparingly for elite markers, awards, scholarship statuses, and "Premium" badges.
- **Background (#F8FAFC):** A clean, off-white base that ensures the deep blues and vibrant greens pop without causing eye strain.

## Typography

The typography strategy uses three distinct families to organize information hierarchy:

1.  **Impact (Montserrat):** Used for high-level headings. Its ExtraBold weight conveys the power and stability of the academy.
2.  **Functional (Inter):** Used for all body text, descriptions, and forms. It provides maximum legibility for parents and scouts.
3.  **Athletic (Bebas Neue):** Reserved for statistics, jersey numbers, scores, and technical data. The condensed nature of the font allows for large, impactful numbers that mimic stadium scoreboards.

Always use `stat-xl` for prominent metrics like "Goals Scored" or "Top Speed" to reinforce the performance-tracking nature of the academy.

## Layout & Spacing

The layout utilizes a **12-column fluid grid** with generous internal padding to create a breathable, "luxury" feel. 

- **Desktop:** 12 columns with 24px gutters. Use 80px - 120px vertical spacing between sections to allow cinematic photography to breathe.
- **Tablet:** 8 columns with 20px gutters.
- **Mobile:** 4 columns with 16px gutters.

Alignment should be structured and geometric. Content blocks should prioritize verticality, with clear, distinct separation between "Media" (cinematic images) and "Data" (white-background cards with stats).

## Elevation & Depth

This design system uses **Ambient Shadows** and **Tonal Layering** to create a sense of premium quality. 

- **Level 0 (Surface):** The background (#F8FAFC).
- **Level 1 (Card):** White surfaces with a very soft, diffused shadow (15% opacity of Primary Navy, 20px blur, 4px offset).
- **Level 2 (Interaction):** When hovering over player cards or action items, the shadow deepens and the element scales slightly (1.02x) to create a tactile, responsive feel.
- **Glassmorphism:** Use backdrop blurs (20px) with 70% opacity Navy overlays when text is placed directly over cinematic football imagery to maintain legibility.

## Shapes

The shape language is **Structured yet Modern**. Following the 18px radius rule (`rounded-lg` in this system), all primary containers and cards feel approachable but retain a professional, architectural integrity.

- **Primary Buttons:** 18px rounded corners.
- **Player Stats Cards:** 18px rounded corners.
- **Input Fields:** 12px (Soft) rounded corners to differentiate from larger structural elements.
- **Avatars/Badges:** Perfect circles are used only for player profile photos and team crests.

## Components

### Buttons
- **Primary:** Primary Navy background, White text. 18px radius. Heavyweight Montserrat text.
- **Action:** Performance Green background, Primary Navy text for high-contrast "Apply Now" or "Join Trials" actions.
- **Ghost:** Transparent with a 2px Primary Navy border.

### Cards
- **Player Card:** Features a tall aspect ratio (3:4). Uses a cinematic photo background with a bottom-aligned Navy gradient. Stats are overlaid using Bebas Neue in Victory Gold or White.
- **Stat Chip:** Small, Performance Green or Navy pills with Inter Bold text used for "In Form," "New," or "Top Talent" labels.

### Input Fields
- White background with a 1px #E2E8F0 border. On focus, the border shifts to Performance Green with a soft glow.

### Lists & Data
- Use clean, horizontal rows for league tables or training schedules. Alternate row colors using the Background color and pure White for readability. Use Bebas Neue for all numerical data in lists.
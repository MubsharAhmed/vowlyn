---
name: Vowlyn Design System
colors:
  surface: '#f7f9ff'
  surface-dim: '#cbdcee'
  surface-bright: '#f7f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#ecf4ff'
  surface-container: '#e2efff'
  surface-container-high: '#d9eafc'
  surface-container-highest: '#d4e4f6'
  on-surface: '#0d1d2a'
  on-surface-variant: '#4c4451'
  inverse-surface: '#223240'
  inverse-on-surface: '#e7f2ff'
  outline: '#7d7483'
  outline-variant: '#cec3d3'
  surface-tint: '#7b41b3'
  primary: '#2e0052'
  on-primary: '#ffffff'
  primary-container: '#4b0082'
  on-primary-container: '#ba7ef4'
  inverse-primary: '#ddb7ff'
  secondary: '#5c5d6e'
  on-secondary: '#ffffff'
  secondary-container: '#e1e1f5'
  on-secondary-container: '#626374'
  tertiary: '#2e141d'
  on-tertiary: '#ffffff'
  tertiary-container: '#462932'
  on-tertiary-container: '#b78f99'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#f0dbff'
  primary-fixed-dim: '#ddb7ff'
  on-primary-fixed: '#2c0050'
  on-primary-fixed-variant: '#622599'
  secondary-fixed: '#e1e1f5'
  secondary-fixed-dim: '#c5c5d8'
  on-secondary-fixed: '#191b29'
  on-secondary-fixed-variant: '#444655'
  tertiary-fixed: '#ffd9e2'
  tertiary-fixed-dim: '#e7bbc6'
  on-tertiary-fixed: '#2d141c'
  on-tertiary-fixed-variant: '#5e3e47'
  background: '#f7f9ff'
  on-background: '#0d1d2a'
  surface-variant: '#d4e4f6'
typography:
  display-lg:
    fontFamily: Montserrat
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Montserrat
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Montserrat
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.2'
  headline-sm:
    fontFamily: Montserrat
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.3'
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: '1.2'
    letterSpacing: 0.01em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: '1.2'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 48px
  xl: 80px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 64px
---

## Brand & Style
The design system is engineered for a high-end business automation and marketing agency. The brand personality is **sophisticated, tech-forward, and authoritative**, yet approachable enough to foster partnership and trust. 

The visual style follows a **Modern-Corporate** aesthetic with a heavy emphasis on **Minimalism** and **Glassmorphism**. It utilizes expansive white space to denote premium quality and clarity of thought. The UI should evoke a sense of seamless efficiency—where complex automation feels as light and effortless as the interface itself.

## Colors
The palette is anchored by **Deep Indigo (#4B0082)**, representing power and professional depth. This is balanced by a secondary **Lavender (#E6E6FA)** which serves as the primary surface color for cards and containers to keep the interface light.

**Tertiary Pale Pink (#FFD1DC)** is reserved for soft highlights, interactive states, or gentle calls-to-action that require a human touch. **Silver-Gray (#708090)** is used for secondary text and borders to maintain high legibility without the harshness of pure black. The background should remain a nearly-white lavender-tinted off-white to maintain a "tech-clean" atmosphere.

## Typography
We pair the geometric confidence of **Montserrat** for headings with the systematic precision of **Inter** for body text. 

- **Headlines:** Use Montserrat with tighter letter-spacing for a "logo-like" feel in titles.
- **Body:** Inter provides exceptional legibility for long-form content and data-heavy automation dashboards.
- **Scale:** Large display type should be used sparingly to highlight key value propositions, while secondary headings should maintain generous line heights to ensure a relaxed reading experience.

## Layout & Spacing
This design system utilizes a **12-column fluid grid** for desktop and a **4-column grid** for mobile. 

- **Rhythm:** We follow an 8px square grid for all internal component spacing to maintain mathematical harmony.
- **Margins:** Large horizontal margins (64px+) on desktop are essential to create the "premium" airy feel required for this brand. 
- **Containers:** Content should be grouped in logic-based sections with significant vertical padding (80px+) to allow the user's eye to rest between different automation features or service offerings.

## Elevation & Depth
Depth is created through **Tonal Layers** and **Ambient Shadows**. We avoid harsh black shadows in favor of tinted shadows that use the primary indigo hue.

- **Surface Levels:** The base background is the lowest level. Content cards sit on Level 1 with a soft lavender background and a 20px blur shadow at 4% opacity. 
- **Overlays:** Modals and dropdowns use Level 2, featuring a subtle backdrop blur (glassmorphism) to maintain context of the underlying page while focusing the user's attention.
- **Depth Color:** Shadows should use `rgba(75, 0, 130, 0.08)` to feel integrated into the brand's purple universe rather than looking "dirty."

## Shapes
The shape language is defined by **expansive roundedness**. All primary containers and buttons use a minimum of 16px (`rounded-lg`) corner radii to communicate friendliness and modern tech sensibilities.

- **Primary Radius:** 16px (1rem) for cards and main buttons.
- **Secondary Radius:** 8px (0.5rem) for smaller inputs and chips.
- **Full Radius:** Use pill-shapes for "Status" indicators or "Live" badges to distinguish them from actionable buttons.

## Components
- **Buttons:** Primary buttons are solid Deep Indigo with white text. Secondary buttons use a Lavender fill with Indigo text. Hover states should involve a subtle scale-up (1.02x) rather than a dramatic color shift.
- **Cards:** Cards are the workhorse of the design system. They must feature a 1px border of `#E6E6FA` (Lavender) and the standard 16px radius.
- **Inputs:** Form fields should have a soft Lavender background that turns white on focus, with a 2px Indigo border to clearly signal active engagement.
- **Chips/Tags:** Use the Pale Pink tertiary color for high-visibility tags like "New" or "Hot," and Silver-Gray for neutral categories.
- **Automation Nodes:** For the agency's specific needs, create "Logic Nodes"—specialized cards with distinct icons and connecting "pipe" visuals that use a gradient from Lavender to Indigo to visualize workflow flows.
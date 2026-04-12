# Design System Document: The Analytical Atelier

## 1. Overview & Creative North Star
### The Creative North Star: "The Digital Ledger"
The objective of this design system is to transform standard sales analytics into a high-end editorial experience. We are moving away from the "cluttered dashboard" trope and toward a "Digital Ledger" aesthetic—characterized by vast breathing room, authoritative typography, and a sense of calm sophistication. 

To break the "template" look, we utilize **intentional asymmetry**. For example, a hero metric should not just be a centered number; it should be a composition of `display-md` typography paired with a secondary `label-md` descriptor, offset to create visual tension and interest. We treat data not as a utility, but as a curated exhibition.

---

## 2. Colors: Tonal Architecture
Our palette is rooted in deep indigo and emerald, supported by an expansive range of surface tones. The goal is to move beyond flat containers and use color as a structural tool.

*   **Primary (#3525cd) & Secondary (#006c49):** Use these for high-intent actions and meaningful data highlights. 
*   **The "No-Line" Rule:** **Strictly prohibit 1px solid borders for sectioning.** Boundaries must be defined solely through background color shifts. For example, a card should be `surface_container_lowest` (#ffffff) sitting on a `background` (#f8f9ff) or `surface_container_low` (#eff4ff) canvas.
*   **Surface Hierarchy & Nesting:** Use the `surface_container` tiers to create depth. 
    *   Level 0: `background` (#f8f9ff)
    *   Level 1: `surface_container_low` (#eff4ff) for sidebar or secondary navigation zones.
    *   Level 2: `surface_container_lowest` (#ffffff) for the primary content cards.
*   **The "Glass & Gradient" Rule:** Floating elements (like dropdown menus or mobile nav) should utilize Glassmorphism: `surface_container_highest` with 80% opacity and a `backdrop-blur-xl`. Main CTAs should use a subtle linear gradient from `primary` (#3525cd) to `primary_container` (#4f46e5) at a 135-degree angle to add "soul" to the action.

---

## 3. Typography: Editorial Authority
We use a dual-font strategy to balance character with precision.

*   **The Display Voice (Manrope):** Use Manrope for all `display`, `headline`, and `title` tokens. It provides a geometric, modern feel that commands attention. 
    *   *Usage:* `display-lg` (3.5rem) should be reserved for the most critical metric (e.g., Total Revenue) to create a clear entry point for the eye.
*   **The Data Voice (Inter):** Use Inter for all `body` and `label` tokens. It is optimized for legibility at small sizes, ensuring that dense sales tables remain readable.
*   **Visual Rhythm:** Maintain a high contrast between scales. Never place a `title-md` and `body-lg` together if they look too similar in weight. If a header is `headline-sm`, the supporting text should jump down to `label-md` to emphasize hierarchy.

---

## 4. Elevation & Depth: Tonal Layering
Traditional drop shadows are a fallback, not a standard. We achieve hierarchy through **Tonal Layering**.

*   **The Layering Principle:** To lift a card, place a `surface_container_lowest` container on a `surface_dim` background. The color shift creates a "natural lift" that feels architectural.
*   **Ambient Shadows:** If a floating element requires a shadow (e.g., a modal), use an ultra-diffused shadow: `box-shadow: 0 20px 50px -12px rgba(18, 28, 42, 0.08)`. The shadow color must be a tinted version of `on_surface` (#121c2a), never pure black.
*   **The "Ghost Border":** If accessibility requires a stroke, use the `outline_variant` (#c7c4d8) at **15% opacity**. This creates a "suggestion" of a border without breaking the editorial flow.
*   **Glassmorphism:** For top navigation bars, use `surface_bright` with a 70% opacity and a `backdrop-blur-md`. This allows the dashboard data to scroll underneath with a soft, frosted effect, creating a sense of continuity.

---

## 5. Components: Elevated DaisyUI Implementation

### Cards & Layout
*   **Rule:** Forbid all divider lines (`<hr>` or `border-b`).
*   **Execution:** Use `xl` (1.5rem) rounded corners for main dashboard cards. Separate content using the spacing scale (e.g., `py-8` between sections) or by nesting a `surface_container` inside a `surface_container_high` block.

### Buttons
*   **Primary:** DaisyUI `.btn-primary` using the gradient rule mentioned in Section 2. Shape should be `md` (0.75rem).
*   **Secondary/Tertiary:** Use `.btn-ghost` with `on_surface_variant` text. High-end design favors "ghost" states for everything except the "North Star" action.

### Tables & Lists (Recent Transactions)
*   Avoid the DaisyUI `.table-zebra` look. Instead, use a clean `surface_container_lowest` background. 
*   **Interactions:** On hover, change the row background to `surface_container_low`. 
*   **Actions:** Use subtle `outline` icons rather than filled ones to maintain the "light" editorial feel.

### Input Fields & Selects
*   Use `surface_container_lowest` for the input background with a "Ghost Border."
*   When focused, the border should transition to `primary` (#3525cd) with a subtle `primary_fixed` (#e2dfff) outer glow (no blur).

---

## 6. Do's and Don'ts

### Do
*   **DO** use white space as a functional element. If a chart feels crowded, increase the padding to `p-10` rather than shrinking the chart.
*   **DO** use `tertiary` (#684000) for "cautionary" data points (e.g., a store performing below average) to provide a sophisticated alternative to generic orange/yellow.
*   **DO** ensure that the `on_background` text (#121c2a) is used for primary headings to maintain a high-contrast, premium feel.

### Don't
*   **DON'T** use `error` (#ba1a1a) for everything negative. Use it only for critical failures. For "down" trends, use a subtle `on_surface_variant` to keep the dashboard "quiet."
*   **DON'T** use the default DaisyUI `rounded-box` (usually 1rem). Stick strictly to our Roundedness Scale: `xl` for containers, `md` for interactive elements.
*   **DON'T** ever use a solid 1px `#000` or `#ccc` border. It kills the premium "editorial" illusion instantly.
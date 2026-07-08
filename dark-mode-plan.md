# Dark/Light Mode Implementation Plan

## Overview

The UteHub2026 theme already uses CSS custom properties extensively (462+ references) in a `:root` block. There are zero `prefers-color-scheme` media queries. The color palette is designed exclusively for light mode. A dark mode toggle can be implemented by layering dark mode overrides on top of the existing CSS variable system using `[data-theme="dark"]` selectors.

---

## Files That Need Modification

### 1. `style.css`

**Why:** All 462+ CSS custom property references need dark mode counterpart values. The existing `:root` block defines light-mode-only colors for backgrounds, text, borders, shadows, and accent colors.

**Changes:**
- Move all current `:root` color variables into a new `:root` block as the **light mode defaults** (no structural change to values, they stay as-is)
- Add a new `@media (prefers-color-scheme: dark)` block that redefines the color variables for dark mode
- Add a `[data-theme="dark"]` block that overrides the same variables (takes precedence over the media query)
- Add a `[data-theme="light"]` block for explicit light mode override
- Update any hardcoded color values that do not use CSS variables (e.g., `background: var(--crimson)` is fine; hardcoded `#0d0d0f` or `#ffffff` elsewhere needs to be checked)
- Add transition rules for smooth theme switching on applicable properties (background, color, border-color)

**Variables that need dark mode values (from `:root`):**

| Variable | Light Value | Dark Mode Purpose |
|---|---|---|
| `--black` | `#0d0d0f` | Becomes the dark background |
| `--ink` | `#14141a` | Becomes light text |
| `--ink-2` | `#55555e` | Becomes muted light text |
| `--meta` | `#8a8a92` | Becomes dim light text |
| `--canvas` | `#f4f4f6` | Becomes dark page background |
| `--card` | `#ffffff` | Becomes dark card background |
| `--wash` | `#fbfbfc` | Becomes dark section background |
| `--line` | `#e5e5ea` | Becomes dark border |
| `--line-2` | `#d6d6dc` | Becomes dark secondary border |

**Non-color variables that stay the same:**
- `--crimson`, `--crimson-deep`, `--crimson-bright` (brand accent - keep as-is)
- `--win` (green - keep as-is)
- `--font-display`, `--font-collegiate`, `--font-body` (fonts - keep as-is)
- `--shadow-sm`, `--shadow` (may need lighter shadow values for dark mode)
- `--r-card`, `--r-input`, `--r-chip` (border radii - keep as-is)

**Shadows in dark mode should use reduced opacity:**
```css
--shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
--shadow: 0 1px 2px rgba(0, 0, 0, 0.25), 0 10px 30px rgba(0, 0, 0, 0.35);
```

### 2. `functions.php`

**Why:** The theme needs to output a theme toggle button in the navigation and handle theme persistence during page loads.

**Changes:**
- Add a `utehub2026_render_theme_toggle()` function that outputs a toggle button inside the primary nav (after the brand logo, before the nav links)
- The function should:
  - Read the current theme from `get_theme_mod('utehub2026_theme', 'default')`
  - Output a `<button class="theme-toggle" type="button" aria-label="Toggle dark mode" data-theme="...">` with a sun/moon SVG icon
  - Use inline SVG icons (similar pattern to existing `utehub2026_get_svg()`)
  - Include a `<style>` block with `noscript` fallback that sets `[data-theme]` on `<html>` based on the PHP-determined value (prevents flash of wrong theme)
- Modify `utehub2026_render_primary_nav()` to call `utehub2026_render_theme_toggle()` after the brand `<a>` tag
- Add a new Customizer section (`utehub2026_appearance`) with a theme selector control:
  - Options: `default` (follows system), `light`, `dark`
  - Sanitized via `sanitize_key()`
- Register the theme mod in `utehub2026_setup()` or `utehub2026_customize_register()`

### 3. `assets/nav.js` (or new `assets/theme-toggle.js`)

**Why:** Client-side JavaScript is needed to handle the toggle click, persist the preference, and apply the theme attribute to `<html>`.

**Recommended approach:** Create a new `assets/theme-toggle.js` file rather than modifying `nav.js`, to keep concerns separate.

**Changes (new file):**
- On DOMContentLoaded (or inline, before paint):
  - Read `localStorage.getItem('utehub2026-theme')`
  - If found, apply `document.documentElement.setAttribute('data-theme', value)`
  - If not found, read `window.matchMedia('(prefers-color-scheme: dark)').matches` and apply `data-theme="dark"` if true
- Toggle click handler:
  - Switch between `data-theme="dark"` and `data-theme="light"`
  - Save to `localStorage`
  - Update button ARIA state and icon
- Listen for `prefers-color-scheme` changes via `matchMedia().addEventListener('change', ...)` to sync when user has `default` mode and changes their system preference
- Export/queue via `wp_enqueue_script` in `functions.php`

**Alternative (single file):** Add the toggle logic to the existing `assets/nav.js`. This keeps all nav-related JS in one file but mixes concerns.

### 4. `assets/utehub-wordmark-light.png` (new asset)

**Why:** The current brand logo `utehub-wordmark-dark.png` is a dark wordmark designed for light backgrounds. In dark mode, it would be invisible or barely visible against a dark background.

**Changes:**
- Create a light/white variant of the wordmark logo
- Modify `utehub2026_get_brand_url()` in `functions.php` to return the light variant when `data-theme="dark"` is set (via inline PHP check or CSS-based logo swapping)

**CSS-only logo swap alternative (no PHP change needed):**
```css
.brand img {
  filter: brightness(0) invert(1);
  transition: filter 0.2s ease;
}
[data-theme="dark"] .brand img {
  filter: brightness(0) invert(1);
}
```
This CSS `filter` approach avoids creating a new asset and handles any logo automatically, though it may affect logos that already contain color. A dedicated light variant PNG is the cleaner long-term solution.

---

## Implementation Strategy

### Phase 1: CSS Foundation

1. Add dark mode CSS variable overrides in `style.css`:
   - `@media (prefers-color-scheme: dark) { :root { ...dark values... } }`
   - `[data-theme="dark"] { :root { ...dark values... } }`
   - `[data-theme="light"] { :root { ...light values... } }`

2. Add CSS transitions for smooth switching:
   ```css
   body, .card, .page-card, .panel, input, select, textarea {
     transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
   }
   ```

3. Ensure all color references use CSS variables (audit for hardcoded colors).

### Phase 2: PHP Toggle & Customizer

4. Add theme Customizer section and control in `functions.php`.
5. Add toggle button HTML in the primary navigation via `utehub2026_render_theme_toggle()`.
6. Add no-JS `<style>` fallback in the toggle output to set `data-theme` on `<html>` on initial load.

### Phase 3: JavaScript Toggle Logic

7. Create `assets/theme-toggle.js` with:
   - Immediate theme application on load (before paint)
   - Toggle click handler
   - localStorage persistence
   - System preference listener

8. Enqueue the script in `utehub2026_enqueue_assets()`.

### Phase 4: Logo & Asset Handling

9. Create `utehub-wordmark-light.png` or implement CSS `filter` swap for the brand logo.

---

## Toggle Placement

The theme toggle button should be placed in the primary navigation bar, between the brand logo and the navigation links:

```html
<nav class="nav" aria-label="Primary">
  <a class="brand" href="/">
    <img src="..." alt="...">
  </a>
  <button class="theme-toggle" type="button" aria-label="Toggle dark mode">
    <!-- sun/moon SVG -->
  </button>
  <button class="nav-toggle" type="button" aria-expanded="false">
    <!-- hamburger -->
  </button>
  <ul class="links" id="primary-menu">
    <!-- nav items -->
  </ul>
</nav>
```

**Rationale:**
- It is in the nav bar where users expect global controls
- It appears after the brand (primary visual anchor) but before navigation items
- On mobile, it stacks naturally within the nav bar alongside the existing hamburger toggle
- It is visible on every page without requiring scroll

**Mobile consideration:** On screens below 900px (where `nav-toggle` activates), the theme toggle should remain visible in the horizontal nav bar alongside the brand and hamburger, not pushed into the dropdown menu.

---

## CSS Custom Properties Strategy

**Yes, CSS custom properties should be used** - and they already are. The existing `:root` block provides the perfect foundation.

**Structure:**

```css
:root {
  /* Light mode (default) - existing values stay here */
  --crimson: #cc0000;
  --black: #0d0d0f;
  --ink: #14141a;
  --ink-2: #55555e;
  --meta: #8a8a92;
  --canvas: #f4f4f6;
  --card: #ffffff;
  --wash: #fbfbfc;
  --line: #e5e5ea;
  --line-2: #d6d6dc;
  --win: #2fa84f;
  --shadow-sm: 0 1px 2px rgba(20, 20, 30, 0.06);
  --shadow: 0 1px 2px rgba(20, 20, 30, 0.05), 0 10px 30px rgba(20, 20, 30, 0.07);
  /* fonts, radii - unchanged */
}

@media (prefers-color-scheme: dark) {
  :root {
    --black: #f4f4f6;
    --ink: #e5e5ea;
    --ink-2: #b0b0b8;
    --meta: #8a8a92;
    --canvas: #0d0d0f;
    --card: #14141a;
    --wash: #1a1a20;
    --line: #2a2a32;
    --line-2: #33333d;
    --win: #2fa84f;
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
    --shadow: 0 1px 2px rgba(0, 0, 0, 0.25), 0 10px 30px rgba(0, 0, 0, 0.35);
  }
}

[data-theme="dark"] {
  /* Same overrides as prefers-color-scheme: dark */
  --black: #f4f4f6;
  --ink: #e5e5ea;
  --ink-2: #b0b0b8;
  --meta: #8a8a92;
  --canvas: #0d0d0f;
  --card: #14141a;
  --wash: #1a1a20;
  --line: #2a2a32;
  --line-2: #33333d;
  --win: #2fa84f;
  --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
  --shadow: 0 1px 2px rgba(0, 0, 0, 0.25), 0 10px 30px rgba(0, 0, 0, 0.35);
}

[data-theme="light"] {
  /* Explicit light - same as :root defaults */
  --black: #0d0d0f;
  --ink: #14141a;
  --ink-2: #55555e;
  --meta: #8a8a92;
  --canvas: #f4f4f6;
  --card: #ffffff;
  --wash: #fbfbfc;
  --line: #e5e5ea;
  --line-2: #d6d6dc;
  --win: #2fa84f;
  --shadow-sm: 0 1px 2px rgba(20, 20, 30, 0.06);
  --shadow: 0 1px 2px rgba(20, 20, 30, 0.05), 0 10px 30px rgba(20, 20, 30, 0.07);
}
```

**Precedence order:** `[data-theme="dark"]` overrides `@media (prefers-color-scheme: dark)` overrides `:root`. This is the correct cascade for a user-toggleable theme.

---

## User Preference Storage

**Mechanism:** `localStorage`

**Key:** `utehub2026-theme`

**Values:** `"dark"`, `"light"`, or `null` (not set = follows system)

**Behavior:**
1. On page load (inline script in `<head>`, before any CSS paint):
   - Check `localStorage.getItem('utehub2026-theme')`
   - If `"dark"` or `"light"`, set `document.documentElement.setAttribute('data-theme', value)`
   - If `null` or not set, check `window.matchMedia('(prefers-color-scheme: dark)').matches`
     - If true, set `data-theme="dark"`
     - If false, no attribute needed (light is default)

2. On toggle click:
   - Determine current state from `data-theme` attribute
   - Set opposite: `dark` -> `light`, `light`/`none` -> `dark`
   - Save to `localStorage.setItem('utehub2026-theme', value)`
   - Update `document.documentElement.setAttribute('data-theme', value)`
   - Update button icon and ARIA label

3. On system preference change:
   - Listen for `prefers-color-scheme` media query changes
   - If `localStorage` has no saved preference, sync the `data-theme` attribute to match the new system setting

**Why localStorage (not cookies/sessionStorage):**
- No server round-trip needed
- Persists across sessions
- No privacy/compliance concerns for a UI preference
- Simpler than a Customizer setting which requires PHP round-trip

**Server-side fallback:** The theme mod (`get_theme_mod('utehub2026_theme')`) serves as a secondary persistence mechanism and allows admins to set a site-wide default. The JavaScript `localStorage` takes precedence when set by the user.

---

## prefers-color-scheme Handling

**Default behavior (no user override):**
- The theme respects the user's OS/browser preference via `@media (prefers-color-scheme: dark)`
- This is the standard, expected behavior

**User overrides system preference:**
- When the user clicks the toggle, `localStorage` is set and `data-theme` is applied
- The `[data-theme="dark"]` / `[data-theme="light"]` selectors in CSS take precedence over `@media (prefers-color-scheme: dark)`
- The user's choice persists even if they later change their system preference

**System preference changes while user has no override:**
- JavaScript `matchMedia('(prefers-color-scheme: dark)').addEventListener('change', ...)` listener detects the change
- Updates `data-theme` attribute to match the new system setting

**Hierarchy:**
```
1. [data-theme="dark"] / [data-theme="light"]  (user explicit choice - highest priority)
2. @media (prefers-color-scheme: dark)          (system preference - medium priority)
3. :root defaults                               (light mode - lowest priority / fallback)
```

---

## Potential Risks & Compatibility Concerns

### 1. Flash of Wrong Content (FOUC)
**Risk:** Medium. Without an inline `<style>` block in `<head>`, the page may flash in light mode before JavaScript applies the dark theme.

**Mitigation:**
- Output an inline `<style>` block in the `<head>` via `wp_enqueue_style` with a priority that ensures it renders first
- The inline style reads `localStorage` and sets `[data-theme]` on `<html>` before any other CSS
- Example:
  ```html
  <style id="utehub2026-theme-override">
    (function() {
      var t = localStorage.getItem('utehub2026-theme');
      if (t === 'dark') document.documentElement.setAttribute('data-theme', 'dark');
      else if (t === 'light') document.documentElement.setAttribute('data-theme', 'light');
      else if (window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.setAttribute('data-theme', 'dark');
    })();
  </style>
  ```

### 2. Third-Party Plugins Inline Styles
**Risk:** Low. Some plugins may output inline styles with hardcoded colors that won't respond to CSS variable changes.

**Mitigation:**
- Audit common BuddyPress and bbPress templates for hardcoded colors
- Check for external services (analytics widgets, chat widgets) that may have their own dark mode

### 3. Images and Media
**Risk:** Low-Medium. User-uploaded images will not be affected by theme changes (expected behavior). The brand logo needs a light variant.

**Mitigation:**
- Create `utehub-wordmark-light.png` or use CSS `filter: brightness(0) invert(1)` for automatic swap
- Consider adding `mix-blend-mode: difference` to the logo as an alternative that works with any logo color

### 4. SVG Icons
**Risk:** Low. Existing SVG icons use `stroke="currentColor"` and `fill="none"`, which means they will inherit the text color in dark mode. This is generally correct but some icons may need adjustment.

**Mitigation:**
- Audit SVG icons that use hardcoded `fill` values (e.g., the pin icon uses `fill="currentColor"` which is fine)
- The `thumb-up` and `thumb-down` SVGs use `fill="currentColor"` - they will follow text color in dark mode

### 5. BuddyPress Compatibility
**Risk:** Medium. BuddyPress has its own CSS that may not use the theme's CSS variables. BuddyPress components (members, activity, messages, groups) may look broken in dark mode.

**Mitigation:**
- Add dark mode overrides for common BuddyPress classes (`.buddypress`, `.bp`, `.activity-item`, `.member`, etc.)
- Test with BuddyPress active
- The theme already has BuddyPress-specific template overrides which may need dark mode updates

### 6. bbPress Compatibility
**Risk:** Medium. Similar to BuddyPress, bbPress has its own styling that may not respond to the theme's CSS variables.

**Mitigation:**
- Add dark mode overrides for bbPress classes (`.bbp-forum-header`, `.bbp-reply-header`, `.bbp-the-content`, etc.)
- Test with bbPress active

### 7. Select Element Styling
**Risk:** Low. `<select>` elements have limited CSS variable support in some browsers for background and border colors.

**Mitigation:**
- Explicitly set `background-color` and `color` on `select` elements in dark mode
- Test on Safari (known for inconsistent select styling)

### 8. Print Styles
**Risk:** Low. Dark mode should not affect print output.

**Mitigation:**
- Ensure `@media print` rules explicitly set light-mode colors regardless of `data-theme`

### 9. Performance
**Risk:** Negligible. CSS variable recalculation is handled efficiently by browsers.

**Mitigation:** None needed - this is a non-issue with modern browsers.

### 10. Accessibility
**Risk:** Low. Dark mode must maintain sufficient color contrast ratios (WCAG AA: 4.5:1 for normal text).

**Mitigation:**
- Verify all dark mode color pairs meet WCAG AA contrast requirements
- The `--meta` variable (`#8a8a92` light) maps to `#8a8a92` in dark mode - verify this still meets contrast against `--canvas: #0d0d0f` (it does: ~5.5:1)

### 11. Existing Customizer Settings
**Risk:** Low. The theme already uses `get_theme_mod()` extensively. Adding a new theme mod should not conflict.

**Mitigation:** Use a unique mod name: `utehub2026_theme`

### 12. Inline SVG fill values
**Risk:** Low. The `utehub2026_get_svg()` function in `functions.php` returns hardcoded SVG strings. Icons using `fill="currentColor"` will adapt; icons using `fill="none"` will also adapt. The pin icon uses `fill="currentColor"` which is correct.

**Mitigation:** No changes needed - the existing SVG approach is compatible with dark mode.

---

## Summary of Files

| File | Action | Scope |
|---|---|---|
| `style.css` | Add dark mode variable overrides | ~50 lines of new CSS |
| `functions.php` | Add toggle button, theme Customizer, logo swap logic | ~80 lines of new PHP |
| `assets/theme-toggle.js` (new) | Toggle click handler, localStorage, system preference sync | ~60 lines of JS |
| `assets/utehub-wordmark-light.png` (new) | Light variant of brand logo | New asset file |

**Total new code: ~190 lines** (excluding the new asset file).

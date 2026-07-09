# Plugin Theming Guide

This document describes the theme contract that custom plugins should use to support light and dark mode inside the `UteHub2026` theme.

## Goal

Use the theme as the source of truth for:

- current light/dark mode
- shared color tokens
- surface/background styles
- theme change events

This avoids hardcoded white backgrounds and black text inside plugin UIs.

## Theme Contract

The theme now exposes stable hooks that plugins can rely on.

### HTML hooks

The theme sets these on page load and updates them when the user toggles mode:

- `html[data-theme="light"]`
- `html[data-theme="dark"]`
- `body[data-theme="light"]`
- `body[data-theme="dark"]`
- `body.theme-light`
- `body.theme-dark`
- `body.utehub-theme-contract`
- `body.utehub-theme-surface`

Recommended plugin CSS should key off `body[data-theme="dark"]` or `body.theme-dark`.

### JavaScript hooks

The theme exposes:

- `window.UteHubTheme.current`
- `window.UteHubTheme.getCurrentTheme()`

The theme dispatches:

- `window` event: `utehub:themechange`

Event payload:

```js
window.addEventListener('utehub:themechange', function (event) {
  console.log(event.detail.theme); // 'light' or 'dark'
});
```

### Theme config object

The nav script localizes this object:

```js
window.UteHubThemeConfig = {
  storageKey: 'utehub2026-theme',
  eventName: 'utehub:themechange',
  bodyClassPrefix: 'theme-',
  bodyThemeAttribute: 'data-theme',
  rootThemeAttribute: 'data-theme'
};
```

Plugins usually do not need this object directly, but it is available if needed.

## Theme Tokens

Prefer theme CSS variables over plugin-owned hardcoded colors.

Common tokens:

```css
--crimson
--crimson-deep
--crimson-bright
--black
--ink
--ink-2
--meta
--canvas
--card
--wash
--line
--line-2
--win
--r-card
--r-input
--r-chip
--shadow-sm
--shadow
```

### Recommended mappings

Use these as the default mental model:

- page background: `var(--canvas)`
- card/panel background: `var(--card)`
- soft inset background: `var(--wash)`
- main text: `var(--ink)`
- secondary text: `var(--ink-2)`
- metadata/muted text: `var(--meta)`
- standard border: `var(--line)`
- stronger border: `var(--line-2)`
- accent/action color: `var(--crimson)`

## Recommended Plugin CSS Pattern

Wrap your plugin output in one stable root class.

Example:

```php
echo '<div class="tk-pickem utehub-plugin-surface">';
echo '...plugin markup...';
echo '</div>';
```

Then style from that root.

```css
.tk-pickem {
  color: var(--ink);
}

.tk-pickem .panel,
.tk-pickem .card,
.tk-pickem table,
.tk-pickem .toolbar {
  background: var(--card);
  border: 1px solid var(--line);
  color: var(--ink);
}

.tk-pickem input,
.tk-pickem select,
.tk-pickem textarea {
  background: var(--card);
  border: 1px solid var(--line-2);
  color: var(--ink);
}

.tk-pickem .muted,
.tk-pickem .meta,
.tk-pickem small {
  color: var(--meta);
}

.tk-pickem .button-primary,
.tk-pickem button[type='submit'] {
  background: var(--crimson);
  border-color: var(--crimson);
  color: #fff;
}
```

## Recommended Plugin JS Pattern

For plugins with charts, custom canvases, dynamic classes, or third-party widgets, react to theme changes in JavaScript.

```js
function applyPluginTheme(theme) {
  var root = document.querySelector('.tk-pickem');
  if (!root) {
    return;
  }

  root.dataset.theme = theme;
}

document.addEventListener('DOMContentLoaded', function () {
  var initialTheme = window.UteHubTheme && window.UteHubTheme.getCurrentTheme
    ? window.UteHubTheme.getCurrentTheme()
    : (document.body.getAttribute('data-theme') || 'light');

  applyPluginTheme(initialTheme);

  window.addEventListener('utehub:themechange', function (event) {
    applyPluginTheme(event.detail.theme);
  });
});
```

## PHP Integration Example

For server-rendered plugin output, you usually do not need to pass the theme manually if your CSS uses the body/html hooks and CSS variables.

Basic example:

```php
function tk_render_widget() {
    echo '<section class="tk-widget utehub-plugin-surface">';
    echo '<h2 class="tk-widget-title">Standings</h2>';
    echo '<div class="tk-widget-body">';
    echo '...';
    echo '</div>';
    echo '</section>';
}
```

If a plugin truly needs the theme in PHP, read the attribute client-side instead of duplicating theme state in plugin settings.

## Rules For Custom Plugins

### Do

- use one root class per plugin or feature
- use theme CSS variables
- test in both `light` and `dark`
- listen for `utehub:themechange` if UI updates after load
- use `var(--card)` and `var(--ink)` before inventing new neutrals
- treat plugin surfaces like first-class theme surfaces

### Do not

- hardcode `#fff` backgrounds for panels, tables, inputs, or nav
- hardcode `#000` or very dark text without checking dark mode
- assume page background is always light
- rely only on theme fallback CSS for complex UI
- use `!important` in plugin CSS unless overriding a third-party library you do not control

## When Theme-Side Fallback CSS Is Still Useful

Theme-side fallback CSS is still appropriate for:

- third-party plugins you do not want to edit
- legacy plugin output
- generic HTML tables/forms inside content pages
- emergency cleanup when a plugin ships light-only styles

But for custom plugins, the preferred path is plugin-owned theme-aware CSS.

## Suggested Minimal Contract For New Plugins

Every new custom plugin should have:

1. One root wrapper class
2. CSS based on theme variables
3. No hardcoded white/black surfaces by default
4. Optional `utehub:themechange` listener if UI is dynamic

Example checklist:

- wrapper class added
- tables use `var(--card)` and `var(--line)`
- forms use `var(--card)`, `var(--ink)`, `var(--line-2)`
- muted copy uses `var(--meta)`
- primary actions use `var(--crimson)`
- JS widgets update on `utehub:themechange`

## Relevant Theme Files

- [functions.php](../functions.php)
- [style.css](../style.css)
- [assets/nav.js](../assets/nav.js)
- [docs/styling.md](./styling.md)

## Current Theme Hook Sources

Theme state contract currently comes from:

- `utehub2026_filter_body_classes()`
- `utehub2026_dark_mode_inline_style()`
- `utehub2026_enqueue_assets()`
- theme toggle logic in `assets/nav.js`

If these change later, update this document too.

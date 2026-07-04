# Styling Guide

## Design System

### Color Palette

**Primary Colors:**
| Variable | Value | Usage |
|----------|-------|-------|
| `--crimson` | `#cc0000` | Primary links, accents |
| `--crimson-deep` | `#a30000` | Link hover states |
| `--crimson-bright` | `#e61717` | Gradients, highlights |

**Neutrals:**
| Variable | Value | Usage |
|----------|-------|-------|
| `--black` | `#0d0d0f` | Navigation background |
| --ink` | `#14141a` | Primary text |
| `--ink-2` | `#55555e` | Secondary text |
| `--meta` | `#8a8a92` | Metadata, timestamps |
| `--canvas` | `#f4f4f6` | Page background |
| `--card` | `#ffffff` | Card backgrounds |
| `--wash` | `#fbfbfc` | Light backgrounds |
| `--line` | `#e5e5ea` | Borders (light) |
| `--line-2` | `#d6d6dc` | Borders (medium) |

**Status Colors:**
| Variable | Value | Usage |
|----------|-------|-------|
| `--win` | `#2fa84f` | Success, online status |

### Typography

**Font Families:**
```css
--font-display: 'Oswald', 'Arial Narrow', sans-serif;  /* Headers */
--font-collegiate: 'Graduate', serif;                  /* Brand elements */
--font-body: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, system-ui, sans-serif;
```

**Base Settings:**
- Root font size: `16px` (line 38)
- Body inherits from root
- No rem conversions in most rules

### Spacing

No CSS custom properties for spacing - uses fixed values:
- `4px`, `8px`, `12px`, `16px`, `24px`, `32px`, `36px`, `48px`

### Border Radius

| Variable | Value | Usage |
|----------|-------|-------|
| `--r-card` | `14px` | Cards, containers |
| `--r-input` | `10px` | Form inputs |
| `--r-chip` | `5px` | Pills, badges |

### Shadows

```css
--shadow-sm: 0 1px 2px rgba(20, 20, 30, 0.06);
--shadow: 0 1px 2px rgba(20, 20, 30, 0.05), 0 10px 30px rgba(20, 20, 30, 0.07);
```

---

## Layout Components

### Navigation (`.nav`)

**Location:** `style.css:81-270`

**Structure:**
```html
<nav class="nav">
    <button class="nav-toggle" aria-expanded="false">
        <span class="nav-toggle-bars"><span></span><span></span><span></span></span>
    </button>
    
    <div class="brand">...</div>
    
    <ul class="links">
        <li class="menu-item">
            <a class="nav-link">Home</a>
        </li>
        <li class="menu-item menu-item-has-children">
            <a class="nav-link">Forums</a>
            <button class="submenu-toggle" aria-expanded="false">
                <svg><!-- chevron --></svg>
            </button>
            <ul class="sub-menu">
                <li><a class="submenu-link">Sub Item</a></li>
            </ul>
        </li>
    </ul>
</nav>
```

**States:**
- `.is-open` - Mobile menu expanded
- `.is-submenu-open` - Submenu visible
- `aria-expanded` - Accessibility attribute on toggles

**Responsive Behavior:**
- Desktop (≥900px): Horizontal layout, hover dropdowns
- Mobile (<900px): Hamburger toggle, click to expand submenus

### Page Wrappers

| Class | Purpose |
|-------|---------|
| `.site-content` | Main content area (min-height: calc(100vh - 70px)) |
| `.page-wrap` / `.uh-wrap` | Content container with max-width |
| `.page-wrap--single-column` | Full-width single column layout |

### Page Headers

**`.feedhead`** - Topics/activity headers (line 388)
```html
<div class="feedhead">
    <div class="ttl">
        <span class="eye">The Boards</span>
        <h1>Forum Title</h1>
    </div>
    <form class="search">...</form>
</div>
```

**`.thread-head`** - Single topic headers (similar structure)

### Tabs

**Location:** `style.css:455-498`

```html
<ul class="tabs">
    <li class="tab current"><a href="#">All Topics</a></li>
    <li class="tab"><a href="#">Sports</a></li>
    <li class="tab hot"><a href="#">Hot 🔥</a></li>
</ul>
```

**States:**
- `.current` - Active tab (bold, brand color underline)
- `.hot` - Special styling for "hot" filter

### Search Forms

**Location:** `style.css:422-454`

```html
<form class="search">
    <svg><!-- search icon --></svg>
    <input type="text" placeholder="Search...">
    <button>Search</button>
</form>
```

---

## Card Components

### Forum Cards (`.forum-card`)

**Location:** `style.css:1800+` (in forum-specific section)

**Structure:**
```html
<article class="forum-card">
    <div class="forum-card-icon">
        <svg><!-- icon based on forum slug --></svg>
    </div>
    
    <div class="forum-card-main">
        <h3><a href="#">Forum Title</a></h3>
        <p>Description text...</p>
        
        <div class="forum-card-subforums">
            <a class="forum-pill">
                <span>Subforum Name</span>
                <small>100 topics · 500 posts</small>
            </a>
        </div>
    </div>
    
    <div class="forum-card-stats">
        <div class="forum-stat">
            <strong>1,234</strong>
            <span>Topics</span>
        </div>
        <div class="forum-stat">
            <strong>5,678</strong>
            <span>Posts</span>
        </div>
    </div>
    
    <div class="forum-card-last">
        <img class="avatar" src="..." alt="">
        <div class="forum-card-last-meta">
            <a href="#">Username</a>
            <span><svg><!-- clock --></svg>2 hours ago</span>
        </div>
    </div>
</article>
```

**Layout:** Grid or flex-based responsive layout

### Forum Pills (`.forum-pill`)

Inline subforum links with stats, rounded background on hover.

---

## Widget Styles

### Rail Widgets (`.rail .widget`)

**Location:** `style.css:270-346`

**Rules:**
- `.panel-b > *` - Add bottom margin
- Lists remove default padding/margins
- Avatars sized consistently (40px, 48px)
- Widget titles get icon prefix via SVG or pseudo-element

### Footer Widgets

Three-column layout:
- `.footer-col-links` - Navigation links
- `.footer-col-app` - App download info
- `.footer-col-social` - Social media links

---

## Form Styles

### Input Fields

```css
input[type="text"],
input[type="email"],
textarea,
select {
    border: 1px solid var(--line);
    border-radius: var(--r-input);
    padding: 8px 12px;
}
```

**States:**
- `.is-invalid` - Error state (red border)
- `:focus` - Focus ring with brand color

### Submit Buttons

```css
input[type="submit"],
button {
    background: var(--crimson);
    color: white;
    border-radius: var(--r-chip);
}
```

**States:**
- `.is-form-valid` - Enabled state
- `.is-form-invalid` - Disabled state
- `.is-submitting` - Loading state

### bbPress Topic Form

Client-side validation adds:
- `.has-topic-form-errors` to form
- Error notice in `.bbp-topic-form-client-notice`

---

## Utility Classes

| Class | Purpose |
|-------|---------|
| `.screen-reader-text` | Visually hidden, accessible to screen readers |
| `.loading` | Loading state (spinner or opacity) |

---

## Responsive Breakpoints

**Primary breakpoint: 900px**

Navigation switches from horizontal to hamburger at this width.

---

## SVG Icons

Icons are inline SVGs rendered via PHP helper:

```php
echo utehub2026_get_svg('search');   // Search icon
echo utehub2026_get_svg('clock');    // Clock icon
echo utehub2026_get_svg('target');   // Sports icon
echo utehub2026_get_svg('trophy');   // Trophy icon
```

**Available icons:** Defined in `functions.php:807-832`

---

## CSS Organization

The stylesheet follows this rough order:

1. **Variables** (lines 8-30) - Custom properties
2. **Reset/Base** (lines 32-80) - Global rules
3. **Navigation** (lines 81-270) - Nav styles
4. **Widgets** (lines 270-346) - Rail/footer widgets
5. **Layout** (lines 346-455) - Wrappers, headers, tabs, search
6. **Topics/Forums** (lines 498+) - Forum-specific components
7. **Members/BuddyPress** - Member directory, profiles
8. **Activity** - Activity stream styles
9. **bbPress** - Forum/thread specific styles
10. **Responsive** - Media queries at end

---

## Customization Tips

### Adding New Colors

```css
:root {
    --my-color: #xxxxxx;
}
```

### Overriding Component Styles

Use more specific selectors or add custom CSS after theme stylesheet:

```css
.uh-wrap .forum-card.custom-class {
    /* Your styles */
}
```

### Adding New Components

Follow existing patterns:
1. Use CSS variables for colors/radius
2. Match spacing scale (4/8/12/16/24/32px)
3. Add responsive considerations
4. Include hover/focus states for accessibility

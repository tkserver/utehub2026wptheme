# UteHub2026 Theme Documentation

A custom WordPress theme for the Ute Hub community platform, integrating bbPress forums and BuddyPress social features.

## Overview

- **Theme Name:** UteHub2026
- **Author:** Tony Korologos
- **Version:** 1.0.0
- **Dependencies:** WordPress, bbPress, BuddyPress

## Documentation Index

- [Architecture](./architecture.md)
- [Functions Reference](./functions-reference.md)
- [JavaScript](./javascript.md)
- [Plugin Theming Guide](./plugin-theming.md)
- [Styling Guide](./styling.md)
- [Templates](./templates.md)

## Architecture

### File Structure

```
UteHub2026/
├── assets/                    # JavaScript files (no build process)
│   ├── nav.js                 # Navigation + topic form validation
│   ├── members-directory.js   # Members directory AJAX filtering
│   ├── activity-directory.js  # Activity stream AJAX filtering
│   └── member-single.js       # Member profile friends + notifications
├── bbpress/                   # bbPress template overrides
│   ├── content-archive-forum.php
│   ├── content-single-forum.php
│   ├── content-single-topic.php
│   ├── form-topic.php
│   ├── form-reply.php
│   └── ...
├── members/                   # BuddyPress member templates
│   ├── index.php             # Members directory
│   ├── single/               # Single member profile tabs
│   │   ├── activity.php
│   │   ├── friends.php
│   │   ├── home.php
│   │   ├── messages.php
│   │   ├── notifications.php
│   │   └── settings.php
├── activity/                  # BuddyPress activity templates
├── template-parts/            # Reusable component fragments
├── functions.php              # Core theme logic (2,038 lines)
├── style.css                  # All styling (5,196 lines)
├── header.php                 # Theme header
├── footer.php                 # Theme footer
├── front-page.php             # Front page template
└── page.php                   # Default page template
```

## Key Features

### 1. Custom Navigation System

Located in `functions.php:859-1054`, the theme uses a custom navigation renderer that supports:
- Mobile-responsive hamburger menu (breakpoint: 900px)
- Submenu dropdowns with keyboard accessibility
- Client-side validation for bbPress topic forms

**JavaScript:** `assets/nav.js` handles:
- Mobile menu toggle
- Submenu state management
- Topic form client-side validation (title, content, forum selection)

### 2. AJAX Directory Filtering

Members and activity directories support real-time filtering without page reloads.

**Members Directory (`assets/members-directory.js`):**
- Scope switching (all, active, popular, new)
- Sorting by name, date registered, etc.
- Cookie persistence for user preferences

**Activity Directory (`assets/activity-directory.js`):**
- Activity type tabs (all activities, forums, members, etc.)
- Filter dropdown (newest, oldest, popular)
- Search integration

### 3. Forum Archive Layout

Custom bbPress archive template (`bbpress/content-archive-forum.php`) organizes forums into sections:
- **Sports** - First 3 forums + slug-matched forums (sports, misc, professional-sports)
- **Community** - Remaining forums

Each forum card displays:
- Icon based on forum slug
- Topic/post counts
- Subforum pills
- Last activity with avatar

### 4. Right Rail Sidebars

Context-aware widget areas rendered via `utehub2026_render_right_rail()`:
- Archive context → `utehub-forum-archive-sidebar`
- Page context → `utehub-page-right-rail-sidebar`
- Single topic → Dynamic based on forum hierarchy

### 5. BuddyPress Integration

**Cover Images:** Custom cover photo feature registered in `functions.php:215-262`

**Member Profile Tabs:** Custom templates for:
- Home (profile summary)
- Friends (with pagination + AJAX refresh)
- Messages
- Notifications (bulk delete functionality)
- Settings

## Core Functions Reference

### Theme Setup (`functions.php:20-39`)
```php
utehub2026_setup()  // Title tag, post thumbnails, custom logo, nav menus
```

### Asset Enqueue (`functions.php:68-157`)
```php
utehub2026_enqueue_assets()  // Loads CSS + JS with filemtime versioning
```

### Navigation
```php
utehub2026_get_default_nav_items()     // Default nav structure (9 items)
utehub2026_get_primary_nav_items()     // Filtered nav items
utehub2026_render_primary_nav()        // Renders full navigation HTML
utehub2026_render_nav_item($item, $depth)  // Individual item renderer
```

### Templates
```php
utehub2026_render_content_page_layout($args)   // Page with right rail
utehub2026_render_topics_feed($base_url)       // Topics feed with tabs
utehub2026_render_right_rail($context, $topic_id)  // Sidebar renderer
```

### Utilities
```php
utehub2026_get_svg($icon)           // SVG icon helper
utehub2026_get_brand_url()          // Branding URL
utehub2026_get_initials($name)      // Avatar fallback initials
utehub2026_get_color_class($seed)   // Consistent color assignment
utehub2026_render_avatar($user_id, $size, $args)  // Avatar renderer
```

### BuddyPress Hooks
```php
utehub2026_members_ajax_querystring($query_string, $object)  # Members AJAX filter
utehub2026_activity_ajax_querystring($query_string, $object) # Activity AJAX filter
utehub2026_member_screen_template()  # Single member template override
```

## Styling Guide

### CSS Custom Properties (`style.css:8-30`)

**Colors:**
| Variable | Value | Usage |
|----------|-------|-------|
| `--crimson` | `#cc0000` | Primary brand color |
| `--crimson-deep` | `#a30000` | Hover states |
| `--black` | `#0d0d0f` | Navigation background |
| `--ink` | `#14141a` | Primary text |
| `--canvas` | `#f4f4f6` | Page background |
| `--card` | `#ffffff` | Card backgrounds |

**Typography:**
- Display: `Oswald` (collegiate headers)
- Body: System fonts (`-apple-system`, `Segoe UI`, etc.)

### Layout Classes

| Class | Purpose |
|-------|---------|
| `.nav` | Primary navigation bar |
| `.uh-wrap` / `.page-wrap` | Content wrapper |
| `.feedhead` | Page headers with title + search |
| `.tabs` | Tab navigation (forum tabs, activity types) |
| `.rail` | Right sidebar container |
| `.forum-card` | Forum archive cards |

## JavaScript Architecture

All JavaScript is vanilla ES6+, loaded in footer (`true` parameter to `wp_enqueue_script`).

### Data Flow Pattern

```javascript
// 1. Configuration via window object (set by wp_localize_script)
window.UteHubMembersDirectory = {
    ajaxUrl: '/wp-admin/admin-ajax.php',
    membersUrl: '/members/'
};

// 2. Event listeners on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () { ... });

// 3. AJAX POST with cookie persistence
fetch(ajaxUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ action: 'members_filter', ... })
});

// 4. DOM update on success
list.innerHTML = html;
```

## Hooks and Filters

### Theme-Specific Actions

| Hook | Location | Purpose |
|------|----------|---------|
| `utehub2026_ensure_server_keys_exist` | `template_redirect:1` | Sanitize $_SERVER |
| `utehub2026_stop_heartbeat` | `init:1` | Disable WP heartbeat |
| `utehub2026_members_ajax_querystring` | `bp_ajax_querystring` | Members AJAX params |
| `utehub2026_activity_ajax_template_loader` | `bp_ajax_querystring` | Activity template |

### WordPress Filters Used

- `rewrite_rules_array:1000` - Prioritize page permalinks
- `show_admin_bar` - Hide admin bar
- `bp_email_use_wp_mail` - Use WP mail for BuddyPress emails

## Customization

### Adding Navigation Items

Edit `functions.php:859-937` in `utehub2026_get_default_nav_items()`:

```php
array(
    'label' => 'New Item',
    'url'   => home_url('/new-page/'),
    'icon'  => 'star',  // SVG icon name
)
```

### Adding Widget Areas

Register in `functions.php:650-694` (`utehub2026_register_sidebars()`):

```php
register_sidebar(array(
    'name'          => 'My Sidebar',
    'id'            => 'my-custom-sidebar',
    'before_widget' => '<div class="widget">',
    'after_widget'  => '</div>',
));
```

### Forum Section Rules

Edit `functions.php:1641-1649` (`utehub2026_get_forum_archive_section_key()`):

```php
if (in_array($slug, array('my-section'), true) || $index < 3) {
    return 'sports';  // or 'community'
}
```

## Browser Support

- Modern browsers (ES6+ support required)
- Mobile-first responsive design (breakpoint: 900px)

## Development Notes

- No build process - edit files directly
- CSS versioning via `filemtime()`
- JavaScript versioning inherits from CSS
- All AJAX requests use POST with form-encoded data
- Cookies persist user preferences for directories

# Architecture Overview

## System Design

UteHub2026 is a WordPress theme that extends bbPress (forums) and BuddyPress (social) with custom layouts, AJAX filtering, and client-side validation.

```
┌─────────────────────────────────────────────────────────────┐
│                      WordPress Core                         │
└─────────────────────────────────────────────────────────────┘
                            │
        ┌───────────────────┼───────────────────┐
        │                   │                   │
        ▼                   ▼                   ▼
┌───────────────┐  ┌───────────────┐  ┌───────────────┐
│   bbPress     │  │   BuddyPress  │  │    Theme      │
│   (Forums)    │  │  (Social)     │  │  (UteHub2026) │
└───────────────┘  └───────────────┘  └───────────────┘
        │                   │                   │
        └───────────────────┼───────────────────┘
                            ▼
              ┌───────────────────────────┐
              │   Custom Templates +      │
              │   AJAX Integration        │
              └───────────────────────────┘
```

## Data Flow

### Page Load Sequence

```
1. WordPress bootstrap
   ↓
2. Theme setup (utehub2026_setup)
   - Title tag, thumbnails, custom logo
   - Nav menus registered
   ↓
3. Asset enqueue (utehub2026_enqueue_assets)
   - style.css (versioned by filemtime)
   - nav.js (always loaded)
   - members-directory.js (if members directory)
   - activity-directory.js (if activity directory)
   - member-single.js (if single member page)
   ↓
4. Template selection
   - front-page.php → topics feed
   - page.php → forum layout OR content + rail
   - bbPress/BuddyPress overrides
   ↓
5. Render output
```

### AJAX Request Flow

```
1. User interaction (filter change, pagination click)
   ↓
2. JavaScript event handler
   - Get current scope/filter from DOM
   - Build POST body with action + params
   - Set cookies for persistence
   ↓
3. Fetch request to /wp-admin/admin-ajax.php
   - Content-Type: application/x-www-form-urlencoded
   - Credentials: same-origin
   ↓
4. WordPress AJAX handler (BuddyPress core)
   - Validates nonce/cookies
   - Runs query with filters
   - Renders template fragment
   ↓
5. JSON response { contents: "<html>...</html>" }
   ↓
6. JavaScript updates DOM
   - Replace container innerHTML
   - Update active tab state
   ↓
7. Remove loading state
```

## Component Architecture

### Navigation System

```
┌─────────────────────────────────────────┐
│              .nav                       │
│  ┌─────┐  ┌──────┐  ┌────────────────┐ │
│  │ ☰   │  │ Logo │  │  Menu Items    │ │
│  └─────┘  └──────┘  └────────────────┘ │
│                                         │
│  Mobile (<900px):                       │
│  - Hamburger toggle                     │
│  - Full-width dropdown menu             │
│  - Click to expand submenus             │
│                                         │
│  Desktop (≥900px):                      │
│  - Horizontal layout                    │
│  - Hover dropdowns                      │
└─────────────────────────────────────────┘
```

**State Management:**
- `.is-open` on nav container
- `.is-submenu-open` on menu items
- `aria-expanded` on toggles

### Forum Archive Layout

```
┌───────────────────────────────────────────────────────┐
│  Breadcrumb                                           │
├───────────────────────────────────────────────────────┤
│  The Boards              [Search]                     │
├───────────────────────────────────────────────────────┤
│                                                       │
│  ┌─────────────────────────────────────────────┐     │
│  │ Sports Section                              │     │
│  ├─────────────────────────────────────────────┤     │
│  │ ┌─────────┐ ┌─────────┐ ┌─────────┐       │     │
│  │ │ Icon    │ │ Icon    │ │ Icon    │       │     │
│  │ │ Title   │ │ Title   │ │ Title   │       │     │
│  │ │ Desc    │ │ Desc    │ │ Desc    │       │     │
│  │ │ Pills   │ │ Pills   │ │ Pills   │       │     │
│  │ │ Stats   │ │ Stats   │ │ Stats   │       │     │
│  │ │ Last    │ │ Last    │ │ Last    │       │     │
│  │ └─────────┘ └─────────┘ └─────────┘       │     │
│  └─────────────────────────────────────────────┘     │
│                                                       │
│  ┌─────────────────────────────────────────────┐     │
│  │ Community Section                           │     │
│  └─────────────────────────────────────────────┘     │
│                                                       │
├──────────┬────────────────────────────────────────────┤
│ Content  │           Right Rail Sidebar               │
│ Area     │   - Login widget                          │
│          │   - Who's online                          │
│          │   - Recent activity                       │
└──────────┴────────────────────────────────────────────┘
```

### Members Directory

```
┌───────────────────────────────────────────────────────┐
│  Members                                              │
├───────────────────────────────────────────────────────┤
│  [All] [Active] [Popular] [New]                       │
├───────────────────────────────────────────────────────┤
│  Sort: [Alphabetical ▼]    [Search...]               │
├───────────────────────────────────────────────────────┤
│                                                       │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐             │
│  │ Avatar   │ │ Avatar   │ │ Avatar   │             │
│  │ Username │ │ Username │ │ Username │             │
│  │ Location │ │ Location │ │ Location │             │
│  └──────────┘ └──────────┘ └──────────┘             │
│                                                       │
│  [1] [2] [3] ... [Next]                              │
└───────────────────────────────────────────────────────┘
```

**AJAX Triggers:**
- Tab click → Change scope (all/active/popular/new)
- Sort change → Change filter (alphabetical, etc.)
- Pagination → Load next page

### Single Member Profile

```
┌───────────────────────────────────────────────────────┐
│  [Cover Image]                                        │
├───────────────────────────────────────────────────────┤
│  ┌─────────┐                                          │
│  │ Avatar  │  Username                                │
│  └─────────┘  Location                               │
│               Stats (posts, friends, etc.)            │
├───────────────────────────────────────────────────────┤
│  [Home] [Profile] [Friends] [Messages] [Notifications]│
├───────────────────────────────────────────────────────┤
│                                                       │
│  Content based on active tab                          │
│                                                       │
└───────────────────────────────────────────────────────┘
```

## Integration Points

### BuddyPress Hooks Used

| Hook | Purpose | File Location |
|------|---------|---------------|
| `bp_ajax_querystring` | Modify AJAX params | functions.php:158, 328 |
| `bp_buddypress_cover_image_settings` | Cover image config | functions.php:236 |
| `bp_member_header_tabs` | Member nav tabs | functions.php:248 |

### bbPress Integration

**Template Overrides:** All in `bbpress/` directory

**Key Customizations:**
- Forum archive sections (Sports/Community)
- Topic form client-side validation
- Search result layouts

### WordPress Core Hooks

| Hook | Priority | Purpose |
|------|----------|---------|
| `after_setup_theme` | 10 | Theme features |
| `init` | 1 | Disable heartbeat, register BP features |
| `template_redirect` | 1 | Server key sanitization |
| `wp_enqueue_scripts` | 10 | Asset loading |
| `customize_register` | 10 | Customizer options |

## File Dependencies

### JavaScript Dependencies

```
nav.js
├── No external dependencies
└── Uses: document, fetch, localStorage (cookies)

members-directory.js
├── window.UteHubMembersDirectory (localized data)
└── Uses: fetch, cookies

activity-directory.js
├── window.UteHubActivityDirectory (localized data)
└── Uses: fetch, cookies, JSON.parse

member-single.js
├── window.UteHubMemberSingle (localized data)
└── Uses: fetch, cookies
```

### PHP Function Dependencies

```
utehub2026_render_topics_feed()
├── utehub2026_get_recent_topics_tabs()
├── utehub2026_get_recent_topics_query_args()
└── utehub2026_render_right_rail()

utehub2026_render_right_rail()
└── utehub2026_get_right_rail_sidebar_id()

utehub2026_get_forum_archive_sections()
├── utehub2026_get_forums_archive_top_level_forums()
├── utehub2026_get_forum_archive_section_key()
└── utehub2026_get_forum_archive_card_data()
```

## State Management

### Client-Side (Cookies)

| Cookie | Scope | Purpose |
|--------|-------|---------|
| `bp-members-scope` | Members directory | Last selected tab |
| `bp-members-filter` | Members directory | Last sort order |
| `bp-activity-scope` | Activity directory | Last activity type |
| `bp-activity-filter` | Activity directory | Last filter |

### Server-Side (Options)

Stored in WordPress options table:

- `utehub2026_home_welcome_rotation` - Rotating welcome messages
- Forum ID options for tab filtering
- Customizer settings

## Performance Considerations

### Optimizations Applied

1. **Script Loading:** All scripts loaded async in footer (`true` param)
2. **Versioning:** CSS/JS versioned by `filemtime()` for cache busting
3. **Query Optimization:** 
   - `no_found_rows: true` where pagination not needed
   - `update_post_meta_cache: false` for read-only queries
4. **Heartbeat Disabled:** Reduces admin-ajax polling

### Potential Bottlenecks

1. **Forum Archive Card Generation:** Calls multiple bbPress functions per forum
2. **Avatar Rendering:** Multiple avatar calls per page
3. **AJAX on Every Filter Change:** No debouncing/throttling

## Security Considerations

### Implemented

- Nonce verification (via WordPress/BP core)
- `sanitize_text_field()` for user input
- `esc_html()`, `esc_url()` for output escaping
- SameSite=Lax cookies

### Notes

- Client-side validation is supplementary only
- Server-side validation always runs
- AJAX handlers use BP's built-in security

## Extension Points

### Adding Features

1. **New Navigation Item:** Edit `utehub2026_get_default_nav_items()`
2. **New Sidebar:** Register in `utehub2026_register_sidebars()`
3. **New Template:** Follow WordPress template hierarchy
4. **New AJAX Handler:** Use BP's `bp_ajax_querystring` filter

### Customization Without Forking

1. **CSS:** Add custom styles after theme stylesheet
2. **Filters:** Use WordPress/BP action/filter hooks
3. **Widgets:** Add to existing sidebars via WP admin
4. **Menu:** Edit via Appearance → Menus

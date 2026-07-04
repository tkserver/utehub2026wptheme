# Functions Reference

Complete list of theme functions in `functions.php` with line numbers and descriptions.

## Theme Setup

| Function | Line | Hook | Description |
|----------|------|------|-------------|
| `utehub2026_setup()` | 20 | `after_setup_theme` | Title tag, thumbnails, custom logo, nav menus |
| `utehub2026_enqueue_assets()` | 68 | `wp_enqueue_scripts` | Loads CSS/JS with filemtime versioning |
| `utehub2026_customize_register()` | 394 | `customize_register` | Theme Customizer options |

## Performance Hooks

| Function | Line | Hook | Description |
|----------|------|------|-------------|
| `utehub2026_ensure_server_keys_exist()` | 10 | `template_redirect:1` | Sanitize $_SERVER['HTTP_HOST'] |
| `utehub2026_stop_heartbeat()` | 16 | `init:1` | Deregister WP heartbeat script |
| `utehub2026_keep_verbose_page_rules_first()` | 42 | `rewrite_rules_array:1000` | Prioritize page permalinks |

## BuddyPress Integration

### Members

| Function | Line | Hook | Description |
|----------|------|------|-------------|
| `utehub2026_members_ajax_querystring()` | 158 | `bp_ajax_querystring` | Modify members AJAX query params |
| `utehub2026_members_ajax_template_loader()` | 192 | `bp_ajax_querystring` | Load custom members template |
| `utehub2026_register_buddypress_cover_image_feature()` | 215 | `init` | Register cover image feature |
| `utehub2026_buddypress_cover_image_settings()` | 236 | Filter | Customize cover image settings |
| `utehub2026_cover_photo_nav_label()` | 248 | Filter | Rename cover photo upload nav |
| `utehub2026_hide_member_groups_nav()` | 253 | Template filter | Remove groups nav from members |
| `utehub2026_member_screen_template()` | 264 | Template redirect | Override single member template |

### Activity

| Function | Line | Hook | Description |
|----------|------|------|-------------|
| `utehub2026_activity_ajax_querystring()` | 328 | `bp_ajax_querystring` | Modify activity AJAX params |
| `utehub2026_get_activity_ajax_feed_url()` | 356 | Helper | Get activity feed URL by scope |
| `utehub2026_activity_ajax_template_loader()` | 371 | `bp_ajax_querystring` | Load custom activity template |

## Customizer

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_customize_register()` | 394 | Registers all customizer controls |

**Customizer Sections:**

1. **Header Options** (line 405)
   - Logo upload
   - Site title/tagline display

2. **Welcome Message Rotation** (line 478)
   - Multi-line text for rotating home messages

3. **Home Topics Heading** (line 529)
   - Custom heading for topics feed on front page

### Sanitization Functions

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_sanitize_multiline_text()` | 581 | Multi-line text sanitization |
| `utehub2026_sanitize_home_welcome_rotation()` | 598 | Welcome message rotation sanitization |

## Helper Functions

### Home Page Content

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_is_member_groups_tab_enabled()` | 569 | Check if groups tab is enabled |
| `utehub2026_is_member_whats_new_enabled()` | 573 | Check if "What's New" is enabled |
| `utehub2026_is_favorite_topic_enabled()` | 577 | Check if favorite topics feature enabled |
| `utehub2026_get_home_welcome_messages()` | 605 | Parse welcome messages from option |
| `utehub2026_get_home_welcome_message()` | 622 | Get random welcome message |
| `utehub2026_get_home_topics_heading()` | 644 | Get custom topics heading |

### Sidebar Management

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_register_sidebars()` | 650 | Register all widget areas |
| `utehub2026_get_right_rail_sidebar_id()` | 746 | Get sidebar ID by context |

**Registered Sidebars:**

1. `utehub-footer-left` - Footer left column
2. `utehub-footer-center` - Footer center (app download)
3. `utehub-footer-right` - Footer right column
4. `utehub-forum-archive-sidebar` - Forum archive right rail
5. `utehub-page-right-rail-sidebar` - Page right rail

### Template Renderers

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_render_content_page_layout()` | 746 | Render page with right rail |
| `utehub2026_render_right_rail()` | 1760 | Render right sidebar |
| `utehub2026_render_topics_feed()` | 1778 | Render topics feed with tabs |
| `utehub2026_render_recently_active()` | 1568 | Render recent authors avatars |
| `utehub2026_render_nav_item()` | 988 | Render single nav item |
| `utehub2026_render_primary_nav()` | 1037 | Render full navigation bar |
| `utehub2026_render_avatar()` | 1080 | Render user avatar with fallbacks |

### Query Helpers

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_get_recent_topics_query_args()` | 1512 | Build topics query args |
| `utehub2026_get_forum_descendant_ids()` | 1478 | Get forum + subforum IDs |

### bbPress Helpers

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_is_bbpress()` | 783 | Check if bbPress active |
| `utehub2026_content_has_forum_layout()` | 787 | Check if page has forum layout |

### Utility Functions

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_get_svg()` | 807 | Get SVG icon by name |
| `utehub2026_get_brand_url()` | 834 | Get brand/logo URL |
| `utehub2026_get_nav_icon_name()` | 846 | Map nav label to icon name |
| `utehub2026_get_initials()` | 1056 | Generate initials from name |
| `utehub2026_get_color_class()` | 1073 | Get consistent color class |

### Navigation Data

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_get_default_nav_items()` | 859 | Default navigation structure |
| `utehub2026_get_primary_nav_items()` | 938 | Filtered nav items for display |

**Default Nav Items:**

1. Home (`/`)
2. Activity (`/activity/`)
3. Members (`/members/`)
4. Forums (`/forums/`)
5. Schedules (custom)
6. Pick'em (custom)
7. Predict (custom)
8. Chat (custom)
9. Contact (mailto)

### Forum Archive Helpers

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_get_forums_archive_top_level_forums()` | 1621 | Get top-level forums |
| `utehub2026_get_forum_archive_section_key()` | 1641 | Determine forum section |
| `utehub2026_get_forum_archive_sections()` | 1651 | Group forums by section |
| `utehub2026_get_forum_archive_icon()` | 1677 | Get icon for forum slug |
| `utehub2026_get_forum_archive_subforums()` | 1690 | Get subforums of a forum |
| `utehub2026_get_forum_archive_card_data()` | 1725 | Build forum card data array |
| `utehub2026_get_forum_archive_stats()` | 1748 | Get total stats (topics, posts, members) |

### Topics Feed Tabs

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_get_recent_topics_tabs()` | 1459 | Get topics filter tabs |
| `utehub2026_get_forum_descendant_ids()` | 1478 | Get forum tree IDs |

**Default Tabs:**

- All (no filter)
- Sports (forum ID from option)
- Community (forum ID from option)
- Hot (by reply count)

### Relative Time Helper

| Function | Line | Description |
|----------|------|-------------|
| `utehub2026_get_relative_time()` | 1495 | Format timestamp as relative time |

## AJAX Handlers

The theme registers these AJAX actions via BuddyPress hooks:

### Members Filter

**Action:** `members_filter` (registered by BP)

**Expected POST data:**
```php
action       => 'members_filter'
object       => 'members'
filter       => sorting option (alphabetical, active, popular, new)
scope        => directory scope (all, active, popular, new)
page         => pagination page number
search_terms => search query
cookie       => current cookies for scope/filter persistence
```

### Activity Widget Filter

**Action:** `activity_widget_filter` (registered by BP)

**Expected POST data:**
```php
action       => 'activity_widget_filter'
scope        => activity type (all, forums, members, etc.)
filter       => sort order (newest, oldest, popular)
search_terms => search query
cookie       => current cookies for scope/filter persistence
```

## Template Override Files

### bbPress Templates (`bbpress/`)

| File | Purpose |
|------|---------|
| `content-archive-forum.php` | Forum archive with sections |
| `content-single-forum.php` | Single forum view |
| `content-single-topic-lead.php` | Topic header in single view |
| `content-single-topic.php` | Topic posts in single view |
| `form-topic.php` | New topic form |
| `form-reply.php` | Reply form |
| `loop-single-reply.php` | Single reply item |
| `loop-single-topic.php` | Single topic item |
| `loop-topics.php` | Topics loop item |
| `loop-search-*.php` | Search results templates |
| `pagination-*.php` | Pagination templates |

### BuddyPress Templates (`members/`, `activity/`)

| File | Purpose |
|------|---------|
| `members/index.php` | Members directory page |
| `members/members-loop.php` | Members loop HTML |
| `members/single/*.php` | Single member profile tabs |
| `activity/index.php` | Activity directory page |

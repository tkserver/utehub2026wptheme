# Templates Guide

## Template Hierarchy

### Core Theme Files

| File | Purpose | Lines |
|------|---------|-------|
| `header.php` | HTML head + opening nav | 21 |
| `footer.php` | Closing content + widgets | 27 |
| `front-page.php` | Front page (topics feed) | 14 |
| `page.php` | Default page template | 18 |
| `index.php` | Fallback index | 5 |

### Template Functions

All templates use these helper functions:

```php
// Navigation
utehub2026_render_primary_nav()

// Content layouts
utehub2026_render_content_page_layout(array $args)
utehub2026_render_topics_feed(string $base_url)
utehub2026_render_right_rail(string $context, int $topic_id = 0)

// Utilities
utehub2026_get_svg(string $icon)
utehub2026_render_avatar(int $user_id, int $size, array $args)
```

---

## Page Templates

### Front Page (`front-page.php`)

**Purpose:** Display topics feed with tabs

**Template Flow:**
```php
get_header();
while (have_posts()) : the_post();
    utehub2026_render_topics_feed(get_permalink());
endwhile;
get_footer();
```

**Output:** Topics feed with "All/Sports/Community/Hot" tabs + right rail sidebar

---

### Default Page (`page.php`)

**Purpose:** Handle both forum pages and regular content pages

**Logic:**
```php
if (utehub2026_content_has_forum_layout(get_the_ID())) {
    // Forum-style page → topics feed
    utehub2026_render_topics_feed(get_permalink());
} else {
    // Regular page → content + right rail
    utehub2026_render_content_page_layout(array('context' => 'page'));
}
```

**Forum Layout Detection:** Checks if page ID matches forum-related options

---

## bbPress Templates

### Location: `bbpress/` directory

Overrides bbPress default templates with custom UteHub styling.

### Archive Templates

#### `content-archive-forum.php` (102 lines)

**Purpose:** Main forums archive page (`/forums/`)

**Structure:**
```php
<div class="uh-wrap forums-archive-page">
    <section>
        <!-- Breadcrumb -->
        <div class="crumb forum-crumb"><?php bbp_breadcrumb(); ?></div>
        
        <!-- Header with title + search -->
        <div class="forums-head">
            <div class="ttl"><span class="eye">The Boards</span><h1>...</h1></div>
            <form class="search">...</form>
        </div>
        
        <!-- Forum sections (Sports/Community) -->
        <?php foreach ($sections as $section): ?>
            <div class="forum-section">
                <h2><?php echo $section['label']; ?></h2>
                <div class="forum-cards">
                    <?php foreach ($section['items'] as $forum): ?>
                        <?php $card = utehub2026_get_forum_archive_card_data($forum); ?>
                        <article class="forum-card">...</article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
    
    <!-- Right rail sidebar -->
    <?php utehub2026_render_right_rail('archive'); ?>
</div>
```

**Key Functions Used:**
- `utehub2026_get_forum_archive_sections()` - Group forums by section
- `utehub2026_get_forum_archive_card_data()` - Build card data array
- `utehub2026_render_right_rail('archive')` - Render sidebar

---

### Single Templates

#### `content-single-forum.php` (3,970 bytes)

**Purpose:** Display single forum with its topics

**Features:**
- Forum header with description
- Topic list with pagination
- Right rail sidebar

#### `content-single-topic-lead.php` (2,275 bytes)

**Purpose:** Topic header in single topic view

**Displays:**
- Topic title
- Author info + avatar
- Timestamp
- Reply count

#### `content-single-topic.php` (3,780 bytes)

**Purpose:** Individual replies within a topic

**Features:**
- Reply content with formatting
- Author sidebar (avatar, name, join date)
- Quote/reply actions

---

### Form Templates

#### `form-topic.php` (7,751 bytes)

**Purpose:** New topic creation form

**Fields:**
- Forum selector (`#bbp_forum_id`)
- Topic title (`#bbp_topic_title`)
- Topic content (`#bbp_topic_content` - TinyMCE)
- Submit button

**Client-Side Validation:** JavaScript in `nav.js:68-243` validates before submit

#### `form-reply.php` (2,311 bytes)

**Purpose:** Reply submission form

**Fields:**
- Reply content (TinyMCE)
- Submit button

---

### Loop Templates

| File | Purpose |
|------|---------|
| `loop-single-topic.php` | Single topic in list view |
| `loop-topics.php` | Topics loop item |
| `loop-single-reply.php` | Single reply in thread |
| `loop-search-*.php` | Search result items |

---

## BuddyPress Templates

### Members Directory (`members/index.php`)

**Purpose:** Members listing with tabs + filtering

**Structure:**
```php
<div class="uh-wrap members-directory">
    <!-- Header -->
    <div class="feedhead">
        <div class="ttl"><h1>Members</h1></div>
    </div>
    
    <!-- Tabs (All/Active/Popular/New) -->
    <ul class="members-tabs">...</ul>
    
    <!-- Filter + Search -->
    <select id="members-order-by">...</select>
    <input type="text" id="members_search">
    
    <!-- Members loop -->
    <div id="members-dir-list" class="loading">
        <?php include 'members-loop.php'; ?>
    </div>
</div>
```

**AJAX:** `assets/members-directory.js` handles filter changes

---

### Single Member (`members/single/*.php`)

#### Profile Tabs Structure

```
members/single/
├── profile.php       # Profile tab wrapper
├── home.php          # Home tab (profile summary)
├── friends.php       # Friends list with pagination
├── messages.php      # Messages tab
├── notifications.php # Notifications with bulk delete
└── settings.php      # Settings tab
```

#### `home.php` (3,066 bytes)

**Purpose:** User profile summary

**Displays:**
- Cover image header
- Profile info (name, avatar, stats)
- Recent activity preview

#### `friends.php` (2,428 bytes)

**Purpose:** Friends list with sorting + pagination

**AJAX:** `assets/member-single.js:68-155` handles refresh

#### `notifications.php` (1,087 bytes)

**Purpose:** Notification management

**Features:**
- Checkbox selection
- Bulk delete via dropdown
- Confirmation dialog

---

### Members Loop (`members/members-loop.php`)

**Purpose:** Render individual member cards in directory

**Structure:**
```html
<div class="item-list">
    <div class="item">
        <div class="item-avatar">
            <a href="#"><img src="..." /></a>
        </div>
        
        <div class="item-title">
            <a href="#">Username</a>
        </div>
        
        <div class="item-options">...</div>
    </div>
</div>
```

---

## Activity Templates

### Activity Directory (`activity/index.php`)

**Purpose:** Activity stream with type filtering

**Structure:**
```php
<div class="uh-wrap activity-directory">
    <!-- Header -->
    <div class="feedhead">
        <h1>Activity</h1>
    </div>
    
    <!-- Type tabs (All/Forums/Members/etc.) -->
    <ul class="activity-type-tabs">...</ul>
    
    <!-- Filter + Search -->
    <select id="activity-filter-by">...</select>
    <form id="search-activity-form">...</form>
    
    <!-- Activity widget -->
    <div data-bp-list="activity" class="loading">...</div>
</div>
```

**AJAX:** `assets/activity-directory.js` handles tab/filter changes

---

## Template Helper Functions

### Layout Renderers

#### `utehub2026_render_content_page_layout()` (line 746)

**Purpose:** Standard page layout with right rail

**Output:**
```php
<div class="uh-wrap">
    <section>
        <?php the_content(); ?>
    </section>
    <?php utehub2026_render_right_rail('page'); ?>
</div>
```

#### `utehub2026_render_topics_feed()` (line 1778)

**Purpose:** Topics feed with tabs and pagination

**Features:**
- Tab navigation (All/Sports/Community/Hot)
- Search integration
- Pagination links
- Topic count display

**Query Args:** Built by `utehub2026_get_recent_topics_query_args()` (line 1512)

#### `utehub2026_render_right_rail()` (line 1760)

**Purpose:** Render context-aware sidebar

**Contexts:**
- `'archive'` → Forum archive sidebar
- `'page'` → Page sidebar  
- Single topic → Dynamic based on forum hierarchy

---

### Data Builders

#### `utehub2026_get_forum_archive_card_data()` (line 1725)

**Returns:**
```php
array(
    'id'             => int,
    'title'          => string,
    'url'            => string,
    'description'    => string,
    'topics'         => int,
    'posts'          => int,
    'subforums'      => array(),
    'last_active_id' => int,
    'last_author_id' => int,
    'last_author'    => string,
    'last_when'      => string (relative time),
    'last_url'       => string,
    'icon'           => string (svg name),
)
```

#### `utehub2026_get_recent_topics_tabs()` (line 1459)

**Returns:**
```php
array(
    array('key' => '', 'label' => 'All Topics', ...),
    array('key' => 'sports', 'label' => 'Sports', 'forum' => int, ...),
    array('key' => 'community', 'label' => 'Community', 'forum' => int, ...),
    array('key' => 'hot', 'label' => 'Hot', ...),
)
```

---

## Widget Areas (Sidebars)

### Registered Sidebars

| ID | Location | Context |
|----|----------|---------|
| `utehub-footer-left` | Footer left column | Always |
| `utehub-footer-center` | Footer center | Always |
| `utehub-footer-right` | Footer right column | Always |
| `utehub-forum-archive-sidebar` | Right rail | Forum archive |
| `utehub-page-right-rail-sidebar` | Right rail | Pages |

### Sidebar Selection Logic

```php
function utehub2026_get_right_rail_sidebar_id($context = '') {
    switch ($context) {
        case 'archive':
            return is_active_sidebar('utehub-forum-archive-sidebar') 
                ? 'utehub-forum-archive-sidebar' 
                : 'utehub-page-right-rail-sidebar';
        
        case 'page':
            return 'utehub-page-right-rail-sidebar';
        
        // Single topic logic...
    }
}
```

---

## Template Conditions

### Forum Layout Detection

```php
function utehub2026_content_has_forum_layout($post = null) {
    // Check if page ID matches forum-related options
    // Returns true for forums, topics pages
}
```

### BuddyPress Conditions

Used in `functions.php` to conditionally load scripts:

```php
if (function_exists('bp_is_members_directory') && bp_is_members_directory()) {
    wp_enqueue_script('utehub2026-members-directory');
}

if (function_exists('bp_is_activity_directory') && bp_is_activity_directory()) {
    wp_enqueue_script('utehub2026-activity-directory');
}

if (function_exists('bp_is_user') && bp_is_user()) {
    wp_enqueue_script('utehub2026-member-single');
}
```

---

## Customization

### Adding New Page Templates

1. Create file: `page-customname.php`
2. Add template header:
   ```php
   <?php
   /**
    * Template Name: Custom Name
    */
   ?>
   ```
3. Use WordPress page attributes to assign template

### Overriding bbPress Templates

Theme already overrides these in `bbpress/` directory. To modify:
1. Edit corresponding file in `bbpress/`
2. Follow existing structure
3. Use theme helper functions where possible

### Adding New Sidebars

Register in `utehub2026_register_sidebars()` (line 650):

```php
register_sidebar(array(
    'name'          => __('My Sidebar', 'utehub2026'),
    'id'            => 'my-custom-sidebar',
    'before_widget' => '<div class="widget">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3 class="widget-title">',
    'after_title'   => '</h3>',
));
```

Then use in templates:
```php
if (is_active_sidebar('my-custom-sidebar')) {
    dynamic_sidebar('my-custom-sidebar');
}
```

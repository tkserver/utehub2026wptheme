# JavaScript Documentation

All JavaScript is vanilla ES6+, loaded in footer without build process.

## File Overview

| File | Size | Purpose |
|------|------|---------|
| `nav.js` | 243 lines | Navigation + topic form validation |
| `members-directory.js` | 79 lines | Members directory AJAX filtering |
| `activity-directory.js` | 118 lines | Activity stream AJAX filtering |
| `member-single.js` | 155 lines | Member profile friends + notifications |

---

## nav.js

### Responsibilities

1. Mobile navigation toggle
2. Submenu state management
3. bbPress topic form client-side validation

### Event Listeners

#### Click Handler (Global)

```javascript
document.addEventListener('click', function (event) {
    // Handles .reply-link[data-login-required]
    // Shows alert if user not logged in
});
```

#### DOMContentLoaded - Navigation

**Mobile Menu Toggle:**
- Toggles `.nav.is-open` class
- Sets `aria-expanded` on toggle button

**Submenu Toggles:**
- Clicking `.submenu-toggle` toggles `.is-submenu-open`
- Only works when viewport ≤ 900px

**Parent Links:**
- Clicking parent with children prevents navigation on mobile
- Opens submenu instead of navigating

#### DOMContentLoaded - Topic Form Validation

**Target:** `#bbp_topic_submit` in `.bbp-topic-form form#new-post`

**Validated Fields:**
1. Topic title (`#bbp_topic_title`)
2. Topic content (`#bbp_topic_content`) - checks TinyMCE if active
3. Forum selection (`#bbp_forum_id`) - must not be '0' or '-1'

**Validation Behavior:**

| Event | Action |
|-------|--------|
| `input` on fields | Real-time validation, no notice shown initially |
| `change` on fields | Real-time validation |
| `submit` | Full validation + show errors if any |

**Error Display:**

```html
<div class="bbp-template-notice bbp-topic-form-client-notice error" role="alert">
    <ul>
        <li>Title is required.</li>
        <li>Content is required.</li>
    </ul>
</div>
```

**Submit Button States:**

| Class | State |
|-------|-------|
| `.is-form-valid` | All fields valid |
| `.is-form-invalid` | At least one field invalid |
| `.is-submitting` | Form being submitted |

**ARIA Attributes:**

- Invalid fields get `aria-invalid="true"` and `.is-invalid` class
- Submit button gets `aria-disabled="true"` when invalid

---

## members-directory.js

### Dependencies

```javascript
window.UteHubMembersDirectory = {
    ajaxUrl: '/wp-admin/admin-ajax.php',
    membersUrl: '/members/'
};
```

Set via `wp_localize_script()` in `functions.php:106-111`.

### DOM Requirements

```html
<select id="members-order-by">...</select>
<div id="members-dir-list" class="loading">...</div>
<ul class="members-tabs">
    <li id="members-all" class="selected"><a href="#">All</a></li>
    <li id="members-active"><a href="#">Active</a></li>
    ...
</ul>
```

### Function Flow

```
User changes sort dropdown
         ↓
getSelectedScope() → Get current tab (all/active/popular/new)
         ↓
getSearchTerms() → Get search input value
         ↓
setCookie('bp-members-scope', scope)
setCookie('bp-members-filter', orderBy)
         ↓
POST to ajaxUrl with:
    - action: 'members_filter'
    - object: 'members'
    - filter: sort order
    - scope: directory scope
    - page: 1 (always reset to first page on filter change)
    - search_terms: search query
    - cookie: current cookies
         ↓
On success: Replace #members-dir-list innerHTML
On error: Redirect to members URL with search param
```

### Cookie Persistence

| Cookie | Value | Purpose |
|--------|-------|---------|
| `bp-members-scope` | all/active/popular/new | Last selected tab |
| `bp-members-filter` | alphabetical/... | Last sort order |

---

## activity-directory.js

### Dependencies

```javascript
window.UteHubActivityDirectory = {
    ajaxUrl: '/wp-admin/admin-ajax.php',
    activityUrl: current page URL
};
```

Auto-populated from `data-ajax-url` and `data-activity-url` attributes on `.activity-directory-shell`, or falls back to defaults.

### DOM Requirements

```html
<div class="activity-directory-shell" data-ajax-url="/wp-admin/admin-ajax.php">
    <ul class="activity-type-tabs">
        <li id="activity-all" class="selected"><a href="#">All</a></li>
        <li id="activity-forums"><a href="#">Forums</a></li>
        ...
    </ul>
    
    <select id="activity-filter-by">
        <option value="newest">Newest</option>
        <option value="oldest">Oldest</option>
    </select>
    
    <form id="search-activity-form">
        <input type="text" name="activity_search" id="activity_search">
    </form>
    
    <div data-bp-list="activity" class="loading">...</div>
</div>
```

### Function Flow

```
User clicks tab / changes filter / submits search
         ↓
getSelectedScope() → Get current activity type tab
         ↓
getSearchTerms() → Get search input value
         ↓
setCookie('bp-activity-scope', scope)
setCookie('bp-activity-filter', filter)
         ↓
POST to ajaxUrl with:
    - action: 'activity_widget_filter'
    - scope: activity type
    - filter: sort order
    - search_terms: search query
    - cookie: current cookies
         ↓
On success: 
    - Parse JSON response
    - Replace [data-bp-list="activity"] innerHTML with contents
    - Update selected tab visually
On error: Redirect to activity URL
```

### Event Handling

**Click on Activity Type Tabs:**
- Uses capturing phase (`true` parameter)
- Prevents default and stops propagation
- Calls `refreshActivity()` with new scope

**Filter Change:**
- Also uses capturing phase
- Prevents default/propagation
- Calls `refreshActivity()` with current scope + new filter

**Search Submit:**
- Prevents default form submission
- Calls `refreshActivity()` with current scope/filter

### Response Format

```json
{
    "contents": "<div>...filtered activity HTML...</div>",
    "position": { ... }  // Optional scroll position data
}
```

---

## member-single.js

### Dependencies

```javascript
window.UteHubMemberSingle = {
    ajaxUrl: '/wp-admin/admin-ajax.php'
};
```

Set via `wp_localize_script()` in `functions.php`.

### Features

#### 1. Notifications Bulk Delete

**Target:** Form `#notifications-bulk-management`

**Functionality:**
- Checkbox to select all notifications
- Indeterminate state when some selected
- Dropdown with "delete-all" option
- Confirmation dialog before deletion

**DOM Structure:**
```html
<form id="notifications-bulk-management">
    <input type="checkbox" id="select-all-notifications">
    
    <label><input type="checkbox" class="notification-check">...</label>
    <label><input type="checkbox" class="notification-check">...</label>
    
    <select id="notification-select">
        <option value="">Bulk actions</option>
        <option value="delete-all">Delete all</option>
    </select>
    
    <button type="submit" id="notification-bulk-manage">Apply</button>
</form>
```

**Sync Logic:**
- Select all checkbox reflects state of individual checkboxes
- Indeterminate when some (not all) selected
- Bulk button disabled until option chosen

#### 2. Friends List Pagination + Filtering

**Dependencies:**
- `<select id="members-friends">` - Sort dropdown
- `<div id="members-friends-list" data-user-id="123">` - Friends list container

**Function Flow:**

```
User changes sort or clicks pagination
         ↓
setCookie('bp-members-scope', 'friends')
setCookie('bp-members-filter', orderBy)
         ↓
POST to ajaxUrl with:
    - action: 'members_filter'
    - object: 'members'
    - filter: sort order (alphabetical, etc.)
    - scope: 'friends'
    - page: calculated from pagination click
    - user_id: from data-user-id attribute
    - cookie: current cookies
         ↓
On success: Replace #members-friends-list innerHTML
On error: Reload page
```

**Pagination Detection:**

| Class | Action |
|-------|--------|
| `.next` | page + 1 |
| `.prev` | page - 1 (min 1) |
| Number text | Parse number from link text |

---

## AJAX Error Handling

All AJAX requests follow this pattern:

```javascript
fetch(url, { ... })
    .then(response => {
        if (!response.ok) throw new Error('...');
        return response[json|text]();
    })
    .then(data => {
        // Success handler
    })
    .catch(() => {
        // Fallback: redirect or reload
    })
    .finally(() => {
        element.classList.remove('loading');
    });
```

---

## Browser Compatibility

- ES6+ required (arrow functions, template literals, fetch API)
- No IE support
- Mobile-first (breakpoint: 900px for nav)

---

## Adding New AJAX Features

1. **Enqueue script** in `utehub2026_enqueue_assets()` with condition check
2. **Localize data** via `wp_localize_script()`:
   ```php
   wp_localize_script(
       'handle',
       'WindowObjectName',
       array(
           'ajaxUrl' => admin_url('admin-ajax.php'),
           'otherData' => get_option('some_option'),
       )
   );
   ```
3. **Create script** following existing patterns:
   - IIFE wrapper for scope isolation
   - Early return if dependencies missing
   - Cookie persistence for user preferences
   - Loading state management
   - Error fallback (redirect or reload)

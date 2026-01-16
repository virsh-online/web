# Admin Area Implementation - Testing Guide

## What Was Implemented

### 1. Authentication System
- **AuthMiddleware** (`app/src/Http/Middleware/AuthMiddleware.php`): 
  - Uses juzdy/core middleware capabilities
  - Protects all admin routes except login/logout
  - Stores intended URL for redirect after login
  - Session-based authentication

### 2. Admin Handlers
All handlers in `app/src/Http/Handler/Admin/`:

- **Login**: Handles authentication (default credentials: admin/admin)
- **Logout**: Clears admin session
- **Index**: Lists all poems in card layout with actions
- **Edit**: Add/edit form with all fields and file upload
- **Delete**: Deletes a poem
- **Toggle**: Enables/disables a poem

### 3. Mobile-Friendly UI
- **Layout** (`app/layout/admin/layout.phtml`):
  - Responsive design with burger menu for mobile
  - Left sidebar menu (desktop) / hamburger menu (mobile)
  - Modern card-based design
  - CSS included directly in layout for easy deployment

### 4. Templates
All templates in `app/layout/admin/`:

- **login.phtml**: Login form
- **index.phtml**: Card-based poems list with edit/delete/disable buttons
- **edit.phtml**: Add/edit form with:
  - Title (text input)
  - Virsh/poem text (textarea)
  - YouTube URL (text input with format hint)
  - Illustration upload (file input with preview)
  - Enabled/disabled checkbox

### 5. Features
- ✅ Card layout instead of table (mobile-friendly)
- ✅ Burger menu for mobile navigation
- ✅ File upload for illustrations (saved to `pub/uploads/`)
- ✅ Enable/disable poems (published/draft)
- ✅ Visual indicators for disabled poems (grayed out)
- ✅ Confirmation dialogs for delete/toggle actions
- ✅ Only enabled poems shown on landing page

## How to Test

### Prerequisites
1. Run database migration:
   ```bash
   mysql -u root -p virsh_online < etc/db/add_enabled_field.sql
   ```

2. Ensure uploads directory is writable:
   ```bash
   chmod 755 pub/uploads/
   ```

### Testing Steps

#### 1. Test Login
- Navigate to: `http://yoursite.com/?q=admin/login`
- Enter credentials: username `admin`, password `admin`
- Should redirect to admin index

#### 2. Test Admin Index (Poems List)
- Navigate to: `http://yoursite.com/?q=admin/index`
- Should see:
  - Header with burger menu icon (mobile)
  - Left sidebar with "Поєзія" and "Вихід" links
  - Button "Додати новий вірш"
  - Cards showing all poems with:
    - Title
    - Excerpt of poem text
    - Illustration (if exists)
    - Three action buttons: Edit, Enable/Disable, Delete

#### 3. Test Add Poem
- Click "Додати новий вірш" button
- Fill in form:
  - Title: "Test Poem"
  - Virsh: "Test poem text..."
  - YouTube: "https://www.youtube.com/embed/VIDEO_ID" (optional)
  - Illustration: Upload an image file
  - Enabled: Check/uncheck
- Click "Зберегти" button
- Should redirect to index showing new poem

#### 4. Test Edit Poem
- Click "Редагувати" button on any poem card
- Should see form pre-filled with poem data
- Current illustration shown if exists
- Modify any field
- Click "Зберегти"
- Should redirect to index with updated poem

#### 5. Test Toggle Enable/Disable
- Click "Вимкнути" button on an enabled poem
- Confirmation dialog should appear
- After confirmation, poem card should become grayed out
- Button should change to "Увімкнути"
- Click again to re-enable
- Poem card should return to normal appearance

#### 6. Test Delete
- Click "Видалити" button on any poem
- Confirmation dialog should appear
- After confirmation, poem should be removed from list

#### 7. Test Landing Page Filter
- Navigate to landing page: `http://yoursite.com/`
- Only enabled poems should be displayed
- Disabled poems should NOT appear

#### 8. Test Mobile Responsiveness
- Resize browser to mobile width (< 768px)
- Burger menu icon should appear in header
- Sidebar should be hidden
- Click burger menu - sidebar should slide in from left
- Click outside sidebar (overlay) - sidebar should close

#### 9. Test Authentication
- Navigate to: `http://yoursite.com/?q=admin/edit`
- If not logged in, should redirect to login page
- After login, should redirect back to edit page

#### 10. Test Logout
- Click "Вихід" link in sidebar
- Should redirect to login page
- Try to access `/?q=admin/index` - should redirect to login

### Expected File Uploads
- Uploaded images saved to: `pub/uploads/poem_[timestamp]_[uniqid].[ext]`
- Path stored in database: `uploads/poem_[timestamp]_[uniqid].[ext]`
- Displayed on landing page and admin with `/` prefix

## Browser Testing Checklist
- [ ] Desktop Chrome/Edge
- [ ] Desktop Firefox
- [ ] Desktop Safari
- [ ] Mobile Chrome (Android)
- [ ] Mobile Safari (iOS)

## Security Notes
- Default credentials are `admin/admin` - **CHANGE IN PRODUCTION**
- Authentication uses session-based storage
- Middleware protects all admin routes
- File uploads limited to image types (accept="image/*")
- Uploaded files stored outside of versioned directories

## Troubleshooting

### "Not authenticated" redirect loop
- Check that session is started in `pub/index.php` (should have `session_start()`)
- Verify middleware is not applied to login route

### File upload not working
- Check directory permissions: `chmod 755 pub/uploads/`
- Verify form has `enctype="multipart/form-data"`
- Check PHP upload limits in php.ini

### Styles not loading
- Styles are embedded in `app/layout/admin/layout.phtml`
- No external CSS files required

### Poems not filtered on landing page
- Verify database has `enabled` column
- Check that migration was run
- Ensure `Index` handler filters by `enabled = 1`

# Admin Area Setup

## Database Migration

Before using the admin area, run the following SQL migration to add the `enabled` field to the `virsh` table:

```sql
ALTER TABLE virsh ADD COLUMN IF NOT EXISTS enabled TINYINT(1) DEFAULT 1 NOT NULL;
UPDATE virsh SET enabled = 1 WHERE enabled IS NULL;
CREATE INDEX IF NOT EXISTS idx_virsh_enabled ON virsh(enabled);
```

Or run the SQL file:
```bash
mysql -u username -p database_name < etc/db/add_enabled_field.sql
```

## Default Login Credentials

- Username: `admin`
- Password: `admin`

**IMPORTANT**: Change these credentials in production!

## Admin Routes

- `/admin/login` - Login page
- `/admin/index` - Poems list (cards view)
- `/admin/edit` - Add new poem
- `/admin/edit?id=X` - Edit existing poem
- `/admin/delete?id=X` - Delete poem
- `/admin/toggle?id=X` - Enable/disable poem
- `/admin/logout` - Logout

## Features

- ✅ Mobile-friendly responsive design
- ✅ Burger menu for mobile navigation
- ✅ Card-based layout for poems list
- ✅ Upload and manage illustrations
- ✅ YouTube video integration
- ✅ Enable/disable poems (published/draft)
- ✅ Authentication using juzdy/core middleware

## Permissions

Ensure the `pub/uploads/` directory is writable by the web server:

```bash
chmod 755 pub/uploads/
```

## Notes

- Uploaded files are stored in `pub/uploads/`
- Uploaded files are excluded from git (see `pub/uploads/.gitignore`)
- Only enabled poems are shown on the landing page
- All admin routes require authentication (except login/logout)

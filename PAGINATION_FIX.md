# Pagination Fix Documentation

## Overview
This document explains the pagination fix implemented for the Virsh Online project.

## Problem Statement
Pagination was not working in either the admin area (`/admin/index`) or the frontend (`/`).

## Root Cause Analysis

### The Issue
The pagination failure was caused by a parameter binding mismatch in the `Collection` class from the `juzdy/core` package. Here's what was happening:

1. **Page Setup:** When setting up pagination, the handlers call:
   ```php
   $collection->setPageSize(20);
   $collection->setPage(1);
   ```

2. **Parameter Pollution:** The `setPage()` method internally calls `getPages()`, which calls `count()` to determine the total number of pages.

3. **SQL Generation:** Before this, `getLimitSql()` is called (from `getSelect()`), which adds pagination parameters to `$this->params`:
   ```php
   $this->params['lim'] = $this->pageSize;  // e.g., 20
   $this->params['off'] = $offset;          // e.g., 0
   ```

4. **Count Query:** The `count()` method calls `getSelect(true)` with `skipLimit=true` to get a SQL query WITHOUT the LIMIT clause:
   ```sql
   SELECT COUNT(*) FROM (SELECT main.* FROM virsh AS main WHERE ...) AS count_query
   -- Note: No LIMIT clause here!
   ```

5. **Binding Failure:** However, the `count()` method then tries to bind ALL parameters including `lim` and `off`:
   ```php
   foreach ($this->params as $key => $value) {
       $stmt->bindValue(":$key", $value, ...);
   }
   ```
   
   This attempts to bind `:lim` and `:off` parameters that don't exist in the SQL query, causing the query to fail or behave incorrectly.

## Solution

Modified the `Collection::count()` method to skip binding the pagination parameters (`lim` and `off`) since they're not used in the count query:

```php
public function count(): int
{
    // ... existing code ...
    
    $sql = $this->getSelect(true);  // Skip LIMIT
    $sql = "SELECT COUNT(*) FROM ($sql) AS count_query";
    $stmt = $this->db->prepare($sql);
    
    // FIX: Skip binding lim/off params when counting
    foreach ($this->params as $key => $value) {
        if ($key === 'lim' || $key === 'off') {
            continue;  // <-- This is the fix
        }
        $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    
    $stmt->execute();
    return $this->count = (int) $stmt->fetchColumn();
}
```

## Implementation Method

Since `juzdy/core` is a vendor package, the fix was implemented using Composer patches:

1. **Created patch file:** `patches/fix-pagination-count.patch`
2. **Installed patch plugin:** `cweagans/composer-patches`
3. **Configured composer.json:** Added patch configuration to `extra.patches`
4. **Applied patch:** The patch is automatically applied when running `composer install`

## Impact

This fix resolves pagination in:
- ✅ Admin area (`/admin/index`) - List of poems with pagination controls
- ✅ Frontend (`/`) - Public poem list with pagination controls

Both areas use the same `Collection` class and were affected by the same bug.

## Testing

To verify the fix works:

1. Navigate to admin area with >20 poems
2. Verify pagination controls appear
3. Click "Next" or "Previous" buttons
4. Verify correct page loads with correct poems
5. Repeat for frontend

## Files Changed

- `composer.json` - Added patch configuration and dev dependency
- `composer.lock` - Updated with patch plugin
- `patches/fix-pagination-count.patch` - The actual fix
- `patches.lock.json` - Tracks applied patches
- `patches/README.md` - Documentation

## Future Considerations

Consider submitting this patch upstream to the `juzdy/core` repository so it can be fixed in the core package itself.

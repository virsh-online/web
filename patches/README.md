# Composer Patches

This directory contains patches for vendor packages that are automatically applied by the `cweagans/composer-patches` plugin.

## Current Patches

### fix-pagination-count.patch

**Package:** `juzdy/core`  
**Description:** Fix pagination count query parameter binding

**Problem:**  
The `Collection::getLimitSql()` method adds pagination parameters (`lim` and `off`) to `$this->params` as a side effect. When `count()` is called with `getSelect(true)` (which skips the LIMIT clause), these parameters are still in `$this->params`, causing PDO to try to bind parameters that don't exist in the SQL query.

**Solution:**  
Modified the `Collection::count()` method to skip binding the `lim` and `off` parameters when executing the count query, since the LIMIT clause is not included in count queries.

**Impact:**  
This fix resolves pagination issues in both the admin area and frontend where pagination was not working correctly.

## How Patches Work

1. Patches are stored in the `patches/` directory
2. They are configured in `composer.json` under `extra.patches`
3. When you run `composer install` or `composer update`, patches are automatically applied
4. A `patches.lock.json` file tracks which patches have been applied

## Applying Patches Manually

If you need to reapply patches:

```bash
composer reinstall juzdy/core
```

Or to reinstall all packages with patches:

```bash
rm -rf vendor/
composer install
```

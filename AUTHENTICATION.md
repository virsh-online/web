# Secure Authentication Implementation

## Overview
This document describes the secure authentication implementation that replaces hardcoded credentials with database-backed authentication.

## Changes Made

### 1. AdminUser Model (`app/src/Model/AdminUser.php`)
- New model for the `admin_user` table
- Methods:
  - `findByEmail(string $email)`: Find user by email address
  - `isEnabled()`: Check if user account is enabled
  - `verifyPassword(string $password)`: Verify password using `password_verify()`
  - `setPassword(string $password)`: Set password with secure hashing using `password_hash()`

### 2. Updated Login Handler (`app/src/Http/Handler/Admin/Login.php`)
- Removed hardcoded credentials
- Authenticates against `admin_user` table
- Uses `password_verify()` for secure password checking
- Validates that user exists and is enabled before allowing login
- Stores actual user ID in session
- Generic error messages to prevent user enumeration

### 3. Enhanced AuthMiddleware (`app/src/Http/Middleware/AuthMiddleware.php`)
- Verifies user ID in session corresponds to an active, enabled user in database
- Automatically logs out users if their account is disabled or deleted
- Handles database errors gracefully

### 4. CLI Script (`bin/create-admin`)
- Standalone PHP script to create admin users
- Interactive prompts or command-line arguments
- Password validation (minimum 8 characters)
- Email validation
- Prevents duplicate email addresses
- Uses `password_hash()` with `PASSWORD_DEFAULT` for secure storage

## Security Features

### Password Security
- Uses PHP's `password_hash()` with `PASSWORD_DEFAULT` algorithm (currently bcrypt)
- Passwords are never stored in plaintext
- Uses `password_verify()` for constant-time comparison
- Minimum password length of 8 characters enforced in CLI

### Account Security
- Disabled accounts cannot log in
- Generic error messages prevent user enumeration attacks
- Session automatically invalidated if account is disabled or deleted
- Database errors don't expose sensitive information

### Input Validation
- Email validation using `filter_var()` with `FILTER_VALIDATE_EMAIL`
- Empty input checks
- Password confirmation in CLI script

## Usage

### Creating an Admin User

#### Method 1: Interactive prompts
```bash
php bin/create-admin
```
The script will prompt for:
- Email address
- Password (hidden input, with confirmation)
- Full name

#### Method 2: Command-line arguments
```bash
php bin/create-admin admin@example.com securepassword123 "Admin Name"
```

### First-Time Setup
1. Ensure the database schema is loaded:
   ```bash
   mysql -u root -p database_name < etc/db/schema.sql
   ```

2. Create your first admin user:
   ```bash
   php bin/create-admin admin@yourdomain.com your-secure-password "Your Name"
   ```

3. Log in at: `https://yoursite.com/?q=admin/login`

### Database Schema
The `admin_user` table (see `etc/db/schema.sql`):
- `id`: Primary key
- `email`: User's email address (used for login)
- `password`: Hashed password (using bcrypt)
- `fullname`: User's full name
- `enabled`: Account status (1 = enabled, 0 = disabled)

## Migration from Hardcoded Credentials

If you were using the old hardcoded credentials:
1. Create new admin users using `bin/create-admin`
2. Remove any `ADMIN_USERNAME` and `ADMIN_PASSWORD` environment variables
3. Test login with database credentials
4. Disable old methods of authentication

## Security Recommendations

1. **Strong Passwords**: Enforce strong password policies (consider adding complexity requirements)
2. **HTTPS**: Always use HTTPS in production to protect credentials in transit
3. **Rate Limiting**: Consider adding rate limiting to prevent brute force attacks
4. **2FA**: Consider implementing two-factor authentication for additional security
5. **Password Reset**: Implement a secure password reset mechanism
6. **Account Lockout**: Consider temporary account lockout after multiple failed attempts
7. **Audit Logging**: Log authentication attempts for security monitoring

## Testing

The implementation has been designed with security best practices:
- No plaintext passwords in code or logs
- Generic error messages
- Proper password hashing
- Account status validation
- Session security

To test:
1. Create a test admin user
2. Try logging in with correct credentials
3. Try logging in with incorrect password
4. Try disabling the user in database and verify login fails
5. Verify session is maintained across requests

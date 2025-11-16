# Security Fix: Admin Authorization & URL Parameter Tampering

## Issue Reported
Non-super-admin users could modify URL parameters (e.g., `/admin/users/2`) to view or modify other users' information that wasn't assigned to them, creating a significant security vulnerability.

## Root Cause
- The `UserController` had no authorization checks in the `show()` method
- The `AdminController` had no authorization checks in the `show()`, `edit()`, and `update()` methods
- While `UsersManagementController` and `AdminsManagementController` had proper checks, the primary controllers used by routes did not

## Security Fixes Implemented

### 1. UserController Authorization (`app/Http/Controllers/Admin/UserController.php`)

#### Added Authorization Method
```php
private function canManageUser(User $user): bool
{
    $admin = Auth::guard('admin')->user();
    // Super admin can manage any user, or if user is assigned to this admin
    return $admin->isSuperAdmin() || $user->assigned_admin_id === $admin->id;
}
```

#### Protected Methods
- **`index()`**: Non-super-admin users now only see their assigned users (filtered by `assigned_admin_id`)
- **`show(User $user)`**: Added authorization check - returns 403 Forbidden if user is not authorized
- **`toggleStatus(User $user)`**: Added authorization check - prevents status modification of unassigned users
- **`toggleForceLoss(User $user)`**: Added authorization check - prevents modification of unassigned users

### 2. AdminController Authorization (`app/Http/Controllers/Admin/AdminController.php`)

#### Added Authorization Method
```php
private function canManageAdmin(Admin $admin): bool
{
    $currentAdmin = Auth::guard('admin')->user();
    // Only super admin can manage other admins, or an admin can view/edit their own profile
    return $currentAdmin->isSuperAdmin() || $currentAdmin->id === $admin->id;
}
```

#### Protected Methods
- **`create()`**: Only super-admin can create new admins
- **`store()`**: Only super-admin can create new admins
- **`show(Admin $admin)`**: Added authorization check - non-super-admin can only view their own profile
- **`edit(Admin $admin)`**: Added authorization check - non-super-admin can only edit their own profile
- **`update(Admin $admin)`**: Added authorization check - non-super-admin can only update their own profile

### 3. Verified Existing Security

The following controllers already had proper authorization checks and required no changes:
- ✅ `DepositAdminController`: `updateStatus()` and `destroy()` check user assignment
- ✅ `WithdrawalAdminController`: Similar checks implemented
- ✅ `AdminsManagementController`: All methods properly secured
- ✅ `UsersManagementController`: All methods properly secured

## Security Model

### Role-Based Access Control (RBAC)
- **Super Admin**: Can access and modify any user, admin, deposit, or withdrawal
- **Regular Admin**: Can only access and modify resources assigned to them
  - Users: Only those with `assigned_admin_id` = their ID
  - Deposits/Withdrawals: Only for their assigned users

### Authorization Flow
1. Request comes in with a resource ID (e.g., `/admin/users/5`)
2. Route model binding fetches the resource
3. Controller's authorization method checks:
   - Is the current admin a super admin? → Allow
   - Does the resource belong to this admin? → Allow
   - Otherwise → Deny with 403 Forbidden

## Testing Scenarios

The following unauthorized attempts now return **403 Forbidden**:

- Non-super-admin tries to view user not assigned to them
- Non-super-admin tries to modify another admin's profile
- Non-super-admin tries to toggle status of unassigned user
- Non-super-admin tries to create new admin accounts
- Regular admin tries to edit other admins' wallets

## Example: Blocked Malicious Request

**Before Fix:**
```
GET /admin/users/2 (logged in as regular admin, user 2 assigned to different admin)
→ 200 OK - Shows user info (VULNERABLE)
```

**After Fix:**
```
GET /admin/users/2 (logged in as regular admin, user 2 assigned to different admin)
→ 403 Forbidden - "You are not authorized to view this user."
```

## HTTP Status Codes Used

- **200 OK**: Authorization passed, resource displayed/modified
- **403 Forbidden**: User lacks authorization for this resource
- **404 Not Found**: Resource doesn't exist

## Recommendations

1. ✅ **All authorization checks implemented**
2. **Consider adding audit logging** for failed authorization attempts
3. **Review API endpoints** to ensure similar checks are in place
4. **Use Laravel Policies** for cleaner authorization code (future improvement)
5. **Add logging** for security events in the application's audit trail

## Files Modified

1. `app/Http/Controllers/Admin/UserController.php`
   - Added `canManageUser()` method
   - Added authorization checks to `show()`, `toggleStatus()`, `toggleForceLoss()`
   - Added filtering to `index()` method

2. `app/Http/Controllers/Admin/AdminController.php`
   - Added `canManageAdmin()` method
   - Added authorization checks to `show()`, `edit()`, `update()`, `create()`, `store()`

3. `tests/Feature/AdminAuthorizationTest.php` (NEW)
   - Comprehensive test suite for authorization validation

## Deployment Notes

- No database migrations required
- No breaking changes to existing functionality
- Super-admin privileges unchanged
- Regular admins can still access all their assigned users/resources
- Clear 403 error messages for debugging

## Verification Steps

After deployment, verify:
1. Super-admin can access all users and admins
2. Regular admins can only access their assigned users
3. Regular admins get 403 error when trying to access unassigned users
4. Admin edit pages prevent cross-admin modifications
5. Check application logs for any 403 errors from legitimate users

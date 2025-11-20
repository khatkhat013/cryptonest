# Admin Approval System - Implementation Guide

## Overview

The Admin Approval System prevents newly registered admins from editing critical records until the Site Owner explicitly approves them. This adds a crucial security layer to the platform.

## Features

### 1. **Approval Status Tracking**
- ✅ **Approved**: Admin can edit all records (users, deposits, withdrawals, trading, AI arbitrage, wallet addresses)
- ⏳ **Pending**: New admin awaiting Site Owner approval; cannot edit records
- ❌ **Rejected**: Admin rejected by Site Owner; cannot edit records

### 2. **Database Schema**
Added 4 columns to the `admins` table:
```sql
- is_approved (boolean, default: false)
- rejection_reason (text, nullable)
- approved_at (timestamp, nullable)
- approved_by (unsignedBigInteger, nullable)
```

### 3. **Middleware Protection**
All sensitive edit/update/delete operations require admin approval:
- User management (toggle status, force loss)
- User assignments
- Deposits management (status updates, deletions)
- Withdrawals management (status updates, deletions)
- Trading operations (updates, deletions)
- AI Arbitrage plans (updates, deletions)

### 4. **Site Owner Dashboard**
Located at: `/admin/admin-approval`
- View all admins with approval status
- View pending admins (requires action)
- Approve pending admins
- Reject admins with reason
- Revoke previously approved admins

## File Structure

```
app/
├── Http/
│   ├── Controllers/Admin/
│   │   └── AdminApprovalController.php    (NEW) - Manages approvals
│   └── Middleware/
│       └── AdminApprovalRequired.php      (NEW) - Checks approval status
├── Models/
│   └── Admin.php                          (UPDATED) - Added approval methods
└── Console/
    └── Commands/
        └── ApproveExistingAdmins.php      (NEW) - CLI command for bulk approval

database/
└── migrations/
    └── 2025_11_20_202848_add_approval_to_admins_table.php (NEW)

resources/
└── views/
    └── admin/
        ├── admin_approval.blade.php       (NEW) - Approval management index
        ├── admin_approval_show.blade.php  (NEW) - Approval detail & actions
        └── dashboard.blade.php            (UPDATED) - Added approval status alert

routes/
└── web.php                                (UPDATED) - Added approval routes & middleware
```

## How It Works

### Registration Flow
1. New admin registers via `/admin/register`
2. Admin account created with `is_approved = false` (pending)
3. Admin dashboard shows "Awaiting Approval" alert
4. Admin cannot access edit/update/delete operations

### Approval Flow (Site Owner)
1. Site Owner logs in (must have `role_id = 2` super admin)
2. Navigates to `/admin/admin-approval`
3. Reviews list of pending admins
4. Clicks "View" to see admin details
5. Can:
   - **Approve**: Sets `is_approved = true`, `approved_at = now()`, `approved_by = site_owner_id`
   - **Reject**: Sets `is_approved = false`, `rejection_reason = reason`
   - **Revoke**: Removes approval from previously approved admin

### Access Control
- **GET /admin/admin-approval**: Site Owner only
- **GET /admin/deposits**: Requires `auth:admin` + `admin-approval` middleware
- **POST /admin/deposits/{id}/status**: Requires `auth:admin` + `admin-approval` middleware
- **DELETE /admin/deposits/{id}**: Requires `auth:admin` + `admin-approval` middleware

## Usage

### Approving Existing Admins (Before System)
For admins registered before this feature was implemented:

```bash
php artisan admins:approve-existing

# Or, approve only super admins:
php artisan admins:approve-existing --super-admin-only
```

### Admin Model Methods
```php
$admin = Admin::find(1);

// Check approval status
$admin->isApproved();      // Returns boolean - true if approved
$admin->isPending();       // Returns boolean - true if pending
$admin->isRejected();      // Returns boolean - true if rejected

// Get approval info
$admin->approved_at;       // DateTime when approved
$admin->approved_by;       // ID of Site Owner who approved
$admin->rejection_reason;  // Reason if rejected
```

### Middleware Logic
The `AdminApprovalRequired` middleware:
1. Checks if admin is authenticated
2. Calls `$admin->isApproved()`
3. If not approved:
   - **For JSON requests**: Returns 403 with status and reason
   - **For web requests**: Redirects to dashboard with warning
4. Approved admins proceed normally

## Frontend Notifications

### Dashboard Alert
- **Pending**: Yellow warning - "Awaiting Site Owner approval"
- **Rejected**: Red danger - Shows rejection reason
- **Approved**: Green success - (No alert, operations enabled)
- **Site Owner**: Blue info - Link to approval management

### Operation Blocked Message
When unapproved admin tries to edit:
```
403 Forbidden
Admin Status: Pending
Message: This admin account is pending Site Owner approval and cannot perform this action.
```

## Security Considerations

1. **Only Site Owner Can Approve**: `isSuperAdmin()` check on all approval endpoints
2. **Audit Trail**: `approved_by` field tracks who approved each admin
3. **Rejection Reason**: Required field when rejecting admins
4. **Cannot Bypass**: Middleware checks every sensitive operation
5. **Revocation Support**: Site Owner can revoke approval at any time

## Testing

### Manual Testing
1. Create new admin account (should be pending)
2. Try accessing `/admin/deposits` - should redirect with alert
3. Log in as Site Owner (ID 1)
4. Go to `/admin/admin-approval`
5. Approve the new admin
6. New admin can now access `/admin/deposits`

### Testing Command
```bash
# Test the approval check
php artisan tinker
>>> $admin = Admin::find(2);
>>> $admin->isApproved();
=> false
>>> $admin->isPending();
=> true
```

## Configuration

### Add to Admin Navigation
Update `resources/views/layouts/admin.blade.php` to include:
```blade
@if(Auth::guard('admin')->user()?->isSuperAdmin())
    <li class="nav-item">
        <a href="{{ route('admin.admin_approval.index') }}" class="nav-link">
            <i class="bi bi-shield-check"></i> Admin Approvals
        </a>
    </li>
@endif
```

## Related Files Already Updated

### routes/web.php
- Added `admin-approval` middleware to all sensitive routes
- All edit/update/delete operations now require approval
- Middleware aliased as `'admin-approval'`

### bootstrap/app.php
- Registered `AdminApprovalRequired` middleware as `admin-approval`

### app/Models/Admin.php
- Added fillable: `is_approved`, `rejection_reason`, `approved_at`, `approved_by`
- Added casts: `is_approved` => boolean, `approved_at` => datetime
- Added methods: `isApproved()`, `isPending()`, `isRejected()`

## Troubleshooting

### Admin Still Can't Edit After Approval
1. Clear application cache: `php artisan cache:clear`
2. Clear config cache: `php artisan config:clear`
3. Verify `is_approved = 1` in database
4. Restart server: `php artisan serve`

### Routes Not Found
1. Verify routes registered: `php artisan route:list | grep admin-approval`
2. Check `routes/web.php` for correct route definitions
3. Verify controller exists: `app/Http/Controllers/Admin/AdminApprovalController.php`

### Middleware Not Triggering
1. Check middleware registered: `php artisan route:list | grep admin-approval`
2. Verify admin authenticated: `Auth::guard('admin')->check()`
3. Check admin approval status: `$admin->isApproved()`

## Next Steps

1. **Email Notifications** (Optional):
   - Send email to admin when approved/rejected
   - Send email to Site Owner when new admin registers

2. **Activity Logging** (Already Compatible):
   - The controller includes `activity()` calls for audit trail
   - Integrate with Laravel Activity Log package if desired

3. **Bulk Actions** (Optional):
   - Batch approve multiple pending admins
   - Batch reject multiple admins

4. **Admin Profile Fields** (Optional):
   - Collect admin's company/department on registration
   - Show in approval dashboard for context

## Rollback

To remove the approval system:

```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Delete files
rm app/Http/Middleware/AdminApprovalRequired.php
rm app/Http/Controllers/Admin/AdminApprovalController.php
rm app/Console/Commands/ApproveExistingAdmins.php

# Remove routes from routes/web.php
# Remove middleware registration from bootstrap/app.php
# Remove approval methods from Admin model
```

## Support

For issues or questions about the admin approval system:
1. Check `storage/logs/laravel.log` for errors
2. Verify database migrations ran: `php artisan migrate:status`
3. Test middleware directly: `php artisan tinker` and check `Auth::guard('admin')->user()->isApproved()`

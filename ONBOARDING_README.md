# Bizadmin - Organization Onboarding Guide (cPanel)

## Prerequisites

- cPanel access to `https://node26.myfcloudau.com:2083`
- Super Admin login credentials
- Organization details (name, email, phone, password, systems, locations)
- Reference SQL file: `application/third_party/bizadmincom_db.sql`

---

## Step-by-Step Instructions

### Step 1: Create Database in cPanel

1. Log in to **cPanel** → **MySQL Databases**
2. Under **Create New Database**, enter the database name  
   - Use the format: `bizadmincom_orgname` (e.g., `bizadmincom_newcafe`)
   - Click **Create Database**
3. Under **MySQL Users → Add New User**, create a user  
   - Username: `bizadmincom_orgname` (must match cPanel prefix rules)
   - Password: generate a strong password and **save it somewhere safe**
   - Click **Create User**
4. Under **Add User to Database**  
   - Select the user you just created
   - Select the database you just created
   - Click **Add** → check **ALL PRIVILEGES** → **Make Changes**

> **Note down these 3 values — you'll need them in Step 3:**
> - Database name (e.g., `bizadmincom_newcafe`)
> - Database username (e.g., `bizadmincom_newcafe`)
> - Database password

---

### Step 2: Import Database Schema via phpMyAdmin

1. In cPanel, go to **phpMyAdmin**
2. Select the **newly created database** from the left sidebar
3. Click the **Import** tab
4. Choose the reference SQL file: `application/third_party/bizadmincom_db.sql`
5. Click **Go** to import

> **IMPORTANT:**
> - All tables will be created (empty) with the correct structure
> - **`SUPPLIERS_orderStatusList`** must be imported **with its data** (the SQL file includes this) — do NOT delete its rows
> - All other tables can be empty — the system will seed the required data automatically
> - If you're importing from another org's dump, that's fine — the system will clean and replace seed data with correct tenant-specific values

---

### Step 3: Add Organization in Super Admin

1. Log in to **Super Admin** (`bizadmin.com.au/superadmin_login`)
2. Go to **Organization** → click **Add New**
3. Fill in the form:

| Field | Default Value | What to enter |
|---|---|---|
| **Organization Name** | _(empty)_ | Full business name (e.g., "New Cafe Melbourne") |
| **Unique Identifier** | _(empty)_ | Short URL slug — becomes the login URL: `bizadmin.com.au/newcafe` |
| **Email** | `kaushika@aaria.com.au` | Admin email for this organization |
| **Phone** | `123456789` | Contact phone number |
| **Password** | `1800@Bendigo123!` | Admin login password (hashed with Argon2) |
| **Status** | `Enable` | Enable or Disable |
| **Upload Logo** | _(none)_ | Organization logo (optional, jpg/png/gif, max 5MB) |
| **Address** | _(empty)_ | Business address (optional) |
| **Location Access** | _(none)_ | Check all locations this organization should access |
| **System Access** | HR, Supplier, Clean, Temp, Compliance, Cash, DMS | Check/uncheck as needed |
| **SMTP Host** | `smtp.office365.com` | Pre-filled, change if needed |
| **SMTP Username** | `info@bizadmin.com.au` | Pre-filled, change if needed |
| **SMTP Password** | `1800@Organic123!` | Pre-filled, change if needed |
| **SMTP Port** | `25` | Pre-filled, change if needed |
| **Protocol** | `SMTP` | Pre-selected |
| **Database Name** | _(empty)_ | The database name from Step 1 |
| **Database Username** | _(empty)_ | The database username from Step 1 |
| **Database Password** | _(empty)_ | The database password from Step 1 |

4. Click **Save**

---

### Step 4: Review Setup Status

After clicking Save, you will be redirected to the **Setup Status** page. The system performs these checks and actions:

| Step | What happens |
|---|---|
| **DB Connection Check** | Verifies the database exists and credentials work |
| **Schema Check** | Verifies all required tables exist (must be imported in Step 2) |
| **Seed Data** | Cleans any old imported data, then inserts fresh: |
| | - 5 default roles: Admin(1), Manager(2), Staff(3), Employee(4), Timesheet(5) — all with `location_id=0` |
| | - 1 admin user with `company = orzId` matching `organization_list_id` in Super Admin |
| | - Role mapping: `group_id=1` (Admin) assigned to the admin user |
| | - Location assignments: all selected locations assigned to admin |
| | - Backup SMTP: `info@bizadmin.com.au`, `location_id=9999`, `system_id=9999` |
| **Config Files** | Tenant database config appended to `application/config/database.php` AND `External/application/config/database.php` |
| **Upload Folders** | `uploaded_files/{org_identifier}/` created with system subfolders (both main and External) |
| **Verification** | Cross-checks all seed data and reports PASS/FAIL for each table |

**If any step fails**, the status page shows the exact error. Fix the issue and use the **"Re-run Setup"** button on the organization listing page.

---

### Step 5: Post-Setup (Organization Admin Tasks)

These tasks should be done by the **organization admin after their first login**:

1. **Configure Notification Times**  
   - Go to each system → notification settings
   - Set notification time for each location and each system
   - Without this, cron job notifications will not work

2. **Configure Location-Specific SMTP** (optional)  
   - If the org wants different email settings per location/system
   - Go to Admin → SMTP settings for each location

3. **Create Staff Users**  
   - Admin can create Manager, Staff, Employee users from HR

---

## Re-run Setup / Fix Failed Onboarding

If setup fails or you need to re-seed data:

1. Go to **Organization** listing page
2. Click the **"Re-run Setup"** button (yellow) next to the organization
3. This will re-verify the DB, clean old data, and re-seed everything fresh
4. Safe to run multiple times — it truncates and re-inserts

---

## Seed Data Cross-Check Reference

After setup, verify these tables in phpMyAdmin:

| Table | Expected Records | Key Fields to Check |
|---|---|---|
| `Global_users` | 1 (admin) | `company` = orzId from `organization_list`, `username` = tenant_identifier, `role_id` = 1 |
| `Global_roles` | 5 | IDs 1-5: Admin, Manager, Staff, Employee, Timesheet. All `location_id = 0` |
| `Global_userid_to_roles` | 1 | `user_id` = admin user ID, `group_id` = 1 |
| `Global_users_to_location` | N (one per location) | All `location_id` values must match those assigned in Super Admin ORZ edit page |
| `Global_SmtpSettings` | 1 (backup) | `smtp_username` = `info@bizadmin.com.au`, `location_id` = 9999, `system_id` = 9999 |
| `SUPPLIERS_orderStatusList` | Multiple | Must have data — imported with the SQL file, NOT auto-generated |

---

## Troubleshooting

### "Cannot connect to database" error on setup
- Verify the database exists in cPanel
- Verify the cPanel user is assigned to the database with ALL PRIVILEGES
- Check DB name, username, and password are entered correctly (no browser autofill issues)

### "Required tables not found" error on setup
- You need to import `bizadmincom_db.sql` via phpMyAdmin **before** adding the org
- Make sure you selected the correct database before importing

### "SUPPLIERS_orderStatusList is empty" warning
- Re-import the SQL file — it includes the order status seed data
- Do NOT truncate this table after import

### User cannot log in to the new organization
- Check `Global_users` → `active` must be `1`
- Check `Global_users` → `company` must match the `organization_list_id` in Super Admin
- Check `Global_userid_to_roles` → must have `group_id = 1` for the admin user
- Verify `database.php` configs (both `application/` and `External/`) have the tenant entry

### Uploaded files not saving
- Verify `uploaded_files/{org_identifier}/` folder exists with correct permissions (755)
- Check both `uploaded_files/` and `External/uploaded_files/` directories

### Config file not updated
- If automated config update failed (file permissions), manually add to **both** files:
  - `application/config/database.php`
  - `External/application/config/database.php`
- Use the same format as existing tenant entries in those files

---

## Delete Organization

1. Go to **Organization** listing page
2. Click the red **Delete** button next to the organization
3. Enter the **PIN** (preset: `1802`) in the modal
4. Confirm deletion — this will permanently:
   - Drop the tenant database
   - Remove config entries from both `database.php` files
   - Delete `uploaded_files/{tenant}/` folders (both main and External)
   - Remove the organization record from Super Admin

> **Forgot PIN?** Click "Forgot PIN?" in the modal — it will email the PIN to the registered email.

---

## Important Reminders

- **Do NOT change system IDs** — HR(104), Supplier(101), Cash(102), Compliance(107), Temp(109), Clean(110), DMS(111), Catering(112), Shifts(113) are referenced in code
- **Do NOT change role IDs** — IDs 1-5 are hardcoded: Admin(1), Manager(2), Staff(3), Employee(4), Timesheet(5)
- **Do NOT manually insert** into `Global_roles` — the system creates all 5 roles automatically
- **Do NOT truncate** `SUPPLIERS_orderStatusList` — it must retain its imported data
- The backup SMTP (`info@bizadmin.com.au`, `location_id=9999`) is used when no location/system-specific SMTP is configured
- The `company` field in `Global_users` **must match** the `organization_list_id` in the Super Admin database

# Bizadmin - Organization Onboarding Guide (cPanel)

## Prerequisites

- cPanel access to `https://node26.myfcloudau.com:2083`
- Super Admin login credentials
- Organization details (name, email, phone, password, systems, locations)

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

> **Note down these 3 values — you'll need them in Step 2:**
> - Database name (e.g., `bizadmincom_newcafe`)
> - Database username (e.g., `bizadmincom_newcafe`)
> - Database password

---

### Step 2: Create Organization in Super Admin

1. Log in to **Super Admin** (`bizadmin.com.au/superadmin_login`)
2. Go to **Organization** → click **Add**
3. Fill in the form:

| Field | What to enter |
|---|---|
| **Organization Name** | Full business name (e.g., "New Cafe Melbourne") |
| **Organization Unique Identifier** | Short URL slug — this becomes the login URL: `bizadmin.com.au/newcafe` |
| **Email** | Admin email for this organization |
| **Phone** | Contact phone number |
| **Password** | Admin login password (will be hashed with Argon2) |
| **Status** | Set to **Enable** |
| **Upload Logo** | Organization logo (optional, jpg/png/gif, max 5MB) |
| **Address** | Business address (optional) |
| **Location Access** | Check all locations this organization should access |
| **System Access** | Check all systems this organization should use (HR, Cash, Supplier, etc.) |
| **Email/SMTP Details** | Mail protocol, host, port, username, password (optional — a backup SMTP is auto-configured) |
| **Database Name** | The database name from Step 1 |
| **Database Username** | The database username from Step 1 |
| **Database Password** | The database password from Step 1 |

4. Click **Save**

---

### Step 3: Review Setup Status

After clicking Save, you will be redirected to the **Setup Status** page. This page shows what the system did automatically:

| Automated Step | What happens |
|---|---|
| **Import Schema** | The reference SQL structure is imported into the new database (all tables created) |
| **Seed Data** | 5 default roles created (Admin, Manager, Staff, Employee, Timesheet) |
| | Admin user created with the credentials you entered |
| | Admin is assigned to all selected locations |
| | Backup SMTP settings inserted (location_id=9999) |
| | Order statuses seeded into `SUPPLIERS_orderStatusList` |
| **Config Files** | Tenant database config appended to `application/config/database.php` |
| | Tenant database config appended to `External/application/config/database.php` |
| **Upload Folders** | `uploaded_files/{org_identifier}/` created with system subfolders |
| | Same structure created in `External/uploaded_files/` |
| **Verification** | All of the above is automatically verified and reported as PASS/FAIL |

**If any step shows an error**, the status page will tell you exactly what went wrong and what needs manual attention.

---

### Step 4: Post-Setup (Organization Admin Tasks)

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

## Troubleshooting

### "Database connection error" after setup
- Verify the database credentials in cPanel are correct
- Check that the cPanel user is assigned to the database with ALL PRIVILEGES
- Verify the tenant identifier in `database.php` matches what you entered in the form

### User not showing on listing page
- Check `Global_users` table → the admin user must have `role_id = 1`
- Check `Global_userid_to_roles` → must have a record with `group_id = 1`

### Systems not loading for the organization
- In `Global_users`, verify `system_ids` contains the correct serialized system IDs
- Cross-check with `organization_list.system_ids` in the Super Admin database

### Uploaded files not saving
- Verify the `uploaded_files/{org_identifier}/` folder exists with correct permissions (755)
- Check both `uploaded_files/` and `External/uploaded_files/` directories

### Config file not updated
- If the automated config update failed (file permissions), manually add to **both** files:
  - `application/config/database.php`
  - `External/application/config/database.php`
- Use the same format as existing tenant entries in those files

---

## Important Reminders

- **Do NOT change system IDs** in the database — HR system ID (104) is statically referenced in `auth/dashboardEmployee`
- **Do NOT change role IDs** — IDs 1-5 are hardcoded (Admin, Manager, Staff, Employee, Timesheet)
- **Do NOT manually insert** into `Global_roles` — the system creates all 5 roles automatically
- The backup SMTP (`info@bizadmin.com.au`, location_id=9999) is used when no location/system-specific SMTP is configured

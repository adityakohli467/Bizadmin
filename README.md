# Bizadmin

this project is developed in Codeignitor 3, bootstrap 5 and tailwind css, using HMVC architecture of CI 3


Prerequisites :
Php 7.4
Mysql 8
Tailwindcss

=============================================

STEPS  FOR SETUP  :

-- clone the main branch
-- import database from refrence.sql file located at the root
-- make sure superadmin files can only be changed at the production level
-- run npm install 
-- composer install
-- setup tailwind css locally so UI doesn break
-- make sure proper env. is setup for windows/linux etc to run CI project
-- bizadmin.com.au/cjs will be the test tenant for any development work
-- please note :  we have different database for different tenant so changes in one table needs to done across all tenants database


================== Database Migrations (Multi-Tenant) ===========================

We have a migration system to apply DB changes across ALL tenant databases at once.

HOW IT WORKS:
- Migration SQL files live in: application/migrations/
- A controller (Migration.php) loops through all tenants in `organization_list` and runs pending migrations
- Each tenant DB tracks which migrations have already been applied via a `schema_migrations` table

ADDING A NEW MIGRATION:
1. Create a .sql file in application/migrations/ with an incrementing number prefix:
   - 003_add_column_to_employee.sql
   - 004_create_new_table.sql
2. Write your SQL inside (ALTER TABLE, CREATE TABLE, etc.)
3. Run the migration (see below)

RUNNING MIGRATIONS:
- Browser (admin only):  yoursite.com/migration/run
- CLI:                   php index.php migration run
- Dry run (preview):     yoursite.com/migration/run?dry=1
- Single tenant only:    yoursite.com/migration/run?tenant=cjs
- Check status:          yoursite.com/migration/status
- List files:            yoursite.com/migration/files

BEST PRACTICE:
- Always test with dry run first:  /migration/run?dry=1
- Then test on single tenant:      /migration/run?tenant=cjs
- Then run for all:                 /migration/run

NOTES:
- Migrations are only applied once per tenant (tracked in schema_migrations table)
- Only super admins or CLI can run migrations
- Multi-statement SQL files are supported (separate statements with ;)


================== Further improvements : ===========================
-- we need to merge bootstrap and tailwind css to work together without breaking any UI
-- need to setup some "PHPUnit" library for testing 
-- slowly we need to move all UI from bootstrap to tailwind css to make all bizadmin pages uniform

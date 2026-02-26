# Tailwind CSS Build & Deployment Guide

## Project Structure

```
Bizadmin/
├── package.json              # npm configuration
├── tailwind.config.js        # Tailwind configuration
├── src/
│   └── css/
│       └── tailwind.css      # Source CSS file
└── theme-assets/
    └── css/
        └── tailwind.min.css  # Compiled output (generated)
```

## Local Development Setup

### Prerequisites
- Node.js (v16 or higher)
- npm (v8 or higher)

### Initial Setup

1. **Install Node.js dependencies:**
   ```bash
   cd c:\xampp\htdocs\Bizadmin
   npm install
   ```

2. **Build CSS for development (watch mode):**
   ```bash
   npm run watch:css
   ```
   This will watch for changes and rebuild automatically.

3. **Build CSS for production:**
   ```bash
   npm run build:css
   ```
   This creates the minified `theme-assets/css/tailwind.min.css` file.

---

## Production Deployment (cPanel)

### Option 1: Build Locally and Upload (Recommended)

1. **Build the CSS locally:**
   ```bash
   cd c:\xampp\htdocs\Bizadmin
   npm install
   npm run build:css
   ```

2. **Upload to cPanel:**
   - Connect to your hosting via FTP/SFTP or cPanel File Manager
   - Upload the generated file:
     - `theme-assets/css/tailwind.min.css`
   
3. **Verify the file is accessible:**
   - Visit: `https://yourdomain.com/theme-assets/css/tailwind.min.css`

### Option 2: Build on Server (If Node.js Available)

If your cPanel hosting supports Node.js:

1. **SSH into your server or use cPanel Terminal:**
   ```bash
   cd ~/public_html/Bizadmin
   # or your specific path
   ```

2. **Upload the build files:**
   - `package.json`
   - `tailwind.config.js`
   - `src/css/tailwind.css`

3. **Install and build:**
   ```bash
   npm install
   npm run build:css
   ```

4. **Set up a cron job (optional) for auto-build:**
   In cPanel > Cron Jobs:
   ```bash
   cd ~/public_html/Bizadmin && npm run build:css
   ```

---

## Files Modified

The following view files have been updated to use the compiled CSS:

### HR Module
- `HR/views/roster/roster.php`
- `HR/views/roster/rosterViewByTM.php`
- `HR/views/timesheet/weeklyTimesheet.php`
- `HR/views/timesheet/weeklyTimesheet2.php`
- `HR/views/timesheet/timesheetWithoutRoster.php`
- `HR/views/timesheet/timesheetList.php`
- `HR/views/timesheet/viewTimesheetWithoutRoster.php`
- `HR/views/timesheet/payroll_calculation.php`
- `HR/views/TimesheetClockIn/clockin.php`
- `HR/views/Leaves/leavesDashboard.php`
- `HR/views/Leaves/leavesDashboardDynamic.php`
- `HR/views/general/dashboard.php`
- `HR/views/general/dashboard_manager.php`

### Compliance Module
- `Compliance/views/forms/kitchenchecklistform.php`

### Catering Module
- `Catering/views/quote/quoteProducts.php`
- `Catering/views/quote/quoteForm.php`

### External Application
- `External/application/views/HR/Employee/onboardingForm.php`

### General Views
- `views/general/MainWebsitePages/header.php`

---

## Important Notes

1. **Custom Colors Available:**
   The following custom colors are defined in `tailwind.config.js`:
   - `primary` (shades 50-900)
   - `success`, `warning`, `danger`, `info`
   - `teal`, `orange`, `neutralgray`
   - `navy`, `navy-dark`, `navy-light`
   - `cafe` (shades 50-900)
   - `magenta`, `accent`, `light`
   - `orange-primary`, `green-primary`, `sky-primary`
   - `shift-green`, `shift-border`
   - `light-gray`, `background`, `secondary`, `teal-dark`

2. **Font Configuration:**
   - Default font: Inter (sans-serif)
   - Additional font class: `font-inter`

3. **After Making CSS Changes:**
   - Edit `src/css/tailwind.css` for custom styles
   - Rebuild with `npm run build:css`
   - Re-upload `theme-assets/css/tailwind.min.css` to production

4. **Adding New Custom Colors:**
   - Edit `tailwind.config.js`
   - Add colors under `theme.extend.colors`
   - Rebuild CSS

---

## Troubleshooting

### Styles Not Loading
1. Check if `theme-assets/css/tailwind.min.css` exists
2. Verify file permissions (644 for files)
3. Clear browser cache
4. Check browser console for 404 errors

### Missing Classes
1. Ensure the PHP file path is in `tailwind.config.js` content array
2. Rebuild CSS after adding new paths

### Build Errors
1. Delete `node_modules` folder
2. Run `npm install` again
3. Try `npm run build:css`

---

## Quick Commands Reference

| Command | Description |
|---------|-------------|
| `npm install` | Install dependencies |
| `npm run build:css` | Build minified CSS for production |
| `npm run watch:css` | Watch and rebuild on changes |
| `npm run build` | Alias for build:css |

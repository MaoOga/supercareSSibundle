# Supercare SSI Bundle System

A comprehensive Surgical Site Infection (SSI) monitoring system for Supercare Hospital.

## Features

- **Nurse Dashboard**: Overview of all patients with search and filtering capabilities
- **Patient Form**: Comprehensive SSI bundle form with all required fields
- **Database Integration**: Complete MySQL database with 12 related tables
- **Responsive Design**: Works on desktop and mobile devices
- **Real-time Search**: Search patients by UHID, name, or surgeon
- **Statistics**: View patient statistics and complications

## Setup Instructions

### 1. Database Setup

1. **Create Database**:

   ```sql
   CREATE DATABASE supercare_ssi;
   USE supercare_ssi;
   ```

2. **Import Tables**:

   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Select the `supercare_ssi` database
   - Go to "Import" tab
   - Choose the `database_setup.sql` file
   - Click "Go" to create all tables

   OR

   - Copy and paste the contents of `database_setup.sql` into the SQL tab
   - Execute the script

### 2. Configuration

1. **Database Connection**:

   - Edit `config.php` if you need to change database credentials
   - Default settings work with XAMPP (localhost, root, no password)

2. **Test Connection**:
   - Visit `http://localhost/supercareSSibundle/test_db.php`
   - Should show "Database connection successful!" and list all tables

### 3. Application Access

1. **Main Dashboard**: `http://localhost/supercareSSibundle/`
2. **Patient Form**: `http://localhost/supercareSSibundle/form.html`
3. **Search Page**: `http://localhost/supercareSSibundle/search.html`
4. **Statistics**: `http://localhost/supercareSSibundle/stats.html`

## Database Structure

The system uses 12 interconnected tables:

1. **patients** - Basic patient information
2. **surgical_details** - Surgery dates and surgeon information
3. **surgical_skin_preparation** - Pre-operative skin preparation details
4. **implanted_materials** - Information about implanted materials
5. **drains** - Drain usage and descriptions
6. **antibiotic_usage** - Antibiotic administration details
7. **post_operative_monitoring** - Daily post-operative monitoring
8. **wound_complications** - SSI and complication tracking
9. **cultural_dressing** - Cultural swap and dressing findings
10. **review_sutures** - Review dates and suture removal
11. **review_phone** - Follow-up review or phone call details

## File Structure

```
supercareSSibundle/
├── index.html              # Nurse dashboard (main page)
├── form.html               # Patient data entry form
├── search.html             # Patient search page
├── stats.html              # Statistics page
├── admin.html              # Admin panel
├── config.php              # Database configuration
├── submit_form.php         # Form submission handler
├── test_db.php             # Database connection test
├── database_setup.sql      # Database creation script
├── script.js               # JavaScript functionality
├── style.css               # CSS styles
├── supercare-hospital_logo.png
└── README.md               # This file
```

## Usage

### Adding New Patients

1. Click "New Patient" button from the dashboard
2. Fill out the comprehensive SSI bundle form
3. Click "Save Patient Data" to submit
4. Data is automatically saved to all relevant database tables

### Viewing Patients

1. Use the search functionality on the dashboard
2. Filter by status (complications, pending review, etc.)
3. Click "View/Edit" to see patient details

### Mobile Usage

The system is fully responsive and includes:

- Mobile-optimized navigation
- Touch-friendly form controls
- Responsive tables that stack on mobile

## Troubleshooting

### Database Connection Issues

1. Ensure XAMPP is running (Apache and MySQL)
2. Check database credentials in `config.php`
3. Verify database `supercare_ssi` exists
4. Run `test_db.php` to diagnose issues

### Form Submission Issues

1. Check browser console for JavaScript errors
2. Verify `submit_form.php` has proper permissions
3. Check PHP error logs in XAMPP
4. Ensure all required fields are filled

### Table Creation Issues

1. Make sure MySQL supports foreign keys
2. Check for syntax errors in `database_setup.sql`
3. Verify database user has CREATE privileges

## Security Notes

- This is a development setup with default XAMPP credentials
- For production, change database credentials and implement proper authentication
- Consider adding input validation and sanitization
- Implement user authentication and authorization

## Support

For technical support or questions, please contact the development team.

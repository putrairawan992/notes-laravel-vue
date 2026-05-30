=== User Department ===
Contributors: do-creative
Tags: users, department, profile, admin
Requires at least: 5.8
Tested up to: 6.8
Stable tag: 1.1.0
Requires PHP: 8.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a Department field to WordPress user profiles and displays it in the Users list table.

== Description ==

This plugin adds a "Department" dropdown field to the WordPress user profile edit page (both for users editing their own profile and for admins editing any user).

The available department options are: Management, Designer, Developer, SEO Team, Finance, and Operations.

**Features:**
- Adds a Department field to user profiles
- Saves the department value as user meta
- Displays department as a column in the Users list table
- Admins can filter the Users list by department
- Settings page (Users → Departments) to manage department options
- Fully self-contained — no manual database setup needed

== Installation ==

1. Upload the `user-department` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Done! The Department field will appear on all user profile pages.

== Frequently Asked Questions ==

= Can I add custom departments? =

Yes! Go to Users → Departments in the admin menu and you can add, edit, or remove department options.

= Does this work with custom user roles? =

Yes, the department field appears for all users regardless of their role.

== Changelog ==

= 1.1.0 =
* Fixed XSS vulnerability in settings page JavaScript — user input from prompt() is now inserted via DOM APIs (textContent/value) instead of innerHTML.
* Fixed department key sanitization in JavaScript to match server-side sanitize_key() behavior.
* Added duplicate key detection when adding a new department via the settings page.
* Fixed get_departments() to respect admin deletions — removed forced merge with defaults that caused deleted departments to reappear.
* Updated "Tested up to" to WordPress 6.8.

= 1.0.0 =
* Initial release.
* Department field on user profiles.
* Department column in Users list.
* Filter by department.
* Settings page for managing departments.

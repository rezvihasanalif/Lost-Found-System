# Lost & Found Application - Complete Rebuild

## Overview
A full-stack Lost & Found web application built with **PHP, HTML, CSS, and JavaScript**. Users can report lost/found items, search and filter items, and manage their own reports.

## Technology Stack
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP
- **Database**: MySQL
- **File Upload**: Native PHP file handling
- **Color Scheme**: Professional and modern (Navy, Blue, Teal, Red, Amber)

## Color Scheme
- **Navbar**: #1A2B5F (Dark Navy)
- **Primary Button**: #2563EB (Blue)
- **Background**: #EFF6FF (Light Blue-Gray)
- **Cards**: #FFFFFF (White)
- **Lost Badge**: #D85A30 (Coral Red)
- **Found Badge**: #1D9E75 (Teal Green)
- **Resolved Badge**: #BA7517 (Amber)
- **Muted Text**: #6B7280 (Gray)

## Project Structure
```
/project
├── index.php                 # Landing page
├── register.php              # Registration page
├── login.php                 # Login page
├── logout.php                # Logout handler
├── home.php                  # Main dashboard (protected)
├── item-detail.php           # Item details page
├── report.php                # Report new item page
├── edit-item.php             # Edit item form
├── delete-item.php           # Delete item handler
├── resolve-item.php          # Mark as resolved handler
├── my-reports.php            # User's reports page
├── settings.php              # Account settings
├── style.css                 # Main stylesheet (responsive)
├── config/
│   ├── config.php            # Configuration constants
│   └── db.php                # Database connection & helpers
├── includes/
│   ├── navbar.php            # Navigation bar include
│   └── footer.php            # Footer include
├── uploads/                  # Uploaded item photos directory
└── project_db_new.sql        # Database schema

```

## Database Setup
1. Import `project_db_new.sql` into your MySQL database:
   ```bash
   mysql -u root -p < project_db_new.sql
   ```
   Or copy the SQL content and execute manually in phpMyAdmin

2. The schema creates:
   - **users table**: id, fullName, email, password (hashed), avatar, createdAt
   - **items table**: id, title, description, type, category, location, date, photo, status, reportedBy, createdAt, updatedAt

## Pages & Routes

### Public Pages
- **/ (index.php)** - Landing page with features overview
- **/register.php** - User registration
- **/login.php** - User login

### Protected Pages (Login Required)
- **/home.php** - Dashboard with all items, search, and filters
- **/report.php** - Report a new lost/found item
- **/item-detail.php?id=X** - View item details
- **/my-reports.php** - Manage user's reports (with tabs for lost/found)
- **/edit-item.php?id=X** - Edit item report
- **/settings.php** - Account settings

### Action Pages
- **/delete-item.php?id=X** - Delete item (POST redirect)
- **/resolve-item.php?id=X** - Mark item as resolved (POST redirect)
- **/logout.php** - Logout and clear session

## Features

### Authentication
- User registration with validation
- Password hashing with PHP's `password_hash()`
- Session-based authentication
- Login/logout functionality

### Item Management
- Report lost or found items
- Upload item photos (JPG, PNG, GIF up to 5MB)
- Edit/delete own reports
- Mark items as resolved
- View item details with full information

### Search & Filtering
- Real-time search by title, description, location
- Filter by Lost/Found type
- Category filtering (9 categories)
- Pagination (12 items per page)

### User Features
- User avatar with initials
- Account settings page
- View all personal reports
- Tab view for lost vs found items

### Responsive Design
- Mobile-first approach
- Works on desktop, tablet, mobile
- Flexible grid layouts
- Touch-friendly buttons

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache with mod_rewrite enabled)

### Steps
1. Extract the project to your web root (e.g., `/htdocs/project`)
2. Update `config/config.php` if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'lost_found_db');
   ```
3. Import the database:
   ```bash
   mysql -u root -p lost_found_db < project_db_new.sql
   ```
4. Ensure `uploads/` directory is writable:
   ```bash
   chmod 755 uploads/
   ```
5. Open `http://localhost/project` in your browser

## Configuration

All configuration is in `config/config.php`:
- **Database credentials**
- **Site URL**
- **Upload directory and max file size**
- **Color scheme constants**
- **Item categories**
- **Pagination items per page**

## Category List
1. Electronics
2. Bags & Wallets
3. Keys
4. Clothing
5. Documents & ID
6. Jewelry
7. Pets
8. Books & Stationery
9. Other

## Helper Functions

### Database
- `sanitize()` - SQL injection prevention
- `esc_html()` - XSS prevention
- `generateInitials()` - User avatar
- `getFileExtension()` - File validation
- `generateUniqueFilename()` - Secure file naming
- `formatDate()` - Date formatting
- `formatDateTime()` - DateTime formatting

## Security Features
- SQL injection prevention with `mysqli_real_escape_string()`
- XSS prevention with `htmlspecialchars()`
- Password hashing with bcrypt
- File upload validation
- File type checking
- Session-based access control
- CSRF protection via proper POST validation

## File Upload Handling
- Maximum file size: 5MB
- Allowed formats: JPG, JPEG, PNG, GIF
- Files stored in `/uploads` with unique names
- Old photos deleted when updated
- Placeholder shown if no photo

## Responsive Breakpoints
- **Desktop**: 1200px and above
- **Tablet**: 768px - 1199px
- **Mobile**: Below 768px

## Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Future Enhancements
1. Email notifications
2. Password reset functionality
3. Messaging system between users
4. Advanced search filters
5. Item statistics
6. User ratings/reviews
7. Admin dashboard
8. Export reports as PDF
9. Two-factor authentication
10. Dark mode toggle

## Testing

### Test User Accounts
You can register new accounts or use:
- Email: `test@example.com`
- Password: `password123`

### Test Scenarios
1. Register a new account
2. Report a lost item with photo
3. Report a found item without photo
4. Search and filter items
5. Edit your report
6. Mark as resolved
7. View other users' items
8. Update account settings

## Support & Troubleshooting

### Common Issues
**1. "Database connection failed"**
- Check MySQL is running
- Verify credentials in `config/config.php`
- Ensure database `lost_found_db` exists

**2. "Upload failed"**
- Check `uploads/` directory permissions (755)
- Verify file size < 5MB
- Check file format is JPG/PNG/GIF

**3. "Session expired"**
- Check PHP sessions are enabled
- Verify `/tmp` directory is writable
- Clear browser cookies if needed

**4. "Page not found"**
- Verify Apache mod_rewrite is enabled
- Check .htaccess if using URL rewriting
- Ensure all files are in correct directories

## Credits
Built as a complete rebuild from scratch with modern web standards.

## License
Open source - feel free to modify and use.

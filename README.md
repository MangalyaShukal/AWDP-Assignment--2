# OVERSO

XAMPP-ready PHP + MySQL oversized T-shirt shop and admin panel.

## Setup

1. Copy `OVERSO` to `C:\xampp\htdocs\OVERSO`.
2. Import `database/overso.sql` in phpMyAdmin.
3. Update `config/database.php` if your MySQL credentials differ.
4. Start Apache and MySQL, then open `http://localhost/OVERSO/`.

## Demo access

- Admin: `admin` / `admin123`
- Active user: `riya` / `password`

New registrations are blocked by design. Approve them from **Admin → Users** before logging in.

## Notes

The project uses Bootstrap 5 from its official CDN, PHP sessions, PDO prepared statements, and a MySQL database. Payment is intentionally simulated for academic demonstration.

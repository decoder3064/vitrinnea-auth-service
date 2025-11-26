# Vitrinnea Auth Service

Authentication and authorization microservice for Vitrinnea platform using Laravel 12 with JWT authentication.

## 🚀 Features

- **JWT Authentication** - Secure token-based auth with tymon/jwt-auth
- **Role-Based Access Control** - Spatie Permission package with 8 roles and 18 permissions
- **User Groups Management** - Assign users to multiple groups
- **Multi-Country Support** - Separate operations for SV (El Salvador) and GT (Guatemala)
- **Email Notifications** - Welcome emails and password reset functionality
- **RESTful API** - Complete CRUD operations for users and groups
- **CORS Enabled** - Ready for frontend integration

## 📋 Prerequisites

- PHP 8.2 or higher
- PostgreSQL 14 or higher
- Composer 2.x
- Laravel 12.x

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/decoder3064/vitrinnea-auth-service.git
cd vitrinnea-auth-service
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file and configure it:

```bash
cp .env.example .env
```

Update the following in your `.env`:

```env
APP_NAME="Vitrinnea Auth"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001

# Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=vitrinnea_auth_dev
DB_USERNAME=your_username
DB_PASSWORD=your_password

# JWT Secret (will be generated)
JWT_SECRET=

# API Authentication for External Services
AUTH_API_KEY=your-api-key-here
AUTH_API_SECRET=your-api-secret-here

# CORS Configuration
CORS_ALLOWED_ORIGINS=*

# Mail Configuration (optional for local dev)
MAIL_MAILER=log
# For production, use real SMTP:
# MAIL_MAILER=smtp
# MAIL_HOST=smtp.mailtrap.io
# MAIL_PORT=2525
# MAIL_USERNAME=your_username
# MAIL_PASSWORD=your_password
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Generate JWT Secret

```bash
php artisan jwt:secret
```

### 6. Create Database

Create a PostgreSQL database:

```bash
createdb vitrinnea_auth_dev
```

Or using psql:

```sql
CREATE DATABASE vitrinnea_auth_dev;
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Seed Database

Seed the database with roles, permissions, groups, and test users:

```bash
php artisan db:seed
```

This creates:
- **8 Roles**: super_admin, admin_sv, admin_gt, warehouse_manager_sv, warehouse_manager_gt, operations, employee
- **18 Permissions**: orders, inventory, users, warehouse, reports, settings management
- **3 Groups**: admin, customer_service, it
- **5 Test Users**: All with password `"password"`

### 9. Start Development Server

```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## 🔐 Default Login Credentials

```json
{
  "email": "admin@vitrinnea.com",
  "password": "password"
}
```

## 📚 API Documentation

Complete API documentation is available in [`API_RESPONSES.md`](API_RESPONSES.md)

### Quick Start Endpoints

**Authentication:**
- `POST /api/auth/login` - Login and get JWT token (requires X-API-Key header)
- `POST /api/auth/register` - Register new user (requires X-API-Key header)
- `GET /api/auth/me` - Get current user info
- `POST /api/auth/logout` - Logout
- `POST /api/auth/refresh` - Refresh token
- `POST /api/auth/verify` - Verify JWT token

**User Management (Admin only):**
- `GET /api/admin/users` - List all users
- `POST /api/admin/users` - Create new user
- `PUT /api/admin/users/{id}` - Update user
- `DELETE /api/admin/users/{id}` - Deactivate user
- `POST /api/admin/users/{id}/activate` - Activate user
- `POST /api/admin/users/{id}/groups` - Assign groups
- `POST /api/admin/users/{id}/reset-password` - Reset password

**Group Management (Admin only):**
- `GET /api/admin/groups` - List all groups
- `POST /api/admin/groups` - Create group
- `PUT /api/admin/groups/{id}` - Update group
- `DELETE /api/admin/groups/{id}` - Delete group

## 🧪 Testing with Postman

Import the Postman collection from [`tests_postman.json`](tests_postman.json)

The collection includes:
- Auto-token capture on login
- All CRUD operations
- Group management tests
- Password reset workflows

## 🏗️ Architecture

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   └── Admin/
│   │       ├── UserController.php
│   │       └── GroupController.php
│   ├── Middleware/
│   │   ├── IsAdmin.php
│   │   └── RestrictEmailDomain.php
│   └── Services/
│       └── AuthService.php
├── Models/
│   ├── User.php
│   └── Group.php
└── Mail/
    ├── WelcomeEmail.php
    └── PasswordResetEmail.php
```

## 🔒 Security

- JWT tokens expire in 60 minutes
- Email domain restricted to `@vitrinnea.com` for all registrations
- API Key authentication required for login/register endpoints (service-to-service)
- Admin middleware protects sensitive endpoints
- Password reset generates random secure passwords
- Rate limiting on authentication endpoints (5 requests per minute)
- CORS configured for production environments

## 🌍 Environment Variables

Key environment variables to configure:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_ENV` | Environment (local/production) | `local` |
| `DB_DATABASE` | Database name | `vitrinnea_auth_dev` |
| `JWT_SECRET` | JWT signing key | Generated |
| `CORS_ALLOWED_ORIGINS` | CORS allowed origins | `*` |
| `MAIL_MAILER` | Mail driver | `log` |

## 📦 Production Deployment

### 1. Update Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
CORS_ALLOWED_ORIGINS=https://your-frontend.com
```

### 2. Optimize Application

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Run Migrations

```bash
php artisan migrate --force
```

### 4. Configure Web Server

Point your web server to the `public/` directory.

**Nginx Example:**
```nginx
server {
    listen 80;
    server_name api.vitrinnea.com;
    root /path/to/vitrinnea-auth/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🛠️ Troubleshooting

### Database Connection Issues
```bash
# Check PostgreSQL is running
sudo systemctl status postgresql

# Test connection
psql -U your_username -d vitrinnea_auth_dev
```

### JWT Token Issues
```bash
# Regenerate JWT secret
php artisan jwt:secret --force

# Clear config cache
php artisan config:clear
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 👥 Roles & Permissions

### Available Roles
- `super_admin` - Full system access
- `admin_sv` - Administrator for El Salvador
- `admin_gt` - Administrator for Guatemala  
- `warehouse_manager_sv` - Warehouse manager SV
- `warehouse_manager_gt` - Warehouse manager GT
- `operations` - Operations staff
- `employee` - Regular employee

### Available Permissions
- Orders: `view_orders`, `create_orders`, `edit_orders`, `delete_orders`
- Inventory: `view_inventory`, `create_inventory`, `edit_inventory`, `delete_inventory`
- Users: `view_users`, `create_users`, `edit_users`, `delete_users`
- Warehouse: `view_warehouse`, `edit_warehouse`
- Reports: `view_reports`, `export_reports`
- Settings: `manage_settings`, `view_settings`

## 📝 License

Proprietary - Vitrinnea

## 👨‍💻 Developer

David Reyes - decoder3064

---

For API response examples and detailed endpoint documentation, see [`API_RESPONSES.md`](API_RESPONSES.md)

# Dokumentasi Proyek Portal Berita

## 📋 Overview
Aplikasi Portal Berita berbasis Website menggunakan Framework Laravel dan Vue.js yang menyediakan platform untuk publishing berita/artikel dengan fitur komentar dan manajemen konten untuk admin dan editor.

## 🏗️ Arsitektur Teknis
- **Frontend**: Laravel Blade, Vue.js 3, Tailwind CSS
- **Backend**: Laravel 10+
- **Database**: MySQL
- **Authentication**: Laravel Auth dengan role-based access
- **Build Tool**: Vite


## 🗄️ Database Schema

### Tables
1. **users**
   - id, name, email, password, role (admin/user), timestamps

2. **posts**
   - id, title, content, published_at, user_id, timestamps

3. **comments**
   - id, post_id, name, email, content, timestamps

### Relationships
- User hasMany Posts
- Post belongsTo User
- Post hasMany Comments
- Comment belongsTo Post

## 👥 User Roles & Permissions

### 1. Admin User
- **Login**: /login
- **Email**: admin@portalberita.com
- **Password**: password123
- **Permissions**:
  - CRUD Posts
  - Manage Comments (view/delete)
  - Access Admin Dashboard
  - View Statistics

  ### 2. Editor User
- **Login**: /login
- **Email**: editor@portalberita.com
- **Password**: editor123
- **Permissions**:
  - CRUD Posts
  - Access Editor Dashboard
  - View Statistics

### 3. Regular User
- **Permissions**:
  - View published posts
  - Add comments
  - Read comments

## 🚀 Fitur Utama

### 🔐 Authentication System
- Laravel Auth dengan custom login page
- Role-based authentication (admin/editor/user)
- Simple password reset tanpa email
- Protected admin routes

### 📊 Admin Dashboard
- Statistics overview (total posts, comments, etc.)
- Recent posts and comments listing
- Post management CRUD
- Comment moderation

### 📝 Post Management
- **Create**: Rich text content dengan published_at option
- **Read**: List view dan detail view
- **Update**: Edit post dengan validation
- **Delete**: Soft delete dengan confirmation

### 💬 Comment System
- Anonymous commenting (name + email)
- Real-time comment display
- Comment form validation
- Admin moderation

### 🎨 Frontend Features
- Responsive design dengan Tailwind CSS
- Vue.js components untuk dynamic content
- Pagination untuk posts dan comments
- Modern UI dengan animations

## 🔧 Installation Guide

### Prerequisites
- PHP 8.1+
- Composer
- Node.js 16+
- MySQL 5.7+

### Step-by-Step Installation
```bash
# 1. Clone project
git clone <repository-url>
cd portal-berita

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install

# 4. Environment setup
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
DB_DATABASE=portal_berita
DB_USERNAME=root
DB_PASSWORD=

# 6. Run migrations and seeders
php artisan migrate --seed

# 7. Build assets
npm run dev

# 8. Start development server
php artisan serve
```

### Default Admin Account
Setelah menjalankan seeder, admin dan editor account otomatis dibuat:
- **Email**: admin@portalberita.com
- **Password**: password123

- **Email**: editor@portalberita.com
- **Password**: editord123

## 📡 API Endpoints

### Public API
```http
GET    /api/posts          # List published posts
GET    /api/posts/{id}     # Get specific post
POST   /api/comments       # Create new comment
GET    /api/posts/{id}/comments # Get post comments
```

### Admin & Editor API (Protected)
```http
GET    /admin/dashboard    # Admin dashboard data
GET    /admin/posts        # List all posts
POST   /admin/posts        # Create new post
PUT    /admin/posts/{id}   # Update post
DELETE /admin/posts/{id}   # Delete post
GET    /admin/comments     # List all comments (Admin)
DELETE /admin/comments/{id} # Delete comment (Admin)
```

## 🎨 Frontend Components

### Vue Components
1. **PostList.vue** - Menampilkan list posts dengan grid layout
2. **PostDetail.vue** - Detail post dengan comments
3. **CommentList.vue** - List comments dengan real-time updates
4. **CommentForm.vue** - Form untuk menambah comment
5. **AdminDashboard.vue** - Admin statistics dashboard
6. **PostForm.vue** - Form create/edit post untuk admin

### Blade Templates
- **layout.blade.php** - Main layout template
- **admin/layout.blade.php** - Admin layout template
- **auth/login.blade.php** - Login page
- **auth/passwords/simple.blade.php** - Simple password reset

## ⚙️ Configuration

### Environment Variables
```env
APP_URL=http://localhost:8000
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portal_berita
DB_USERNAME=root
DB_PASSWORD=


### Tailwind Configuration
```javascript
// tailwind.config.js
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                'inter': ['Inter', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
```

## 🛠️ Development Commands

### Common Artisan Commands
```bash
# Database operations
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Cache management
php artisan optimize:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Development
php artisan serve
npm run dev
npm run build
```

### NPM Commands
```bash
npm run dev      # Development build with hot reload
npm run build    # Production build
npm run watch    # Watch for changes
```

## 🧪 Testing Data

### Sample Posts
Setelah menjalankan seeder, akan tersedia:
- 10 published posts
- 2 draft posts
- 50+ comments
- 3 regular users
- 2 admin users

### Sample Content
Posts mencakup topik:
- Teknologi AI
- Web Development
- Cybersecurity
- Programming tutorials
- Technology trends

## 🔒 Security Features

### Implemented Security
- CSRF protection
- SQL injection prevention (Eloquent)
- XSS protection (Blade escaping)
- Rate limiting pada password reset
- Role-based access control
- Input validation dengan Form Requests

### Authentication Security
- Password hashing dengan bcrypt
- Protected admin routes
- Session management
- Secure password reset flow

## 📱 Responsive Design

### Breakpoints
- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

### Components Behavior
- Navigation responsive dengan hamburger menu
- Grid layout adaptif
- Touch-friendly buttons
- Optimized images dan icons


### Server Requirements
- PHP 8.1+ dengan extensions: mbstring, xml, mysql, json
- MySQL 5.7+ atau MariaDB 10.2+
- Web server (Apache/Nginx)
- Node.js 16+ (untuk build assets)

## 📊 Performance Optimization

### Implemented Optimizations
- Laravel caching mechanisms
- Eager loading relationships
- Pagination untuk large datasets
- Optimized database queries
- Minified CSS/JS untuk production

### Recommended Optimizations
- Redis untuk caching
- CDN untuk assets
- Database indexing
- Image optimization
- HTTP/2 implementation

## 🐛 Troubleshooting

### Common Issues
1. **CSRF Token Mismatch**
   - Clear browser cookies
   - Check CSRF token in form

2. **Vite Assets Not Loading**
   - Run `npm run build`
   - Check Vite configuration

3. **Database Connection Error**
   - Verify .env configuration
   - Check MySQL service status

4. **Authentication Issues**
   - Clear session cookies
   - Verify user role in database

### Debug Mode
Untuk development, enable debug mode:
```env
APP_DEBUG=true
```

Untuk production, pastikan:
```env
APP_DEBUG=false
```

## 📝 License & Credits

### Technologies Used
- **Laravel** - PHP framework
- **Vue.js** - JavaScript framework
- **Tailwind CSS** - CSS framework
- **MySQL** - Database
- **Vite** - Build tool

### Development Guidelines
- Follow PSR-12 coding standards
- Use meaningful commit messages
- Write clear documentation
- Test before deployment
- Maintain code consistency

---

**Note**: Dokumentasi ini diperbarui terakhir pada 1 September 2025. Untuk informasi terbaru, selalu refer ke README.md di repository project.
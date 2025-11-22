# SoCo E-Commerce Project - Structure Overview

## Project Summary

**SoCo E-Commerce** is a Laravel-based online platform for selling **school uniforms and accessories**. The project is specifically designed for parents to purchase school-specific uniforms for their children with features like student profile management, school-based product filtering, and order tracking.

### Business Model
- **Product**: School uniforms and accessories
- **Target Audience**: Parents/Guardians purchasing uniforms for students
- **Unique Feature**: School-specific uniform catalog (products filtered by school, grade, and gender)
- **Location**: Coimbatore, Tamil Nadu, India
- **Contact**: hello@theskoolstore.com | +91 9994878486

---

## Current Project Status

### ✅ Completed
1. **Frontend Design Conversion**
   - HTML/CSS converted to Laravel Blade templates
   - Asset paths properly configured using Laravel's `asset()` helper
   - Responsive layout with Bootstrap
   - Homepage with hero section, about section, services, and FAQ

2. **Basic Structure**
   - Laravel 12.39.0 framework
   - PHP 8.3.10
   - MySQL database configured
   - Frontend and Admin layout templates created

### 🚧 In Progress / To Be Implemented
1. **Authentication System** (Not yet implemented)
   - Parent/Guardian registration
   - Login system
   - Student profile management

2. **Database Models** (Not yet created)
   - Schools
   - Products
   - Students
   - Orders
   - Cart
   - Order Items
   - Exchange/Refund Requests

3. **Core Features** (From Phase 1 documentation)
   - School list and filtering
   - Product catalog with school/grade/gender filtering
   - Shopping cart
   - Checkout process
   - Order management
   - Exchange/Refund system

---

## Project Structure

```
soco-ecommerce/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Controller.php (Base controller)
│   │       └── Front/
│   │           └── HomeController.php (Homepage controller)
│   ├── Models/
│   │   └── User.php (Laravel default user model)
│   └── Providers/
│       └── AppServiceProvider.php
│
├── config/
│   ├── app.php
│   ├── database.php (MySQL configured)
│   ├── session.php (Database sessions)
│   └── ... (other Laravel config files)
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   └── 0001_01_01_000002_create_jobs_table.php
│   ├── factories/
│   └── seeders/
│
├── public/
│   ├── assets/ (Static assets - CSS, JS, Images, Fonts)
│   │   ├── css/ (Bootstrap, custom styles, sliders)
│   │   ├── js/ (jQuery, Bootstrap, sliders, main.js)
│   │   ├── img/ (441 image files - logos, icons, product images)
│   │   └── fonts/
│   ├── index.php (Laravel entry point)
│   └── favicon.ico
│
├── resources/
│   ├── views/
│   │   ├── frontend/
│   │   │   ├── layouts/
│   │   │   │   └── app.blade.php (Main frontend layout)
│   │   │   └── index.blade.php (Homepage)
│   │   ├── admin/
│   │   │   └── layouts/
│   │   │       └── admin.blade.php (Admin layout - empty)
│   │   └── welcome.blade.php (Laravel default)
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php (Only homepage route currently)
│   └── console.php
│
├── storage/
│   ├── app/ (File uploads)
│   ├── framework/ (Cache, sessions, views)
│   └── logs/ (Laravel logs)
│
└── docs/
    ├── phase-1-portal-flow.md (Project requirements)
    ├── laravel-conversion-summary.md (Conversion notes)
    └── project-structure-overview.md (This file)
```

---

## Key Features (From Requirements)

### 1. **Homepage**
- Hero banner with call-to-action
- About section (7+ years experience, 200+ clients)
- Services section
- FAQ section
- Footer with contact info and links

### 2. **User Flow (To Be Implemented)**
```
Home → Login/Register → Student Profile Creation → 
Browse Products (filtered by school) → Add to Cart → 
Checkout → Order Confirmation → Order Tracking
```

### 3. **Student Profile Requirements**
- School selection
- Grade/Class
- Section
- Gender
- Profile photo (optional)

### 4. **Product Filtering Logic**
- Products filtered by:
  - Selected School
  - Student's Grade
  - Student's Gender
- Size selection per product
- Customization options (logos, embroidery)

### 5. **Shopping Cart**
- Items grouped by student
- Multiple students per parent account
- Size selection required before checkout
- Edit quantities and remove items

### 6. **Order Management**
- Order status tracking:
  1. Order Placed
  2. Order Accepted
  3. Order Packed
  4. Out for Delivery
  5. Delivered
  6. Completed
- Order history view
- Active vs Completed orders filter

### 7. **Exchange/Refund System**
- Request exchange/refund from order details
- Reason selection dropdown
- Photo upload (optional)
- Status tracking for requests

---

## Technology Stack

### Backend
- **Framework**: Laravel 12.39.0
- **PHP**: 8.3.10
- **Database**: MySQL
- **Session Driver**: Database

### Frontend
- **CSS Framework**: Bootstrap (minified)
- **JavaScript Libraries**:
  - jQuery 3.6.0
  - Bootstrap JS
  - LayerSlider (for hero carousel)
  - Slick Slider
  - Magnific Popup
  - Isotope (for filtering)
- **Build Tool**: Vite (configured but not actively used for assets)
- **CSS Preprocessor**: Sass (source files in `public/assets/sass/`)

### Development Tools
- **Package Manager**: Composer (PHP), NPM (Node.js)
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint

---

## Current Routes

### Frontend Routes
- `GET /` → `HomeController@index` → `frontend.index` (Homepage)

### Routes To Be Created
- `/login` - Parent login
- `/register` - Parent registration
- `/dashboard` - Student dashboard (after login)
- `/products` - Product listing (filtered by school)
- `/cart` - Shopping cart
- `/checkout` - Checkout process
- `/orders` - Order history
- `/orders/{id}` - Order details
- `/about` - About page
- `/services` - Services page
- `/faq` - FAQ page
- `/contact` - Contact page

---

## Database Schema (To Be Created)

### Core Tables Needed

1. **schools**
   - id, name, address, logo, contact_info, status

2. **products**
   - id, school_id, name, description, image, price, category, status

3. **product_variants**
   - id, product_id, size, gender, grade, stock_quantity

4. **students**
   - id, user_id (parent), school_id, name, grade, section, gender, photo

5. **carts**
   - id, user_id, student_id, product_variant_id, quantity

6. **orders**
   - id, user_id, order_number, total_amount, status, shipping_address, payment_method, created_at

7. **order_items**
   - id, order_id, student_id, product_variant_id, quantity, price

8. **exchanges_refunds**
   - id, order_item_id, type (exchange/refund), reason, photo, status, created_at

---

## Next Development Steps

### Phase 1: Database Setup
1. Create migrations for all required tables
2. Create Eloquent models
3. Set up relationships between models
4. Create seeders for sample data

### Phase 2: Authentication
1. Implement parent registration
2. Implement login system
3. Create student profile management
4. Add middleware for protected routes

### Phase 3: Product Management
1. Create product listing page
2. Implement school/grade/gender filtering
3. Add product detail page
4. Implement size selection

### Phase 4: Shopping Cart
1. Add to cart functionality
2. Cart view page
3. Update/remove cart items
4. Cart validation (student profile completion)

### Phase 5: Checkout & Orders
1. Checkout page
2. Address management
3. Payment integration (or placeholder)
4. Order confirmation
5. Order tracking page

### Phase 6: Exchange/Refund
1. Request exchange/refund form
2. Photo upload functionality
3. Admin approval workflow
4. Status updates

---

## Important Configuration Notes

### Environment Variables (.env)
- `APP_KEY` - ✅ Generated
- `DB_CONNECTION=mysql` - ✅ Configured
- `DB_DATABASE` - Set to your database name
- `SESSION_DRIVER=database` - ✅ Configured (requires sessions table)

### Asset Management
- All static assets are in `public/assets/`
- Use `{{ asset('assets/...') }}` in Blade templates
- No build process needed for current assets (pre-compiled)

### Session Storage
- Currently using database sessions
- Requires `sessions` table (created by migration)
- Make sure to run migrations after creating database

---

## Development Commands

```bash
# Start development server
php artisan serve --port=8080

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Database operations
php artisan migrate
php artisan migrate:status
php artisan make:migration create_products_table
php artisan make:model Product

# Create controllers
php artisan make:controller Front/ProductController
php artisan make:controller Front/CartController --resource

# View routes
php artisan route:list
```

---

## Contact & Support Information

- **Email**: hello@theskoolstore.com
- **Phone**: +91 9994878486
- **Address**: No.219, Dr.Radhakrishnan Road, Tatabad, Coimbatore, Tamil Nadu - 641012
- **Business Hours**: Store operates during business hours; orders outside hours process next day

---

## Notes

- The project was converted from static HTML/CSS to Laravel
- Frontend design is complete and responsive
- Backend functionality needs to be implemented
- Follow the Phase 1 requirements document for feature implementation
- All assets are pre-compiled and ready to use


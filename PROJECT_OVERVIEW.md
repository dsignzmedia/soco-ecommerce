# SoCo E-Commerce - Complete Project Overview

## Project Summary

**SoCo E-Commerce** (The Skool Store) is a comprehensive Laravel-based multi-tenant e-commerce platform for selling **school uniforms and accessories**. The platform serves multiple user types including parents, schools, and various admin roles, with specialized product categories (Back-to-School and Merchandise).

### Business Information
- **Product**: School uniforms and accessories
- **Target Audience**: Parents/Guardians, Schools, Students
- **Location**: Coimbatore, Tamil Nadu, India
- **Contact**: hello@theskoolstore.com | +91 9994878486
- **Address**: No.219, Dr.Radhakrishnan Road, Tatabad, Coimbatore, Tamil Nadu - 641012

---

## Technology Stack

### Backend
- **Framework**: Laravel 12.0
- **PHP**: 8.2+
- **Database**: MySQL
- **Session Driver**: Database
- **PDF Generation**: DomPDF (barryvdh/laravel-dompdf)
- **Payment Gateway**: Razorpay (Laravel Socialite for OAuth)
- **Authentication**: Multi-guard system (default, bts_admin, merch_admin)

### Frontend
- **CSS Framework**: Tailwind CSS 4.0 (via Vite)
- **Build Tool**: Vite 7.0
- **JavaScript**: Axios, Vanilla JS
- **Templating**: Blade Templates

### Development Tools
- **Package Managers**: Composer (PHP), NPM (Node.js)
- **Testing**: PHPUnit
- **Code Style**: Laravel Pint

---

## User Roles & Authentication

### User Roles (Defined in User Model)
1. **ROLE_PARENT (0)**: Parents/Guardians purchasing uniforms
2. **ROLE_SCHOOL (1)**: Schools managing their own orders and students
3. **ROLE_MASTER_ADMIN (2)**: Full system administration
4. **ROLE_INVENTORY_ADMIN (3)**: Inventory and order fulfillment management
5. **ROLE_GUEST (4)**: Guest users (browsing without account)
6. **ROLE_BACK_TO_SCHOOL_ADMIN (5)**: Back-to-School product management
7. **ROLE_MERCHANDISE_ADMIN (6)**: Merchandise product management

### Authentication Methods
- **Email/Phone + Password**: Traditional login
- **OTP Verification**: Phone-based OTP for quick login
- **Google OAuth**: Social login via Laravel Socialite
- **Guest Mode**: Browse without account, limited functionality

---

## Project Structure

### Core Directories

```
soco-ecommerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Front/          # Frontend controllers (Parents, Schools)
│   │   │   ├── Admin/
│   │   │   │   ├── Master/     # Master Admin controllers
│   │   │   │   ├── Inventory/  # Inventory Admin controllers
│   │   │   │   ├── BackToSchool/ # BTS Admin controllers
│   │   │   │   └── Merchandise/  # Merchandise Admin controllers
│   │   │   └── PortalAuthController.php
│   │   └── Middleware/         # Role-based access control
│   ├── Models/
│   │   ├── User.php            # Main user model
│   │   ├── StudentProfile.php  # Student profiles
│   │   ├── Cart.php            # Shopping cart
│   │   ├── Payment.php         # Payment records
│   │   ├── ProductVariant.php  # Product size variants
│   │   ├── Admin/Master/       # Master admin models
│   │   ├── BackToSchool/       # BTS models (extends ProductMapping)
│   │   └── Merchandise/        # Merchandise models
│   ├── Services/
│   │   ├── RazorpayService.php # Payment processing
│   │   └── SmsService.php      # SMS notifications
│   └── Support/
│       └── AuditLogger.php     # Audit trail logging
├── database/
│   ├── migrations/             # 51 migration files
│   └── seeders/                # Database seeders
├── resources/
│   ├── views/
│   │   ├── frontend/           # Parent/School frontend views
│   │   ├── admin/              # Admin panel views
│   │   └── emails/             # Email templates
│   ├── css/
│   └── js/
├── routes/
│   └── web.php                 # All application routes
└── public/                     # Public assets
```

---

## Core Features

### 1. Frontend (Parents & Schools)

#### Parent Features
- **Registration & Login**: Email/Phone + OTP, Google OAuth
- **Student Profile Management**: Create multiple student profiles per parent
  - School selection
  - Grade/Class
  - Section
  - Gender
  - Profile photo (optional)
- **Product Browsing**: 
  - School-specific product catalog
  - Filter by school, grade, gender
  - Product details with size charts
- **Shopping Cart**: 
  - Items grouped by student profile
  - Size selection
  - Quantity management
- **Checkout Process**:
  - Address management (multiple addresses)
  - Payment via Razorpay
  - Order confirmation
- **Order Management**:
  - Order history
  - Order tracking
  - Invoice download
  - Return/Exchange requests
- **Account Management**:
  - Profile settings
  - Address book
  - Order history

#### School Features
- **School Dashboard**: Overview of school orders and students
- **Order Management**: View and track orders placed for school
- **Student Management**: View students associated with school
- **Product Catalog**: View products mapped to school
- **Reports**: Generate and download order reports (PDF)
- **Settings**: School profile management

### 2. Master Admin Portal

#### Catalog Management
- **Product Mapping**: Create and manage products mapped to schools
- **Product Variants**: Manage sizes and stock per variant
- **Bulk Operations**: Export products (PDF, Excel)
- **Product Details**: Images, descriptions, size charts, videos

#### School Management
- **School CRUD**: Create, edit, manage schools
- **Grade Management**: Define grades per school
- **Product Mapping**: Map products to schools and grades
- **School Settings**: Contact info, logos, status

#### Order Management
- **Order Overview**: All orders across the platform
- **Order Status Updates**: Track order lifecycle
- **Invoice Generation**: PDF invoices
- **Payment Tracking**: View payment details

#### Inventory Management
- **Stock Overview**: View all products and stock levels
- **Inventory Adjustments**: Manual stock adjustments with audit trail
- **Low Stock Alerts**: Threshold-based alerts
- **Reports**: Inventory reports and analytics

#### Shipping Management
- **Shipping Zones**: Define shipping zones by pincode
- **Shipping Settings**: Configure shipping costs and methods
- **Courier Integration**: Track courier assignments

#### System Settings
- **Payment Gateways**: Configure Razorpay and other gateways
- **Invoice Templates**: Customize invoice layouts
- **Email Templates**: Manage email notifications
- **SMS Templates**: Configure SMS notifications
- **App Branding**: Customize logo, colors, branding
- **Audit Logs**: Track all system changes
- **Backups**: Database backup and restore functionality

#### Returns & Exchanges
- **Request Management**: View and process return/exchange requests
- **Approval Workflow**: Approve, deny, or process requests
- **Refund Processing**: Handle refunds via Razorpay
- **Exchange Orders**: Generate new orders for exchanges

### 3. Inventory Admin Portal

#### Order Fulfillment
- **Order Processing**: View and process orders
- **Status Updates**: Update order status (Packed, Shipped, etc.)
- **Packing Slips**: Generate packing slips
- **Shipping Labels**: Print shipping labels
- **Shipping Management**: Assign couriers and tracking numbers

#### Inventory Management
- **Stock Updates**: Update inventory levels
- **Adjustments**: Make inventory adjustments
- **Reports**: Inventory movement reports

#### Returns & Exchanges (View Only)
- View return/exchange requests (read-only)

### 4. Back-to-School Admin Portal

#### Product Management
- **Product CRUD**: Manage Back-to-School products
- **Product Export**: Export products to PDF/Excel
- **Inventory Management**: Update stock levels

#### Order Management
- **Order Processing**: View and process BTS orders
- **Invoice Generation**: Generate invoices
- **Status Updates**: Update order status

#### Reports
- Sales reports and analytics

### 5. Merchandise Admin Portal

#### Product Management
- **Product CRUD**: Manage Merchandise products
- **Product Export**: Export products
- **Inventory Management**: Stock updates

#### Order Management
- **Order Processing**: View and process merchandise orders
- **Status Updates**: Update order status

#### Print Queue
- **Print Job Management**: Manage print jobs for customized merchandise
- **Job Status Tracking**: Track print job progress

#### Reports
- Sales and performance reports

---

## Database Schema

### Core Tables

#### Users & Authentication
- **users**: User accounts (parents, schools, admins)
- **student_profiles**: Student profiles linked to parents
- **user_addresses**: Shipping addresses
- **otps**: OTP verification records

#### Products & Inventory
- **schools**: School information
- **grades**: Grades per school
- **product_mappings**: Main product catalog (supports multiple product types)
- **product_variants**: Size variants for products
- **inventory_adjustments**: Inventory change audit trail

#### Orders & Payments
- **orders**: Order records
- **payments**: Payment transaction records
- **carts**: Shopping cart items
- **return_exchange_requests**: Return/exchange requests

#### Shipping
- **shipping_settings**: Shipping configuration
- **shipping_zones**: Shipping zones
- **shipping_zone_pincodes**: Pincode-based shipping rules

#### System Configuration
- **payment_gateways**: Payment gateway configurations
- **invoice_templates**: Invoice template designs
- **email_templates**: Email notification templates
- **sms_templates**: SMS notification templates
- **app_branding**: Application branding settings
- **audit_logs**: System audit trail
- **backups**: Database backup records

#### Merchandise
- **print_jobs**: Print queue for customized merchandise

#### Notifications
- **notifications**: System notifications

---

## Key Models & Relationships

### User Model
- **Relationships**:
  - `school()` → BelongsTo School (for school users)
  - `studentProfiles()` → HasMany StudentProfile
  - `addresses()` → HasMany UserAddress

### ProductMapping Model
- **Relationships**:
  - `school()` → BelongsTo School
  - `grade()` → BelongsTo Grade
  - `variants()` → HasMany ProductVariant
  - `inventoryAdjustments()` → HasMany InventoryAdjustment

### Order Model
- **Relationships**:
  - `school()` → BelongsTo School
  - `payments()` → HasMany Payment

### Cart Model
- **Relationships**:
  - `user()` → BelongsTo User
  - `studentProfile()` → BelongsTo StudentProfile
  - `product()` → BelongsTo ProductMapping

### Product Inheritance
- **BackToSchool\Product**: Extends ProductMapping with global scope `product_type = 'back_to_school'`
- **Merchandise\Product**: Extends ProductMapping with global scope `product_type = 'merchandised'`

---

## Middleware & Security

### Custom Middleware
- **CheckRole**: Role-based access control
- **CheckMasterAdmin**: Master admin access
- **CheckInventoryAdmin**: Inventory admin access
- **EnsureBackToSchoolAdmin**: BTS admin access
- **EnsureMerchandiseAdmin**: Merchandise admin access
- **EnsureParent**: Parent user access
- **CheckSchool**: School user access
- **PreventBackHistory**: Prevent browser back button after logout
- **EnsureGuestUser**: Guest user access
- **RedirectIfMasterAdmin**: Redirect if already logged in as master admin
- **RedirectIfInventoryAdmin**: Redirect if already logged in as inventory admin
- **RedirectIfStoreAdmin**: Redirect if already logged in as store admin

### Authentication Guards
- **default**: Standard authentication (parents, schools, master admin, inventory admin)
- **bts_admin**: Back-to-School admin guard
- **merch_admin**: Merchandise admin guard

---

## Payment Integration

### Razorpay Integration
- **Service Class**: `RazorpayService`
- **Features**:
  - Order creation
  - Payment verification
  - Refund processing
  - Credentials from database (PaymentGateway model) or .env fallback

### Payment Flow
1. User initiates checkout
2. Razorpay order created via API
3. User completes payment on Razorpay
4. Payment verification callback
5. Order confirmation and invoice generation

---

## Order Status Workflow

### Order Statuses
1. **Order Placed**: Initial order creation
2. **Order Accepted**: Order confirmed by admin
3. **Order Packed**: Items packed and ready
4. **Out for Delivery**: Shipped with tracking
5. **Delivered**: Order delivered to customer
6. **Completed**: Order fully processed

### Return/Exchange Statuses
- **Pending**: Request submitted
- **Approved**: Request approved by admin
- **Received**: Returned item received
- **Refunded**: Refund processed
- **Exchanged**: Exchange order generated
- **Denied**: Request rejected

---

## Key Services

### RazorpayService
- Payment order creation
- Payment verification
- Refund processing
- Credential management

### SmsService
- SMS sending (currently logs to file, ready for provider integration)
- Template-based messaging

### AuditLogger
- System-wide audit trail
- Tracks all critical operations
- Stored in `audit_logs` table

---

## Routes Structure

### Frontend Routes
- `/` - Homepage
- `/login` - Unified login (parents/schools)
- `/register` - Parent registration
- `/parent/*` - Parent dashboard and features
- `/school/*` - School dashboard and features
- `/shop` - Public shop (guest browsing)

### Admin Routes
- `/MasterAdmin/*` - Master admin portal
- `/InventoryAdmin/*` - Inventory admin portal
- `/StoreAdmin/login` - Unified store admin login
- `/BackToSchoolAdmin/*` - BTS admin portal
- `/MerchandiseAdmin/*` - Merchandise admin portal

---

## Key Features Summary

### Multi-Product Type Support
- **Back-to-School Products**: School-specific uniforms
- **Merchandise Products**: Customizable merchandise items
- **Product Mapping**: Flexible product-to-school mapping

### Multi-Tenant Architecture
- School-based product filtering
- School-specific dashboards
- Grade-based product availability

### Advanced Inventory Management
- Variant-based stock tracking
- Low stock alerts
- Inventory adjustment audit trail
- Real-time stock updates

### Comprehensive Order Management
- Multi-status order tracking
- Invoice generation (PDF)
- Payment integration
- Return/Exchange workflow

### Admin Portal Hierarchy
- **Master Admin**: Full system control
- **Inventory Admin**: Order fulfillment focus
- **Store Admins**: Product-specific management (BTS/Merch)

### User Experience Features
- Guest browsing
- OTP-based quick login
- Google OAuth integration
- Multiple student profiles per parent
- Address book management
- Order tracking
- PDF invoice downloads

---

## Configuration Files

### Environment Variables (.env)
- Database configuration
- Razorpay credentials (RAZORPAY_KEY, RAZORPAY_SECRET)
- Google OAuth credentials
- SMS provider credentials
- Mail configuration
- App settings

### Key Config Files
- `config/app.php`: Application settings
- `vite.config.js`: Frontend build configuration
- `bootstrap/app.php`: Middleware and route configuration

---

## Development Commands

```bash
# Setup
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed

# Development
php artisan serve
npm run dev

# Production Build
npm run build

# Database
php artisan migrate
php artisan migrate:rollback
php artisan db:seed

# Cache Management
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## Current Implementation Status

### ✅ Fully Implemented
- Multi-role authentication system
- Student profile management
- Product catalog with variants
- Shopping cart functionality
- Checkout and payment (Razorpay)
- Order management system
- Invoice generation (PDF)
- Return/Exchange system
- Admin portals (all roles)
- Inventory management
- Shipping configuration
- System settings
- Audit logging
- Notification system

### 🔄 Ongoing/Enhancement Areas
- SMS provider integration (currently logging)
- Print queue automation for merchandise
- Advanced reporting and analytics
- Email template customization
- Backup automation

---

## Security Features

- Role-based access control (RBAC)
- Multi-guard authentication
- OTP verification
- Password hashing
- CSRF protection
- SQL injection prevention (Eloquent ORM)
- XSS protection (Blade templating)
- Audit trail for sensitive operations
- Secure payment processing

---

## File Uploads & Storage

- Product images stored in `storage/app/public`
- Student profile photos
- Return/exchange request photos
- Invoice PDFs
- Backup files

---

## Notification System

- In-app notifications
- Email notifications (template-based)
- SMS notifications (template-based, ready for provider)
- Order status updates
- Payment confirmations
- Return/exchange status updates

---

## Reporting & Analytics

- Order reports (Master Admin, Schools)
- Inventory reports
- Sales analytics
- PDF export functionality
- Excel export (for products)

---

This is a comprehensive, production-ready e-commerce platform with multi-tenant support, advanced inventory management, and a complete admin hierarchy. The codebase follows Laravel best practices and is well-structured for scalability and maintenance.


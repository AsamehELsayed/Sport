# SportApp - Comprehensive E-commerce Platform

## Overview

SportApp is a modern, multi-tenant e-commerce platform specifically designed for sports equipment and apparel retailers. Built with Laravel (PHP) and Vue.js, it provides a complete solution for online sports retail businesses with advanced features including email marketing, customer management, and comprehensive analytics.

## 🚀 Key Features

### E-commerce Core
- **Product Management**: Complete catalog management with variants, categories, and brands
- **Inventory Tracking**: Real-time stock management with variant-level tracking
- **Order Processing**: Full order lifecycle management with status tracking
- **Shopping Cart**: Advanced cart functionality with save-for-later capabilities
- **Wishlist System**: Customer wishlist management
- **Invoice Generation**: Automated PDF invoice creation

### Multi-Tenant Architecture
- **Store Isolation**: Each store operates independently
- **Custom Branding**: Store-specific logos, colors, and branding
- **Independent Data**: Separate product catalogs, customers, and orders per store

### Email Marketing System
- **Template Management**: Drag-and-drop email template creation
- **Campaign Scheduling**: Automated email campaign scheduling
- **Analytics Tracking**: Open rates, click rates, and delivery tracking
- **Customer Segmentation**: Target campaigns to specific customer groups
- **SMTP Configuration**: Flexible email provider integration

### Customer Management
- **Customer Profiles**: Detailed customer information and preferences
- **Customer Groups**: Organize customers for targeted marketing
- **Order History**: Complete purchase history and tracking
- **Email Preferences**: Granular email opt-in/opt-out management

### Analytics & Reporting
- **Sales Analytics**: Revenue tracking and trend analysis
- **Customer Analytics**: Behavior analysis and lifetime value
- **Product Performance**: Best-selling products and inventory insights
- **Marketing Analytics**: Campaign performance and ROI tracking

## 📊 Documentation

### Entity Relationship Diagram (ERD)
- **[Detailed ERD Documentation](ERD_Diagram.md)** - Complete database schema documentation
- **[Visual ERD Diagram](ERD_Visual_Diagram.md)** - Mermaid-based visual representation

### User Stories
- **[User Stories Documentation](User_Stories.md)** - Comprehensive user stories for all user roles

## 🏗️ Architecture

### Technology Stack
- **Backend**: Laravel 11 (PHP 8.2+)
- **Frontend**: Vue.js 3 with Inertia.js
- **Database**: MySQL/PostgreSQL
- **Styling**: Tailwind CSS
- **Email**: Laravel Mail with queue support
- **File Storage**: Local/Cloud storage support

### Database Schema
The application uses 15 main tables with comprehensive relationships:

#### Core Tables
- `users` - Multi-tenant user management
- `products` - Product catalog with variants
- `categories` - Product categorization
- `brands` - Brand management
- `orders` - Order processing
- `order_items` - Order line items
- `invoices` - Invoice generation

#### Marketing Tables
- `email_templates` - Email template management
- `email_campaigns` - Campaign creation and scheduling
- `email_campaign_recipients` - Recipient tracking
- `email_settings` - SMTP configuration
- `customer_groups` - Customer segmentation

#### Supporting Tables
- `product_variants` - Size/color variants
- `wishlists` - Customer wishlists
- `customer_group_user` - Customer group assignments

## 👥 User Roles

### 1. Guest Users
- Browse products and categories
- View product details
- Register for accounts
- Access sales and promotions

### 2. Registered Customers
- Complete shopping experience
- Manage profiles and addresses
- Track orders and history
- Manage wishlists
- Control email preferences

### 3. Store Administrators
- Manage product catalogs
- Process orders and invoices
- Create email campaigns
- Analyze sales and customer data
- Configure store settings

### 4. System Administrators
- Manage multi-tenant infrastructure
- Monitor system performance
- Handle security and compliance
- Manage data backups

## 🚀 Getting Started

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+ or PostgreSQL 13+
- Redis (for queue processing)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SportApp
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

### Configuration

#### Email Marketing Setup
1. Configure SMTP settings in admin panel
2. Set up email templates
3. Configure sending schedules
4. Test email delivery

#### Multi-Tenant Setup
1. Create store administrator accounts
2. Configure store branding
3. Set up product catalogs
4. Configure payment methods

## 📁 Project Structure

```
SportApp/
├── app/
│   ├── Http/Controllers/     # Application controllers
│   ├── Models/              # Eloquent models
│   ├── Jobs/                # Queue jobs
│   └── Console/Commands/    # Artisan commands
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/            # Database seeders
│   └── factories/          # Model factories
├── resources/
│   ├── js/                 # Vue.js components
│   ├── css/                # Stylesheets
│   └── views/              # Blade templates
├── routes/                 # Application routes
└── storage/                # File storage
```

## 🔧 Key Features Implementation

### Multi-Tenancy
- User-based tenant isolation
- Store-specific configurations
- Independent data management
- Custom branding per store

### Email Marketing
- Template-based email creation
- Campaign scheduling and automation
- Delivery tracking and analytics
- Customer segmentation
- SMTP provider flexibility

### E-commerce
- Product variant management
- Real-time inventory tracking
- Order status management
- Invoice generation
- Customer wishlists

### Analytics
- Sales performance tracking
- Customer behavior analysis
- Product performance insights
- Marketing campaign analytics

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --filter=ProductTest

# Run with coverage
php artisan test --coverage
```

### Test Structure
- **Feature Tests**: End-to-end functionality testing
- **Unit Tests**: Individual component testing
- **Database Tests**: Data integrity testing

## 📈 Performance Optimization

### Database Optimization
- Indexed foreign key columns
- Optimized query patterns
- Efficient data relationships
- Proper constraint management

### Application Performance
- Caching strategies
- Queue processing for heavy tasks
- Asset optimization
- Lazy loading implementation

## 🔒 Security Features

### Data Protection
- Encrypted sensitive data
- Secure password hashing
- CSRF protection
- SQL injection prevention
- XSS protection

### Access Control
- Role-based permissions
- Multi-tenant data isolation
- Secure authentication
- Session management

## 📊 Monitoring & Analytics

### System Monitoring
- Application performance metrics
- Database query optimization
- Error tracking and logging
- Uptime monitoring

### Business Analytics
- Sales performance tracking
- Customer behavior analysis
- Product performance insights
- Marketing campaign effectiveness

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Check the documentation
- Review the user stories for feature requests

## 🔄 Version History

### v1.0.0 (Current)
- Initial release with core e-commerce features
- Multi-tenant architecture
- Email marketing system
- Customer management
- Analytics and reporting

---

**SportApp** - Empowering sports retailers with a comprehensive e-commerce solution.

-- SportApp Database Schema for MySQL Workbench
-- Multi-tenant E-commerce Platform for Sports Equipment

-- Drop database if exists (for fresh installation)
-- DROP DATABASE IF EXISTS sportapp;

-- Create database
CREATE DATABASE IF NOT EXISTS sportapp
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sportapp;

-- =====================================================
-- CORE TABLES
-- =====================================================

-- Users table (Multi-tenant support)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    phone VARCHAR(20) NULL,
    date_of_birth DATE NULL,
    address_line_1 VARCHAR(255) NULL,
    address_line_2 VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    postal_code VARCHAR(20) NULL,
    country VARCHAR(100) DEFAULT 'US',
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say') NULL,
    bio TEXT NULL,
    profile_photo VARCHAR(255) NULL,
    marketing_emails BOOLEAN DEFAULT TRUE,
    order_updates BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    preferred_sports VARCHAR(255) NULL,
    timezone VARCHAR(100) NULL,
    page_name VARCHAR(255) NULL,
    website_name VARCHAR(255) NULL,
    logo_path VARCHAR(255) NULL,
    primary_color VARCHAR(7) DEFAULT '#3B82F6',
    secondary_color VARCHAR(7) DEFAULT '#1F2937',
    accent_color VARCHAR(7) DEFAULT '#10B981',
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_is_admin (is_admin),
    INDEX idx_marketing_emails (marketing_emails),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    icon VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Brands table
CREATE TABLE brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    logo VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products table
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    images JSON NULL,
    category_id BIGINT UNSIGNED NULL,
    brand_id BIGINT UNSIGNED NULL,
    sku VARCHAR(255) NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    discount TINYINT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,

    INDEX idx_category_id (category_id),
    INDEX idx_brand_id (brand_id),
    INDEX idx_sku (sku),
    INDEX idx_is_active (is_active),
    INDEX idx_is_featured (is_featured),
    INDEX idx_price (price),
    INDEX idx_discount (discount)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product variants table
CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    size VARCHAR(50) NULL,
    color VARCHAR(50) NULL,
    sku VARCHAR(255) NULL UNIQUE,
    stock INT DEFAULT 0,
    price_adjustment DECIMAL(10,2) DEFAULT 0.00,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    UNIQUE KEY product_variant_unique (product_id, size, color),
    INDEX idx_product_id (product_id),
    INDEX idx_sku (sku),
    INDEX idx_stock (stock),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders table
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    order_number VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0,
    shipping DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,

    -- Customer Information
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NULL,

    -- Shipping Address
    shipping_address_line_1 VARCHAR(255) NOT NULL,
    shipping_address_line_2 VARCHAR(255) NULL,
    shipping_city VARCHAR(100) NOT NULL,
    shipping_state VARCHAR(100) NOT NULL,
    shipping_postal_code VARCHAR(20) NOT NULL,
    shipping_country VARCHAR(100) NOT NULL,

    -- Payment Information
    payment_method VARCHAR(100) DEFAULT 'credit_card',
    payment_status VARCHAR(100) DEFAULT 'pending',
    transaction_id VARCHAR(255) NULL,

    -- Order Notes
    notes TEXT NULL,
    admin_notes TEXT NULL,

    -- Timestamps
    processed_at TIMESTAMP NULL,
    shipped_at TIMESTAMP NULL,
    delivered_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_user_id (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_customer_email (customer_email),
    INDEX idx_created_at (created_at),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order items table
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NULL,
    product_name VARCHAR(255) NOT NULL,
    product_sku VARCHAR(255) NULL,
    quantity INT NOT NULL,
    size VARCHAR(50) NULL,
    color VARCHAR(50) NULL,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    subtotal DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,

    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    INDEX idx_product_sku (product_sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Invoices table
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    invoice_number VARCHAR(255) NOT NULL UNIQUE,
    invoice_date DATE NOT NULL,
    due_date DATE NULL,
    status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',
    subtotal DECIMAL(10,2) NOT NULL,
    tax DECIMAL(10,2) DEFAULT 0,
    shipping DECIMAL(10,2) DEFAULT 0,
    discount DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    notes TEXT NULL,
    terms_conditions TEXT NULL,
    payment_method VARCHAR(100) NULL,
    payment_reference VARCHAR(255) NULL,
    paid_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,

    INDEX idx_order_id (order_id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_status (status),
    INDEX idx_invoice_date (invoice_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wishlists table
CREATE TABLE wishlists (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    UNIQUE KEY user_product_unique (user_id, product_id),
    INDEX idx_user_id (user_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- EMAIL MARKETING SYSTEM
-- =====================================================

-- Email templates table
CREATE TABLE email_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type VARCHAR(100) DEFAULT 'custom',
    variables JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email campaigns table
CREATE TABLE email_campaigns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    email_template_id BIGINT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'scheduled', 'sending', 'sent', 'cancelled') DEFAULT 'draft',
    scheduled_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    total_recipients INT DEFAULT 0,
    sent_count INT DEFAULT 0,
    opened_count INT DEFAULT 0,
    clicked_count INT DEFAULT 0,
    bounced_count INT DEFAULT 0,
    recipient_filters JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (email_template_id) REFERENCES email_templates(id) ON DELETE SET NULL,

    INDEX idx_user_id (user_id),
    INDEX idx_email_template_id (email_template_id),
    INDEX idx_status (status),
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email campaign recipients table
CREATE TABLE email_campaign_recipients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email_campaign_id BIGINT UNSIGNED NOT NULL,
    recipient_id BIGINT UNSIGNED NOT NULL,
    status ENUM('pending', 'sent', 'delivered', 'opened', 'clicked', 'bounced', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    opened_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    error_message TEXT NULL,
    tracking_id VARCHAR(255) NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (email_campaign_id) REFERENCES email_campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_email_campaign_id (email_campaign_id),
    INDEX idx_recipient_id (recipient_id),
    INDEX idx_status (status),
    INDEX idx_tracking_id (tracking_id),
    INDEX idx_sent_at (sent_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email settings table
CREATE TABLE email_settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    mail_driver VARCHAR(100) DEFAULT 'smtp',
    mail_host VARCHAR(255) NULL,
    mail_port INT NULL,
    mail_username VARCHAR(255) NULL,
    mail_password VARCHAR(255) NULL,
    mail_encryption VARCHAR(50) DEFAULT 'tls',
    mail_from_address VARCHAR(255) NULL,
    mail_from_name VARCHAR(255) NULL,
    timezone VARCHAR(100) DEFAULT 'UTC',
    sending_schedule JSON NULL,
    daily_send_limit INT DEFAULT 1000,
    track_opens BOOLEAN DEFAULT TRUE,
    track_clicks BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    UNIQUE KEY user_email_settings_unique (user_id),
    INDEX idx_user_id (user_id),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CUSTOMER MANAGEMENT
-- =====================================================

-- Customer groups table
CREATE TABLE customer_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    color VARCHAR(7) DEFAULT '#3B82F6',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_name (name),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Customer group user pivot table
CREATE TABLE customer_group_user (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_group_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (customer_group_id) REFERENCES customer_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    UNIQUE KEY customer_group_user_unique (customer_group_id, user_id),
    INDEX idx_customer_group_id (customer_group_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- LARAVEL SYSTEM TABLES
-- =====================================================

-- Password reset tokens table
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions table
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_user_id (user_id),
    INDEX idx_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Jobs table for queue processing
CREATE TABLE jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,

    INDEX idx_queue (queue),
    INDEX idx_reserved_at (reserved_at),
    INDEX idx_available_at (available_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Failed jobs table
CREATE TABLE failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_failed_at (failed_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache table
CREATE TABLE cache (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,

    INDEX idx_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cache locks table
CREATE TABLE cache_locks (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL,

    INDEX idx_expiration (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- SAMPLE DATA INSERTS
-- =====================================================

-- Insert sample categories
INSERT INTO categories (name, slug, description, is_active, sort_order) VALUES
('Running', 'running', 'Running shoes and accessories', TRUE, 1),
('Basketball', 'basketball', 'Basketball equipment and apparel', TRUE, 2),
('Soccer', 'soccer', 'Soccer balls and gear', TRUE, 3),
('Fitness', 'fitness', 'Fitness equipment and accessories', TRUE, 4),
('Swimming', 'swimming', 'Swimming gear and accessories', TRUE, 5);

-- Insert sample brands
INSERT INTO brands (name, slug, description, is_active, sort_order) VALUES
('Nike', 'nike', 'Just Do It', TRUE, 1),
('Adidas', 'adidas', 'Impossible is Nothing', TRUE, 2),
('Under Armour', 'under-armour', 'The Only Way Is Through', TRUE, 3),
('Puma', 'puma', 'Forever Faster', TRUE, 4),
('Reebok', 'reebok', 'Be More Human', TRUE, 5);

-- Insert sample customer groups
INSERT INTO customer_groups (name, description, color, is_active) VALUES
('VIP Customers', 'High-value customers with special privileges', '#FFD700', TRUE),
('New Customers', 'Recently registered customers', '#87CEEB', TRUE),
('Loyal Customers', 'Customers with multiple purchases', '#98FB98', TRUE),
('Inactive Customers', 'Customers who haven\'t purchased recently', '#D3D3D3', TRUE);

-- Insert sample admin user
INSERT INTO users (name, email, password, is_admin, marketing_emails, order_updates) VALUES
('Admin User', 'admin@sportapp.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE, TRUE, TRUE);

-- Insert sample email settings for admin
INSERT INTO email_settings (user_id, mail_driver, mail_host, mail_port, mail_encryption, is_active) VALUES
(1, 'smtp', 'smtp.mailtrap.io', 2525, 'tls', FALSE);

-- =====================================================
-- VIEWS FOR ANALYTICS
-- =====================================================

-- Sales summary view
CREATE VIEW sales_summary AS
SELECT
    DATE(created_at) as sale_date,
    COUNT(*) as total_orders,
    SUM(total) as total_revenue,
    AVG(total) as average_order_value,
    COUNT(DISTINCT user_id) as unique_customers
FROM orders
WHERE status != 'cancelled'
GROUP BY DATE(created_at)
ORDER BY sale_date DESC;

-- Product performance view
CREATE VIEW product_performance AS
SELECT
    p.id,
    p.name,
    p.sku,
    c.name as category_name,
    b.name as brand_name,
    COUNT(oi.id) as times_ordered,
    SUM(oi.quantity) as total_quantity_sold,
    SUM(oi.subtotal) as total_revenue,
    AVG(oi.price) as average_price
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
LEFT JOIN brands b ON p.brand_id = b.id
LEFT JOIN order_items oi ON p.id = oi.product_id
LEFT JOIN orders o ON oi.order_id = o.id AND o.status != 'cancelled'
GROUP BY p.id, p.name, p.sku, c.name, b.name
ORDER BY total_revenue DESC;

-- Customer analytics view
CREATE VIEW customer_analytics AS
SELECT
    u.id,
    u.name,
    u.email,
    COUNT(o.id) as total_orders,
    SUM(o.total) as total_spent,
    AVG(o.total) as average_order_value,
    MAX(o.created_at) as last_order_date,
    MIN(o.created_at) as first_order_date,
    DATEDIFF(MAX(o.created_at), MIN(o.created_at)) as customer_lifetime_days
FROM users u
LEFT JOIN orders o ON u.id = o.user_id AND o.status != 'cancelled'
WHERE u.is_admin = FALSE
GROUP BY u.id, u.name, u.email
ORDER BY total_spent DESC;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

DELIMITER //

-- Procedure to generate order number
CREATE PROCEDURE GenerateOrderNumber(OUT order_number VARCHAR(255))
BEGIN
    DECLARE next_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(order_number, 9) AS UNSIGNED)), 0) + 1 INTO next_id
    FROM orders
    WHERE order_number LIKE 'ORD-%';

    SET order_number = CONCAT('ORD-', LPAD(next_id, 8, '0'));
END //

-- Procedure to generate invoice number
CREATE PROCEDURE GenerateInvoiceNumber(OUT invoice_number VARCHAR(255))
BEGIN
    DECLARE next_id INT;
    SELECT COALESCE(MAX(CAST(SUBSTRING(invoice_number, 10) AS UNSIGNED)), 0) + 1 INTO next_id
    FROM invoices
    WHERE invoice_number LIKE 'INV-%';

    SET invoice_number = CONCAT('INV-', LPAD(next_id, 8, '0'));
END //

-- Procedure to update product stock
CREATE PROCEDURE UpdateProductStock(
    IN p_product_id BIGINT,
    IN p_size VARCHAR(50),
    IN p_color VARCHAR(50),
    IN p_quantity INT
)
BEGIN
    DECLARE current_stock INT;

    SELECT stock INTO current_stock
    FROM product_variants
    WHERE product_id = p_product_id
    AND (size = p_size OR (size IS NULL AND p_size IS NULL))
    AND (color = p_color OR (color IS NULL AND p_color IS NULL))
    LIMIT 1;

    IF current_stock IS NOT NULL THEN
        UPDATE product_variants
        SET stock = GREATEST(0, current_stock - p_quantity)
        WHERE product_id = p_product_id
        AND (size = p_size OR (size IS NULL AND p_size IS NULL))
        AND (color = p_color OR (color IS NULL AND p_color IS NULL));
    END IF;
END //

DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

DELIMITER //

-- Trigger to update order totals when order items change
CREATE TRIGGER update_order_totals_after_insert
AFTER INSERT ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders
    SET subtotal = (
        SELECT SUM(subtotal)
        FROM order_items
        WHERE order_id = NEW.order_id
    ),
    total = (
        SELECT SUM(subtotal) + tax + shipping
        FROM order_items
        WHERE order_id = NEW.order_id
    ) + (
        SELECT tax + shipping
        FROM orders
        WHERE id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END //

-- Trigger to update order totals when order items are updated
CREATE TRIGGER update_order_totals_after_update
AFTER UPDATE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders
    SET subtotal = (
        SELECT SUM(subtotal)
        FROM order_items
        WHERE order_id = NEW.order_id
    ),
    total = (
        SELECT SUM(subtotal) + tax + shipping
        FROM order_items
        WHERE order_id = NEW.order_id
    ) + (
        SELECT tax + shipping
        FROM orders
        WHERE id = NEW.order_id
    )
    WHERE id = NEW.order_id;
END //

-- Trigger to update order totals when order items are deleted
CREATE TRIGGER update_order_totals_after_delete
AFTER DELETE ON order_items
FOR EACH ROW
BEGIN
    UPDATE orders
    SET subtotal = (
        SELECT COALESCE(SUM(subtotal), 0)
        FROM order_items
        WHERE order_id = OLD.order_id
    ),
    total = (
        SELECT COALESCE(SUM(subtotal), 0) + tax + shipping
        FROM order_items
        WHERE order_id = OLD.order_id
    ) + (
        SELECT tax + shipping
        FROM orders
        WHERE id = OLD.order_id
    )
    WHERE id = OLD.order_id;
END //

DELIMITER ;

-- =====================================================
-- COMMENTS AND DOCUMENTATION
-- =====================================================

-- Add comments to tables for documentation
ALTER TABLE users COMMENT = 'Multi-tenant user management with customer and admin roles';
ALTER TABLE products COMMENT = 'Product catalog with category and brand relationships';
ALTER TABLE orders COMMENT = 'Order management with customer and shipping information';
ALTER TABLE email_campaigns COMMENT = 'Email marketing campaigns with tracking and analytics';
ALTER TABLE customer_groups COMMENT = 'Customer segmentation for targeted marketing';

-- Add comments to key columns
ALTER TABLE users MODIFY COLUMN is_admin BOOLEAN DEFAULT FALSE COMMENT 'Determines if user is a store administrator';
ALTER TABLE products MODIFY COLUMN is_active BOOLEAN DEFAULT TRUE COMMENT 'Controls product visibility in catalog';
ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending' COMMENT 'Order lifecycle status';
ALTER TABLE email_campaigns MODIFY COLUMN status ENUM('draft', 'scheduled', 'sending', 'sent', 'cancelled') DEFAULT 'draft' COMMENT 'Campaign execution status';

-- =====================================================
-- FINAL NOTES
-- =====================================================

/*
This SQL script creates a complete SportApp database schema for MySQL Workbench.

Key Features:
- Multi-tenant architecture with user-based store isolation
- Complete e-commerce functionality (products, orders, invoices)
- Advanced email marketing system with tracking
- Customer management and segmentation
- Comprehensive analytics views
- Stored procedures for common operations
- Triggers for data integrity
- Sample data for testing

To use in MySQL Workbench:
1. Open MySQL Workbench
2. Create a new connection to your MySQL server
3. Open this SQL file
4. Execute the script
5. The database will be created with all tables, relationships, and sample data

The schema supports:
- Multiple stores per user (multi-tenancy)
- Product variants with stock tracking
- Order management with status tracking
- Email marketing with analytics
- Customer segmentation
- Comprehensive reporting
*/

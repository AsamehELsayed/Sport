# SportApp - Entity Relationship Diagram (ERD)

## Overview
SportApp is a comprehensive e-commerce platform for sports equipment and apparel with multi-tenant capabilities, email marketing features, and customer management.

## Database Schema

### Core Entities

#### 1. Users Table
```
users
├── id (PK)
├── name
├── email (unique)
├── email_verified_at
├── password
├── is_admin (boolean)
├── phone
├── date_of_birth
├── address_line_1
├── address_line_2
├── city
├── state
├── postal_code
├── country
├── gender (enum: male, female, other, prefer_not_to_say)
├── bio
├── profile_photo
├── marketing_emails (boolean)
├── order_updates (boolean)
├── last_login_at
├── preferred_sports
├── timezone
├── page_name
├── website_name
├── logo_path
├── primary_color
├── secondary_color
├── accent_color
├── remember_token
├── created_at
└── updated_at
```

#### 2. Categories Table
```
categories
├── id (PK)
├── name
├── slug (unique)
├── description
├── image
├── icon
├── is_active (boolean)
├── sort_order
├── created_at
└── updated_at
```

#### 3. Brands Table
```
brands
├── id (PK)
├── name
├── slug (unique)
├── description
├── logo
├── website
├── is_active (boolean)
├── sort_order
├── created_at
└── updated_at
```

#### 4. Products Table
```
products
├── id (PK)
├── name
├── description
├── price (decimal)
├── images (json)
├── category_id (FK -> categories.id)
├── brand_id (FK -> brands.id)
├── sku (unique)
├── is_active (boolean)
├── is_featured (boolean)
├── discount (tinyint)
├── created_at
└── updated_at
```

#### 5. Product Variants Table
```
product_variants
├── id (PK)
├── product_id (FK -> products.id)
├── size
├── color
├── sku (unique)
├── stock (integer)
├── price_adjustment (decimal)
├── is_active (boolean)
├── created_at
└── updated_at
```

#### 6. Orders Table
```
orders
├── id (PK)
├── user_id (FK -> users.id)
├── order_number (unique)
├── status (enum: pending, processing, shipped, delivered, cancelled)
├── subtotal (decimal)
├── tax (decimal)
├── shipping (decimal)
├── total (decimal)
├── customer_name
├── customer_email
├── customer_phone
├── shipping_address_line_1
├── shipping_address_line_2
├── shipping_city
├── shipping_state
├── shipping_postal_code
├── shipping_country
├── payment_method
├── payment_status
├── transaction_id
├── notes
├── admin_notes
├── processed_at
├── shipped_at
├── delivered_at
├── created_at
└── updated_at
```

#### 7. Order Items Table
```
order_items
├── id (PK)
├── order_id (FK -> orders.id)
├── product_id (FK -> products.id)
├── product_name
├── product_sku
├── quantity
├── size
├── color
├── price (decimal)
├── original_price (decimal)
├── discount_amount (decimal)
├── discount_percentage (decimal)
├── subtotal (decimal)
├── notes
├── created_at
└── updated_at
```

#### 8. Invoices Table
```
invoices
├── id (PK)
├── order_id (FK -> orders.id)
├── invoice_number (unique)
├── invoice_date
├── due_date
├── status (enum: draft, sent, paid, overdue, cancelled)
├── subtotal (decimal)
├── tax (decimal)
├── shipping (decimal)
├── discount (decimal)
├── total (decimal)
├── notes
├── terms_conditions
├── payment_method
├── payment_reference
├── paid_at
├── sent_at
├── created_at
└── updated_at
```

#### 9. Wishlists Table
```
wishlists
├── id (PK)
├── user_id (FK -> users.id)
├── product_id (FK -> products.id)
├── created_at
└── updated_at
```

### Email Marketing System

#### 10. Email Templates Table
```
email_templates
├── id (PK)
├── user_id (FK -> users.id)
├── name
├── subject
├── content
├── type (default: custom)
├── variables (json)
├── is_active (boolean)
├── created_at
└── updated_at
```

#### 11. Email Campaigns Table
```
email_campaigns
├── id (PK)
├── user_id (FK -> users.id)
├── email_template_id (FK -> email_templates.id)
├── name
├── subject
├── content
├── status (enum: draft, scheduled, sending, sent, cancelled)
├── scheduled_at
├── sent_at
├── total_recipients
├── sent_count
├── opened_count
├── clicked_count
├── bounced_count
├── recipient_filters (json)
├── created_at
└── updated_at
```

#### 12. Email Campaign Recipients Table
```
email_campaign_recipients
├── id (PK)
├── email_campaign_id (FK -> email_campaigns.id)
├── recipient_id (FK -> users.id)
├── status (enum: pending, sent, delivered, opened, clicked, bounced, failed)
├── sent_at
├── opened_at
├── clicked_at
├── error_message
├── tracking_id (unique)
├── created_at
└── updated_at
```

#### 13. Email Settings Table
```
email_settings
├── id (PK)
├── user_id (FK -> users.id)
├── mail_driver
├── mail_host
├── mail_port
├── mail_username
├── mail_password
├── mail_encryption
├── mail_from_address
├── mail_from_name
├── timezone
├── sending_schedule (json)
├── daily_send_limit
├── track_opens (boolean)
├── track_clicks (boolean)
├── is_active (boolean)
├── created_at
└── updated_at
```

### Customer Management

#### 14. Customer Groups Table
```
customer_groups
├── id (PK)
├── name
├── description
├── color
├── is_active (boolean)
├── created_at
└── updated_at
```

#### 15. Customer Group User (Pivot Table)
```
customer_group_user
├── id (PK)
├── customer_group_id (FK -> customer_groups.id)
├── user_id (FK -> users.id)
├── created_at
└── updated_at
```

## Relationships

### One-to-Many Relationships
- **User → Orders**: One user can have many orders
- **User → Email Templates**: One user (admin) can create many email templates
- **User → Email Campaigns**: One user (admin) can create many email campaigns
- **User → Email Settings**: One user (admin) can have one email settings configuration
- **Category → Products**: One category can have many products
- **Brand → Products**: One brand can have many products
- **Product → Product Variants**: One product can have many variants
- **Order → Order Items**: One order can have many order items
- **Order → Invoice**: One order can have one invoice
- **Email Campaign → Email Campaign Recipients**: One campaign can have many recipients

### Many-to-Many Relationships
- **Users ↔ Customer Groups**: Many users can belong to many customer groups (via customer_group_user pivot table)
- **Users ↔ Products**: Many users can wishlist many products (via wishlists table)

### Foreign Key Constraints
- `products.category_id` → `categories.id` (on delete: set null)
- `products.brand_id` → `brands.id` (on delete: set null)
- `orders.user_id` → `users.id` (on delete: cascade)
- `order_items.order_id` → `orders.id` (on delete: cascade)
- `order_items.product_id` → `products.id` (on delete: set null)
- `product_variants.product_id` → `products.id` (on delete: cascade)
- `wishlists.user_id` → `users.id` (on delete: cascade)
- `wishlists.product_id` → `products.id` (on delete: cascade)
- `invoices.order_id` → `orders.id` (on delete: cascade)
- `email_templates.user_id` → `users.id` (on delete: cascade)
- `email_campaigns.user_id` → `users.id` (on delete: cascade)
- `email_campaigns.email_template_id` → `email_templates.id` (on delete: set null)
- `email_campaign_recipients.email_campaign_id` → `email_campaigns.id` (on delete: cascade)
- `email_campaign_recipients.recipient_id` → `users.id` (on delete: cascade)
- `email_settings.user_id` → `users.id` (on delete: cascade)
- `customer_group_user.customer_group_id` → `customer_groups.id` (on delete: cascade)
- `customer_group_user.user_id` → `users.id` (on delete: cascade)

## Unique Constraints
- `users.email` (unique)
- `products.sku` (unique)
- `product_variants.sku` (unique)
- `orders.order_number` (unique)
- `invoices.invoice_number` (unique)
- `categories.slug` (unique)
- `brands.slug` (unique)
- `email_campaign_recipients.tracking_id` (unique)
- `wishlists.user_id + product_id` (composite unique)
- `customer_group_user.customer_group_id + user_id` (composite unique)
- `product_variants.product_id + size + color` (composite unique)

## Indexes
- `users.email` (unique index)
- `orders.user_id` (index)
- `order_items.order_id` (index)
- `product_variants.product_id` (index)
- `wishlists.user_id` (index)
- `email_campaign_recipients.email_campaign_id` (index)
- `email_campaign_recipients.recipient_id` (index)
- `sessions.user_id` (index)
- `sessions.last_activity` (index)

## Notes
- The system supports multi-tenancy through the `users` table where each user can be an admin with their own store
- Email marketing system includes tracking capabilities for opens and clicks
- Product variants support different sizes and colors with individual stock levels
- Customer groups allow for targeted email campaigns
- The system includes comprehensive order management with invoice generation
- Wishlist functionality allows customers to save products for later purchase

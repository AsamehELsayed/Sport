# SportApp - Visual Entity Relationship Diagram

## Database Schema Visualization

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        boolean is_admin
        string phone
        date date_of_birth
        string address_line_1
        string address_line_2
        string city
        string state
        string postal_code
        string country
        enum gender
        text bio
        string profile_photo
        boolean marketing_emails
        boolean order_updates
        timestamp last_login_at
        string preferred_sports
        string timezone
        string page_name
        string website_name
        string logo_path
        string primary_color
        string secondary_color
        string accent_color
        string remember_token
        timestamps
    }

    categories {
        bigint id PK
        string name
        string slug UK
        text description
        string image
        string icon
        boolean is_active
        integer sort_order
        timestamps
    }

    brands {
        bigint id PK
        string name
        string slug UK
        text description
        string logo
        string website
        boolean is_active
        integer sort_order
        timestamps
    }

    products {
        bigint id PK
        string name
        text description
        decimal price
        json images
        bigint category_id FK
        bigint brand_id FK
        string sku UK
        boolean is_active
        boolean is_featured
        tinyint discount
        timestamps
    }

    product_variants {
        bigint id PK
        bigint product_id FK
        string size
        string color
        string sku UK
        integer stock
        decimal price_adjustment
        boolean is_active
        timestamps
    }

    orders {
        bigint id PK
        bigint user_id FK
        string order_number UK
        enum status
        decimal subtotal
        decimal tax
        decimal shipping
        decimal total
        string customer_name
        string customer_email
        string customer_phone
        string shipping_address_line_1
        string shipping_address_line_2
        string shipping_city
        string shipping_state
        string shipping_postal_code
        string shipping_country
        string payment_method
        string payment_status
        string transaction_id
        text notes
        text admin_notes
        timestamp processed_at
        timestamp shipped_at
        timestamp delivered_at
        timestamps
    }

    order_items {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        string product_name
        string product_sku
        integer quantity
        string size
        string color
        decimal price
        decimal original_price
        decimal discount_amount
        decimal discount_percentage
        decimal subtotal
        text notes
        timestamps
    }

    invoices {
        bigint id PK
        bigint order_id FK
        string invoice_number UK
        date invoice_date
        date due_date
        enum status
        decimal subtotal
        decimal tax
        decimal shipping
        decimal discount
        decimal total
        text notes
        text terms_conditions
        string payment_method
        string payment_reference
        timestamp paid_at
        timestamp sent_at
        timestamps
    }

    wishlists {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
        timestamps
    }

    email_templates {
        bigint id PK
        bigint user_id FK
        string name
        string subject
        text content
        string type
        json variables
        boolean is_active
        timestamps
    }

    email_campaigns {
        bigint id PK
        bigint user_id FK
        bigint email_template_id FK
        string name
        string subject
        text content
        enum status
        timestamp scheduled_at
        timestamp sent_at
        integer total_recipients
        integer sent_count
        integer opened_count
        integer clicked_count
        integer bounced_count
        json recipient_filters
        timestamps
    }

    email_campaign_recipients {
        bigint id PK
        bigint email_campaign_id FK
        bigint recipient_id FK
        enum status
        timestamp sent_at
        timestamp opened_at
        timestamp clicked_at
        text error_message
        string tracking_id UK
        timestamps
    }

    email_settings {
        bigint id PK
        bigint user_id FK
        string mail_driver
        string mail_host
        integer mail_port
        string mail_username
        string mail_password
        string mail_encryption
        string mail_from_address
        string mail_from_name
        string timezone
        json sending_schedule
        integer daily_send_limit
        boolean track_opens
        boolean track_clicks
        boolean is_active
        timestamps
    }

    customer_groups {
        bigint id PK
        string name
        text description
        string color
        boolean is_active
        timestamps
    }

    customer_group_user {
        bigint id PK
        bigint customer_group_id FK
        bigint user_id FK
        timestamps
    }

    %% Relationships
    users ||--o{ orders : "places"
    users ||--o{ email_templates : "creates"
    users ||--o{ email_campaigns : "creates"
    users ||--o{ email_settings : "configures"
    users ||--o{ wishlists : "has"
    users }o--o{ customer_groups : "belongs_to"

    categories ||--o{ products : "contains"
    brands ||--o{ products : "manufactures"
    products ||--o{ product_variants : "has"
    products ||--o{ order_items : "included_in"
    products ||--o{ wishlists : "saved_in"

    orders ||--o{ order_items : "contains"
    orders ||--|| invoices : "generates"

    email_campaigns ||--o{ email_campaign_recipients : "sends_to"
    email_templates ||--o{ email_campaigns : "used_in"

    customer_groups }o--o{ users : "includes"
```

## Key Features of the Schema

### Multi-Tenant Architecture
- Each `user` can be a store administrator with their own store
- Store-specific data is linked through `user_id` foreign keys
- Independent email marketing, product catalogs, and customer management

### E-commerce Core
- **Products** with variants (sizes, colors, stock levels)
- **Orders** with detailed line items and pricing
- **Invoices** for order documentation
- **Wishlists** for customer product saving

### Email Marketing System
- **Templates** for reusable email designs
- **Campaigns** with scheduling and tracking
- **Recipients** with detailed delivery tracking
- **Settings** for SMTP configuration

### Customer Management
- **Customer Groups** for targeted marketing
- **User Profiles** with detailed information
- **Email Preferences** for opt-in/opt-out management

### Advanced Features
- **Product Variants** with individual stock tracking
- **Order Status Tracking** with timestamps
- **Email Tracking** with open/click analytics
- **Multi-address Support** for shipping flexibility

## Database Constraints

### Unique Constraints
- `users.email` - Email addresses must be unique
- `products.sku` - Product SKUs must be unique
- `orders.order_number` - Order numbers must be unique
- `invoices.invoice_number` - Invoice numbers must be unique
- `categories.slug` - Category slugs must be unique
- `brands.slug` - Brand slugs must be unique

### Composite Unique Constraints
- `wishlists(user_id, product_id)` - Users can only wishlist a product once
- `customer_group_user(customer_group_id, user_id)` - Users can only be in a group once
- `product_variants(product_id, size, color)` - Unique variant combinations

### Foreign Key Constraints
- Cascade deletes for dependent records (orders, wishlists, etc.)
- Set null for optional relationships (product categories, brands)
- Proper indexing on foreign key columns for performance

## Performance Considerations

### Indexed Columns
- All foreign key columns are indexed
- Unique constraint columns are indexed
- Frequently queried columns (email, status, etc.)

### Query Optimization
- Composite indexes for common query patterns
- JSON columns for flexible data storage
- Proper data types for efficient storage

### Scalability Features
- Multi-tenant architecture supports multiple stores
- Email queuing system for large campaigns
- Inventory tracking prevents overselling
- Audit trails for data integrity

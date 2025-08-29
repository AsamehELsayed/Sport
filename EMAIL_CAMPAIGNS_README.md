# Email Campaigns Module

A comprehensive email marketing system for the SportApp that allows admins to create, manage, and send email campaigns to customers based on customer groups and timezone preferences.

## Features

### 🎯 Core Features
- **Email Templates**: Create and manage reusable email templates with variable support
- **Customer Groups**: Target specific customer segments for campaigns
- **Campaign Management**: Create, schedule, and track email campaigns
- **Email Settings**: Configure SMTP settings with timezone support
- **Tracking**: Monitor email opens, clicks, and delivery status
- **Unsubscribe Management**: Handle customer opt-outs automatically

### 📧 Email Template Types
- **Welcome Emails**: For new customer onboarding
- **Sales Promotions**: Special offers and discounts
- **Newsletters**: Regular updates and content
- **Abandoned Cart**: Reminder emails for incomplete purchases
- **Custom Templates**: Fully customizable templates

### ⚙️ Advanced Features
- **Timezone Support**: Respect customer timezones for optimal sending times
- **Sending Schedule**: Configure business hours and sending windows
- **Variable Replacement**: Dynamic content with customer data
- **HTML Email Support**: Rich, responsive email templates
- **Queue Processing**: Background job processing for reliable delivery
- **Performance Analytics**: Track open rates, click rates, and bounce rates

## Database Structure

### Tables
1. **email_templates** - Store reusable email templates
2. **email_campaigns** - Campaign definitions and metadata
3. **email_campaign_recipients** - Individual recipient tracking
4. **email_settings** - SMTP configuration and preferences
5. **email_campaign_customer_groups** - Campaign-to-group relationships

### Key Relationships
- Campaigns belong to users (admins)
- Campaigns can use templates
- Campaigns target customer groups
- Recipients are tracked individually
- Settings are user-specific

## Installation & Setup

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Sample Data
```bash
php artisan db:seed --class=EmailTemplateSeeder
```

### 3. Configure Queue (Recommended)
Add to your `.env`:
```env
QUEUE_CONNECTION=database
```

### 4. Set Up Cron Job
Add to your server's crontab:
```bash
* * * * * cd /path/to/your/app && php artisan schedule:run >> /dev/null 2>&1
```

## Usage

### Admin Interface

#### Email Templates
- **Create Templates**: Build reusable email templates with variables
- **Preview Templates**: Test how emails will look with sample data
- **Template Types**: Choose from predefined categories or create custom ones
- **Variable Support**: Use dynamic placeholders like `{{customer_name}}`

#### Email Campaigns
- **Create Campaigns**: Set up new email campaigns
- **Select Recipients**: Choose customer groups to target
- **Schedule Sending**: Set specific dates and times for delivery
- **Monitor Progress**: Track sending status and performance metrics

#### Email Settings
- **SMTP Configuration**: Set up email provider credentials
- **Timezone Settings**: Configure customer timezone preferences
- **Sending Schedule**: Define business hours and sending windows
- **Tracking Options**: Enable/disable open and click tracking
- **Test Connection**: Verify email settings work correctly

### Available Variables

Use these variables in your email templates:

| Variable | Description | Example |
|----------|-------------|---------|
| `{{customer_name}}` | Customer's full name | "John Doe" |
| `{{customer_email}}` | Customer's email address | "john@example.com" |
| `{{customer_phone}}` | Customer's phone number | "+1234567890" |
| `{{order_number}}` | Order reference number | "ORD-12345" |
| `{{order_total}}` | Order total amount | "$99.99" |
| `{{website_name}}` | Your website name | "SportApp" |
| `{{unsubscribe_link}}` | Unsubscribe URL | "https://.../unsubscribe/token" |

### API Endpoints

#### Email Templates
- `GET /admin/email-templates` - List all templates
- `POST /admin/email-templates` - Create new template
- `GET /admin/email-templates/{id}` - View template
- `PUT /admin/email-templates/{id}` - Update template
- `DELETE /admin/email-templates/{id}` - Delete template
- `GET /admin/email-templates/{id}/preview` - Preview template

#### Email Campaigns
- `GET /admin/email-campaigns` - List all campaigns
- `POST /admin/email-campaigns` - Create new campaign
- `GET /admin/email-campaigns/{id}` - View campaign
- `PUT /admin/email-campaigns/{id}` - Update campaign
- `DELETE /admin/email-campaigns/{id}` - Delete campaign
- `POST /admin/email-campaigns/{id}/send` - Send campaign
- `GET /admin/email-campaigns/{id}/preview` - Preview campaign
- `GET /admin/email-campaigns/{id}/recipients` - View recipients

#### Email Settings
- `GET /admin/email-settings` - View settings
- `POST /admin/email-settings` - Update settings
- `POST /admin/email-settings/test` - Test email connection

### Tracking Endpoints
- `GET /track/open/{id}` - Track email opens
- `GET /unsubscribe/{token}` - Handle unsubscribes

## Configuration

### Environment Variables
```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Your Company Name"

# Queue Configuration
QUEUE_CONNECTION=database
```

### Supported Email Providers
- **SMTP**: Gmail, Outlook, custom SMTP servers
- **Mailgun**: Professional email service
- **Amazon SES**: AWS email service
- **Postmark**: Transactional email service

## Commands

### Process Email Campaigns
```bash
php artisan email:process-campaigns
```

This command processes pending email campaigns and sends emails to recipients. It should be run via cron job for automatic processing.

### Manual Campaign Processing
```bash
# Process all pending campaigns
php artisan email:process-campaigns

# Process specific campaign (via code)
SendEmailCampaignJob::dispatch($campaign, $recipient);
```

## Best Practices

### Email Template Design
1. **Mobile-First**: Design for mobile devices first
2. **Clear CTAs**: Use prominent call-to-action buttons
3. **Personalization**: Use customer data for relevance
4. **Unsubscribe Link**: Always include unsubscribe option
5. **Testing**: Preview templates before sending

### Campaign Management
1. **Segment Your Audience**: Use customer groups effectively
2. **Respect Timezones**: Schedule emails for optimal times
3. **Monitor Performance**: Track metrics and optimize
4. **A/B Testing**: Test different subject lines and content
5. **Compliance**: Follow email marketing regulations

### Technical Considerations
1. **Queue Processing**: Use background jobs for reliability
2. **Rate Limiting**: Respect email provider limits
3. **Error Handling**: Monitor failed deliveries
4. **Backup Plans**: Have fallback email providers
5. **Security**: Secure SMTP credentials

## Troubleshooting

### Common Issues

#### Emails Not Sending
1. Check SMTP settings in Email Settings
2. Verify email provider credentials
3. Check queue worker is running
4. Review error logs

#### Poor Delivery Rates
1. Verify sender reputation
2. Check email content for spam triggers
3. Monitor bounce rates
4. Use proper authentication (SPF, DKIM)

#### Template Variables Not Working
1. Ensure variables are properly formatted: `{{variable_name}}`
2. Check variable names match available options
3. Test with sample data

### Logs
Check Laravel logs for detailed error information:
```bash
tail -f storage/logs/laravel.log
```

## Security & Compliance

### GDPR Compliance
- Unsubscribe links in every email
- Customer consent tracking
- Data retention policies
- Right to be forgotten

### Email Authentication
- SPF records
- DKIM signatures
- DMARC policies

### Data Protection
- Encrypted SMTP credentials
- Secure unsubscribe tokens
- Audit logging
- Access controls

## Support

For technical support or feature requests, please refer to the main project documentation or create an issue in the repository.

---

**Note**: This module is designed to work with the existing SportApp customer management system and integrates seamlessly with the admin interface.

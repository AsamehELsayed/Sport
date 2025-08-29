# Email Campaigns Scheduler Setup Guide

## ✅ Scheduled Commands Status

Your email campaigns scheduler is now **FULLY CONFIGURED** and ready to use!

### 📋 Current Schedule

| Command | Frequency | Next Run | Purpose |
|---------|-----------|----------|---------|
| `email:process-campaigns` | Every minute | Every minute | Process and send pending emails |
| `email:cleanup-old-campaigns` | Daily at 2:00 AM | Daily at 2:00 AM | Clean up old campaign data |

## 🚀 Quick Setup

### For Linux/Mac (Cron Job)

1. **Open your crontab:**
   ```bash
   crontab -e
   ```

2. **Add this line:**
   ```bash
   * * * * * cd /path/to/your/SportApp && php artisan schedule:run >> /dev/null 2>&1
   ```

3. **Save and exit**

4. **Verify it's set up:**
   ```bash
   crontab -l
   ```

### For Windows (Task Scheduler)

1. **Open Task Scheduler:**
   - Press `Win + R`
   - Type `taskschd.msc`
   - Press Enter

2. **Create Basic Task:**
   - Name: "Email Campaigns Processing"
   - Trigger: Daily, every 1 minute
   - Action: Start a program
   - Program: `php`
   - Arguments: `artisan schedule:run`
   - Start in: `C:\Users\gamer\Desktop\SportApp`

3. **OR use PowerShell (as Administrator):**
   ```powershell
   schtasks /create /tn "Email Campaigns" /tr "php C:\Users\gamer\Desktop\SportApp\artisan schedule:run" /sc minute /mo 1 /st 00:00 /f
   ```

## 🧪 Testing Your Setup

### Manual Testing

1. **Test email processing:**
   ```bash
   php artisan email:process-campaigns
   ```

2. **Test cleanup:**
   ```bash
   php artisan email:cleanup-old-campaigns
   ```

3. **View scheduled tasks:**
   ```bash
   php artisan schedule:list
   ```

### Expected Output

When you run `php artisan schedule:list`, you should see:
```
* * * * *  php artisan email:process-campaigns .............................................. Next Due: X seconds from now
0 2 * * *  php artisan email:cleanup-old-campaigns ........................................... Next Due: X hours from now
```

## 📊 Monitoring & Logs

### Log Files Location

- **Email campaigns log:** `storage/logs/email-campaigns.log`
- **Email cleanup log:** `storage/logs/email-cleanup.log`
- **Laravel log:** `storage/logs/laravel.log`

### Monitor Logs

```bash
# Watch email campaigns log
tail -f storage/logs/email-campaigns.log

# Watch Laravel log
tail -f storage/logs/laravel.log
```

## ⚙️ Configuration Options

### Email Processing Frequency

The email processing runs every minute by default. To change this, edit `app/Console/Scheduler.php`:

```php
// Change from every minute to every 5 minutes
$schedule->command('email:process-campaigns')
    ->everyFiveMinutes()  // Instead of everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
```

### Cleanup Frequency

The cleanup runs daily at 2:00 AM. To change this:

```php
// Change to weekly cleanup
$schedule->command('email:cleanup-old-campaigns')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->appendOutputTo(storage_path('logs/email-cleanup.log'));
```

### Retention Period

By default, cleanup removes data older than 90 days. To change this:

```bash
# Clean up data older than 30 days
php artisan email:cleanup-old-campaigns --days=30

# Clean up data older than 180 days
php artisan email:cleanup-old-campaigns --days=180
```

## 🔧 Troubleshooting

### Common Issues

#### 1. Commands Not Running
**Symptoms:** Emails not being sent, no log entries

**Solutions:**
- Check if cron/task scheduler is running
- Verify the path in your cron job is correct
- Check file permissions
- Review Laravel logs

#### 2. Permission Errors
**Symptoms:** "Permission denied" errors

**Solutions:**
- Ensure the web server user has write permissions to `storage/logs/`
- Check file ownership
- Run: `chmod -R 755 storage/logs/`

#### 3. Memory Issues
**Symptoms:** Commands timing out or failing

**Solutions:**
- Increase PHP memory limit in `php.ini`
- Process emails in smaller batches
- Monitor server resources

### Debug Commands

```bash
# Test if Laravel can run commands
php artisan --version

# Check if the command exists
php artisan list | grep email

# Test the command manually
php artisan email:process-campaigns --verbose

# Check queue status (if using queues)
php artisan queue:work --once
```

## 📈 Performance Optimization

### For High Volume

1. **Use Queue Workers:**
   ```bash
   php artisan queue:work --daemon
   ```

2. **Process in Batches:**
   - The system processes 50 emails per batch by default
   - Adjust in `ProcessEmailCampaigns.php` if needed

3. **Database Optimization:**
   - Add indexes to frequently queried columns
   - Regular cleanup of old data
   - Monitor query performance

### Monitoring Commands

```bash
# Check queue status
php artisan queue:status

# Monitor failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## 🔒 Security Considerations

1. **File Permissions:**
   - Ensure log files are not publicly accessible
   - Set proper ownership and permissions

2. **Cron Job Security:**
   - Use absolute paths in cron jobs
   - Avoid running as root
   - Log all activities

3. **Database Security:**
   - Regular backups
   - Monitor for unusual activity
   - Use database transactions

## 📞 Support

If you encounter issues:

1. **Check the logs first:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Test manually:**
   ```bash
   php artisan email:process-campaigns
   ```

3. **Verify configuration:**
   - Email settings in admin panel
   - Database connectivity
   - File permissions

4. **Common fixes:**
   - Restart the cron service: `sudo service cron restart`
   - Clear Laravel cache: `php artisan cache:clear`
   - Restart queue workers: `php artisan queue:restart`

---

## 🎉 You're All Set!

Your email campaigns scheduler is now configured and ready to automatically process email campaigns. The system will:

- ✅ Process pending emails every minute
- ✅ Clean up old data daily at 2:00 AM
- ✅ Log all activities for monitoring
- ✅ Handle errors gracefully
- ✅ Respect timezone settings
- ✅ Prevent overlapping executions

**Next Steps:**
1. Set up your email provider settings in the admin panel
2. Create your first email campaign
3. Monitor the logs to ensure everything is working
4. Enjoy automated email marketing! 🚀

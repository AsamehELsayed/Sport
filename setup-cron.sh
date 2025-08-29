#!/bin/bash

# Email Campaigns Cron Job Setup Script
# This script helps you set up the cron job for processing email campaigns

echo "=========================================="
echo "Email Campaigns Cron Job Setup"
echo "=========================================="

# Get the current directory
CURRENT_DIR=$(pwd)
echo "Current directory: $CURRENT_DIR"

# Create the cron job entry
CRON_JOB="* * * * * cd $CURRENT_DIR && php artisan schedule:run >> /dev/null 2>&1"

echo ""
echo "Add this line to your crontab:"
echo "=========================================="
echo "$CRON_JOB"
echo "=========================================="

echo ""
echo "To add this to your crontab, run:"
echo "crontab -e"
echo ""
echo "Then paste the line above and save."
echo ""
echo "To verify the cron job is set up, run:"
echo "crontab -l"
echo ""
echo "=========================================="
echo "Manual Testing Commands:"
echo "=========================================="
echo "Test email processing:"
echo "php artisan email:process-campaigns"
echo ""
echo "Test cleanup:"
echo "php artisan email:cleanup-old-campaigns"
echo ""
echo "View scheduled tasks:"
echo "php artisan schedule:list"
echo ""
echo "=========================================="
echo "Log Files:"
echo "=========================================="
echo "Email campaigns log: storage/logs/email-campaigns.log"
echo "Email cleanup log: storage/logs/email-cleanup.log"
echo "Laravel log: storage/logs/laravel.log"

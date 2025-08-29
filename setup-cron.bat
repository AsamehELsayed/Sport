@echo off
echo ==========================================
echo Email Campaigns Scheduled Task Setup
echo ==========================================

REM Get the current directory
set CURRENT_DIR=%CD%
echo Current directory: %CURRENT_DIR%

echo.
echo For Windows, you need to set up a scheduled task:
echo.
echo 1. Open Task Scheduler (taskschd.msc)
echo 2. Create Basic Task
echo 3. Name: "Email Campaigns Processing"
echo 4. Trigger: Daily, every 1 minute
echo 5. Action: Start a program
echo 6. Program: %CURRENT_DIR%\vendor\bin\sail
echo 7. Arguments: artisan schedule:run
echo 8. Start in: %CURRENT_DIR%
echo.
echo OR use PowerShell to create the task:
echo.
echo PowerShell command:
echo schtasks /create /tn "Email Campaigns" /tr "php %CURRENT_DIR%\artisan schedule:run" /sc minute /mo 1 /st 00:00 /f
echo.
echo ==========================================
echo Manual Testing Commands:
echo ==========================================
echo Test email processing:
echo php artisan email:process-campaigns
echo.
echo Test cleanup:
echo php artisan email:cleanup-old-campaigns
echo.
echo View scheduled tasks:
echo php artisan schedule:list
echo.
echo ==========================================
echo Log Files:
echo ==========================================
echo Email campaigns log: storage\logs\email-campaigns.log
echo Email cleanup log: storage\logs\email-cleanup.log
echo Laravel log: storage\logs\laravel.log
echo.
pause

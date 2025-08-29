<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mail_driver',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
        'timezone',
        'sending_schedule',
        'daily_send_limit',
        'track_opens',
        'track_clicks',
        'is_active',
    ];

    protected $casts = [
        'sending_schedule' => 'array',
        'track_opens' => 'boolean',
        'track_clicks' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'mail_password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getMailConfig(): array
    {
        return [
            'driver' => $this->mail_driver,
            'host' => $this->mail_host,
            'port' => $this->mail_port,
            'username' => $this->mail_username,
            'password' => $this->mail_password,
            'encryption' => $this->mail_encryption,
            'from' => [
                'address' => $this->mail_from_address,
                'name' => $this->mail_from_name,
            ],
        ];
    }

    public function isWithinSendingSchedule(): bool
    {
        if (empty($this->sending_schedule)) {
            return true;
        }

        $now = now()->setTimezone($this->timezone);
        $dayOfWeek = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        if (!isset($this->sending_schedule[$dayOfWeek])) {
            return false;
        }

        $schedule = $this->sending_schedule[$dayOfWeek];

        if (!isset($schedule['enabled']) || !$schedule['enabled']) {
            return false;
        }

        if (isset($schedule['start_time']) && isset($schedule['end_time'])) {
            return $currentTime >= $schedule['start_time'] && $currentTime <= $schedule['end_time'];
        }

        return true;
    }
}

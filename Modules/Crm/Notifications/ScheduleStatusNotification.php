<?php

namespace Modules\Crm\Notifications;

use App\Utils\NotificationUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleStatusNotification extends Notification
{
    use Queueable;

    protected $schedule;
    protected $send_email;

    public function __construct($schedule, $send_email = false)
    {
        $this->schedule = $schedule;
        $this->send_email = $send_email;

        if ($send_email) {
            (new NotificationUtil())->configureEmail();
        }
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        if (isPusherEnabled()) {
            $channels[] = 'broadcast';
        }
        if ($this->send_email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $subject = __('crm::lang.email_schedule_status_subject', ['title' => $this->schedule->title]);

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('crm::lang.email_schedule_greeting'))
            ->line(__(
                'crm::lang.email_schedule_status_line1',
                [
                    'title'  => $this->schedule->title,
                    'status' => $this->schedule->status_label,
                ]
            ))
            ->action(__('crm::lang.email_schedule_action'), url('/crm/follow-ups'))
            ->line(__('crm::lang.email_schedule_line2'));
    }

    public function toArray($notifiable)
    {
        return [
            'schedule_id' => $this->schedule->id,
            'business_id' => $this->schedule->business_id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->schedule->title,
            'body'  => $this->schedule->body ?? '',
            'link'  => url('/crm/follow-ups'),
        ]);
    }
}

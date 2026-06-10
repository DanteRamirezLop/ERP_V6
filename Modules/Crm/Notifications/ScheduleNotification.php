<?php

namespace Modules\Crm\Notifications;

use App\Utils\NotificationUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScheduleNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $schedule;

    public $channels;

    public function __construct($schedule, $channels)
    {
        $this->schedule = $schedule;
        $this->channels = $channels;

        if (in_array('mail', $channels)) {
            (new NotificationUtil())->configureEmail();
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        if (isPusherEnabled()) {
            $this->channels[] = 'broadcast';
        }

        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $subject = __('crm::lang.email_schedule_subject', ['title' => $this->schedule->title]);

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('crm::lang.email_schedule_greeting'))
            ->line(__(
                'crm::lang.email_schedule_line1',
                [
                    'title'          => $this->schedule->title,
                    'startdatetime'  => $this->schedule->start_datetime,
                ]
            ))
            ->action(__('crm::lang.email_schedule_action'), url('/login'))
            ->line(__('crm::lang.email_schedule_line2'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'schedule_id' => $this->schedule->id,
            'business_id' => $this->schedule->business_id,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->schedule->broadcast_title,
            'body' => $this->schedule->body,
            'link' => $this->schedule->link,
        ]);
    }
}

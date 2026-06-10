<?php

namespace Modules\Project\Notifications;

use App\Utils\NotificationUtil;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectStatusUpdatedNotification extends Notification
{
    use Queueable;

    protected $project;
    protected $send_email;

    public function __construct($project, $send_email = false)
    {
        $this->project = $project;
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
        $subject = __('project::lang.email_project_status_subject', ['project' => $this->project->name]);

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('project::lang.email_new_project_greeting'))
            ->line(__(
                'project::lang.email_project_status_line1',
                [
                    'project' => $this->project->name,
                    'status'  => $this->project->status_label,
                ]
            ))
            ->action(__('project::lang.email_new_project_action'), $this->project->link)
            ->line(__('project::lang.email_new_project_line2'));
    }

    public function toArray($notifiable)
    {
        return [
            'project_id' => $this->project->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'title' => $this->project->title,
            'body'  => $this->project->body,
            'link'  => $this->project->link,
        ]);
    }
}

<?php

namespace Modules\Project\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProjectAssignedNotification extends Notification
{
    use Queueable;

    protected $project;
    protected $send_email;

    public function __construct($project, $send_email = false)
    {
        $this->project = $project;
        $this->send_email = $send_email;
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
        $subject = __('project::lang.email_new_project_subject', ['project' => $this->project->name]);

        $statusLabel = \Modules\Project\Entities\Project::statusDropdown()[$this->project->status] ?? $this->project->status;

        return (new MailMessage)
            ->subject($subject)
            ->greeting(__('project::lang.email_new_project_greeting'))
            ->line(__(
                'project::lang.email_new_project_line1',
                [
                    'created_by' => $this->project->created_by_name ?? '',
                    'project'    => $this->project->name,
                ]
            ))
            ->line(__('project::lang.email_new_project_project_name', ['project' => $this->project->name]))
            ->line(__('project::lang.email_new_project_status', ['status' => $statusLabel]))
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

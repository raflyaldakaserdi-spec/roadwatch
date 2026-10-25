<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $assignmentId,
        public string $event,
        public string $title,
        public string $message,
        public string $url
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignmentId,
            'event' => $this->event,
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}

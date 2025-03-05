<?php

namespace App\Notifications;

use App\Models\Event;
use App\Traits\GeneratesConfirmationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCancellationNotification extends Notification implements ShouldQueue
{
	use Queueable, GeneratesConfirmationToken;

	public $event, $user;

	public function __construct(Event $event, $user)
	{
		$this->event = $event;
		$this->user = $user;
	}

	public function via($notifiable)
	{
		return ['mail', 'database'];
	}

	public function toDatabase($notifiable)
	{
		return [
			'event_id' => $this->event->id,
			'user_id' => $this->user->id,
			'event_title' => $this->event->title,
			'message' => "You have requested to cancel your registration for the event {$this->event->title}.",
		];
	}

	public function toMail($notifiable)
	{
		$confirmationLink = $this->generateConfirmationToken($notifiable->user, $this->event->event, 'cancellation');

		return (new MailMessage)
			->subject('Confirmation of Unsubscribe')
			->greeting("Hello, {$notifiable->name}!")
			->line("You have requested to cancel your registration for the event **{$this->event->title}**, but need to confirm.")
			->action('Confirm Cancellation', $confirmationLink)
			->line('Confirm CancellationIf you have not requested this cancellation, please ignore this email.');
	}
}

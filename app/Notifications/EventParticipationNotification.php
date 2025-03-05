<?php

namespace App\Notifications;

use App\Models\Event;
use App\Traits\GeneratesConfirmationToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventParticipationNotification extends Notification implements ShouldQueue
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
			'event_title' => $this->event->title,
			'user_name' => $this->user->name,
			'confirmation_link' => $this->generateConfirmationToken($notifiable->user, $this->event->event, 'participation'),
			'event_id' => $this->event->id,
		];
	}

	public function toMail($notifiable)
	{
		$confirmationLink = $this->generateConfirmationToken($notifiable->user, $this->event->event, 'participation');

		return (new MailMessage)
			->subject('Event Registration Confirmation')
			->greeting("Hello, {$notifiable->name}!")
			->line("You registered for the event **{$this->event->title}**, but needs to confirm your participation.")
			->action('Confirm Registration', $confirmationLink)
			->line('If you did not request this registration, please ignore this email.');
	}
}

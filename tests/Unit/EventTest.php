<?php

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventCancellationNotification;
use App\Notifications\EventParticipationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function it_creates_an_event()
	{
		$user = User::factory()->create([
			'role' => 'admin',
		]);

		// Simula o login como o usuário
		$this->actingAs($user);

		$eventData = [
			'title' => 'Test Event',
			'description' => 'This is a test event.',
			'start_time' => now()->addDays(1),
			'end_time' => now()->addDays(2),
			'location' => 'Test Location',
			'capacity' => 100,
			'status' => 'open'
		];

		$response = $this->postJson(route('events.store'), $eventData);

		$response->assertStatus(201);
		$response->assertJsonFragment(['success' => true]);

		// Verifica se o evento foi realmente criado no banco de dados
		$this->assertDatabaseHas('events', [
			'title' => 'Test Event',
			'description' => 'This is a test event.'
		]);
	}

	/** @test */
	public function it_allows_user_to_participate_in_event()
	{
		Notification::fake();

		$event = Event::factory()->create();
		$user = User::factory()->create();

		$this->actingAs($user);

		// Envia a requisição POST para a rota de participação
		$response = $this->postJson(route('events.participate', $event->event));

		// Verifica se a resposta é bem-sucedida
		$response->assertStatus(200);
		$response->assertJsonFragment([
			'success' => true,
			'message' => 'You are now participating in the event.'
		]);

		// Recarrega o evento para garantir que o usuário foi adicionado
		$event->refresh();
		$this->assertTrue($event->attendees->contains($user));

		// Verifica se a notificação foi enviada ao usuário
		Notification::assertSentTo($user, EventParticipationNotification::class);
	}

	/** @test */
	public function it_prevents_user_from_participating_again_in_event()
	{
		$event = Event::factory()->create();
		$user = User::factory()->create();

		// Simula o usuário autenticado e adiciona ele como participante
		$this->actingAs($user);
		$event->attendees()->attach($user);

		// Envia uma requisição POST para a rota de participação novamente
		$response = $this->postJson(route('events.participate', $event->event));

		// Verifica se a resposta de erro é retornada
		$response->assertStatus(400);
		$response->assertJsonFragment(['success' => false, 'message' => 'You are already participating in this event.']);
	}

	/** @test */
	public function it_allows_user_to_leave_event()
	{
		Notification::fake();

		$event = Event::factory()->create();
		$user = User::factory()->create();

		// Simula o usuário autenticado e o adiciona como participante
		$this->actingAs($user);
		$event->attendees()->attach($user);

		// Envia uma requisição POST para a rota de "leave"
		$response = $this->postJson(route('events.leave', $event->event));

		// Verifica se a resposta é bem-sucedida
		$response->assertStatus(200);
		$response->assertJsonFragment([
			'success' => true,
			'message' => 'You left the event.'
		]);

		// Verifica se a notificação de cancelamento foi enviada
		Notification::assertSentTo($user, EventCancellationNotification::class);
	}

	/** @test */
	public function it_prevents_user_from_leaving_event_if_not_participating()
	{
		$event = Event::factory()->create();
		$user = User::factory()->create();

		// Simula o usuário autenticado, mas sem adicioná-lo ao evento
		$this->actingAs($user);

		// Envia uma requisição POST para a rota de "leave"
		$response = $this->postJson(route('events.leave', $event->event));

		// Verifica se a resposta de erro é retornada
		$response->assertStatus(400);
		$response->assertJsonFragment([
			'success' => false,
			'message' => 'You are not participating in this event.'
		]);
	}

	/** @test */
	public function it_confirms_participation_action()
	{
		$event = Event::factory()->create();
		$user = User::factory()->create();

		$this->actingAs($user);
		$event->attendees()->attach($user);

		// Usar a trait para gerar o token corretamente
		$trait = new class {
			use \App\Traits\GeneratesConfirmationToken;
		};

		$confirmationUrl = $trait->generateConfirmationToken($user->user, $event->event, 'participation');

		// Extrair o token da URL gerada
		$token = last(explode('/', $confirmationUrl));

		// Enviar a requisição com o token gerado
		$response = $this->getJson(route('events.confirmAction', [
			'event' => $event->event,
			'action' => 'participation',
			'token' => $token
		]));

		$response->assertStatus(200);
		$response->assertJsonFragment([
			'success' => true,
			'message' => 'Participation confirmed.'
		]);
	}

	/** @test */
	public function it_confirms_cancellation_action()
	{
		$event = Event::factory()->create();
		$user = User::factory()->create();

		$this->actingAs($user);
		$event->attendees()->attach($user);

		// Usar a trait para gerar o token corretamente
		$trait = new class {
			use \App\Traits\GeneratesConfirmationToken;
		};

		$confirmationUrl = $trait->generateConfirmationToken($user->user, $event->event, 'cancellation');

		// Extrair o token da URL gerada
		$token = last(explode('/', $confirmationUrl));

		// Enviar a requisição com o token gerado
		$response = $this->getJson(route('events.confirmAction', [
			'event' => $event->event,
			'action' => 'cancellation',
			'token' => $token
		]));

		$response->assertStatus(200);
		$response->assertJsonFragment([
			'success' => true,
			'message' => 'Cancellation confirmed.'
		]);
	}

	/** @test */
	public function it_validates_event_capacity()
	{
		$user = User::factory()->create();
		$this->actingAs($user);

		// Cria um evento com capacidade limitada
		$event = Event::factory()->create(['capacity' => 2]);

		// Inscreve dois usuários
		$user2 = User::factory()->create();
		$user3 = User::factory()->create();
		$event->attendees()->attach($user);
		$event->attendees()->attach($user2);

		// Tenta inscrever um terceiro usuário, o que deve falhar
		$response = $this->postJson(route('events.participate', $event->event), ['user' => $user3]);

		// O evento já está lotado
		$response->assertStatus(400);
		$response->assertJsonFragment([
			'success' => false,
			'message' => 'Event capacity reached.'
		]);
	}

	/** @test */
	public function it_updates_an_event()
	{
		$user = User::factory()->create([
			'role' => 'admin',
		]);
		$this->actingAs($user);

		// Cria um evento
		$event = Event::factory()->create();

		// Dados para atualização
		$updatedData = [
			'title' => 'Updated Event Title',
			'description' => 'Updated description of the event.',
			'start_time' => '2025-03-10 10:00:00',
			'end_time' => '2025-03-10 12:00:00',
			'location' => 'New Location',
			'capacity' => 50,
			'status' => 'open',
		];

		$response = $this->putJson(route('events.update', $event->event), $updatedData);

		$response->assertStatus(200);
		$response->assertJsonFragment(['success' => true]);

		// Verifica se os dados foram atualizados no banco de dados
		$this->assertDatabaseHas('events', [
			'title' => 'Updated Event Title',
			'description' => 'Updated description of the event.'
		]);
	}
}

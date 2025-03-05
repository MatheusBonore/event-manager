<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Events') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-3">
			<caption class="p-5 text-lg font-semibold text-left">
				<p class="text-gray-900 whitespace-nowrap dark:text-white">Our Events</p>
				<p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">Browse the list of events available in our system. Stay updated on locations, capacities, and statuses to plan your participation efficiently.</p>
			</caption>

			@if(isset($events) && !empty($events))
				<div class="mt-3 relative overflow-x-auto shadow-md sm:rounded-3xl">
					<table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
						<thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
							<tr>
								<th scope="col" class="px-6 py-3">
									Title
								</th>
								<th scope="col" class="px-6 py-3"></th>
								<th scope="col" class="px-6 py-3"></th>
								<th scope="col" class="px-6 py-3">
									Location
								</th>
								<th scope="col" class="px-6 py-3 text-center">
									Capacity
								</th>
								<th scope="col" class="px-6 py-3">
									Status
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($events as $index_event => $event)
								@php
									switch ($event['status']) {
										case 'open':
											$bg_color = 'bg-green-500';
											$border_color = 'border-r-green-500';
											$status_text = 'Open';
											break;
										case 'canceled':
											$bg_color = 'bg-red-500';
											$border_color = 'border-r-red-500';
											$status_text = 'Canceled';
											break;
										case 'closed':
											$bg_color = 'bg-gray-500';
											$border_color = 'border-r-gray-500';
											$status_text = 'Closed';
											break;
										default:
											$bg_color = 'bg-blue-500';
											$border_color = 'border-r-blue-500';
											$status_text = 'Unknown';
											break;
									}
								@endphp

								<tr onclick="openModalEvent({{ json_encode(['user' => $user, 'event' => $event]) }})" class="{{ $loop->last ? 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600 ' : ' bg-white border-b-2 dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600' }} cursor-pointer">
									<th scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
										<x-event.avatar class="relative cursor-pointer {{ ($event['users_user'] === $user['user']) ? 'ring-2 ring-indigo-300 dark:ring-indigo-500' : '' }}" data-tooltip-target="tooltip-avatar-{{ $index_event }}" data-tooltip-placement="bottom" :value="$event['creator']['initials_name']" />

										<x-event.tooltip :id="'tooltip-avatar-' . $index_event">
											@if ($event['users_user'] === $user['user'])
												You
											@else
												{{ $event['creator']['name'] }}
											@endif
										</x-event.tooltip>

										<div class="ps-3">
											<div class="text-base font-semibold">{{ $event['title'] }}</div>
											<div class="font-normal text-gray-500 cursor-pointer" data-tooltip-target="tooltip-description-{{ $index_event }}" data-tooltip-placement="right">{{ $event['truncated_description'] }}</div>

											<x-event.tooltip :id="'tooltip-description-' . $index_event">
												{{ $event['description'] }}
											</x-event.tooltip>

											<div class="font-normal text-gray-500">{{ $event['start_date_formatted'] }}</div>
											<div class="font-normal text-gray-500">{{ $event['event_status'] }}</div>
										</div>
									</th>
									<td class="px-6 py-4">
										@php
											$isParticipating = $event['attendees']->filter(function($attendee) use ($user) {
												return isset($attendee['user']) && $attendee['user'] == $user['user'];
											})->isNotEmpty();
										@endphp

										@if ($isParticipating)
											Participating
										@endif
									</td>
									<td class="px-6 py-4" nowrap>
										<div class="flex -space-x-4 rtl:space-x-reverse">
											@php
												$names = [];
											@endphp

											@foreach ($event['attendees'] as $index_attendees => $attendees)
												@php
													$id_tooltip = md5($index_event . '-' . $index_attendees);
												@endphp

												@if ($index_attendees < 5)
													<x-event.avatar data-tooltip-target="{{ $id_tooltip }}" data-tooltip-placement="bottom" class="relative z-[{{ $index_attendees + 10 }}] cursor-pointer {{ ($attendees['user'] === $user['user']) ? 'ring-2 ring-indigo-300 dark:ring-indigo-500' : '' }}" :value="$attendees['initials_name']" />

													<x-event.tooltip :id="$id_tooltip">
														@if ($attendees['user'] === $user['user'])
															You
														@else
															{{ $attendees['name'] }}
														@endif
													</x-event.tooltip>
												@else
													@php
														array_push($names, $attendees['name']);
													@endphp
												@endif
											@endforeach

											@if (count($event['attendees']) > 5)
												<a class="relative z-[{{ 15 }}] flex items-center justify-center w-10 h-10 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800" href="#">
													+{{ count($event['attendees']) - 5 }}
												</a>

												{{-- Colocar para abrir modal para ver outros participantes --}}

												{{-- {{ implode(PHP_EOL, $names) }} --}}
											@endif
										</div>
									</td>
									<td class="px-6 py-4">
										{{ $event['location'] }}
									</td>
									<td class="px-6 py-4 text-center" nowrap>
										{{ $event['attendees']->count() }} /
										{{ $event['capacity'] }}
									</td>
									<td class="px-6 py-4 text-center border-r-4 {{ $border_color}}">
										<div class="flex text-center items-center">
											<div class="h-2.5 w-2.5 rounded-full {{ $bg_color }} me-2"></div> {{ $status_text }}
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			@else
				<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-3xl">
					<div class="p-6 text-gray-900 dark:text-gray-100">
						{{ __("No event found!") }}
					</div>
				</div>
			@endif
		</div>
	</div>

	@push('scripts')
		<script>
			window.onload = function() {
				const userData = @js($user);
				const eventData = @js($event_more);
				
				if (eventData && Object.keys(eventData).length > 0) {
					openModalEvent({ user: userData, event: eventData });
				}
			};

			function formatDateTime(dateTime) {
				if (!dateTime) return "";

				let date = new Date(dateTime);
				let year = date.getFullYear();
				let month = String(date.getMonth() + 1).padStart(2, '0');
				let day = String(date.getDate()).padStart(2, '0');
				let hours = String(date.getHours()).padStart(2, '0');
				let minutes = String(date.getMinutes()).padStart(2, '0');

				return `${year}-${month}-${day}T${hours}:${minutes}`;
			}

			function openModalEvent(data) {
				// Preencher os campos do modal
				document.getElementById("avatar-more-details-modal").innerHTML = data.event.creator.initials_name || "";

				document.getElementById("name-more-details-modal").innerHTML = data.event.creator.name || "";
				document.getElementById("start-date-formatted-more-details-modal").innerHTML = data.event.start_date_formatted || "";
				document.getElementById("event-status-more-details-modal").innerHTML = data.event.event_status || "";

				document.getElementById("event").value = data.event.event || "";

				document.getElementById("title").disabled = true;
				document.getElementById("description").disabled = true;
				document.getElementById("start_time").disabled = true;
				document.getElementById("end_time").disabled = true;
				document.getElementById("location").disabled = true;
				document.getElementById("capacity").disabled = true;
				document.getElementById("status").disabled = true;

				document.getElementById("title").value = data.event.title || "";
				document.getElementById("description").value = data.event.description || "";
				document.getElementById("start_time").value = formatDateTime(data.event.start_time) || "";
				document.getElementById("end_time").value = formatDateTime(data.event.end_time) || "";
				document.getElementById("location").value = data.event.location || "";
				document.getElementById("capacity").value = data.event.capacity || "";
				document.getElementById("status").value = data.event.status || "open";

				if (data.user.user == data.event.users_user) {
					document.getElementById("title").disabled = false;
					document.getElementById("description").disabled = false;
					document.getElementById("start_time").disabled = false;
					document.getElementById("end_time").disabled = false;
					document.getElementById("location").disabled = false;
					document.getElementById("capacity").disabled = false;
					document.getElementById("status").disabled = false;
				}

				document.getElementById('btn-participate-more-details-modal').style.display = 'none';
				document.getElementById('btn-participate-more-details-modal').disabled = true;

				document.getElementById('btn-leave-more-details-modal').style.display = 'none';
				document.getElementById('btn-leave-more-details-modal').disabled = true;

				if (data.event.attendees.some(attendee => attendee.user === data.user.user)) {
					document.getElementById('btn-leave-more-details-modal').style.display = 'block';
					document.getElementById('btn-leave-more-details-modal').disabled = false;
				} else {
					document.getElementById('btn-participate-more-details-modal').style.display = 'block';
					document.getElementById('btn-participate-more-details-modal').disabled = false;
				}

				document.getElementById('btn-save-more-details-modal').style.display = 'block';
				document.getElementById('btn-save-more-details-modal').disabled = false;

				if (data.user.user != data.event.users_user) {
					document.getElementById('btn-save-more-details-modal').style.display = 'none';
					document.getElementById('btn-save-more-details-modal').disabled = true;
				}

				window.dispatchEvent(new CustomEvent('open-modal', { detail: 'more-details-modal' }));
			}

			function participateEvent() {
				const event = document.getElementById("event").value;

				document.getElementById('error-details-modal').innerHTML = '';

				fetch(`/events/${event}/participate`, {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						document.getElementById('error-details-modal').innerHTML = data.message;
					} else {
						window.location.href = `{{ url('/events?event=${event}') }}`;
					}
				})
				.catch(error => console.error('An error occurred while attending the event.'));
			}

			function leaveEvent() {
				const event = document.getElementById("event").value;

				document.getElementById('error-details-modal').innerHTML = '';

				fetch(`/events/${event}/leave`, {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success == false) {
						document.getElementById('error-details-modal').innerHTML = data.message;
					} else {
						window.location.href = `{{ url('/events?event=${event}') }}`;
					}
				})
				.catch(error => console.error('An error occurred while leaving the event.'));
			}
		</script>
	@endpush

	<x-event.modal id="more-details-modal" name="more-details-modal" title="More event details" focusable>
		<form class="p-4 md:p-5">
			@csrf

			<input type="hidden" name="event" id="event" value="" />

			<div class="flex items-center gap-1 h-full mb-2">
				<x-event.avatar class="relative cursor-pointer" name="avatar-more-details-modal" :value="'AM'" />

				<div class="ps-3">
					<div class="text-base font-semibold text-gray-900 dark:text-white" id="name-more-details-modal">name-more-details-modal</div>
					<div class="font-normal text-gray-500" id="start-date-formatted-more-details-modal">start-date-formatted-more-details-modal</div>
					<div class="font-normal text-gray-500" id="event-status-more-details-modal">event-status-more-details-modal</div>
				</div>
			</div>

			<div class="grid gap-4 mb-4 grid-cols-2">
				<div class="col-span-2">
					<x-input-label for="title" :value="__('Title')" />
					<x-event.text-input type="text" name="title" id="title" placeholder="Event title" disabled />
				</div>
				<div class="col-span-2">
					<x-input-label for="description" :value="__('Description')" />
					<textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-3xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Event description" disabled></textarea>
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="start_time" :value="__('Start Time')" />
					<x-event.text-input type="datetime-local" name="start_time" id="start_time" disabled />
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="end_time" :value="__('End Time')" />
					<x-event.text-input type="datetime-local" name="end_time" id="end_time" disabled />
				</div>
				<div class="col-span-2">
					<x-input-label for="location" :value="__('Location')" />
					<x-event.text-input type="text" name="location" id="location" placeholder="Event location" disabled />
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="capacity" :value="__('Capacity')" />
					<x-event.text-input type="number" name="capacity" id="capacity" placeholder="Number of attendees" disabled />
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="status" :value="__('Status')" />
					<select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-3xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" disabled>
						<option value="open">Open</option>
						<option value="closed">Closed</option>
						<option value="canceled">Canceled</option>
					</select>
				</div>
			</div>

			<x-text-error id="error-details-modal" class="mb-4"></x-text-error>

			<div class="flex justify-between">
				<div>
					<button type="button" onclick="participateEvent()" id="btn-participate-more-details-modal" class="text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Participate</button>
					<button type="button" onclick="leaveEvent()" id="btn-leave-more-details-modal" class="text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Leave</button>
				</div>

				<button type="button" id="btn-save-more-details-modal" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Save changes</button>
			</div>
		</form>
	</x-event.modal>
</x-app-layout>
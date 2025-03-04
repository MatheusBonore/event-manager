<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="grid grid-cols-2 md:grid-cols-3 gap-2">
				<div class="max-w-sm p-6 bg-white rounded-3xl shadow-sm dark:bg-gray-800">
					<h5 id="created_events" class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
						{{ $created_events }}
					</h5>
					<p class="mb-3 font-normal text-gray-700 dark:text-gray-400">My Created Events</p>

					<div class="flex justify-between">
						<button onclick="openModalAddEvent()" class="inline-flex items-center text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
							<svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
							</svg>
							Add Event
						</button>

						<a href="{{ route('events', ['creator' => $user['user']]) }}" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
							See all
							<svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
							</svg>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			function openModalAddEvent() {
				window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-event-modal' }));
			}

			document.getElementById('event-form').addEventListener('submit', function(event) {
				event.preventDefault();

				const formData = new FormData(this);

				fetch('{{ url("/events") }}', {
					method: 'POST',
					headers: {
						'Accept': 'application/json',
						'X-CSRF-TOKEN': '{{ csrf_token() }}'
					},
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					if (data.errors) {
						document.getElementById('error-add-event-modal').innerHTML = '';

						if (data.errors) {
							let errorHtml = '<ul>';
							for (let field in data.errors) {
								if (data.errors.hasOwnProperty(field)) {
									data.errors[field].forEach(errorMessage => {
										errorHtml += `<li>${errorMessage}</li>`;
									});
								}
							}
							errorHtml += '</ul>';
							document.getElementById('error-add-event-modal').innerHTML = errorHtml;
						}
					} else {
						let created_events = parseInt(document.getElementById('created_events').innerHTML);
						created_events++;
						document.getElementById('created_events').innerHTML = created_events;

						window.dispatchEvent(new CustomEvent('close-modal', { detail: 'add-event-modal' }));

						document.getElementById('event-form').reset();
					}
				})
				.catch(error => {
					console.log('aqui');
					alert('An error occurred while creating the event.');
				});
			});
		</script>
	@endpush

	<x-event.modal id="add-event-modal" name="add-event-modal" title="Add event" focusable>
		<form method="post" action="{{ route(name: 'events.store') }}" class="p-4 md:p-5" id="event-form">
			@csrf

			<div class="flex items-center gap-1 h-full mb-2">
				<x-event.avatar class="relative cursor-pointer" name="avatar-add-event-modal" :value="$user['initials_name']" />

				<div class="ps-3">
					<div class="text-base font-semibold text-gray-900 dark:text-white" id="name-add-event-modal">
						{{ $user['name'] }}
					</div>
				</div>
			</div>

			<div class="grid gap-4 mb-4 grid-cols-2">
				<div class="col-span-2">
					<x-input-label for="title" :value="__('Title')" />
					<x-event.text-input type="text" name="title" id="title" placeholder="Event title" required />
				</div>
				<div class="col-span-2">
					<x-input-label for="description" :value="__('Description')" />
					<textarea id="description" name="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-3xl border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Event description"></textarea>
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="start_time" :value="__('Start Time')" />
					<x-event.text-input type="datetime-local" name="start_time" id="start_time" required />
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="end_time" :value="__('End Time')" />
					<x-event.text-input type="datetime-local" name="end_time" id="end_time" required />
				</div>
				<div class="col-span-2">
					<x-input-label for="location" :value="__('Location')" />
					<x-event.text-input type="text" name="location" id="location" placeholder="Event location" required />
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="capacity" :value="__('Capacity')" />
					<x-event.text-input type="number" name="capacity" id="capacity" placeholder="Number of attendees" required />
				</div>
				<div class="col-span-2 sm:col-span-1">
					<x-input-label for="status" :value="__('Status')" />
					<select id="status" name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-3xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
						<option value="open">Open</option>
						<option value="closed">Closed</option>
						<option value="canceled">Canceled</option>
					</select>
				</div>
			</div>

			<x-text-error id="error-add-event-modal" class="mb-4"></x-text-error>

			<button class="inline-flex items-center text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
				<svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
				</svg>
				Add Event
			</button>
		</form>
	</x-event.modal>
</x-app-layout>
<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
			<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
				<div class="p-6 bg-white rounded-3xl shadow-sm dark:bg-gray-800">
					<h5 id="created_events" class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
						{{ $created_events }}
					</h5>
					<p class="mb-3 font-normal text-gray-700 dark:text-gray-400">My Created Events</p>

					<div class="flex justify-between h-10">
						<button onclick="openModalAddEvent()" class="inline-flex items-center text-white bg-green-700 hover:bg-green-800 focus:outline-none focus:ring-4 focus:ring-green-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
							<svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
								<path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
							</svg>
							Add Event
						</button>

						<a href="{{ route('events', ['creator' => $user['user']]) }}" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
							See all my registered
							<svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
							</svg>
						</a>
					</div>
				</div>

				<div class="p-6 bg-white rounded-3xl shadow-sm dark:bg-gray-800">
					<div class="flex justify-between gap-2">
						<div>
							<h5 id="created_events" class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
								{{ $open_events }}
							</h5>
							<p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Open</p>
						</div>

						<div>
							<h5 id="created_events" class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
								{{ $closed_events }}
							</h5>
							<p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Closed</p>
						</div>

						<div>
							<h5 id="created_events" class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
								{{ $canceled_events }}
							</h5>
							<p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Canceled</p>
						</div>
					</div>

					<div class="flex justify-end h-10">
						<a href="{{ route('events') }}" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
							See all
							<svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
							</svg>
						</a>
					</div>
				</div>

				<div class="p-6 bg-white rounded-3xl shadow-sm dark:bg-gray-800">
					<h5 id="created_events" class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
						{{ $attending_events }}
					</h5>
					<p class="mb-3 font-normal text-gray-700 dark:text-gray-400">Events I'm attending</p>

					<div class="flex justify-end h-10">
						<a href="{{ route('events', ['attendees' => $user['user']]) }}" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
							See events I'm attending
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

						showToast();
					}
				})
				.catch(error => {
					console.log('aqui');
					alert('An error occurred while creating the event.');
				});
			});

			// Toast functions
			function showToast() {
				const toast = document.getElementById('toast-success');

				toast.classList.remove('hidden');
				
				setTimeout(() => {
					toast.classList.add('hidden');
				}, 5000);
			}

			document.querySelector('[aria-label="Close"]').addEventListener('click', function() {
				const toast = document.getElementById('toast-success');
				toast.classList.add('hidden');
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

	<div id="toast-success" class="fixed right-5 bottom-5 hidden flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow-lg dark:text-gray-400 dark:bg-gray-800" role="alert">
		<div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
			<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
				<path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
			</svg>
			<span class="sr-only">Check icon</span>
		</div>
		<div class="ms-3 text-sm font-normal">successfully created event.</div>
		<button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" data-dismiss-target="#toast-success" aria-label="Close">
			<span class="sr-only">Close</span>
			<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
				<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
			</svg>
		</button>
	</div>
</x-app-layout>
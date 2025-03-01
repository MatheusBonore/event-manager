<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
			{{ __('Events') }}
		</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
							@foreach ($events as $event)
								<tr class="{{ $loop->last ? 'bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-600' : 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
									<th scope="row" class="flex items-center px-6 py-4 text-gray-900 whitespace-nowrap dark:text-white">
										<x-dashboard.avatar class="relative" :value="$event['user']['initials_name']" />
										<div class="ps-3">
											<div class="text-base font-semibold">{{ $event['title'] }}</div>
											<div class="font-normal text-gray-500">{{ $event['truncated_description'] }}</div>
										</div>
									</th>
									<td class="px-6 py-4">
										<div class="flex -space-x-4 rtl:space-x-reverse">
											@foreach ($event['users'] as $index => $user)
												@if ($index < 5)
													<x-dashboard.avatar class="relative z-[{{ $index + 10 }}]" :value="$user['initials_name']" />
												@endif
											@endforeach

											@if (count($event['users']) > 5)
												<a class="relative z-[{{ 15 }}] flex items-center justify-center w-10 h-10 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800" href="#">
													+{{ count($event['users']) - 5 }}
												</a>
											@endif
										</div>
									</td>
									<td class="px-6 py-4">
										{{ $event['location'] }}
									</td>
									<td class="px-6 py-4 text-center">
										{{ $event['users']->count() }} /
										{{ $event['capacity'] }}
									</td>
									<td class="px-6 py-4 text-center">
										<div class="flex text-center items-center">
											@php
												switch ($event['status']) {
													case 'open':
														$color = 'bg-green-500';
														$status_text = 'Open';
														break;
													case 'canceled':
														$color = 'bg-red-500';
														$status_text = 'Canceled';
														break;
													case 'closed':
														$color = 'bg-gray-500';
														$status_text = 'Closed';
														break;
													default:
														$color = 'bg-blue-500';
														$status_text = 'Unknown';
														break;
												}
											@endphp
											<div class="h-2.5 w-2.5 rounded-full {{ $color }} me-2"></div> {{ $status_text }}
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
</x-app-layout>
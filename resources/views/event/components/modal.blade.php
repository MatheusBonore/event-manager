@props([
	'name',
	'title' => 'Modal',
	'show' => false,
	'maxWidth' => '2xl'
])

@php
	$maxWidth = [
		'sm' => 'sm:max-w-sm',
		'md' => 'sm:max-w-md',
		'lg' => 'sm:max-w-lg',
		'xl' => 'sm:max-w-xl',
		'2xl' => 'sm:max-w-2xl',
	][$maxWidth];
@endphp

<div class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: {{ $show ? 'block' : 'none' }};"
	x-data="{
		show: @js($show),
		focusables() {
			let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
			return [...$el.querySelectorAll(selector)].filter(el => !el.hasAttribute('disabled'));
		},
		firstFocusable() { return this.focusables()[0] },
		lastFocusable() { return this.focusables().slice(-1)[0] },
		nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
		prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
		nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
		prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1 },
	}"
	x-init="$watch('show', value => {
		if (value) {
			document.body.classList.add('overflow-y-hidden');
			{{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
		} else {
			document.body.classList.remove('overflow-y-hidden');
		}
	})"
	x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
	x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
	x-on:close.stop="show = false"
	x-on:keydown.escape.window="show = false"
	x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
	x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
	x-show="show"
>
	<div class="fixed inset-0 transform transition-all"
		x-show="show"
		x-on:click="show = false"
		x-transition:enter="ease-out duration-300"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-100"
		x-transition:leave="ease-in duration-200"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
	>
		<div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
	</div>

	<div class="mb-6 bg-white dark:bg-gray-800 rounded-3xl overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto p-6"
		x-show="show"
		x-transition:enter="ease-out duration-300"
		x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
		x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
		x-transition:leave="ease-in duration-200"
		x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
		x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
	>
		<div class="relative w-full ">
			<div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 border-gray-200">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white">
					{{-- Create New Product --}}
					{{ $title }}
				</h3>
				<button type="button" x-on:click="$dispatch('close')" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
					<svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
						<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
					</svg>
					<span class="sr-only">Close modal</span>
				</button>
			</div>

			{{ $slot }}
		</div>
	</div>
</div>

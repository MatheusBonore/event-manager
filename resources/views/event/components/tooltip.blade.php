@props([
	'id' => 'tooltip-'. uniqid()
])

<div id="{{ $id }}" role="tooltip" {{ $attributes->merge(['class' => "absolute z-10 invisible inline-block max-w-64 px-3 text-sm font-medium text-white transition-opacity duration-300 bg-gray-900 rounded-lg shadow-xs opacity-0 tooltip dark:bg-gray-700 whitespace-pre-line"]) }}>
	{{ $slot }}
	<div class="tooltip-arrow" data-popper-arrow></div>
</div>
@props([
	'name' => 'avatar-' . uniqid(),
	'value'
])

<div {{ $attributes->merge(['class' => "relative inline-flex items-center justify-center w-10 h-10 overflow-hidden border-2 border-white dark:border-gray-800 bg-gray-100 rounded-full dark:bg-gray-600"]) }}>
	<span class="font-medium text-gray-600 dark:text-gray-300">
		<span id="{{ $name }}">{{ $value }}</span>
	</span>
</div>
<?php

	namespace App\View\Components\event;

	use Closure;
	use Illuminate\View\Component;
	use Illuminate\Contracts\View\View;

	class TextInput extends Component {
		public function render(): View|Closure|string {
			return view('event.components.text-input');
		}
	}

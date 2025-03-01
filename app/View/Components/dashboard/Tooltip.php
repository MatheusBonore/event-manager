<?php

	namespace App\View\Components\dashboard;

	use Closure;
	use Illuminate\View\Component;
	use Illuminate\Contracts\View\View;

	class Tooltip extends Component {
		public function render(): View|Closure|string {
			return view('dashboard.components.tooltip');
		}
	}

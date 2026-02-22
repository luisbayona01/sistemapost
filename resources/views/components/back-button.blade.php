@props(['href' => null, 'text' => 'Volver'])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200']) }}>
        <i class="fas fa-arrow-left"></i>
        <span>{{ $text }}</span>
    </a>
@else
    <button type="button" onclick="window.history.back()" {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors duration-200']) }}>
        <i class="fas fa-arrow-left"></i>
        <span>{{ $text }}</span>
    </button>
@endif

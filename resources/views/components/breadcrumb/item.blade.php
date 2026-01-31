@props([
'active' => false,
'href' => null,
'content'
])

<div class="flex items-center">
    @if ($href)
    <a href="{{ $href }}" class="hover:text-gray-900">
        {{ $content }}
    </a>
    @else
    <span class="text-gray-900 font-semibold">{{ $content }}</span>
    @endif
    @if (!$active)
    <span class="mx-2 text-gray-600">/</span>
    @endif
</div>

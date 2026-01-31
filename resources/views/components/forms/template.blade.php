@props([
'action',
'method',
'patch' => false,
'file' => false
])
<div class="bg-white rounded-lg shadow">
    <form action="{{ $action }}" method="{{ $method }}" @if($file) enctype="multipart/form-data" @endif>

        @if ($patch)
        @method('PATCH')
        @endif

        @csrf

        @if (isset($header))
        <div class="p-4 border-b border-gray-200">
            {{$header}}
        </div>
        @endif

        <div class="p-6">
            {{$slot}}
        </div>

        <div class="p-4 border-t border-gray-200 text-center">
            {{$footer}}
        </div>

    </form>
</div>

<div class="space-y-1">
    <button
        type="button"
        onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.fa-chevron-right').classList.toggle('rotate-90');"
        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900"
        role="button"
        aria-expanded="false">
        <i class="{{$icon}} w-5 text-center"></i>
        <span class="flex-1 text-left">{{$content}}</span>
        <i class="fas fa-chevron-right w-4 transition-transform duration-300"></i>
    </button>

    <nav class="hidden pl-8 space-y-1" role="navigation">
        {{$slot}}
    </nav>
</div>

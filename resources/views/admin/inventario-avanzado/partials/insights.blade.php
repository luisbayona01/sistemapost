@if(isset($insights) && count($insights) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        @foreach($insights as $insight)
            <div
                class="relative group overflow-hidden bg-white/80 backdrop-blur-md border border-slate-100 p-5 rounded-[2rem] shadow-xl shadow-slate-200/50 hover:shadow-indigo-100 transition-all duration-500">
                <!-- Decorative background elements -->
                <div
                    class="absolute -right-4 -top-4 w-16 h-16 bg-gradient-to-br {{ $insight['type'] == 'danger' ? 'from-rose-500/10' : ($insight['type'] == 'warning' ? 'from-amber-500/10' : 'from-indigo-500/10') }} to-transparent rounded-full blur-2xl">
                </div>

                <div class="flex items-start gap-4">
                    <div
                        class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center text-lg
                                {{ $insight['type'] == 'danger' ? 'bg-rose-50 text-rose-600' :
                    ($insight['type'] == 'warning' ? 'bg-amber-50 text-amber-600' :
                        ($insight['type'] == 'success' ? 'bg-emerald-50 text-emerald-600' : 'bg-indigo-50 text-indigo-600')) }}">
                        <i class="{{ $insight['icon'] ?? 'fas fa-brain' }}"></i>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4
                                class="text-[10px] font-black uppercase tracking-widest {{ $insight['type'] == 'danger' ? 'text-rose-400' : ($insight['type'] == 'warning' ? 'text-amber-400' : 'text-slate-400') }}">
                                {{ $insight['title'] }}
                            </h4>
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $insight['type'] == 'danger' ? 'bg-rose-500 animate-ping' : ($insight['type'] == 'warning' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                        </div>
                        <p class="mt-1.5 text-xs text-slate-600 leading-relaxed font-medium">
                            {{ $insight['message'] }}
                        </p>
                    </div>
                </div>

                <!-- Bottom accent bar -->
                <div
                    class="absolute bottom-0 left-1/2 -translate-x-1/2 w-1/3 h-1 rounded-t-full 
                            {{ $insight['type'] == 'danger' ? 'bg-rose-400' : ($insight['type'] == 'warning' ? 'bg-amber-400' : 'bg-indigo-400') }} opacity-40">
                </div>
            </div>
        @endforeach
    </div>
@endif

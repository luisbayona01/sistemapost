<?php
use App\Models\Empresa;
$empresa = Empresa::first();
?>
<nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between h-16 px-6 max-w-full">
        <!-- Left: Brand + Toggle -->
        <div class="flex items-center gap-4">
            <!-- Sidebar Toggle -->
            <button type="button"
                class="inline-flex items-center justify-center w-10 h-10 text-slate-500 hover:bg-slate-100 rounded-lg transition-all duration-200 cursor-pointer"
                x-on:click.stop="sidebarOpen = !sidebarOpen" id="sidebarToggle" aria-label="Toggle sidebar">
                <i class="fas fa-bars text-lg"></i>
            </button>

            <!-- Home Button (Casita) -->
            <a href="{{ route('admin.dashboard.index') }}"
                class="inline-flex items-center justify-center w-10 h-10 bg-slate-50 text-slate-500 hover:bg-indigo-600 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-slate-200"
                title="Volver al Panel Administrativo">
                <i class="fas fa-home text-lg"></i>
            </a>

            <!-- Brand -->
            <a class="text-sm font-black text-slate-800 hover:text-indigo-600 transition-colors hidden md:inline-block tracking-tighter uppercase ml-2"
                href="{{ route('admin.dashboard.index') }}">
                {{ $empresa->nombre ?? 'Sistema Paraíso' }}
            </a>
        </div>

        <!-- Center: Search (hidden on mobile) -->
        <div class="hidden lg:flex flex-1 max-w-md mx-8">
            <form class="w-full relative" role="search">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input name="search" type="text" placeholder="Buscar..."
                        class="w-full pl-10 pr-4 py-2 text-sm bg-gray-100 text-gray-900 placeholder-gray-500 rounded-lg border border-transparent focus:outline-none focus:bg-white focus:border-blue-300 focus:ring-2 focus:ring-blue-200 transition-all duration-200"
                        aria-label="Buscar en el panel" />
                </div>
            </form>
        </div>

        <!-- Right: Actions -->
        <div class="flex items-center gap-2">
            @auth
                <!-- Notifications Dropdown -->
                <div class="relative group">
                    <button
                        class="relative inline-flex items-center justify-center w-10 h-10 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all duration-200"
                        id="notificationsDropdown" aria-label="Notificaciones" aria-haspopup="true">
                        <i class="fas fa-bell text-lg"></i>
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <span
                                class="absolute top-1 right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </button>

                    <!-- Notifications Dropdown Menu -->
                    <div
                        class="absolute right-0 w-96 mt-2 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-40">
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="font-semibold text-gray-900">Notificaciones</h3>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span
                                    class="inline-flex items-center justify-center px-3 py-1 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="max-h-80 overflow-y-auto">
                            @forelse (Auth::user()->unreadNotifications->take(5) as $notification)
                                <a href="#"
                                    class="block px-6 py-3 hover:bg-blue-50 border-b border-gray-100 last:border-b-0 transition-colors duration-200">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $notification->data['message'] ?? 'Nueva notificación' }}
                                    </p>
                                    <time
                                        class="text-xs text-gray-500 mt-1 block">{{ $notification->created_at->diffForHumans() }}</time>
                                </a>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    <i class="fas fa-inbox text-2xl mb-2 block opacity-50"></i>
                                    <p class="text-sm">Sin notificaciones nuevas</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Footer -->
                        @if(Auth::user()->unreadNotifications->count() > 0)
                            <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                                <a href="#"
                                    class="text-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors block">
                                    Ver todas las notificaciones
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative group">
                    <button
                        class="inline-flex items-center justify-center w-10 h-10 text-gray-600 hover:bg-gray-100 hover:text-gray-900 rounded-lg transition-all duration-200"
                        aria-label="Menú de usuario" aria-haspopup="true">
                        <i class="fas fa-circle-user text-xl"></i>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div
                        class="absolute right-0 w-56 mt-2 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-40">
                        <!-- User Info -->
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-br from-blue-50 to-indigo-50">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-600 mt-1">{{ Auth::user()->email }}</p>
                        </div>

                        <!-- Menu Items -->
                        <div class="py-2">
                            @can('ver-perfil')
                                <a href="{{ route('profile.index') }}"
                                    class="flex items-center gap-3 px-6 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-gear text-gray-400 w-4"></i>
                                    <span>Configuraciones</span>
                                </a>
                            @endcan

                            @can('ver-registro-actividad')
                                <a href="{{ route('root.activity-log.index') }}"
                                    class="flex items-center gap-3 px-6 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                    <i class="fas fa-history text-gray-400 w-4"></i>
                                    <span>Registro de actividad</span>
                                </a>
                            @endcan

                            <div class="border-t border-gray-100 my-2"></div>

                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-3 px-6 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                    <i class="fas fa-sign-out-alt text-red-400 w-4"></i>
                                    <span>Cerrar sesión</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <!-- Guest Link -->
                <a href="{{ route('login.index') }}"
                    class="text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors">
                    Iniciar Sesión
                </a>
            @endauth
        </div>
    </div>
</nav>

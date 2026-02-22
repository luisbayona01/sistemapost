<aside id="layoutSidenav_nav"
    class="w-64 bg-slate-900 text-white shadow-xl flex flex-col h-screen fixed left-0 top-16 z-40 border-r border-slate-800 transition-transform duration-300 overflow-y-auto -translate-x-full"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    <nav class="flex flex-col h-full font-light">
        <!-- Main Navigation -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-4">
            @auth
                <!-- Dashboard -->
                <div>
                    <x-nav.nav-link content='Panel Administrativo' icon='fas fa-chart-pie'
                        :href="route('admin.dashboard.index')" class="text-white hover:text-emerald-400" />

                    @if(auth()->user()->hasRole('Root'))
                        <x-nav.nav-link content='Módulos del Sistema' icon='fas fa-cubes' :href="route('admin.modulos.index')"
                            class="text-emerald-400 font-bold" />
                    @endif
                    @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                        <x-nav.nav-link content='Control Ejecutivo (Móvil)' icon='fas fa-mobile-alt text-emerald-400'
                            :href="route('executive.dashboard')" />
                    @endif
                </div>
            @endauth

            <!-- PUNTO DE VENTA (Priority) -->
            @if(\App\Helpers\ModuleHelper::isEnabled('pos'))
                <div class="pt-2">
                    <x-nav.nav-link content='Ventas (POS)' icon='fas fa-cash-register' :href="route('pos.index')"
                        class="text-emerald-400 font-black text-lg py-3" />
                </div>
            @endif

            <!-- Módulo CINE -->
            @if(\App\Helpers\ModuleHelper::isEnabled('cinema'))
                <div class="pt-2">
                    <p class="px-3 text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Boletería & Cine</p>

                    <div class="px-3 py-2 bg-slate-800/50 rounded-lg border border-slate-700">
                        <x-nav.nav-link content='Cartelera (Horarios)' icon='fas fa-calendar-alt'
                            :href="route('funciones.index')" />

                        @can('crear-producto')
                            <x-nav.nav-link content='Añadir Película' icon='fas fa-plus-circle'
                                :href="route('peliculas.create')" />
                        @endcan

                        @can('ver-producto')
                            <x-nav.nav-link content='Catálogo de Cintas' icon='fas fa-film' :href="route('peliculas.index')" />
                        @endcan

                        <x-nav.nav-link content='Distribuidores (Cine)' icon='fas fa-truck-loading'
                            :href="route('distribuidores.index')" />

                        @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                            <x-nav.nav-link content='Tarifas de Entrada' icon='fas fa-ticket-alt'
                                :href="route('admin.tarifas.index')" />
                        @endif
                    </div>
                </div>
            @endif

            <!-- Inventario & Productos -->
            @if(\App\Helpers\ModuleHelper::isEnabled('pos') || \App\Helpers\ModuleHelper::isEnabled('inventory'))
                <div class="pt-2">
                    <p class="px-3 text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Dulcería & Snacks</p>

                    @can('ver-producto')
                        <x-nav.nav-link content='Productos (Dulcería)' icon='fas fa-cookie-bite'
                            :href="route('productos.index')" />
                    @endcan

                    @can('ver-inventario')
                        <x-nav.nav-link content='Inventario / Stock' icon='fas fa-boxes' :href="route('inventario.index')" />
                        <x-nav.nav-link content='Carga Masiva (Excel)' icon='fas fa-file-import'
                            :href="route('inventario.carga-masiva.index')" />
                    @endcan

                    @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                        <x-nav.nav-link content='Facturas de Compra' icon='fas fa-file-invoice-dollar'
                            :href="route('admin.facturas.index')" />
                        <x-nav.nav-link content='Proveedores (General)' icon='fas fa-id-card'
                            :href="route('proveedores.index')" />
                    @endif

                    @can('ver-inventario')
                        @if(\App\Helpers\ModuleHelper::isEnabled('inventory'))
                            <x-nav.link-collapsed id="collapseConfiteria" icon="fa-solid fa-boxes-stacked"
                                content="Gestión de Insumos">
                                <x-nav.link-collapsed-item :href="route('inventario-avanzado.index')" content="Escritorio IA" />
                                <x-nav.link-collapsed-item :href="route('inventario-avanzado.almacen')" content="Inventario" />
                                <x-nav.link-collapsed-item :href="route('inventario-avanzado.cocina')" content="Costos y Recetas" />
                                <x-nav.link-collapsed-item :href="route('inventario-avanzado.auditoria')"
                                    content="Auditoría de Fugas" />
                            </x-nav.link-collapsed>
                        @endif
                    @endcan
                </div>
            @endif


            <!-- Auditoría y Ventas -->
            <div class="pt-2">
                <p class="px-3 text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Auditoría Técnica</p>

                @can('ver-venta')
                    @if(\App\Helpers\ModuleHelper::isEnabled('pos'))
                        <x-nav.link-collapsed id="collapseVentas" icon="fa-solid fa-history" content="Transacciones">
                            <x-nav.link-collapsed-item :href="route('ventas.index')" content="Ventas del Día" />
                            <x-nav.link-collapsed-item :href="route('admin.devoluciones.index')" content="Devoluciones" />
                        </x-nav.link-collapsed>
                    @endif
                @endcan

                @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                    @if(\App\Helpers\ModuleHelper::isEnabled('reports'))
                        <x-nav.link-collapsed id="collapseReportes" icon="fa-solid fa-chart-line" content="Analítica">
                            <x-nav.link-collapsed-item :href="route('admin.reportes.consolidado')"
                                content="Reporte de Ventas" />

                            @if(\App\Helpers\ModuleHelper::isEnabled('cinema'))
                                <x-nav.link-collapsed-item :href="route('admin.reportes.cinema')" content="Ocupación de Cine" />
                                <x-nav.link-collapsed-item :href="route('admin.reportes.peliculas')"
                                    content="Ventas por Película" />
                            @endif

                            @if(\App\Helpers\ModuleHelper::isEnabled('pos'))
                                <x-nav.link-collapsed-item :href="route('admin.reportes.confiteria')"
                                    content="Ventas de Dulcería" />
                            @endif
                        </x-nav.link-collapsed>
                    @endif
                @endif
            </div>

            <!-- Finanzas & Caja -->
            @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador', 'cajero']))
                <div class="pt-2">
                    <p class="px-3 text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Finanzas & Caja</p>
                    <div class="px-3 py-2 bg-slate-800/50 rounded-lg border border-slate-700">
                        <x-nav.nav-link content='Control de Cajas' icon='fas fa-vault' :href="route('admin.cajas.index')" />
                        @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                            <x-nav.nav-link content='Cierre de Jornada' icon='fas fa-check-double'
                                :href="route('admin.cajas.cierre-dia')" />
                            <x-nav.nav-link content='Reporte Fiscal INC' icon='fas fa-file-invoice'
                                :href="route('reports.fiscal.inc')" />
                        @endif
                    </div>
                </div>
            @endif

            <!-- Ajustes -->
            <div class="pt-2">
                <p class="px-3 text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Corporativo</p>
                @if(auth()->user()->hasRole(['Root', 'Gerente', 'administrador']))
                    <x-nav.nav-link content='Datos Empresa' icon='fas fa-building' :href="route('empresa.index')" />
                    <x-nav.nav-link content='Gente & Perfiles' icon='fas fa-users-cog' :href="route('users.index')" />
                @endif
            </div>

        </div>

        @auth
            <!-- User Footer -->
            <div class="border-t border-slate-800 p-4 bg-slate-900 bg-opacity-50">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-sm font-black text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate uppercase font-bold">Terminal Activa</p>
                    </div>
                </div>
            </div>
        @endauth
    </nav>
</aside>
<aside
    id="layoutSidenav_nav"
    class="w-64 bg-white text-gray-900 shadow-lg hidden md:flex md:flex-col h-screen fixed left-0 top-16 z-40 border-r border-gray-200 overflow-y-auto">
    <nav class="flex flex-col h-full">
        <!-- Main Navigation -->
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
            <!-- Inicio Section -->
            <x-nav.heading>Inicio</x-nav.heading>
            <x-nav.nav-link
                content='Panel'
                icon='fas fa-chart-line'
                :href="route('panel')" />

            <!-- Módulos Section -->
            <x-nav.heading>Catálogos</x-nav.heading>

            @can('ver-categoria')
            <x-nav.nav-link
                content='Categorías'
                icon='fa-solid fa-tag'
                :href="route('categorias.index')" />
            @endcan

            @can('ver-presentacione')
            <x-nav.nav-link
                content='Presentaciones'
                icon='fa-solid fa-box'
                :href="route('presentaciones.index')" />
            @endcan

            @can('ver-marca')
            <x-nav.nav-link
                content='Marcas'
                icon='fa-solid fa-tag'
                :href="route('marcas.index')" />
            @endcan

            @can('ver-producto')
            <x-nav.nav-link
                content='Productos'
                icon='fa-solid fa-cube'
                :href="route('productos.index')" />
            @endcan

            <!-- Inventario Section -->
            <x-nav.heading>Inventario</x-nav.heading>

            @can('ver-inventario')
            <x-nav.nav-link
                content='Inventario'
                icon='fa-solid fa-warehouse'
                :href="route('inventario.index')" />
            @endcan

            @can('ver-kardex')
            <x-nav.nav-link
                content='Kardex'
                icon='fa-solid fa-book'
                :href="route('kardex.index')" />
            @endcan

            <!-- Operaciones Section -->
            <x-nav.heading>Operaciones</x-nav.heading>

            @can('ver-cliente')
            <x-nav.nav-link
                content='Clientes'
                icon='fa-solid fa-users'
                :href="route('clientes.index')" />
            @endcan

            @can('ver-proveedore')
            <x-nav.nav-link
                content='Proveedores'
                icon='fa-solid fa-person-dolly'
                :href="route('proveedores.index')" />
            @endcan

            @can('ver-caja')
            <x-nav.nav-link
                content='Cajas'
                icon='fa-solid fa-cash-register'
                :href="route('cajas.index')" />
            @endcan

            <!-- Transacciones Section -->
            <x-nav.heading>Transacciones</x-nav.heading>

            @can('ver-compra')
            <x-nav.link-collapsed
                id="collapseCompras"
                icon="fa-solid fa-shopping-cart"
                content="Compras">
                @can('ver-compra')
                <x-nav.link-collapsed-item :href="route('compras.index')" content="Listar" />
                @endcan
                @can('crear-compra')
                <x-nav.link-collapsed-item :href="route('compras.create')" content="Nueva" />
                @endcan
            </x-nav.link-collapsed>
            @endcan

            @can('ver-venta')
            <x-nav.link-collapsed
                id="collapseVentas"
                icon="fa-solid fa-receipt"
                content="Ventas">
                @can('ver-venta')
                <x-nav.link-collapsed-item :href="route('ventas.index')" content="Listar" />
                @endcan
                @can('crear-venta')
                <x-nav.link-collapsed-item :href="route('ventas.create')" content="Nueva" />
                @endcan
            </x-nav.link-collapsed>
            @endcan

            <!-- Administración Section -->
            @hasrole('administrador')
            <x-nav.heading>Administración</x-nav.heading>

            @can('ver-empresa')
            <x-nav.nav-link
                content='Empresa'
                icon='fa-solid fa-building'
                :href="route('empresa.index')" />
            @endcan

            @can('ver-empleado')
            <x-nav.nav-link
                content='Empleados'
                icon='fa-solid fa-people-group'
                :href="route('empleados.index')" />
            @endcan

            @can('ver-user')
            <x-nav.nav-link
                content='Usuarios'
                icon='fa-solid fa-user-shield'
                :href="route('users.index')" />
            @endcan

            @can('ver-role')
            <x-nav.nav-link
                content='Roles'
                icon='fa-solid fa-shield'
                :href="route('roles.index')" />
            @endcan
            @endhasrole
        </div>

        <!-- User Footer -->
        <div class="border-t border-gray-200 p-4 space-y-3 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="px-2">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Usuario actual</p>
                <p class="text-sm font-bold text-gray-900 mt-2 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-600 truncate mt-1">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </nav>
</aside>

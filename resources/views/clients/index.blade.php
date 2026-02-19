@extends('layouts.panel')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('content')
    <x-ui.card title="Crear cliente" subtitle="Registro rapido para iniciar membresia y credenciales.">
        <form method="POST" action="{{ route('clients.store') }}" class="space-y-4">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Nombre</span>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Apellido</span>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Documento</span>
                    <input type="text" name="document_number" value="{{ old('document_number') }}" required class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Telefono</span>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted md:col-span-2">
                    <span>Ruta de foto (photo_path)</span>
                    <input type="text" name="photo_path" value="{{ old('photo_path') }}" placeholder="storage/clients/foto.jpg" class="ui-input">
                </label>

                <label class="space-y-1 text-sm font-semibold ui-muted">
                    <span>Estado</span>
                    <select name="status" class="ui-input">
                        <option value="active" @selected(old('status', 'active') === 'active')>Activo</option>
                        <option value="inactive" @selected(old('status') === 'inactive')>Inactivo</option>
                    </select>
                </label>
            </div>

            <x-ui.button type="submit" variant="success">Guardar cliente</x-ui.button>
        </form>
    </x-ui.card>

    <x-ui.card title="Clientes del gimnasio" subtitle="Listado operativo para gestion diaria.">
        <form method="GET" action="{{ route('clients.index') }}" class="mb-4 grid gap-3 md:grid-cols-[1fr_auto]">
            <input type="text" name="q" value="{{ $search }}" placeholder="Buscar por documento o nombre..." class="ui-input">
            <x-ui.button type="submit" variant="secondary">Buscar</x-ui.button>
        </form>

        <div class="overflow-x-auto">
            <table class="ui-table min-w-[760px]">
                <thead>
                <tr class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    <th class="px-3 py-3">ID</th>
                    <th class="px-3 py-3">Nombre</th>
                    <th class="px-3 py-3">Documento</th>
                    <th class="px-3 py-3">Estado</th>
                    <th class="px-3 py-3">Detalle</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($clients as $client)
                    <tr class="border-b border-slate-100 text-sm odd:bg-white even:bg-slate-50 dark:border-slate-800 dark:odd:bg-slate-900 dark:even:bg-slate-950/50">
                        <td class="px-3 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ $client->id }}</td>
                        <td class="px-3 py-3 dark:text-slate-100">{{ $client->full_name }}</td>
                        <td class="px-3 py-3 dark:text-slate-200">{{ $client->document_number }}</td>
                        <td class="px-3 py-3">
                            <x-ui.badge :variant="$client->status === 'active' ? 'success' : 'danger'">{{ $client->status }}</x-ui.badge>
                        </td>
                        <td class="px-3 py-3">
                            <x-ui.button :href="route('clients.show', $client->id)" size="sm" variant="secondary">Abrir</x-ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-6 text-center text-sm text-slate-500 dark:text-slate-300">No hay clientes registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    </x-ui.card>
@endsection

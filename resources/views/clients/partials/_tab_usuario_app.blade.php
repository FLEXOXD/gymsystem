@php
    $appUsernameValue = old('app_username', (string) ($client->app_username ?? ''));
    $hasAppUsername = trim((string) ($client->app_username ?? '')) !== '';
    $hasAppPassword = trim((string) ($client->app_password ?? '')) !== '';
    $appAccessEnabled = $hasAppUsername && $hasAppPassword;
@endphp

<div class="grid gap-6 xl:grid-cols-12">
    <div class="space-y-6 xl:col-span-8">
        <x-ui.card title="Usuario app del cliente" subtitle="Edita el usuario usado para iniciar sesión en la app cliente.">
            <form method="POST"
                  action="{{ route('clients.app-account.update', $client->id) }}"
                  class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="active_tab" value="app_access">

                <div class="space-y-2">
                    <label for="app_username" class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Usuario app
                    </label>
                    <input id="app_username"
                           name="app_username"
                           type="text"
                           value="{{ $appUsernameValue }}"
                           autocomplete="off"
                           class="ui-input @error('app_username') border-rose-400 focus:border-rose-400 focus:ring-rose-300/40 @enderror"
                           placeholder="ej: maria.perez">
                    @error('app_username')
                        <p class="text-xs font-semibold text-rose-500 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <x-ui.button type="submit" size="sm">Guardar usuario</x-ui.button>
            </form>
        </x-ui.card>

        <x-ui.card title="Restablecer contraseña app" subtitle="Define una nueva contraseña cuando el cliente la olvide.">
            @if (! $hasAppUsername)
                <x-ui.alert type="warning" class="mb-4">
                    Primero debes guardar un usuario app para este cliente.
                </x-ui.alert>
            @endif

            <form method="POST"
                  action="{{ route('clients.app-password.reset', $client->id) }}"
                  class="space-y-4">
                @csrf
                @method('PATCH')
                <input type="hidden" name="active_tab" value="app_access">

                <div class="space-y-2">
                    <label for="app_password" class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Nueva contraseña app
                    </label>
                    <input id="app_password"
                           name="app_password"
                           type="password"
                           autocomplete="new-password"
                           class="ui-input @error('app_password') border-rose-400 focus:border-rose-400 focus:ring-rose-300/40 @enderror"
                           placeholder="Mínimo 8 caracteres">
                    @error('app_password')
                        <p class="text-xs font-semibold text-rose-500 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="app_password_confirmation" class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-400">
                        Confirmar contraseña
                    </label>
                    <input id="app_password_confirmation"
                           name="app_password_confirmation"
                           type="password"
                           autocomplete="new-password"
                           class="ui-input @error('app_password_confirmation') border-rose-400 focus:border-rose-400 focus:ring-rose-300/40 @enderror"
                           placeholder="Repite la contraseña">
                    @error('app_password_confirmation')
                        <p class="text-xs font-semibold text-rose-500 dark:text-rose-300">{{ $message }}</p>
                    @enderror
                </div>

                @if ($hasAppUsername)
                    <x-ui.button type="submit" size="sm" variant="secondary">
                        Restablecer contraseña
                    </x-ui.button>
                @else
                    <x-ui.button type="submit" size="sm" variant="secondary" disabled>
                        Restablecer contraseña
                    </x-ui.button>
                @endif
            </form>
        </x-ui.card>
    </div>

    <div class="xl:col-span-4">
        <x-ui.card title="Estado de acceso" subtitle="Resumen rápido del acceso cliente PWA.">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-600 dark:text-slate-400">Usuario actual</dt>
                    <dd class="font-semibold text-slate-900 dark:text-slate-100">{{ $hasAppUsername ? $client->app_username : 'Sin usuario' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-600 dark:text-slate-400">Contraseña app</dt>
                    <dd class="font-semibold {{ $hasAppPassword ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">
                        {{ $hasAppPassword ? 'Configurada' : 'No configurada' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <dt class="text-slate-600 dark:text-slate-400">Estado de acceso</dt>
                    <dd class="font-semibold {{ $appAccessEnabled ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">
                        {{ $appAccessEnabled ? 'Habilitado' : 'Pendiente' }}
                    </dd>
                </div>
            </dl>

            <div class="mt-4 rounded-lg border border-slate-300 bg-slate-100 p-3 text-xs text-slate-600 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-300">
                Por seguridad, la contraseña actual nunca se muestra. Si el cliente la olvida, restablécela desde esta pestaña.
            </div>
        </x-ui.card>
    </div>
</div>

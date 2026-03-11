<div class="rounded-2xl border border-slate-300/70 bg-slate-100 p-2.5 dark:border-white/10 dark:bg-slate-900/40">
    <nav class="client-tabs-strip flex flex-nowrap gap-2 overflow-x-auto pb-1 lg:flex-wrap lg:overflow-visible lg:pb-0" aria-label="Tabs de cliente">
        <button type="button"
                class="client-tab-chip ui-button shrink-0 px-4 py-2 text-xs font-bold uppercase tracking-wide"
                x-bind:class="activeTab === 'summary' ? 'ui-button-primary' : 'ui-button-ghost'"
                x-on:click="setTab('summary')">
            Resumen
        </button>

        @if (! empty($canShowProgress))
            <button type="button"
                    class="client-tab-chip ui-button shrink-0 px-4 py-2 text-xs font-bold uppercase tracking-wide"
                    x-bind:class="activeTab === 'progress' ? 'ui-button-primary' : 'ui-button-ghost'"
                    x-on:click="setTab('progress')">
                Rendimiento
            </button>
        @endif

        <button type="button"
                class="client-tab-chip ui-button shrink-0 px-4 py-2 text-xs font-bold uppercase tracking-wide"
                x-bind:class="activeTab === 'membership' ? 'ui-button-primary' : 'ui-button-ghost'"
                x-on:click="setTab('membership')">
            Membresía
        </button>

        <button type="button"
                class="client-tab-chip ui-button shrink-0 px-4 py-2 text-xs font-bold uppercase tracking-wide"
                x-bind:class="activeTab === 'attendance' ? 'ui-button-primary' : 'ui-button-ghost'"
                x-on:click="setTab('attendance')">
            Asistencias
        </button>

        <button type="button"
                class="client-tab-chip ui-button shrink-0 px-4 py-2 text-xs font-bold uppercase tracking-wide"
                x-bind:class="activeTab === 'credentials' ? 'ui-button-primary' : 'ui-button-ghost'"
                x-on:click="setTab('credentials')">
            Credenciales
        </button>

        @if (! empty($canManageClientAccounts))
            <button type="button"
                    class="client-tab-chip ui-button shrink-0 px-4 py-2 text-xs font-bold uppercase tracking-wide"
                    x-bind:class="activeTab === 'app_access' ? 'ui-button-primary' : 'ui-button-ghost'"
                    x-on:click="setTab('app_access')">
                Usuario App
            </button>
        @endif
    </nav>
</div>

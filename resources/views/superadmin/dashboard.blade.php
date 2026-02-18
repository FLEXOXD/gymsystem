@extends('layouts.panel')

@section('title', 'Panel SuperAdmin')
@section('page-title', 'Panel Global')

@section('content')
    <x-ui.card title="Panel Empresarial Global" subtitle="Vista consolidada de suscripciones SaaS por gimnasio.">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400">Total gimnasios</p>
                <p class="mt-2 text-3xl font-black text-slate-900 dark:text-slate-100">{{ (int) $kpis['total_gyms'] }}</p>
            </article>
            <article class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-800 dark:bg-emerald-900/40">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-200">Activos</p>
                <p class="mt-2 text-3xl font-black text-emerald-800 dark:text-emerald-200">{{ (int) $kpis['active_gyms'] }}</p>
            </article>
            <article class="rounded-2xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-800 dark:bg-amber-900/40">
                <p class="text-xs font-bold uppercase tracking-widest text-amber-700 dark:text-amber-200">En Gracia</p>
                <p class="mt-2 text-3xl font-black text-amber-800 dark:text-amber-200">{{ (int) $kpis['grace_gyms'] }}</p>
            </article>
            <article class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-sm dark:border-rose-800 dark:bg-rose-900/40">
                <p class="text-xs font-bold uppercase tracking-widest text-rose-700 dark:text-rose-200">Suspendidos</p>
                <p class="mt-2 text-3xl font-black text-rose-800 dark:text-rose-200">{{ (int) $kpis['suspended_gyms'] }}</p>
            </article>
            <article class="rounded-2xl border border-cyan-200 bg-cyan-50 p-4 shadow-sm dark:border-cyan-800 dark:bg-cyan-900/40">
                <p class="text-xs font-bold uppercase tracking-widest text-cyan-700 dark:text-cyan-200">MRR Estimado</p>
                <p class="mt-2 text-3xl font-black text-cyan-800 dark:text-cyan-200">${{ number_format((float) $kpis['mrr_estimated'], 2) }}</p>
            </article>
            <article class="rounded-2xl border border-indigo-200 bg-indigo-50 p-4 shadow-sm dark:border-indigo-800 dark:bg-indigo-900/40">
                <p class="text-xs font-bold uppercase tracking-widest text-indigo-700 dark:text-indigo-200">Vencen En 7 Dias</p>
                <p class="mt-2 text-3xl font-black text-indigo-800 dark:text-indigo-200">{{ (int) $kpis['vencen_en_7_dias'] }}</p>
            </article>
            <article class="rounded-2xl border border-orange-200 bg-orange-50 p-4 shadow-sm dark:border-orange-800 dark:bg-orange-900/40">
                <p class="text-xs font-bold uppercase tracking-widest text-orange-700 dark:text-orange-200">En Gracia Hoy</p>
                <p class="mt-2 text-3xl font-black text-orange-800 dark:text-orange-200">{{ (int) $kpis['en_gracia_hoy'] }}</p>
            </article>
        </section>

        <div class="mt-5">
            <x-ui.button :href="route('superadmin.gyms.index')" variant="secondary">Ver lista global de gimnasios</x-ui.button>
        </div>
    </x-ui.card>
@endsection

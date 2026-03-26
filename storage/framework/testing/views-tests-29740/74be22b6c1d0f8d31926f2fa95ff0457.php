<?php $__env->startSection('title', 'Clases'); ?>
<?php $__env->startSection('page-title', 'Clases y reservas'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .classes-page {
        display: grid;
        gap: 1rem;
    }

    .classes-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid rgb(16 185 129 / 0.18);
        border-radius: 1.25rem;
        padding: 1.15rem;
        background:
            radial-gradient(circle at top right, rgb(45 212 191 / 0.12), transparent 30%),
            radial-gradient(circle at bottom left, rgb(14 165 233 / 0.08), transparent 24%),
            linear-gradient(155deg, rgb(255 255 255 / 0.98), rgb(248 250 252 / 0.95));
        box-shadow: 0 24px 46px -36px rgb(15 23 42 / 0.32);
    }

    .theme-dark .classes-hero,
    .dark .classes-hero {
        border-color: rgb(45 212 191 / 0.22);
        background:
            radial-gradient(circle at top right, rgb(45 212 191 / 0.08), transparent 30%),
            radial-gradient(circle at bottom left, rgb(14 165 233 / 0.08), transparent 24%),
            linear-gradient(160deg, rgb(3 10 24 / 0.94), rgb(10 20 35 / 0.92));
        box-shadow: 0 28px 52px -38px rgb(2 8 23 / 0.88);
    }

    .classes-kpi-grid {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }

    .classes-kpi-card {
        border: 1px solid rgb(148 163 184 / 0.24);
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        background: rgb(255 255 255 / 0.78);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.86);
    }

    .theme-dark .classes-kpi-card,
    .dark .classes-kpi-card {
        border-color: rgb(71 85 105 / 0.68);
        background: rgb(15 23 42 / 0.62);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .classes-kpi-label {
        font-size: 0.7rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .classes-kpi-label,
    .dark .classes-kpi-label {
        color: rgb(148 163 184 / 0.9);
    }

    .classes-kpi-value {
        margin-top: 0.45rem;
        font-size: 1.55rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.05em;
        color: rgb(15 23 42 / 0.98);
    }

    .theme-dark .classes-kpi-value,
    .dark .classes-kpi-value {
        color: rgb(248 250 252 / 0.98);
    }

    .classes-split {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 1200px) {
        .classes-split {
            grid-template-columns: minmax(0, 0.96fr) minmax(0, 1.04fr);
            align-items: start;
        }
    }

    .classes-form-grid {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
    }

    .classes-table-wrap {
        overflow: auto;
        border-radius: 1rem;
        border: 1px solid rgb(148 163 184 / 0.24);
    }

    .theme-dark .classes-table-wrap,
    .dark .classes-table-wrap {
        border-color: rgb(71 85 105 / 0.62);
    }

    .classes-soft-note {
        border-radius: 1rem;
        border: 1px dashed rgb(148 163 184 / 0.55);
        padding: 1rem;
        text-align: center;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $routeParams = is_array($routeParams ?? null) ? $routeParams : [];
        $formatClassStatus = static function (?string $status): array {
            return match (trim((string) $status)) {
                'cancelled' => ['label' => 'Cancelada', 'variant' => 'danger'],
                default => ['label' => 'Programada', 'variant' => 'success'],
            };
        };
    ?>

    <div class="classes-page">
        <section class="classes-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl space-y-2">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-300">Modulo premium</p>
                    <h2 class="text-2xl font-black tracking-tight text-slate-950 dark:text-white">Agenda de clases, cupos y avisos en un solo lugar</h2>
                    <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Organiza las clases del gimnasio, revisa reservas y manda avisos rápidos a los clientes que ya tienen cupo o siguen en lista de espera.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('classes.index', $routeParams + ['date' => now()->toDateString()]),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('classes.index', $routeParams + ['date' => now()->toDateString()])),'variant' => 'secondary']); ?>Hoy <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if($nextClass): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('classes.show', $routeParams + ['gymClass' => $nextClass->id]),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('classes.show', $routeParams + ['gymClass' => $nextClass->id])),'variant' => 'primary']); ?>Ver próxima clase <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="classes-kpi-grid mt-4">
                <article class="classes-kpi-card">
                    <p class="classes-kpi-label">Clases hoy</p>
                    <p class="classes-kpi-value"><?php echo e((int) $todayClassesCount); ?></p>
                </article>
                <article class="classes-kpi-card">
                    <p class="classes-kpi-label">Reservas hoy</p>
                    <p class="classes-kpi-value"><?php echo e((int) $todayReservationsCount); ?></p>
                </article>
                <article class="classes-kpi-card">
                    <p class="classes-kpi-label">Lista de espera</p>
                    <p class="classes-kpi-value"><?php echo e((int) $waitlistCount); ?></p>
                </article>
                <article class="classes-kpi-card">
                    <p class="classes-kpi-label">Próxima clase</p>
                    <p class="classes-kpi-value text-base !leading-tight !tracking-normal">
                        <?php echo e($nextClass ? $nextClass->name : 'Sin agenda'); ?>

                    </p>
                </article>
            </div>
        </section>

        <?php if($isGlobalScope): ?>
            <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'warning','title' => 'Vista global']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'warning','title' => 'Vista global']); ?>
                Estás viendo varias sedes al mismo tiempo. Puedes revisar agenda y reservas, pero para crear o editar clases debes entrar a una sede específica.
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
        <?php endif; ?>

        <section class="classes-split">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Nueva clase','subtitle' => 'Programa una sesión con horario, cupos, instructor y espacio.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Nueva clase','subtitle' => 'Programa una sesión con horario, cupos, instructor y espacio.']); ?>
                <?php if(! $canCreateClasses): ?>
                    <div class="classes-soft-note text-sm text-slate-600 dark:text-slate-300">
                        Solo el dueño y desde una sede específica puede crear clases nuevas.
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('classes.store', $routeParams)); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <div class="classes-form-grid">
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Nombre</span>
                                <input type="text" name="name" value="<?php echo e(old('name')); ?>" required maxlength="120" class="ui-input" placeholder="Ej: Boxeo funcional">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Categoría</span>
                                <input type="text" name="category" value="<?php echo e(old('category')); ?>" maxlength="80" class="ui-input" placeholder="Boxeo, Yoga, Cross">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Nivel</span>
                                <input type="text" name="level" value="<?php echo e(old('level')); ?>" maxlength="40" class="ui-input" placeholder="Inicial, Intermedio">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Instructor</span>
                                <input type="text" name="instructor_name" value="<?php echo e(old('instructor_name')); ?>" maxlength="120" class="ui-input" placeholder="Nombre del instructor">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Sala</span>
                                <input type="text" name="room_name" value="<?php echo e(old('room_name')); ?>" maxlength="80" class="ui-input" placeholder="Sala principal">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Cupos</span>
                                <input type="number" name="capacity" value="<?php echo e(old('capacity', 12)); ?>" min="1" max="300" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Inicio</span>
                                <input type="datetime-local" name="starts_at" value="<?php echo e(old('starts_at', now()->setTime(18, 0)->format('Y-m-d\\TH:i'))); ?>" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Fin</span>
                                <input type="datetime-local" name="ends_at" value="<?php echo e(old('ends_at', now()->setTime(19, 0)->format('Y-m-d\\TH:i'))); ?>" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Estado</span>
                                <select name="status" class="ui-input">
                                    <option value="scheduled" <?php if(old('status', 'scheduled') === 'scheduled'): echo 'selected'; endif; ?>>Programada</option>
                                    <option value="cancelled" <?php if(old('status') === 'cancelled'): echo 'selected'; endif; ?>>Cancelada</option>
                                </select>
                            </label>
                        </div>

                        <label class="block space-y-2 text-sm">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Descripción</span>
                            <textarea name="description" rows="4" class="ui-input" placeholder="Qué incluye, recomendaciones o detalles del día."><?php echo e(old('description')); ?></textarea>
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                            <input type="checkbox" name="allow_waitlist" value="1" <?php if(old('allow_waitlist', true)): echo 'checked'; endif; ?>>
                            Permitir lista de espera cuando ya no haya cupos.
                        </label>

                        <div class="flex flex-wrap gap-3">
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary']); ?>Guardar clase <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                            <span class="text-xs text-slate-500 dark:text-slate-400">Las reservas se habilitan en la app móvil del cliente.</span>
                        </div>
                    </form>
                <?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>

            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Agenda próxima','subtitle' => 'Consulta la programación de las próximas dos semanas.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Agenda próxima','subtitle' => 'Consulta la programación de las próximas dos semanas.']); ?>
                <form method="GET" action="<?php echo e(route('classes.index', $routeParams)); ?>" class="mb-4 grid gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto]">
                    <label class="space-y-2 text-sm">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">Buscar clase o instructor</span>
                        <input type="search" name="search" value="<?php echo e($search); ?>" class="ui-input" placeholder="Ej: boxeo, zumba, Ana">
                    </label>
                    <label class="space-y-2 text-sm">
                        <span class="font-semibold text-slate-700 dark:text-slate-200">Desde</span>
                        <input type="date" name="date" value="<?php echo e($selectedDate); ?>" class="ui-input">
                    </label>
                    <div class="flex items-end gap-2">
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary']); ?>Filtrar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('classes.index', $routeParams),'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('classes.index', $routeParams)),'variant' => 'ghost']); ?>Limpiar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    </div>
                </form>

                <?php if($classes->isEmpty()): ?>
                    <div class="classes-soft-note text-sm text-slate-600 dark:text-slate-300">
                        No encontramos clases dentro del rango seleccionado.
                    </div>
                <?php else: ?>
                    <div class="classes-table-wrap">
                        <table class="ui-table min-w-[960px] text-sm">
                            <thead>
                                <tr>
                                    <th>Clase</th>
                                    <th>Horario</th>
                                    <th>Instructor</th>
                                    <th>Cupos</th>
                                    <th>Estado</th>
                                    <th>Sede</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $statusMeta = $formatClassStatus($classItem->status);
                                        $gymLabel = trim((string) ($classItem->gym?->name ?? 'Sede actual'));
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="space-y-1">
                                                <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($classItem->name); ?></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    <?php echo e($classItem->category ?: 'Clase general'); ?>

                                                    <?php if($classItem->room_name): ?>
                                                        · <?php echo e($classItem->room_name); ?>

                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="space-y-1">
                                                <p><?php echo e(optional($classItem->starts_at)->format('d/m/Y H:i')); ?></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">Hasta <?php echo e(optional($classItem->ends_at)->format('H:i')); ?></p>
                                            </div>
                                        </td>
                                        <td><?php echo e($classItem->instructor_name ?: '-'); ?></td>
                                        <td>
                                            <div class="space-y-1">
                                                <p><?php echo e((int) $classItem->reserved_count); ?>/<?php echo e((int) $classItem->capacity); ?></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">Espera: <?php echo e((int) $classItem->waitlist_count); ?></p>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $statusMeta['variant']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusMeta['variant'])]); ?><?php echo e($statusMeta['label']); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                                        </td>
                                        <td><?php echo e($gymLabel); ?></td>
                                        <td>
                                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('classes.show', $routeParams + ['gymClass' => $classItem->id]),'variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('classes.show', $routeParams + ['gymClass' => $classItem->id])),'variant' => 'ghost','size' => 'sm']); ?>Abrir <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $attributes = $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93)): ?>
<?php $component = $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93; ?>
<?php unset($__componentOriginaldae4cd48acb67888a4631e1ba48f2f93); ?>
<?php endif; ?>
        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/classes/index.blade.php ENDPATH**/ ?>
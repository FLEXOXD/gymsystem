

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

    .classes-kpi-card,
    .classes-modal-stat {
        border: 1px solid rgb(148 163 184 / 0.24);
        border-radius: 1rem;
        padding: 0.9rem 1rem;
        background: rgb(255 255 255 / 0.78);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.86);
    }

    .theme-dark .classes-kpi-card,
    .dark .classes-kpi-card,
    .theme-dark .classes-modal-stat,
    .dark .classes-modal-stat {
        border-color: rgb(71 85 105 / 0.68);
        background: rgb(15 23 42 / 0.62);
        box-shadow: inset 0 1px 0 rgb(255 255 255 / 0.04);
    }

    .classes-kpi-label,
    .classes-modal-stat-label {
        font-size: 0.7rem;
        font-weight: 900;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgb(71 85 105 / 0.92);
    }

    .theme-dark .classes-kpi-label,
    .dark .classes-kpi-label,
    .theme-dark .classes-modal-stat-label,
    .dark .classes-modal-stat-label {
        color: rgb(148 163 184 / 0.9);
    }

    .classes-kpi-value,
    .classes-modal-stat-value {
        margin-top: 0.45rem;
        font-size: 1.55rem;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -0.05em;
        color: rgb(15 23 42 / 0.98);
    }

    .theme-dark .classes-kpi-value,
    .dark .classes-kpi-value,
    .theme-dark .classes-modal-stat-value,
    .dark .classes-modal-stat-value {
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

    .classes-form-alert {
        border-radius: 1rem;
        border: 1px solid rgb(248 113 113 / 0.35);
        background: rgb(254 242 242 / 0.9);
        padding: 0.9rem 1rem;
        color: rgb(153 27 27);
    }

    .theme-dark .classes-form-alert,
    .dark .classes-form-alert {
        border-color: rgb(248 113 113 / 0.28);
        background: rgb(69 10 10 / 0.45);
        color: rgb(254 202 202);
    }

    .classes-modal-summary {
        display: grid;
        gap: 0.85rem;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }

    .classes-weekday-picker {
        display: grid;
        gap: 0.65rem;
    }

    .classes-weekday-grid {
        display: grid;
        gap: 0.55rem;
        grid-template-columns: repeat(auto-fit, minmax(72px, 1fr));
    }

    .classes-weekday-option {
        position: relative;
    }

    .classes-weekday-option input {
        position: absolute;
        inset: 0;
        opacity: 0;
        pointer-events: none;
    }

    .classes-weekday-chip {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.95rem;
        border: 1px solid rgb(148 163 184 / 0.24);
        background: rgb(255 255 255 / 0.78);
        padding: 0.72rem 0.55rem;
        font-size: 0.78rem;
        font-weight: 900;
        letter-spacing: 0.04em;
        color: rgb(51 65 85 / 0.95);
        cursor: pointer;
        transition: border-color 0.18s ease, background 0.18s ease, color 0.18s ease, transform 0.18s ease;
    }

    .classes-weekday-option input:checked + .classes-weekday-chip {
        border-color: rgb(6 182 212 / 0.46);
        background: linear-gradient(135deg, rgb(14 165 233 / 0.14), rgb(16 185 129 / 0.14));
        color: rgb(14 116 144);
        transform: translateY(-1px);
    }

    .theme-dark .classes-weekday-chip,
    .dark .classes-weekday-chip {
        border-color: rgb(71 85 105 / 0.68);
        background: rgb(15 23 42 / 0.62);
        color: rgb(226 232 240 / 0.94);
    }

    .theme-dark .classes-weekday-option input:checked + .classes-weekday-chip,
    .dark .classes-weekday-option input:checked + .classes-weekday-chip {
        border-color: rgb(34 211 238 / 0.44);
        background: linear-gradient(135deg, rgb(8 47 73 / 0.92), rgb(6 78 59 / 0.9));
        color: rgb(165 243 252);
    }

    .classes-weekday-summary {
        font-size: 0.72rem;
        line-height: 1.4;
        color: rgb(100 116 139 / 0.94);
    }

    .theme-dark .classes-weekday-summary,
    .dark .classes-weekday-summary {
        color: rgb(148 163 184 / 0.9);
    }

    .classes-price-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        border: 1px solid transparent;
        padding: 0.28rem 0.72rem;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .classes-price-pill.is-free {
        border-color: rgb(16 185 129 / 0.38);
        background: rgb(16 185 129 / 0.14);
        color: rgb(5 150 105);
    }

    .classes-price-pill.is-paid {
        border-color: rgb(245 158 11 / 0.42);
        background: linear-gradient(135deg, rgb(245 158 11 / 0.16), rgb(249 115 22 / 0.18));
        color: rgb(180 83 9);
    }

    .theme-dark .classes-price-pill.is-free,
    .dark .classes-price-pill.is-free {
        border-color: rgb(52 211 153 / 0.34);
        background: rgb(16 185 129 / 0.16);
        color: rgb(167 243 208);
    }

    .theme-dark .classes-price-pill.is-paid,
    .dark .classes-price-pill.is-paid {
        border-color: rgb(251 191 36 / 0.34);
        background: linear-gradient(135deg, rgb(245 158 11 / 0.18), rgb(251 191 36 / 0.16));
        color: rgb(253 230 138);
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
        $weekdayOptions = \App\Models\GymClass::WEEKDAY_FULL_LABELS;
        $weekdayKeys = \App\Models\GymClass::weekdayKeys();
        $normalizeWeekdaySelection = static function ($value) {
            return \App\Models\GymClass::normalizeWeekdaySelection(is_array($value) ? $value : []);
        };
        $formatWeekdays = static function ($classModel): string {
            return $classModel instanceof \App\Models\GymClass
                ? $classModel->activeWeekdaysLabel()
                : 'Todos los dias';
        };
        $encodeWeekdays = static function ($classModel): string {
            if (! ($classModel instanceof \App\Models\GymClass)) {
                return implode(',', \App\Models\GymClass::weekdayKeys());
            }

            return implode(',', $classModel->activeWeekdays());
        };
        $describeClassPrice = static function ($classModel): array {
            $amount = (float) ($classModel?->price ?? 0);

            return $amount <= 0
                ? ['label' => 'Gratis', 'classes' => 'classes-price-pill is-free', 'input' => '0.00']
                : [
                    'label' => '$'.number_format($amount, 2, '.', ','),
                    'classes' => 'classes-price-pill is-paid',
                    'input' => number_format($amount, 2, '.', ''),
                ];
        };

        $canManageClasses = (bool) ($canManageClasses ?? $canCreateClasses ?? false);
        $isCreateFormRequest = old('_classes_form_mode') === 'create';
        $isEditFormRequest = old('_classes_form_mode') === 'edit';
        $editingClassId = $isEditFormRequest ? (int) old('edit_class_id', 0) : 0;
        $editingClass = null;

        if ($editingClassId > 0) {
            $editingClass = $classes->firstWhere('id', $editingClassId);

            if (! $editingClass && $nextClass && (int) $nextClass->id === $editingClassId) {
                $editingClass = $nextClass;
            }
        }

        $openEditModalOnLoad = $canManageClasses && $isEditFormRequest && $editingClass !== null;
        $createOld = static function (string $key, $default = null) use ($isCreateFormRequest) {
            return $isCreateFormRequest ? old($key, $default) : $default;
        };
        $editOld = static function (string $key, $default = null) use ($isEditFormRequest) {
            return $isEditFormRequest ? old($key, $default) : $default;
        };
        $createAllowWaitlistChecked = $isCreateFormRequest ? (bool) old('allow_waitlist') : true;
        $editAllowWaitlistChecked = $isEditFormRequest
            ? (bool) old('allow_waitlist')
            : (bool) ($editingClass?->allow_waitlist ?? true);
        $editModalSubtitle = $editingClass
            ? $editingClass->name.' | '.optional($editingClass->starts_at)->format('d/m/Y H:i').' | '.($editingClass->instructor_name ?: 'Instructor por definir')
            : 'Actualiza horario, cupos y estado sin salir de la agenda.';
        $editReservationsLabel = $editingClass
            ? ((int) $editingClass->reserved_count).'/'.((int) $editingClass->capacity)
            : '0/0';
        $editWaitlistLabel = $editingClass ? (string) ((int) $editingClass->waitlist_count) : '0';
        $editGymLabel = $editingClass ? trim((string) ($editingClass->gym?->name ?? 'Sede actual')) : 'Sede actual';
        $editPriceMeta = $describeClassPrice($editingClass);
        $editDetailUrl = $editingClass
            ? route('classes.show', $routeParams + ['gymClass' => $editingClass->id])
            : route('classes.index', $routeParams);
        $editAction = $editingClass
            ? route('classes.update', $routeParams + ['gymClass' => $editingClass->id])
            : '#';
        $splitMomentDate = static function ($value): string {
            try {
                if ($value instanceof \DateTimeInterface) {
                    return \Carbon\Carbon::instance($value)->format('Y-m-d');
                }

                $rawValue = trim((string) $value);
                if ($rawValue !== '') {
                    return \Carbon\Carbon::parse($rawValue)->format('Y-m-d');
                }
            } catch (\Throwable) {
            }

            return '';
        };
        $splitMomentTime = static function ($value): string {
            try {
                if ($value instanceof \DateTimeInterface) {
                    return \Carbon\Carbon::instance($value)->format('H:i');
                }

                $rawValue = trim((string) $value);
                if ($rawValue !== '') {
                    return \Carbon\Carbon::parse($rawValue)->format('H:i');
                }
            } catch (\Throwable) {
            }

            return '';
        };
        $createStartSeed = now()->setTime(18, 0);
        $createEndSeed = now()->setTime(19, 0);
        $createStartDateValue = $createOld('start_date', $splitMomentDate($createOld('starts_at', $createStartSeed)));
        $createStartTimeValue = $createOld('start_time', $splitMomentTime($createOld('starts_at', $createStartSeed)));
        $createEndDateValue = $createOld('end_date', $splitMomentDate($createOld('ends_at', $createEndSeed)));
        $createEndTimeValue = $createOld('end_time', $splitMomentTime($createOld('ends_at', $createEndSeed)));
        $createPriceValue = $createOld('price', '0.00');
        $createActiveWeekdays = $normalizeWeekdaySelection($createOld('active_weekdays', $weekdayKeys));
        $editStartDateValue = $editOld('start_date', $splitMomentDate($editOld('starts_at', $editingClass?->starts_at)));
        $editStartTimeValue = $editOld('start_time', $splitMomentTime($editOld('starts_at', $editingClass?->starts_at)));
        $editEndDateValue = $editOld('end_date', $splitMomentDate($editOld('ends_at', $editingClass?->ends_at)));
        $editEndTimeValue = $editOld('end_time', $splitMomentTime($editOld('ends_at', $editingClass?->ends_at)));
        $editPriceValue = $editOld('price', $editPriceMeta['input']);
        $editActiveWeekdays = $isEditFormRequest
            ? $normalizeWeekdaySelection($editOld('active_weekdays', $editingClass?->activeWeekdays() ?? $weekdayKeys))
            : ($editingClass?->activeWeekdays() ?? $weekdayKeys);
    ?>

    <div class="classes-page">
        <section class="classes-hero">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-3xl space-y-2">
                    <p class="text-xs font-black uppercase tracking-[0.18em] text-emerald-600 dark:text-emerald-300">Modulo premium</p>
                    <h2 class="text-2xl font-black tracking-tight text-slate-950 dark:text-white">Agenda de clases, cupos y avisos en un solo lugar</h2>
                    <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">
                        Organiza las clases del gimnasio, revisa reservas y mueve la edicion al modal sin salir de esta agenda.
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
                        <?php
                            $nextClassGymLabel = trim((string) ($nextClass->gym?->name ?? 'Sede actual'));
                        ?>
                        <?php if($canManageClasses): ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('classes.show', $routeParams + ['gymClass' => $nextClass->id]),'variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('classes.show', $routeParams + ['gymClass' => $nextClass->id])),'variant' => 'secondary']); ?>Abrir proxima clase <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'primary','class' => 'js-class-edit-trigger','ariaHaspopup' => 'dialog','ariaControls' => 'class-edit-modal','dataOpenClassEdit' => true,'dataClassId' => ''.e((int) $nextClass->id).'','dataClassUpdateUrl' => ''.e(route('classes.update', $routeParams + ['gymClass' => $nextClass->id])).'','dataClassDetailUrl' => ''.e(route('classes.show', $routeParams + ['gymClass' => $nextClass->id])).'','dataClassName' => ''.e($nextClass->name).'','dataClassCategory' => ''.e($nextClass->category ?: '').'','dataClassLevel' => ''.e($nextClass->level ?: '').'','dataClassInstructor' => ''.e($nextClass->instructor_name ?: '').'','dataClassRoom' => ''.e($nextClass->room_name ?: '').'','dataClassCapacity' => ''.e((int) $nextClass->capacity).'','dataClassPrice' => ''.e(number_format((float) ($nextClass->price ?? 0), 2, '.', '')).'','dataClassStartsAt' => ''.e(optional($nextClass->starts_at)->format('Y-m-d\\TH:i')).'','dataClassStartsLabel' => ''.e(optional($nextClass->starts_at)->format('d/m/Y H:i')).'','dataClassEndsAt' => ''.e(optional($nextClass->ends_at)->format('Y-m-d\\TH:i')).'','dataClassStatus' => ''.e($nextClass->status).'','dataClassDescription' => ''.e($nextClass->description ?: '').'','dataClassAllowWaitlist' => ''.e($nextClass->allow_waitlist ? '1' : '0').'','dataClassReservedCount' => ''.e((int) $nextClass->reserved_count).'','dataClassWaitlistCount' => ''.e((int) $nextClass->waitlist_count).'','dataClassGym' => ''.e($nextClassGymLabel).'','dataClassWeekdays' => ''.e($encodeWeekdays($nextClass)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'primary','class' => 'js-class-edit-trigger','aria-haspopup' => 'dialog','aria-controls' => 'class-edit-modal','data-open-class-edit' => true,'data-class-id' => ''.e((int) $nextClass->id).'','data-class-update-url' => ''.e(route('classes.update', $routeParams + ['gymClass' => $nextClass->id])).'','data-class-detail-url' => ''.e(route('classes.show', $routeParams + ['gymClass' => $nextClass->id])).'','data-class-name' => ''.e($nextClass->name).'','data-class-category' => ''.e($nextClass->category ?: '').'','data-class-level' => ''.e($nextClass->level ?: '').'','data-class-instructor' => ''.e($nextClass->instructor_name ?: '').'','data-class-room' => ''.e($nextClass->room_name ?: '').'','data-class-capacity' => ''.e((int) $nextClass->capacity).'','data-class-price' => ''.e(number_format((float) ($nextClass->price ?? 0), 2, '.', '')).'','data-class-starts-at' => ''.e(optional($nextClass->starts_at)->format('Y-m-d\\TH:i')).'','data-class-starts-label' => ''.e(optional($nextClass->starts_at)->format('d/m/Y H:i')).'','data-class-ends-at' => ''.e(optional($nextClass->ends_at)->format('Y-m-d\\TH:i')).'','data-class-status' => ''.e($nextClass->status).'','data-class-description' => ''.e($nextClass->description ?: '').'','data-class-allow-waitlist' => ''.e($nextClass->allow_waitlist ? '1' : '0').'','data-class-reserved-count' => ''.e((int) $nextClass->reserved_count).'','data-class-waitlist-count' => ''.e((int) $nextClass->waitlist_count).'','data-class-gym' => ''.e($nextClassGymLabel).'','data-class-weekdays' => ''.e($encodeWeekdays($nextClass)).'']); ?>Editar proxima clase <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('classes.show', $routeParams + ['gymClass' => $nextClass->id]),'variant' => 'primary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('classes.show', $routeParams + ['gymClass' => $nextClass->id])),'variant' => 'primary']); ?>Ver proxima clase <?php echo $__env->renderComponent(); ?>
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
                    <p class="classes-kpi-label">Proxima clase</p>
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
                Estas viendo varias sedes al mismo tiempo. Puedes revisar agenda y reservas, pero para crear o editar clases debes entrar a una sede especifica.
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Nueva clase','subtitle' => 'Programa una sesion con horario, cupos, instructor y espacio.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Nueva clase','subtitle' => 'Programa una sesion con horario, cupos, instructor y espacio.']); ?>
                <?php if(! $canManageClasses): ?>
                    <div class="classes-soft-note text-sm text-slate-600 dark:text-slate-300">
                        Solo el dueno y desde una sede especifica puede crear clases nuevas.
                    </div>
                <?php else: ?>
                    <?php if($isCreateFormRequest && $errors->any()): ?>
                        <div class="classes-form-alert mb-4">
                            <p class="text-sm font-semibold">Revisa los datos de la nueva clase.</p>
                            <ul class="mt-2 space-y-1 text-sm">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('classes.store', $routeParams)); ?>" class="space-y-4">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="_classes_form_mode" value="create">

                        <div class="classes-form-grid">
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Nombre</span>
                                <input type="text" name="name" value="<?php echo e($createOld('name', '')); ?>" required maxlength="120" class="ui-input" placeholder="Ej: Boxeo funcional">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Categoria</span>
                                <input type="text" name="category" value="<?php echo e($createOld('category', '')); ?>" maxlength="80" class="ui-input" placeholder="Boxeo, Yoga, Cross">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Nivel</span>
                                <input type="text" name="level" value="<?php echo e($createOld('level', '')); ?>" maxlength="40" class="ui-input" placeholder="Inicial, Intermedio">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Instructor</span>
                                <input type="text" name="instructor_name" value="<?php echo e($createOld('instructor_name', '')); ?>" maxlength="120" class="ui-input" placeholder="Nombre del instructor">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Sala</span>
                                <input type="text" name="room_name" value="<?php echo e($createOld('room_name', '')); ?>" maxlength="80" class="ui-input" placeholder="Sala principal">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Cupos</span>
                                <input type="number" name="capacity" value="<?php echo e($createOld('capacity', 12)); ?>" min="1" max="300" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Precio</span>
                                <input type="number" name="price" value="<?php echo e($createPriceValue); ?>" min="0" max="99999.99" step="0.01" class="ui-input" placeholder="0.00">
                                <span class="text-xs text-slate-500 dark:text-slate-400">Deja 0 para mostrar Gratis.</span>
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Fecha inicio</span>
                                <input type="date" name="start_date" value="<?php echo e($createStartDateValue); ?>" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Hora inicio</span>
                                <input type="time" name="start_time" value="<?php echo e($createStartTimeValue); ?>" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Fecha fin</span>
                                <input type="date" name="end_date" value="<?php echo e($createEndDateValue); ?>" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Hora fin</span>
                                <input type="time" name="end_time" value="<?php echo e($createEndTimeValue); ?>" required class="ui-input">
                            </label>
                            <label class="space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Estado</span>
                                <select name="status" class="ui-input">
                                    <option value="scheduled" <?php if($createOld('status', 'scheduled') === 'scheduled'): echo 'selected'; endif; ?>>Programada</option>
                                    <option value="cancelled" <?php if($createOld('status', 'scheduled') === 'cancelled'): echo 'selected'; endif; ?>>Cancelada</option>
                                </select>
                            </label>
                        </div>

                        <div class="classes-weekday-picker">
                            <div>
                                <p class="font-semibold text-slate-700 dark:text-slate-200">Dias activos</p>
                                <p class="classes-weekday-summary">Marca solo los dias en que esta clase debe aparecer dentro del rango elegido.</p>
                            </div>
                            <div class="classes-weekday-grid">
                                <?php $__currentLoopData = $weekdayOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekdayValue => $weekdayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="classes-weekday-option">
                                        <input type="checkbox" name="active_weekdays[]" value="<?php echo e($weekdayValue); ?>" <?php if(in_array((int) $weekdayValue, $createActiveWeekdays, true)): echo 'checked'; endif; ?>>
                                        <span class="classes-weekday-chip"><?php echo e($weekdayLabel); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <p class="classes-weekday-summary">Si dejas todos marcados, la clase correra todos los dias del rango.</p>
                        </div>

                        <label class="block space-y-2 text-sm">
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Descripcion</span>
                            <textarea name="description" rows="4" class="ui-input" placeholder="Que incluye, recomendaciones o detalles del dia."><?php echo e($createOld('description', '')); ?></textarea>
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                            <input type="checkbox" name="allow_waitlist" value="1" <?php if($createAllowWaitlistChecked): echo 'checked'; endif; ?>>
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
                            <span class="text-xs text-slate-500 dark:text-slate-400">Las reservas se habilitan en la app movil del cliente.</span>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Agenda proxima','subtitle' => 'Consulta la programacion de las proximas dos semanas.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Agenda proxima','subtitle' => 'Consulta la programacion de las proximas dos semanas.']); ?>
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
                                    <th>Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $classItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $statusMeta = $formatClassStatus($classItem->status);
                                        $gymLabel = trim((string) ($classItem->gym?->name ?? 'Sede actual'));
                                        $priceMeta = $describeClassPrice($classItem);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="space-y-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="font-semibold text-slate-900 dark:text-slate-100"><?php echo e($classItem->name); ?></p>
                                                    <span class="<?php echo e($priceMeta['classes']); ?>"><?php echo e($priceMeta['label']); ?></span>
                                                </div>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                                    <?php echo e($classItem->category ?: 'Clase general'); ?>

                                                    <?php if($classItem->room_name): ?>
                                                        | <?php echo e($classItem->room_name); ?>

                                                    <?php endif; ?>
                                                </p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">Dias: <?php echo e($formatWeekdays($classItem)); ?></p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="space-y-1">
                                                <p>Inicio <?php echo e(optional($classItem->starts_at)->format('d/m/Y')); ?> | <?php echo e(optional($classItem->starts_at)->format('H:i')); ?></p>
                                                <p class="text-xs text-slate-500 dark:text-slate-400">Fin <?php echo e(optional($classItem->ends_at)->format('d/m/Y')); ?> | <?php echo e(optional($classItem->ends_at)->format('H:i')); ?></p>
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
                                            <?php if($canManageClasses): ?>
                                                <div class="flex flex-wrap gap-2">
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
                                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'secondary','size' => 'sm','class' => 'js-class-edit-trigger','ariaHaspopup' => 'dialog','ariaControls' => 'class-edit-modal','dataOpenClassEdit' => true,'dataClassId' => ''.e((int) $classItem->id).'','dataClassUpdateUrl' => ''.e(route('classes.update', $routeParams + ['gymClass' => $classItem->id])).'','dataClassDetailUrl' => ''.e(route('classes.show', $routeParams + ['gymClass' => $classItem->id])).'','dataClassName' => ''.e($classItem->name).'','dataClassCategory' => ''.e($classItem->category ?: '').'','dataClassLevel' => ''.e($classItem->level ?: '').'','dataClassInstructor' => ''.e($classItem->instructor_name ?: '').'','dataClassRoom' => ''.e($classItem->room_name ?: '').'','dataClassCapacity' => ''.e((int) $classItem->capacity).'','dataClassPrice' => ''.e(number_format((float) ($classItem->price ?? 0), 2, '.', '')).'','dataClassStartsAt' => ''.e(optional($classItem->starts_at)->format('Y-m-d\\TH:i')).'','dataClassStartsLabel' => ''.e(optional($classItem->starts_at)->format('d/m/Y H:i')).'','dataClassEndsAt' => ''.e(optional($classItem->ends_at)->format('Y-m-d\\TH:i')).'','dataClassStatus' => ''.e($classItem->status).'','dataClassDescription' => ''.e($classItem->description ?: '').'','dataClassAllowWaitlist' => ''.e($classItem->allow_waitlist ? '1' : '0').'','dataClassReservedCount' => ''.e((int) $classItem->reserved_count).'','dataClassWaitlistCount' => ''.e((int) $classItem->waitlist_count).'','dataClassGym' => ''.e($gymLabel).'','dataClassWeekdays' => ''.e($encodeWeekdays($classItem)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'secondary','size' => 'sm','class' => 'js-class-edit-trigger','aria-haspopup' => 'dialog','aria-controls' => 'class-edit-modal','data-open-class-edit' => true,'data-class-id' => ''.e((int) $classItem->id).'','data-class-update-url' => ''.e(route('classes.update', $routeParams + ['gymClass' => $classItem->id])).'','data-class-detail-url' => ''.e(route('classes.show', $routeParams + ['gymClass' => $classItem->id])).'','data-class-name' => ''.e($classItem->name).'','data-class-category' => ''.e($classItem->category ?: '').'','data-class-level' => ''.e($classItem->level ?: '').'','data-class-instructor' => ''.e($classItem->instructor_name ?: '').'','data-class-room' => ''.e($classItem->room_name ?: '').'','data-class-capacity' => ''.e((int) $classItem->capacity).'','data-class-price' => ''.e(number_format((float) ($classItem->price ?? 0), 2, '.', '')).'','data-class-starts-at' => ''.e(optional($classItem->starts_at)->format('Y-m-d\\TH:i')).'','data-class-starts-label' => ''.e(optional($classItem->starts_at)->format('d/m/Y H:i')).'','data-class-ends-at' => ''.e(optional($classItem->ends_at)->format('Y-m-d\\TH:i')).'','data-class-status' => ''.e($classItem->status).'','data-class-description' => ''.e($classItem->description ?: '').'','data-class-allow-waitlist' => ''.e($classItem->allow_waitlist ? '1' : '0').'','data-class-reserved-count' => ''.e((int) $classItem->reserved_count).'','data-class-waitlist-count' => ''.e((int) $classItem->waitlist_count).'','data-class-gym' => ''.e($gymLabel).'','data-class-weekdays' => ''.e($encodeWeekdays($classItem)).'']); ?>Editar <?php echo $__env->renderComponent(); ?>
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
                                            <?php else: ?>
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

        <?php if($canManageClasses): ?>
            <div id="class-edit-modal" class="ui-modal-backdrop <?php echo e($openEditModalOnLoad ? '' : 'hidden'); ?>" role="dialog" aria-modal="true" aria-labelledby="class-edit-modal-title" aria-hidden="<?php echo e($openEditModalOnLoad ? 'false' : 'true'); ?>">
                <div class="ui-modal-shell w-full max-w-5xl" data-class-edit-dialog tabindex="-1">
                    <header class="flex items-start justify-between border-b border-slate-800 px-5 py-4">
                        <div>
                            <h3 id="class-edit-modal-title" class="text-xl font-black text-slate-100">Editar clase</h3>
                            <p id="class-edit-modal-subtitle" class="mt-1 text-sm text-slate-400"><?php echo e($editModalSubtitle); ?></p>
                        </div>
                        <button type="button" class="ui-button ui-button-ghost px-2 py-1 text-sm" data-close-class-edit aria-label="Cerrar">
                            X
                        </button>
                    </header>

                    <div class="ui-modal-scroll-body px-5 py-5">
                        <section class="classes-modal-summary mb-5">
                            <article class="classes-modal-stat">
                                <p class="classes-modal-stat-label">Reservas</p>
                                <p id="class-edit-reservations" class="classes-modal-stat-value"><?php echo e($editReservationsLabel); ?></p>
                            </article>
                            <article class="classes-modal-stat">
                                <p class="classes-modal-stat-label">Espera</p>
                                <p id="class-edit-waitlist" class="classes-modal-stat-value"><?php echo e($editWaitlistLabel); ?></p>
                            </article>
                            <article class="classes-modal-stat">
                                <p class="classes-modal-stat-label">Sede</p>
                                <p id="class-edit-gym" class="classes-modal-stat-value text-base !leading-tight !tracking-normal"><?php echo e($editGymLabel); ?></p>
                            </article>
                            <article class="classes-modal-stat">
                                <p class="classes-modal-stat-label">Precio</p>
                                <div class="mt-3">
                                    <span id="class-edit-price" class="<?php echo e($editPriceMeta['classes']); ?>"><?php echo e($editPriceMeta['label']); ?></span>
                                </div>
                            </article>
                        </section>

                        <?php if($openEditModalOnLoad && $errors->any()): ?>
                            <div class="classes-form-alert mb-4">
                                <p class="text-sm font-semibold">Revisa los datos de la clase.</p>
                                <ul class="mt-2 space-y-1 text-sm">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <li><?php echo e($error); ?></li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form id="class-edit-form" method="POST" action="<?php echo e($editAction); ?>" class="space-y-4">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PUT'); ?>
                            <input type="hidden" name="_classes_form_mode" value="edit">
                            <input type="hidden" name="edit_class_id" id="class-edit-id" value="<?php echo e($editingClassId > 0 ? $editingClassId : ''); ?>">

                            <div class="classes-form-grid">
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Nombre</span>
                                    <input type="text" name="name" value="<?php echo e($editOld('name', $editingClass?->name ?? '')); ?>" required maxlength="120" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Categoria</span>
                                    <input type="text" name="category" value="<?php echo e($editOld('category', $editingClass?->category ?? '')); ?>" maxlength="80" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Nivel</span>
                                    <input type="text" name="level" value="<?php echo e($editOld('level', $editingClass?->level ?? '')); ?>" maxlength="40" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Instructor</span>
                                    <input type="text" name="instructor_name" value="<?php echo e($editOld('instructor_name', $editingClass?->instructor_name ?? '')); ?>" maxlength="120" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Sala</span>
                                    <input type="text" name="room_name" value="<?php echo e($editOld('room_name', $editingClass?->room_name ?? '')); ?>" maxlength="80" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Cupos</span>
                                    <input type="number" name="capacity" value="<?php echo e($editOld('capacity', $editingClass?->capacity ?? 12)); ?>" min="1" max="300" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Precio</span>
                                    <input type="number" name="price" value="<?php echo e($editPriceValue); ?>" min="0" max="99999.99" step="0.01" class="ui-input" placeholder="0.00">
                                    <span class="text-xs text-slate-500 dark:text-slate-400">Deja 0 para mostrar Gratis.</span>
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Fecha inicio</span>
                                    <input type="date" name="start_date" value="<?php echo e($editStartDateValue); ?>" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Hora inicio</span>
                                    <input type="time" name="start_time" value="<?php echo e($editStartTimeValue); ?>" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Fecha fin</span>
                                    <input type="date" name="end_date" value="<?php echo e($editEndDateValue); ?>" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Hora fin</span>
                                    <input type="time" name="end_time" value="<?php echo e($editEndTimeValue); ?>" class="ui-input">
                                </label>
                                <label class="space-y-2 text-sm">
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">Estado</span>
                                    <select name="status" class="ui-input">
                                        <option value="scheduled" <?php if($editOld('status', $editingClass?->status ?? 'scheduled') === 'scheduled'): echo 'selected'; endif; ?>>Programada</option>
                                        <option value="cancelled" <?php if($editOld('status', $editingClass?->status ?? 'scheduled') === 'cancelled'): echo 'selected'; endif; ?>>Cancelada</option>
                                    </select>
                                </label>
                            </div>

                            <div class="classes-weekday-picker">
                                <div>
                                    <p class="font-semibold text-slate-700 dark:text-slate-200">Dias activos</p>
                                    <p class="classes-weekday-summary">Define exactamente que dias debe repetirse la clase dentro del rango.</p>
                                </div>
                                <div class="classes-weekday-grid">
                                    <?php $__currentLoopData = $weekdayOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $weekdayValue => $weekdayLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="classes-weekday-option">
                                            <input type="checkbox" name="active_weekdays[]" value="<?php echo e($weekdayValue); ?>" <?php if(in_array((int) $weekdayValue, $editActiveWeekdays, true)): echo 'checked'; endif; ?>>
                                            <span class="classes-weekday-chip"><?php echo e($weekdayLabel); ?></span>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <p class="classes-weekday-summary">Esto hace evidente si la clase corre todos los dias o solo lunes, miercoles, viernes, etc.</p>
                            </div>

                            <label class="block space-y-2 text-sm">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">Descripcion</span>
                                <textarea name="description" rows="4" class="ui-input"><?php echo e($editOld('description', $editingClass?->description ?? '')); ?></textarea>
                            </label>

                            <label class="inline-flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-200">
                                <input type="checkbox" name="allow_waitlist" id="class-edit-allow-waitlist" value="1" <?php if($editAllowWaitlistChecked): echo 'checked'; endif; ?>>
                                Mantener lista de espera activa.
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
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary']); ?>Guardar cambios <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['id' => 'class-edit-detail-link','href' => $editDetailUrl,'variant' => 'ghost']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'class-edit-detail-link','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($editDetailUrl),'variant' => 'ghost']); ?>Ver reservas <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','dataCloseClassEdit' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','data-close-class-edit' => true]); ?>Cerrar <?php echo $__env->renderComponent(); ?>
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
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const modal = document.getElementById('class-edit-modal');
        if (!modal) {
            return;
        }

        const dialog = modal.querySelector('[data-class-edit-dialog]');
        const form = document.getElementById('class-edit-form');
        const subtitle = document.getElementById('class-edit-modal-subtitle');
        const reservationsValue = document.getElementById('class-edit-reservations');
        const waitlistValue = document.getElementById('class-edit-waitlist');
        const gymValue = document.getElementById('class-edit-gym');
        const priceValue = document.getElementById('class-edit-price');
        const detailLink = document.getElementById('class-edit-detail-link');
        const editIdInput = document.getElementById('class-edit-id');
        const shouldOpenOnLoad = <?php echo json_encode($openEditModalOnLoad, 15, 512) ?>;
        let lastFocusedElement = null;
        let previousBodyOverflow = '';

        const getField = function (name) {
            return form ? form.elements.namedItem(name) : null;
        };

        const nameField = getField('name');
        const categoryField = getField('category');
        const levelField = getField('level');
        const instructorField = getField('instructor_name');
        const roomField = getField('room_name');
        const capacityField = getField('capacity');
        const priceField = getField('price');
        const startDateField = getField('start_date');
        const startTimeField = getField('start_time');
        const endDateField = getField('end_date');
        const endTimeField = getField('end_time');
        const statusField = getField('status');
        const descriptionField = getField('description');
        const waitlistField = getField('allow_waitlist');
        const weekdayFields = Array.from(form ? form.querySelectorAll('input[name="active_weekdays[]"]') : []);
        const defaultWeekdays = weekdayFields.length > 0
            ? weekdayFields.map(function (field) { return String(field.value || '').trim(); }).filter(Boolean)
            : ['1', '2', '3', '4', '5', '6', '7'];

        const splitDateTime = function (value) {
            const raw = String(value || '').trim();
            if (raw === '') {
                return { date: '', time: '' };
            }

            const parts = raw.split('T');
            return {
                date: parts[0] || '',
                time: (parts[1] || '').slice(0, 5),
            };
        };

        const describePrice = function (value) {
            const amount = Number.parseFloat(String(value || '0').trim());
            if (!Number.isFinite(amount) || amount <= 0) {
                return {
                    input: '0.00',
                    label: 'Gratis',
                    classes: 'classes-price-pill is-free',
                };
            }

            return {
                input: amount.toFixed(2),
                label: '$' + amount.toFixed(2),
                classes: 'classes-price-pill is-paid',
            };
        };

        const normalizeWeekdays = function (value) {
            const values = Array.isArray(value)
                ? value
                : String(value || '').split(',');
            const normalized = values
                .map(function (item) {
                    return String(item || '').trim();
                })
                .filter(function (item, index, items) {
                    return item !== '' && items.indexOf(item) === index;
                });

            return normalized.length > 0 ? normalized : defaultWeekdays;
        };

        const getFocusableElements = function (container) {
            return Array.from(container.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'))
                .filter(function (element) {
                    return !element.hasAttribute('hidden') && element.offsetParent !== null;
                });
        };

        const trapFocus = function (event) {
            const focusable = getFocusableElements(modal);
            if (focusable.length === 0) {
                event.preventDefault();
                return;
            }

            const first = focusable[0];
            const last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
                return;
            }

            if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        };

        const fillForm = function (data) {
            const startParts = splitDateTime(data.startsAt);
            const endParts = splitDateTime(data.endsAt);

            if (nameField) nameField.value = data.name || '';
            if (categoryField) categoryField.value = data.category || '';
            if (levelField) levelField.value = data.level || '';
            if (instructorField) instructorField.value = data.instructor || '';
            if (roomField) roomField.value = data.room || '';
            if (capacityField) capacityField.value = data.capacity || '12';
            if (priceField) priceField.value = describePrice(data.price).input;
            if (startDateField) startDateField.value = startParts.date;
            if (startTimeField) startTimeField.value = startParts.time;
            if (endDateField) endDateField.value = endParts.date;
            if (endTimeField) endTimeField.value = endParts.time;
            if (statusField) statusField.value = data.status || 'scheduled';
            if (descriptionField) descriptionField.value = data.description || '';
            if (waitlistField) waitlistField.checked = data.allowWaitlist === '1';

            const selectedWeekdays = normalizeWeekdays(data.activeWeekdays);
            weekdayFields.forEach(function (field) {
                field.checked = selectedWeekdays.includes(String(field.value || '').trim());
            });
        };

        const updateMeta = function (data) {
            if (subtitle) {
                const pieces = [
                    data.name || '',
                    data.startsLabel || '',
                    data.instructor || 'Instructor por definir',
                ].filter(Boolean);
                subtitle.textContent = pieces.join(' | ');
            }

            if (reservationsValue) {
                reservationsValue.textContent = (data.reservedCount || '0') + '/' + (data.capacity || '0');
            }

            if (waitlistValue) {
                waitlistValue.textContent = data.waitlistCount || '0';
            }

            if (gymValue) {
                gymValue.textContent = data.gym || 'Sede actual';
            }

            if (priceValue) {
                const priceMeta = describePrice(data.price);
                priceValue.textContent = priceMeta.label;
                priceValue.className = priceMeta.classes;
            }

            if (detailLink) {
                detailLink.setAttribute('href', data.detailUrl || '#');
            }
        };

        const openModal = function (focusTarget) {
            lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
            previousBodyOverflow = document.body.style.overflow;
            modal.classList.remove('hidden');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            window.requestAnimationFrame(function () {
                const target = focusTarget || dialog || modal;
                if (target && typeof target.focus === 'function') {
                    target.focus();
                }
            });
        };

        const closeModal = function () {
            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = previousBodyOverflow;

            if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                lastFocusedElement.focus();
            }
        };

        const openFromButton = function (button) {
            const data = {
                id: button.dataset.classId || '',
                updateUrl: button.dataset.classUpdateUrl || '',
                detailUrl: button.dataset.classDetailUrl || '',
                name: button.dataset.className || '',
                category: button.dataset.classCategory || '',
                level: button.dataset.classLevel || '',
                instructor: button.dataset.classInstructor || '',
                room: button.dataset.classRoom || '',
                capacity: button.dataset.classCapacity || '0',
                price: button.dataset.classPrice || '0',
                startsAt: button.dataset.classStartsAt || '',
                startsLabel: button.dataset.classStartsLabel || '',
                endsAt: button.dataset.classEndsAt || '',
                status: button.dataset.classStatus || 'scheduled',
                description: button.dataset.classDescription || '',
                allowWaitlist: button.dataset.classAllowWaitlist || '0',
                reservedCount: button.dataset.classReservedCount || '0',
                waitlistCount: button.dataset.classWaitlistCount || '0',
                gym: button.dataset.classGym || 'Sede actual',
                activeWeekdays: button.dataset.classWeekdays || defaultWeekdays,
            };

            if (form) {
                form.action = data.updateUrl || '#';
            }

            if (editIdInput) {
                editIdInput.value = data.id || '';
            }

            fillForm(data);
            updateMeta(data);
            openModal(nameField || dialog);
        };

        document.querySelectorAll('[data-open-class-edit]').forEach(function (button) {
            button.addEventListener('click', function () {
                openFromButton(this);
            });
        });

        document.querySelectorAll('[data-close-class-edit]').forEach(function (button) {
            button.addEventListener('click', closeModal);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (modal.classList.contains('hidden')) {
                return;
            }

            if (event.key === 'Escape') {
                event.preventDefault();
                closeModal();
                return;
            }

            if (event.key === 'Tab') {
                trapFocus(event);
            }
        });

        if (shouldOpenOnLoad) {
            openModal(nameField || dialog);
        }
    })();
</script>
<?php $__env->stopPush(); ?>



<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/classes/index.blade.php ENDPATH**/ ?>
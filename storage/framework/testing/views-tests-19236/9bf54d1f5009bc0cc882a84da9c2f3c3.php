<div x-cloak
     x-show="adjustmentModalOpen"
     x-transition.opacity
     class="ui-modal-backdrop"
     x-on:click.self="closeMembershipAdjustmentModal()"
     x-on:keydown.escape.window="closeMembershipAdjustmentModal()">
    <div class="ui-modal-shell max-w-5xl" x-transition.scale.origin.top>
        <form method="POST"
              x-bind:action="adjustmentFormAction()"
              class="flex min-h-0 flex-1 flex-col space-y-0">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <input type="hidden" name="client_id" value="<?php echo e($client->id); ?>">
            <input type="hidden" name="active_tab" value="membership">
            <input type="hidden" name="membership_form_mode" value="adjustment">
            <input type="hidden" name="adjust_membership_id" x-bind:value="selectedAdjustmentMembershipId || ''">

            <header class="flex items-start justify-between border-b border-slate-800 px-6 py-4">
                <div>
                    <h3 class="text-xl font-black text-slate-100">Ajustar membresia</h3>
                    <p class="mt-1 text-sm text-slate-400">Corrige la ventana de acceso sin alterar el cobro original y dejando historial separado.</p>
                </div>
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','xOn:click' => 'closeMembershipAdjustmentModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','x-on:click' => 'closeMembershipAdjustmentModal()']); ?>Cerrar <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </header>

            <div class="ui-modal-scroll-body space-y-5 px-6 py-5">
                <?php if($errors->hasAny(['membership_adjustment', 'adjustment_type', 'reason', 'notes', 'extra_days', 'starts_at', 'ends_at'])): ?>
                    <div class="rounded-xl border border-rose-400/70 bg-rose-500/15 p-4 text-sm text-rose-100">
                        <p class="font-semibold">No se pudo guardar el ajuste.</p>
                        <p class="mt-1 text-rose-100/90">Corrige los campos marcados y vuelve a intentarlo.</p>
                    </div>
                <?php endif; ?>

                <div class="rounded-xl border border-cyan-500/30 bg-cyan-500/10 p-4 text-sm text-cyan-100">
                    <p class="font-semibold">Formulario guiado</p>
                    <p class="mt-1 text-cyan-50/90">Al cambiar el tipo de ajuste, los motivos se filtran automaticamente para mostrar solo opciones coherentes.</p>
                </div>

                <div class="grid gap-5 xl:grid-cols-[minmax(0,2fr)_minmax(320px,1fr)]">
                    <div class="space-y-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Membresia a ajustar</span>
                                <select class="ui-input"
                                        x-model="selectedAdjustmentMembershipId"
                                        x-on:change="setSelectedAdjustmentMembership($event.target.value, false)">
                                    <template x-for="membership in adjustmentMemberships" :key="membership.id">
                                        <option :value="membership.id" x-text="`#${membership.id} - ${membership.planName}`"></option>
                                    </template>
                                </select>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300">
                                <span>Tipo de ajuste</span>
                                <select name="adjustment_type"
                                        class="ui-input"
                                        x-model="adjustmentForm.type"
                                        x-on:change="syncAdjustmentReason()"
                                        x-ref="adjustmentTypeInput">
                                    <?php $__currentLoopData = $adjustmentTypeLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>"><?php echo e($label); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <p class="text-xs text-slate-400" x-text="currentAdjustmentTypeHelp()"></p>
                                <?php $__errorArgs = ['adjustment_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <label class="space-y-1 text-sm font-semibold text-slate-300 md:col-span-2">
                                <span>Motivo</span>
                                <select name="reason" class="ui-input" x-model="adjustmentForm.reason">
                                    <template x-for="reasonOption in allowedAdjustmentReasons()" :key="reasonOption.value">
                                        <option :value="reasonOption.value" x-text="reasonOption.label"></option>
                                    </template>
                                </select>
                                <p class="text-xs text-slate-400" x-text="currentAdjustmentReasonHelp()"></p>
                                <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </label>

                            <template x-if="adjustmentForm.type === 'reschedule_start' || adjustmentForm.type === 'manual_window'">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Nueva fecha de inicio</span>
                                    <input type="date" name="starts_at" class="ui-input" x-model="adjustmentForm.startsAt">
                                </label>
                            </template>

                            <template x-if="adjustmentForm.type === 'manual_window'">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Nueva fecha de fin</span>
                                    <input type="date" name="ends_at" class="ui-input" x-model="adjustmentForm.endsAt">
                                </label>
                            </template>

                            <template x-if="adjustmentForm.type === 'extend_access'">
                                <label class="space-y-1 text-sm font-semibold text-slate-300">
                                    <span>Dias extra</span>
                                    <input type="number" name="extra_days" min="1" max="90" step="1" class="ui-input" x-model="adjustmentForm.extraDays">
                                </label>
                            </template>
                        </div>

                        <?php $__errorArgs = ['starts_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <?php $__errorArgs = ['ends_at'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <?php $__errorArgs = ['extra_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <?php $__errorArgs = ['membership_adjustment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                        <label class="space-y-1 text-sm font-semibold text-slate-300">
                            <span>Notas (opcional)</span>
                            <textarea name="notes"
                                      rows="4"
                                      maxlength="500"
                                      class="ui-input"
                                      x-model="adjustmentForm.notes"
                                      placeholder="Detalle breve del acuerdo o correccion aplicada."></textarea>
                            <p class="text-xs text-slate-400">Queda guardado en el historial de ajustes.</p>
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-xs font-semibold text-rose-300"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </label>
                    </div>

                    <aside class="space-y-4 rounded-2xl border border-slate-700 bg-slate-950/70 p-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Membresia seleccionada</p>
                            <p class="mt-2 text-lg font-black text-slate-100" x-text="selectedAdjustmentMembership() ? selectedAdjustmentMembership().planName : 'Sin seleccion'"></p>
                            <p class="mt-1 text-sm text-slate-400" x-text="selectedAdjustmentMembership() ? ('#' + selectedAdjustmentMembership().id) : ''"></p>
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Antes</p>
                            <p class="mt-2 text-sm text-slate-300">Inicio: <span class="font-semibold text-slate-100" x-text="selectedAdjustmentMembership() ? formatDateLabel(selectedAdjustmentMembership().startsAt) : '-'"></span></p>
                            <p class="mt-1 text-sm text-slate-300">Fin: <span class="font-semibold text-slate-100" x-text="selectedAdjustmentMembership() ? formatDateLabel(selectedAdjustmentMembership().endsAt) : '-'"></span></p>
                        </div>

                        <div class="rounded-xl border border-cyan-500/30 bg-cyan-500/10 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-cyan-100/80">Preview</p>
                            <p class="mt-2 text-sm text-cyan-50">Inicio: <span class="font-semibold" x-text="formatDateLabel(adjustmentPreview().startsAt)"></span></p>
                            <p class="mt-1 text-sm text-cyan-50">Fin: <span class="font-semibold" x-text="formatDateLabel(adjustmentPreview().endsAt)"></span></p>
                            <p class="mt-1 text-sm text-cyan-50">Estado visible: <span class="font-semibold" x-text="adjustmentPreview().statusLabel"></span></p>
                            <p class="mt-1 text-sm text-cyan-50">Cambio neto: <span class="font-semibold" x-text="adjustmentPreview().deltaLabel"></span></p>
                        </div>

                        <div class="rounded-xl border border-slate-800 bg-slate-900/70 p-4 text-xs text-slate-400">
                            <p class="font-semibold text-slate-300">Guia rapida</p>
                            <p class="mt-2">`Mover fecha de inicio` recalcula el fin con la duracion normal del plan.</p>
                            <p class="mt-1">`Sumar dias al final` solo extiende el acceso sin tocar el inicio.</p>
                            <p class="mt-1">`Corregir fechas manualmente` sirve para casos administrativos excepcionales.</p>
                        </div>
                    </aside>
                </div>
            </div>

            <footer class="ui-modal-sticky-footer flex justify-end gap-3 px-6 py-4">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','xOn:click' => 'closeMembershipAdjustmentModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','x-on:click' => 'closeMembershipAdjustmentModal()']); ?>Cancelar <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'secondary','xBind:disabled' => '!selectedAdjustmentMembership()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'secondary','x-bind:disabled' => '!selectedAdjustmentMembership()']); ?>Guardar ajuste <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
            </footer>
        </form>
    </div>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/partials/_modal_membership_adjustment.blade.php ENDPATH**/ ?>
<?php
    $formIdPrefix = trim((string) ($formIdPrefix ?? 'fitness-form'));
    if ($formIdPrefix === '') {
        $formIdPrefix = 'fitness-form';
    }

    $nextScreen = mb_strtolower(trim((string) old('next_screen', ($nextScreen ?? 'progress'))));
    if (! in_array($nextScreen, ['home', 'progress', 'physical', 'nutrition'], true)) {
        $nextScreen = 'progress';
    }

    $isModalForm = (bool) ($isModalForm ?? false);
    $submitLabel = trim((string) ($submitLabel ?? 'Guardar datos físicos'));
    if ($submitLabel === '') {
        $submitLabel = 'Guardar datos físicos';
    }

    $birthDateValue = old('birth_date', $fitnessProfileModel?->birth_date?->format('Y-m-d'));
    $heightValue = old('height_cm', $fitnessProfileModel?->height_cm);
    $weightValue = old('weight_kg', $fitnessProfileModel?->weight_kg);
    $sexValue = mb_strtolower(trim((string) old('sex', (string) ($fitnessProfileModel?->sex ?? ''))));
    $goalValue = mb_strtolower(trim((string) old('goal', (string) ($fitnessProfileModel?->goal ?? ''))));
    $secondaryGoalValue = mb_strtolower(trim((string) old('secondary_goal', (string) ($fitnessProfileModel?->secondary_goal ?? ''))));
    $experienceValue = mb_strtolower(trim((string) old('experience_level', (string) ($fitnessProfileModel?->experience_level ?? ''))));
    $daysValue = (string) old('days_per_week', $fitnessProfileModel?->days_per_week);
    $minutesValue = (string) old('session_minutes', $fitnessProfileModel?->session_minutes);

    $defaultLimitations = is_array($fitnessLimitations ?? null) ? $fitnessLimitations : ['ninguna'];
    $selectedLimitations = old('limitations', $defaultLimitations);
    if (! is_array($selectedLimitations)) {
        $selectedLimitations = [$selectedLimitations];
    }
    $selectedLimitations = array_values(array_filter(array_map(
        static fn ($item): string => mb_strtolower(trim((string) $item)),
        $selectedLimitations
    ), static fn (string $value): bool => $value !== ''));
    if ($selectedLimitations === []) {
        $selectedLimitations = ['ninguna'];
    }

    $hasFitnessErrors = $errors->has('birth_date')
        || $errors->has('sex')
        || $errors->has('height_cm')
        || $errors->has('weight_kg')
        || $errors->has('goal')
        || $errors->has('secondary_goal')
        || $errors->has('experience_level')
        || $errors->has('days_per_week')
        || $errors->has('session_minutes')
        || $errors->has('limitations')
        || $errors->has('limitations.*');
?>

<?php if($hasFitnessErrors): ?>
    <p class="profile-message profile-message-error">Revisa los campos marcados antes de guardar.</p>
<?php endif; ?>

<form method="POST" action="<?php echo e(route('client-mobile.fitness-profile.save', ['gymSlug' => $gym->slug])); ?>" class="space-y-3" data-fitness-form="1">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="_fitness_form" value="1">
    <input type="hidden" name="_fitness_modal" value="<?php echo e($isModalForm ? '1' : '0'); ?>">
    <input type="hidden" name="next_screen" value="<?php echo e($nextScreen); ?>">

    <div class="fitness-grid-2">
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Fecha de nacimiento</span>
            <input type="date" name="birth_date" class="module-input" min="<?php echo e(now()->subYears(90)->format('Y-m-d')); ?>" max="<?php echo e(now()->subYears(12)->format('Y-m-d')); ?>" value="<?php echo e(is_string($birthDateValue) ? $birthDateValue : ''); ?>" required>
            <?php $__errorArgs = ['birth_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Sexo</span>
            <div class="fitness-chip-grid">
                <?php $__currentLoopData = $fitnessSexOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $inputId = $formIdPrefix.'-sex-'.$value;
                        $isChecked = $sexValue === $value;
                    ?>
                    <label for="<?php echo e($inputId); ?>" class="fitness-chip">
                        <input id="<?php echo e($inputId); ?>" type="radio" name="sex" value="<?php echo e($value); ?>" class="fitness-chip-input" <?php echo e($isChecked ? 'checked' : ''); ?> required>
                        <span class="fitness-chip-label"><?php echo e($label); ?></span>
                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['sex'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
    </div>

    <div class="fitness-grid-2">
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Altura (cm)</span>
            <input type="number" name="height_cm" class="module-input" min="120" max="250" step="0.1" value="<?php echo e($heightValue !== null ? $heightValue : ''); ?>" placeholder="Ej: 170">
            <?php $__errorArgs = ['height_cm'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Peso actual (kg)</span>
            <input type="number" name="weight_kg" class="module-input" min="30" max="400" step="0.1" value="<?php echo e($weightValue !== null ? $weightValue : ''); ?>" placeholder="Ej: 72.5">
            <?php $__errorArgs = ['weight_kg'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Objetivo principal</p>
        <div class="fitness-chip-grid">
            <?php $__currentLoopData = $fitnessGoalOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $inputId = $formIdPrefix.'-goal-'.$value;
                    $isChecked = $goalValue === $value;
                ?>
                <label for="<?php echo e($inputId); ?>" class="fitness-chip">
                    <input id="<?php echo e($inputId); ?>" type="radio" name="goal" value="<?php echo e($value); ?>" class="fitness-chip-input" <?php echo e($isChecked ? 'checked' : ''); ?> required>
                    <span class="fitness-chip-label"><?php echo e($label); ?></span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['goal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Objetivo secundario (opcional)</p>
        <div class="fitness-chip-grid">
            <?php
                $secondaryNoneId = $formIdPrefix.'-secondary-none';
            ?>
            <label for="<?php echo e($secondaryNoneId); ?>" class="fitness-chip">
                <input id="<?php echo e($secondaryNoneId); ?>" type="radio" name="secondary_goal" value="" class="fitness-chip-input" <?php echo e($secondaryGoalValue === '' ? 'checked' : ''); ?>>
                <span class="fitness-chip-label">Sin secundario</span>
            </label>
            <?php $__currentLoopData = $fitnessGoalOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $inputId = $formIdPrefix.'-secondary-goal-'.$value;
                    $isChecked = $secondaryGoalValue === $value;
                ?>
                <label for="<?php echo e($inputId); ?>" class="fitness-chip">
                    <input id="<?php echo e($inputId); ?>" type="radio" name="secondary_goal" value="<?php echo e($value); ?>" class="fitness-chip-input" <?php echo e($isChecked ? 'checked' : ''); ?>>
                    <span class="fitness-chip-label"><?php echo e($label); ?></span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['secondary_goal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <p class="fitness-inline-help">Se usa para afinar predicción, estado corporal y rutina sugerida.</p>
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Nivel de experiencia</p>
        <div class="fitness-chip-grid">
            <?php $__currentLoopData = $fitnessLevelOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $inputId = $formIdPrefix.'-level-'.$value;
                    $isChecked = $experienceValue === $value;
                ?>
                <label for="<?php echo e($inputId); ?>" class="fitness-chip">
                    <input id="<?php echo e($inputId); ?>" type="radio" name="experience_level" value="<?php echo e($value); ?>" class="fitness-chip-input" <?php echo e($isChecked ? 'checked' : ''); ?> required>
                    <span class="fitness-chip-label"><?php echo e($label); ?></span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['experience_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Días por semana</p>
        <div class="fitness-chip-grid">
            <?php $__currentLoopData = [3, 4, 5, 6, 7]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $daysOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $optionValue = (string) $daysOption;
                    $inputId = $formIdPrefix.'-days-'.$optionValue;
                    $isChecked = $daysValue === $optionValue;
                ?>
                <label for="<?php echo e($inputId); ?>" class="fitness-chip">
                    <input id="<?php echo e($inputId); ?>" type="radio" name="days_per_week" value="<?php echo e($optionValue); ?>" class="fitness-chip-input" <?php echo e($isChecked ? 'checked' : ''); ?> required>
                    <span class="fitness-chip-label"><?php echo e($optionValue); ?> días</span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['days_per_week'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Duración de sesión</p>
        <div class="fitness-chip-grid">
            <?php $__currentLoopData = [45, 60, 90, 120]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $minutesOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $optionValue = (string) $minutesOption;
                    $inputId = $formIdPrefix.'-minutes-'.$optionValue;
                    $isChecked = $minutesValue === $optionValue;
                ?>
                <label for="<?php echo e($inputId); ?>" class="fitness-chip">
                    <input id="<?php echo e($inputId); ?>" type="radio" name="session_minutes" value="<?php echo e($optionValue); ?>" class="fitness-chip-input" <?php echo e($isChecked ? 'checked' : ''); ?> required>
                    <span class="fitness-chip-label"><?php echo e($optionValue); ?> min</span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php $__errorArgs = ['session_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="profile-field-error"><?php echo e((string) $message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Limitaciones o molestias</p>
        <div class="fitness-chip-grid">
            <?php $__currentLoopData = $fitnessLimitationsOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $inputId = $formIdPrefix.'-limit-'.$value;
                    $isChecked = in_array($value, $selectedLimitations, true);
                ?>
                <label for="<?php echo e($inputId); ?>" class="fitness-chip">
                    <input id="<?php echo e($inputId); ?>" type="checkbox" name="limitations[]" value="<?php echo e($value); ?>" class="fitness-chip-input" <?php echo e($isChecked ? 'checked' : ''); ?>>
                    <span class="fitness-chip-label"><?php echo e($label); ?></span>
                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php if($errors->has('limitations') || $errors->has('limitations.*')): ?>
            <p class="profile-field-error"><?php echo e((string) ($errors->first('limitations') ?: $errors->first('limitations.*'))); ?></p>
        <?php endif; ?>
        <p class="fitness-inline-help">Si eliges "Ninguna", se desmarcan automáticamente las demás opciones.</p>
    </div>

    <button type="submit" class="module-action module-action-primary w-full"><?php echo e($submitLabel); ?></button>
</form>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/client-mobile/partials/fitness-profile-form.blade.php ENDPATH**/ ?>
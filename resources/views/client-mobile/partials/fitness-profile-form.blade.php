@php
    $formIdPrefix = trim((string) ($formIdPrefix ?? 'fitness-form'));
    if ($formIdPrefix === '') {
        $formIdPrefix = 'fitness-form';
    }

    $nextScreen = mb_strtolower(trim((string) ($nextScreen ?? 'progress')));
    if (! in_array($nextScreen, ['home', 'progress', 'physical'], true)) {
        $nextScreen = 'progress';
    }

    $isModalForm = (bool) ($isModalForm ?? false);
    $submitLabel = trim((string) ($submitLabel ?? 'Guardar datos fisicos'));
    if ($submitLabel === '') {
        $submitLabel = 'Guardar datos fisicos';
    }

    $ageValue = old('age', $fitnessProfileModel?->age);
    $heightValue = old('height_cm', $fitnessProfileModel?->height_cm);
    $weightValue = old('weight_kg', $fitnessProfileModel?->weight_kg);
    $sexValue = mb_strtolower(trim((string) old('sex', (string) ($fitnessProfileModel?->sex ?? ''))));
    $goalValue = mb_strtolower(trim((string) old('goal', (string) ($fitnessProfileModel?->goal ?? ''))));
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

    $hasFitnessErrors = $errors->has('age')
        || $errors->has('sex')
        || $errors->has('height_cm')
        || $errors->has('weight_kg')
        || $errors->has('goal')
        || $errors->has('experience_level')
        || $errors->has('days_per_week')
        || $errors->has('session_minutes')
        || $errors->has('limitations')
        || $errors->has('limitations.*');
@endphp

@if ($hasFitnessErrors)
    <p class="profile-message profile-message-error">Revisa los campos marcados antes de guardar.</p>
@endif

<form method="POST" action="{{ route('client-mobile.fitness-profile.save', ['gymSlug' => $gym->slug]) }}" class="space-y-3" data-fitness-form="1">
    @csrf
    <input type="hidden" name="_fitness_form" value="1">
    <input type="hidden" name="_fitness_modal" value="{{ $isModalForm ? '1' : '0' }}">
    <input type="hidden" name="next_screen" value="{{ $nextScreen }}">

    <div class="fitness-grid-2">
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Edad</span>
            <input type="number" name="age" class="module-input" min="12" max="90" step="1" value="{{ $ageValue !== null ? $ageValue : '' }}" placeholder="Ej: 29" required>
            @error('age')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
        </label>
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Sexo</span>
            <div class="fitness-chip-grid">
                @foreach ($fitnessSexOptions as $value => $label)
                    @php
                        $inputId = $formIdPrefix.'-sex-'.$value;
                        $isChecked = $sexValue === $value;
                    @endphp
                    <label for="{{ $inputId }}" class="fitness-chip">
                        <input id="{{ $inputId }}" type="radio" name="sex" value="{{ $value }}" class="fitness-chip-input" {{ $isChecked ? 'checked' : '' }} required>
                        <span class="fitness-chip-label">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
            @error('sex')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
        </label>
    </div>

    <div class="fitness-grid-2">
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Altura (cm)</span>
            <input type="number" name="height_cm" class="module-input" min="120" max="250" step="0.1" value="{{ $heightValue !== null ? $heightValue : '' }}" placeholder="Ej: 170">
            @error('height_cm')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
        </label>
        <label class="block space-y-1 text-sm">
            <span class="fitness-field-label">Peso actual (kg)</span>
            <input type="number" name="weight_kg" class="module-input" min="30" max="400" step="0.1" value="{{ $weightValue !== null ? $weightValue : '' }}" placeholder="Ej: 72.5">
            @error('weight_kg')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
        </label>
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Objetivo</p>
        <div class="fitness-chip-grid">
            @foreach ($fitnessGoalOptions as $value => $label)
                @php
                    $inputId = $formIdPrefix.'-goal-'.$value;
                    $isChecked = $goalValue === $value;
                @endphp
                <label for="{{ $inputId }}" class="fitness-chip">
                    <input id="{{ $inputId }}" type="radio" name="goal" value="{{ $value }}" class="fitness-chip-input" {{ $isChecked ? 'checked' : '' }} required>
                    <span class="fitness-chip-label">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('goal')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Nivel de experiencia</p>
        <div class="fitness-chip-grid">
            @foreach ($fitnessLevelOptions as $value => $label)
                @php
                    $inputId = $formIdPrefix.'-level-'.$value;
                    $isChecked = $experienceValue === $value;
                @endphp
                <label for="{{ $inputId }}" class="fitness-chip">
                    <input id="{{ $inputId }}" type="radio" name="experience_level" value="{{ $value }}" class="fitness-chip-input" {{ $isChecked ? 'checked' : '' }} required>
                    <span class="fitness-chip-label">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @error('experience_level')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Dias por semana</p>
        <div class="fitness-chip-grid">
            @foreach ([3, 4, 5, 6, 7] as $daysOption)
                @php
                    $optionValue = (string) $daysOption;
                    $inputId = $formIdPrefix.'-days-'.$optionValue;
                    $isChecked = $daysValue === $optionValue;
                @endphp
                <label for="{{ $inputId }}" class="fitness-chip">
                    <input id="{{ $inputId }}" type="radio" name="days_per_week" value="{{ $optionValue }}" class="fitness-chip-input" {{ $isChecked ? 'checked' : '' }} required>
                    <span class="fitness-chip-label">{{ $optionValue }} dias</span>
                </label>
            @endforeach
        </div>
        @error('days_per_week')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Duracion de sesion</p>
        <div class="fitness-chip-grid">
            @foreach ([45, 60, 90] as $minutesOption)
                @php
                    $optionValue = (string) $minutesOption;
                    $inputId = $formIdPrefix.'-minutes-'.$optionValue;
                    $isChecked = $minutesValue === $optionValue;
                @endphp
                <label for="{{ $inputId }}" class="fitness-chip">
                    <input id="{{ $inputId }}" type="radio" name="session_minutes" value="{{ $optionValue }}" class="fitness-chip-input" {{ $isChecked ? 'checked' : '' }} required>
                    <span class="fitness-chip-label">{{ $optionValue }} min</span>
                </label>
            @endforeach
        </div>
        @error('session_minutes')<p class="profile-field-error">{{ (string) $message }}</p>@enderror
    </div>

    <div class="space-y-1">
        <p class="fitness-field-label">Limitaciones o molestias</p>
        <div class="fitness-chip-grid">
            @foreach ($fitnessLimitationsOptions as $value => $label)
                @php
                    $inputId = $formIdPrefix.'-limit-'.$value;
                    $isChecked = in_array($value, $selectedLimitations, true);
                @endphp
                <label for="{{ $inputId }}" class="fitness-chip">
                    <input id="{{ $inputId }}" type="checkbox" name="limitations[]" value="{{ $value }}" class="fitness-chip-input" {{ $isChecked ? 'checked' : '' }}>
                    <span class="fitness-chip-label">{{ $label }}</span>
                </label>
            @endforeach
        </div>
        @if ($errors->has('limitations') || $errors->has('limitations.*'))
            <p class="profile-field-error">{{ (string) ($errors->first('limitations') ?: $errors->first('limitations.*')) }}</p>
        @endif
        <p class="fitness-inline-help">Si eliges "Ninguna", se desmarcan automaticamente las demas opciones.</p>
    </div>

    <button type="submit" class="module-action module-action-primary w-full">{{ $submitLabel }}</button>
</form>

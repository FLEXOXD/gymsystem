<?php $__env->startSection('title', 'SuperAdmin Gimnasio'); ?>
<?php $__env->startSection('page-title', 'Crear gimnasio'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $defaultTimezone = old('gym_timezone', $defaultTimezone ?? 'America/Guayaquil');
        $defaultCurrency = old('gym_currency_code', 'USD');
        $defaultLanguage = old('gym_language_code', 'es');
        $addressCountry = old('gym_address_country', 'ec');
        $addressState = old('gym_address_state', '');
        $addressCity = old('gym_address_city', '');
        $addressLine = old('gym_address_line', '');
        $defaultAdminGender = old('admin_gender', '');
        $defaultAdminIdentificationType = old('admin_identification_type', '');
        $defaultAdminIdentificationNumber = old('admin_identification_number', '');
        $statesForCountry = $locationCatalog[$addressCountry]['states'] ?? [];
        $citiesForState = $statesForCountry[$addressState] ?? [];
        $gymsWithAdmins = $gymsWithAdmins ?? collect();
        $adminEditErrorKeys = [
            'admin_user_id',
            'admin_name',
            'admin_email',
            'admin_gender',
            'admin_birth_date',
            'admin_identification_type',
            'admin_identification_number',
            'admin_country_iso',
            'admin_phone_country_dial',
            'admin_phone_number',
            'admin_profile_photo',
        ];
        $adminEditHasErrors = $errors->hasAny($adminEditErrorKeys);
        $adminEditOldGymId = (int) old('admin_gym_id', 0);
        $adminEditRouteTemplate = route('superadmin.gyms.admin-user.update', ['gym' => '__GYM__']);
        $adminEditOldData = [
            'hasErrors' => $adminEditHasErrors,
            'gymId' => $adminEditOldGymId,
            'userId' => (int) old('admin_user_id', 0),
            'name' => (string) old('admin_name', ''),
            'email' => (string) old('admin_email', ''),
            'gender' => (string) old('admin_gender', ''),
            'birthDate' => (string) old('admin_birth_date', ''),
            'identificationType' => (string) old('admin_identification_type', ''),
            'identificationNumber' => (string) old('admin_identification_number', ''),
            'countryIso' => strtolower((string) old('admin_country_iso', '')),
            'phoneCountryDial' => (string) old('admin_phone_country_dial', ''),
            'phoneNumber' => (string) old('admin_phone_number', ''),
        ];
    ?>

    <div class="space-y-5">
        <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Crear nuevo gimnasio','subtitle' => 'Se crea el gimnasio y su usuario administrador principal.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Crear nuevo gimnasio','subtitle' => 'Se crea el gimnasio y su usuario administrador principal.']); ?>
            <form method="POST" action="<?php echo e(route('superadmin.gyms.store')); ?>" class="grid gap-3 lg:grid-cols-3">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Nombre del gimnasio</label>
                    <input type="text" name="gym_name" value="<?php echo e(old('gym_name')); ?>" class="ui-input" placeholder="Ej: Titan Gym" required>
                    <?php $__errorArgs = ['gym_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Telefono</label>
                    <input type="text" name="gym_phone" value="<?php echo e(old('gym_phone')); ?>" class="ui-input" placeholder="+593 999 999 999">
                    <?php $__errorArgs = ['gym_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Pais</label>
                    <select id="gym-address-country" name="gym_address_country" class="ui-input" required>
                        <?php $__currentLoopData = $locationCatalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $countryCode => $countryMeta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($countryCode); ?>" <?php if($addressCountry === $countryCode): echo 'selected'; endif; ?>><?php echo e($countryMeta['label']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['gym_address_country'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Provincia / Estado</label>
                    <select id="gym-address-state" name="gym_address_state" class="ui-input" required>
                        <option value="">Selecciona provincia/estado</option>
                        <?php $__currentLoopData = array_keys($statesForCountry); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stateName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($stateName); ?>" <?php if($addressState === $stateName): echo 'selected'; endif; ?>><?php echo e($stateName); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['gym_address_state'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Ciudad</label>
                    <select id="gym-address-city" name="gym_address_city" class="ui-input" required>
                        <option value="">Selecciona ciudad</option>
                        <?php $__currentLoopData = $citiesForState; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cityName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cityName); ?>" <?php if($addressCity === $cityName): echo 'selected'; endif; ?>><?php echo e($cityName); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['gym_address_city'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Direccion (linea)</label>
                    <input type="text" name="gym_address_line" value="<?php echo e($addressLine); ?>" class="ui-input" placeholder="Barrio, avenida, referencia">
                    <?php $__errorArgs = ['gym_address_line'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="lg:col-span-3 rounded-xl border border-[var(--border)] bg-[var(--card-muted)] p-3">
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Zona horaria</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <input id="gym-timezone-search" type="text" class="ui-input min-w-[240px] flex-1" placeholder="Buscar por pais, ciudad o zona (ej: ecuador, bogota, mexico)">
                        <button id="gym-timezone-detect" type="button" class="ui-button ui-button-muted px-3 py-2 text-xs font-bold">Usar navegador</button>
                    </div>
                    <p id="gym-timezone-hint" class="ui-muted mt-1 text-xs"></p>

                    <select id="gym-timezone-select" name="gym_timezone" class="ui-input mt-2" required>
                        <?php $__currentLoopData = $timezoneOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timezoneValue => $timezoneLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($timezoneValue); ?>" <?php if($defaultTimezone === $timezoneValue): echo 'selected'; endif; ?>><?php echo e($timezoneLabel); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <p id="gym-timezone-selected" class="ui-muted mt-1 text-xs"></p>
                    <?php $__errorArgs = ['gym_timezone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Moneda</label>
                    <select name="gym_currency_code" class="ui-input" required>
                        <?php $__currentLoopData = $currencyOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currencyCode => $currencyMeta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($currencyCode); ?>" <?php if($defaultCurrency === $currencyCode): echo 'selected'; endif; ?>><?php echo e($currencyCode); ?> - <?php echo e($currencyMeta['name']); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['gym_currency_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Idioma</label>
                    <select name="gym_language_code" class="ui-input" required>
                        <?php $__currentLoopData = $languageOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $langCode => $langLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($langCode); ?>" <?php if($defaultLanguage === $langCode): echo 'selected'; endif; ?>><?php echo e($langLabel); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['gym_language_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Nombre del admin</label>
                    <input type="text" name="admin_name" value="<?php echo e(old('admin_name')); ?>" class="ui-input" placeholder="Ej: Carlos Perez" required>
                    <?php $__errorArgs = ['admin_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Correo del admin</label>
                    <input type="email" name="admin_email" value="<?php echo e(old('admin_email')); ?>" class="ui-input" placeholder="admin@gym.com" required>
                    <?php $__errorArgs = ['admin_email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Genero del admin</label>
                    <select name="admin_gender" class="ui-input">
                        <option value="" <?php if($defaultAdminGender === ''): echo 'selected'; endif; ?>>No especificado</option>
                        <option value="male" <?php if($defaultAdminGender === 'male'): echo 'selected'; endif; ?>>Hombre</option>
                        <option value="female" <?php if($defaultAdminGender === 'female'): echo 'selected'; endif; ?>>Mujer</option>
                        <option value="other" <?php if($defaultAdminGender === 'other'): echo 'selected'; endif; ?>>Otro</option>
                        <option value="prefer_not_say" <?php if($defaultAdminGender === 'prefer_not_say'): echo 'selected'; endif; ?>>Prefiero no decir</option>
                    </select>
                    <?php $__errorArgs = ['admin_gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Tipo de identificacion</label>
                    <select name="admin_identification_type" class="ui-input">
                        <option value="" <?php if($defaultAdminIdentificationType === ''): echo 'selected'; endif; ?>>No especificado</option>
                        <option value="cedula" <?php if($defaultAdminIdentificationType === 'cedula'): echo 'selected'; endif; ?>>Cedula</option>
                        <option value="dni" <?php if($defaultAdminIdentificationType === 'dni'): echo 'selected'; endif; ?>>DNI</option>
                        <option value="passport" <?php if($defaultAdminIdentificationType === 'passport'): echo 'selected'; endif; ?>>Pasaporte</option>
                    </select>
                    <?php $__errorArgs = ['admin_identification_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Cedula / numero de identificacion</label>
                    <input type="text" name="admin_identification_number" value="<?php echo e($defaultAdminIdentificationNumber); ?>" class="ui-input" placeholder="Ej: 1726309071">
                    <?php $__errorArgs = ['admin_identification_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Contrasena del admin</label>
                    <input type="password" name="admin_password" class="ui-input" placeholder="Minimo 8 caracteres" required>
                    <?php $__errorArgs = ['admin_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-xs font-semibold text-rose-600 dark:text-rose-300"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="ui-muted mb-1 block text-xs font-bold uppercase tracking-wide">Confirmar contrasena</label>
                    <input type="password" name="admin_password_confirmation" class="ui-input" placeholder="Repite la contrasena" required>
                </div>

                <div class="lg:col-span-3 flex items-center justify-between gap-3">
                    <p class="ui-muted text-xs">El slug se genera automaticamente y se usa en URLs tipo <span class="font-semibold">/mi-gym/panel</span>.</p>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>Crear gimnasio <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Usuarios de gimnasios','subtitle' => 'Edita datos del usuario administrador por gimnasio o elimina el gimnasio completo.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Usuarios de gimnasios','subtitle' => 'Edita datos del usuario administrador por gimnasio o elimina el gimnasio completo.']); ?>
            <p class="mb-3 text-xs font-semibold text-rose-700 dark:text-rose-300">
                Eliminar gimnasio borrara todo: clientes, planes, membresias, caja, reportes y usuario del gimnasio.
            </p>
            <?php if($adminEditHasErrors): ?>
                <div class="mb-3 rounded-xl border border-rose-300/60 bg-rose-100/60 px-3 py-2 text-sm font-semibold text-rose-800 dark:border-rose-300/40 dark:bg-rose-300/10 dark:text-rose-200">
                    Revisa los datos del usuario. Hay campos con errores.
                </div>
            <?php endif; ?>

            <div class="overflow-x-auto">
                <table class="ui-table min-w-[980px]">
                    <thead>
                    <tr>
                        <th>Gimnasio</th>
                        <th>Admin</th>
                        <th>Correo</th>
                        <th>Genero</th>
                        <th>Cedula / ID</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $gymsWithAdmins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gymRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $adminUser = $gymRow->users->first();
                        ?>
                        <tr>
                            <td>
                                <p class="font-semibold"><?php echo e($gymRow->name); ?></p>
                                <p class="ui-muted text-xs">/<?php echo e($gymRow->slug); ?>/panel</p>
                            </td>
                            <td><?php echo e($adminUser?->name ?? '-'); ?></td>
                            <td><?php echo e($adminUser?->email ?? '-'); ?></td>
                            <td><?php echo e($adminUser?->gender ?? '-'); ?></td>
                            <td><?php echo e($adminUser?->identification_number ?? '-'); ?></td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <?php if($adminUser): ?>
                                        <button
                                            type="button"
                                            class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold"
                                            data-edit-admin
                                            data-gym-id="<?php echo e((int) $gymRow->id); ?>"
                                            data-user-id="<?php echo e((int) $adminUser->id); ?>"
                                            data-admin-name="<?php echo e((string) $adminUser->name); ?>"
                                            data-admin-email="<?php echo e((string) $adminUser->email); ?>"
                                            data-admin-gender="<?php echo e((string) ($adminUser->gender ?? '')); ?>"
                                            data-admin-birth-date="<?php echo e(optional($adminUser->birth_date)->format('Y-m-d')); ?>"
                                            data-admin-identification-type="<?php echo e((string) ($adminUser->identification_type ?? '')); ?>"
                                            data-admin-identification-number="<?php echo e((string) ($adminUser->identification_number ?? '')); ?>"
                                            data-admin-country-iso="<?php echo e(strtolower((string) ($adminUser->country_iso ?? $gymRow->address_country_code ?? 'ec'))); ?>"
                                            data-admin-phone-country-dial="<?php echo e((string) ($adminUser->phone_country_dial ?? '+593')); ?>"
                                            data-admin-phone-number="<?php echo e((string) ($adminUser->phone_number ?? '')); ?>"
                                        >
                                            Editar usuario
                                        </button>
                                    <?php endif; ?>

                                    <form method="POST"
                                          action="<?php echo e(route('superadmin.gyms.destroy', $gymRow->id)); ?>"
                                          onsubmit="return confirm('Se eliminara el gimnasio y todos sus datos. Deseas continuar?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'danger']); ?>Eliminar gimnasio <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center text-sm text-slate-500">No hay gimnasios registrados.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="admin-edit-modal" class="fixed inset-0 z-[80] hidden items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/60" data-admin-modal-close></div>
                <div class="relative z-[81] w-full max-w-5xl rounded-2xl border border-[var(--border)] bg-[var(--card)] p-4 shadow-2xl">
                    <div class="mb-3 flex items-center justify-between">
                        <h3 class="ui-heading text-lg">Editar usuario del gimnasio</h3>
                        <button type="button" class="ui-button ui-button-ghost px-3 py-2 text-xs font-bold" data-admin-modal-close>Cerrar</button>
                    </div>

                    <form id="admin-edit-form" method="POST" action="#" enctype="multipart/form-data" class="grid gap-3 md:grid-cols-3">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" id="modal-admin-user-id" name="admin_user_id" value="<?php echo e((int) old('admin_user_id', 0)); ?>">
                        <input type="hidden" id="modal-admin-gym-id" name="admin_gym_id" value="<?php echo e($adminEditOldGymId > 0 ? $adminEditOldGymId : 0); ?>">

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Nombre
                            <input id="modal-admin-name" type="text" name="admin_name" class="ui-input" value="<?php echo e(old('admin_name')); ?>" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-2">
                            Correo
                            <input id="modal-admin-email" type="email" name="admin_email" class="ui-input" value="<?php echo e(old('admin_email')); ?>" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Genero
                            <select id="modal-admin-gender" name="admin_gender" class="ui-input">
                                <option value="">No especificado</option>
                                <option value="male">Hombre</option>
                                <option value="female">Mujer</option>
                                <option value="other">Otro</option>
                                <option value="prefer_not_say">Prefiero no decir</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Nacimiento
                            <input id="modal-admin-birth-date" type="date" name="admin_birth_date" class="ui-input" value="<?php echo e(old('admin_birth_date')); ?>">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Tipo de identificacion
                            <select id="modal-admin-identification-type" name="admin_identification_type" class="ui-input">
                                <option value="">No especificado</option>
                                <option value="cedula">Cedula</option>
                                <option value="dni">DNI</option>
                                <option value="passport">Pasaporte</option>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Numero identificacion
                            <input id="modal-admin-identification-number" type="text" name="admin_identification_number" class="ui-input" value="<?php echo e(old('admin_identification_number')); ?>" placeholder="Ej: 1726309071">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Pais
                            <select id="modal-admin-country-iso" name="admin_country_iso" class="ui-input">
                                <option value="">No especificado</option>
                                <?php $__currentLoopData = $locationCatalog; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $countryCode => $countryMeta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($countryCode); ?>"><?php echo e($countryMeta['label']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Codigo telefono
                            <input id="modal-admin-phone-country-dial" type="text" name="admin_phone_country_dial" class="ui-input" value="<?php echo e(old('admin_phone_country_dial')); ?>" placeholder="+593">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Telefono
                            <input id="modal-admin-phone-number" type="text" name="admin_phone_number" class="ui-input" value="<?php echo e(old('admin_phone_number')); ?>" placeholder="0991234567">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide md:col-span-3">
                            Foto de perfil (opcional)
                            <input type="file" name="admin_profile_photo" class="ui-input" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                            <p class="ui-muted text-[11px]">JPG/PNG/WEBP, maximo 15MB.</p>
                        </label>

                        <?php if($adminEditHasErrors): ?>
                            <div class="rounded-xl border border-rose-300/60 bg-rose-100/60 px-3 py-2 text-xs font-semibold text-rose-800 dark:border-rose-300/40 dark:bg-rose-300/10 dark:text-rose-200 md:col-span-3">
                                <?php $__currentLoopData = $errors->getMessages(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $errorKey => $errorMessages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(in_array($errorKey, $adminEditErrorKeys, true)): ?>
                                        <?php $__currentLoopData = $errorMessages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $errorMessage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <p><?php echo e($errorMessage); ?></p>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>

                        <div class="md:col-span-3 flex justify-end gap-2">
                            <button type="button" class="ui-button ui-button-ghost" data-admin-modal-close>Cancelar</button>
                            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>Guardar cambios <?php echo $__env->renderComponent(); ?>
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
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        const locationCatalog = <?php echo json_encode($locationCatalog, 15, 512) ?>;
        const countrySelect = document.getElementById('gym-address-country');
        const stateSelect = document.getElementById('gym-address-state');
        const citySelect = document.getElementById('gym-address-city');

        const selectedStateValue = <?php echo json_encode($addressState, 15, 512) ?>;
        const selectedCityValue = <?php echo json_encode($addressCity, 15, 512) ?>;

        function replaceOptions(select, items, placeholder) {
            if (!select) return;
            select.innerHTML = '';

            const placeholderOption = document.createElement('option');
            placeholderOption.value = '';
            placeholderOption.textContent = placeholder;
            select.appendChild(placeholderOption);

            items.forEach(function (item) {
                const option = document.createElement('option');
                option.value = item;
                option.textContent = item;
                select.appendChild(option);
            });
        }

        function getStates(countryCode) {
            const country = locationCatalog[countryCode] || null;
            if (!country || !country.states) return [];
            return Object.keys(country.states);
        }

        function getCities(countryCode, stateName) {
            const country = locationCatalog[countryCode] || null;
            if (!country || !country.states || !country.states[stateName]) return [];
            return country.states[stateName];
        }

        function syncStates(preferredState, preferredCity) {
            if (!countrySelect || !stateSelect) return;
            const states = getStates(countrySelect.value);
            replaceOptions(stateSelect, states, 'Selecciona provincia/estado');

            if (preferredState && states.includes(preferredState)) {
                stateSelect.value = preferredState;
            }

            syncCities(preferredCity);
        }

        function syncCities(preferredCity) {
            if (!countrySelect || !stateSelect || !citySelect) return;
            const cities = getCities(countrySelect.value, stateSelect.value);
            replaceOptions(citySelect, cities, 'Selecciona ciudad');

            if (preferredCity && cities.includes(preferredCity)) {
                citySelect.value = preferredCity;
            }
        }

        countrySelect?.addEventListener('change', function () {
            syncStates('', '');
        });

        stateSelect?.addEventListener('change', function () {
            syncCities('');
        });

        syncStates(selectedStateValue, selectedCityValue);

        const timezoneSearch = document.getElementById('gym-timezone-search');
        const timezoneSelect = document.getElementById('gym-timezone-select');
        const timezoneDetect = document.getElementById('gym-timezone-detect');
        const timezoneHint = document.getElementById('gym-timezone-hint');
        const timezoneSelected = document.getElementById('gym-timezone-selected');

        const timezoneSource = Array.from(timezoneSelect?.options || []).map(function (option) {
            return {
                value: option.value,
                label: option.textContent || option.value,
                search: (option.value + ' ' + (option.textContent || '')).toLowerCase(),
            };
        });

        function renderTimezoneOptions(query, preferredValue) {
            if (!timezoneSelect) return;
            const normalized = (query || '').trim().toLowerCase();
            const current = preferredValue || timezoneSelect.value;

            const filtered = timezoneSource.filter(function (item) {
                return normalized === '' || item.search.includes(normalized);
            });

            timezoneSelect.innerHTML = '';
            filtered.forEach(function (item) {
                const option = document.createElement('option');
                option.value = item.value;
                option.textContent = item.label;
                timezoneSelect.appendChild(option);
            });

            const hasCurrent = filtered.some(function (item) {
                return item.value === current;
            });
            if (hasCurrent) {
                timezoneSelect.value = current;
            }

            updateTimezoneLabel();
        }

        function updateTimezoneLabel() {
            if (!timezoneSelect || !timezoneSelected) return;
            const label = timezoneSelect.options[timezoneSelect.selectedIndex]?.textContent || '-';
            timezoneSelected.textContent = 'Seleccionada: ' + label;
        }

        function detectTimezone() {
            try {
                return Intl.DateTimeFormat().resolvedOptions().timeZone || '';
            } catch (error) {
                return '';
            }
        }

        timezoneSearch?.addEventListener('input', function () {
            renderTimezoneOptions(timezoneSearch.value, timezoneSelect?.value || '');
        });

        timezoneSelect?.addEventListener('change', updateTimezoneLabel);

        timezoneDetect?.addEventListener('click', function () {
            const detected = detectTimezone();
            if (!detected) {
                if (timezoneHint) timezoneHint.textContent = 'No se pudo detectar la zona horaria del navegador.';
                return;
            }

            if (timezoneHint) timezoneHint.textContent = 'Detectada en este equipo: ' + detected;
            renderTimezoneOptions('', detected);
        });

        const detectedOnLoad = detectTimezone();
        if (timezoneHint) {
            timezoneHint.textContent = detectedOnLoad
                ? 'Detectada en este equipo: ' + detectedOnLoad
                : 'No se pudo detectar automaticamente la zona horaria.';
        }

        renderTimezoneOptions('', timezoneSelect?.value || '');

        const adminEditModal = document.getElementById('admin-edit-modal');
        const adminEditForm = document.getElementById('admin-edit-form');
        const adminEditButtons = Array.from(document.querySelectorAll('[data-edit-admin]'));
        const adminModalCloseButtons = Array.from(document.querySelectorAll('[data-admin-modal-close]'));
        const adminUpdateRouteTemplate = <?php echo json_encode($adminEditRouteTemplate, 15, 512) ?>;
        const adminEditOldData = <?php echo json_encode($adminEditOldData, 15, 512) ?>;

        const modalFields = {
            userId: document.getElementById('modal-admin-user-id'),
            gymId: document.getElementById('modal-admin-gym-id'),
            name: document.getElementById('modal-admin-name'),
            email: document.getElementById('modal-admin-email'),
            gender: document.getElementById('modal-admin-gender'),
            birthDate: document.getElementById('modal-admin-birth-date'),
            identificationType: document.getElementById('modal-admin-identification-type'),
            identificationNumber: document.getElementById('modal-admin-identification-number'),
            countryIso: document.getElementById('modal-admin-country-iso'),
            phoneCountryDial: document.getElementById('modal-admin-phone-country-dial'),
            phoneNumber: document.getElementById('modal-admin-phone-number'),
        };

        function setModalValue(field, value) {
            if (!field) return;
            field.value = value || '';
        }

        function openAdminModal(data) {
            if (!adminEditModal || !adminEditForm) return;

            const gymId = String(data.gymId || '').trim();
            if (gymId === '') return;

            adminEditForm.action = adminUpdateRouteTemplate.replace('__GYM__', gymId);
            setModalValue(modalFields.userId, String(data.userId || ''));
            setModalValue(modalFields.gymId, gymId);
            setModalValue(modalFields.name, data.name || '');
            setModalValue(modalFields.email, data.email || '');
            setModalValue(modalFields.gender, data.gender || '');
            setModalValue(modalFields.birthDate, data.birthDate || '');
            setModalValue(modalFields.identificationType, data.identificationType || '');
            setModalValue(modalFields.identificationNumber, data.identificationNumber || '');
            setModalValue(modalFields.countryIso, (data.countryIso || '').toLowerCase());
            setModalValue(modalFields.phoneCountryDial, data.phoneCountryDial || '');
            setModalValue(modalFields.phoneNumber, data.phoneNumber || '');

            adminEditModal.classList.remove('hidden');
            adminEditModal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function closeAdminModal() {
            if (!adminEditModal) return;
            adminEditModal.classList.add('hidden');
            adminEditModal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }

        adminEditButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openAdminModal({
                    gymId: button.dataset.gymId || '',
                    userId: button.dataset.userId || '',
                    name: button.dataset.adminName || '',
                    email: button.dataset.adminEmail || '',
                    gender: button.dataset.adminGender || '',
                    birthDate: button.dataset.adminBirthDate || '',
                    identificationType: button.dataset.adminIdentificationType || '',
                    identificationNumber: button.dataset.adminIdentificationNumber || '',
                    countryIso: button.dataset.adminCountryIso || '',
                    phoneCountryDial: button.dataset.adminPhoneCountryDial || '',
                    phoneNumber: button.dataset.adminPhoneNumber || '',
                });
            });
        });

        adminModalCloseButtons.forEach(function (button) {
            button.addEventListener('click', closeAdminModal);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAdminModal();
            }
        });

        if (adminEditOldData.hasErrors && Number(adminEditOldData.gymId) > 0) {
            openAdminModal(adminEditOldData);
        }
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
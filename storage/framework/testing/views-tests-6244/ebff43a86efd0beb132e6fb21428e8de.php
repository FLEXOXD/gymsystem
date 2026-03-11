<?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Credenciales','subtitle' => 'Gestión de QR y RFID con acciones directas.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Credenciales','subtitle' => 'Gestión de QR y RFID con acciones directas.']); ?>
    <?php
        $clientPhoneRaw = trim((string) ($client->phone ?? ''));
        $gymCountryCode = strtolower(trim((string) ($client->gym?->address_country_code ?? '')));
        $countryDialByIso = [
            'ec' => '593',
            'co' => '57',
            'pe' => '51',
            'mx' => '52',
            'cl' => '56',
            'ar' => '54',
            'bo' => '591',
            'us' => '1',
        ];
        $countryDial = (string) ($countryDialByIso[$gymCountryCode] ?? '');

        $normalizeWhatsappPhone = function (string $rawPhone, string $dialPrefix): string {
            $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';
            if ($digits === '') {
                return '';
            }

            while (str_starts_with($digits, '00')) {
                $digits = substr($digits, 2);
            }

            if ($digits === '') {
                return '';
            }

            if ($dialPrefix !== '' && str_starts_with($digits, $dialPrefix)) {
                return $digits;
            }

            if (str_starts_with($digits, '0')) {
                $local = ltrim($digits, '0');
                if ($local === '') {
                    return '';
                }

                $fallbackDial = $dialPrefix !== '' ? $dialPrefix : '593';
                return $fallbackDial.$local;
            }

            if ($dialPrefix !== '' && strlen($digits) >= 7 && strlen($digits) <= 10) {
                return $dialPrefix.$digits;
            }

            return $digits;
        };

        $whatsappDigits = $normalizeWhatsappPhone($clientPhoneRaw, $countryDial);
        $hasWhatsappPhone = strlen($whatsappDigits) >= 8;

        $whatsappQrMessage = '';
        $whatsappQrUrl = '';
        $publicCardUrl = '';
        $publicQrImageUrl = '';
        $publicQrDownloadUrl = '';
        $sanitizeAddressForQr = static function (string $raw): string {
            $value = trim((string) preg_replace('/\s+/', ' ', str_replace(["\r", "\n"], ' ', $raw)));
            if ($value === '') {
                return '';
            }

            $parts = array_values(array_filter(array_map('trim', explode(',', $value)), static fn (string $part): bool => $part !== ''));
            $filtered = array_values(array_filter($parts, static function (string $part): bool {
                $digits = preg_replace('/\D+/', '', $part) ?? '';
                $looksLikePhone = strlen($digits) >= 7;
                $hasPhoneKeyword = preg_match('/\b(tel|teléfono|cel|móvil|whatsapp|phone)\b/i', $part) === 1;
                if ($hasPhoneKeyword) {
                    return false;
                }
                if ($looksLikePhone) {
                    $hasLetters = preg_match('/[[:alpha:]]/u', $part) === 1;
                    if (! $hasLetters) {
                        return false;
                    }
                }

                return true;
            }));

            return implode(', ', $filtered);
        };
        $contextGym = request()->attributes->get('active_gym');
        if (! $contextGym instanceof \App\Models\Gym) {
            $contextGym = null;
        }

        $gymNameForQr = trim((string) ($client->gym?->name ?? $contextGym?->name ?? ''));
        $gymSlugForQr = trim((string) ($client->gym?->slug ?? $contextGym?->slug ?? ''));
        $gymAddressForQr = trim((string) ($client->gym?->address ?? $contextGym?->address ?? ''));
        if ($gymAddressForQr === '') {
            $gymAddressForQr = collect([
                trim((string) ($client->gym?->address_line ?? $contextGym?->address_line ?? '')),
                trim((string) ($client->gym?->address_city ?? $contextGym?->address_city ?? '')),
                trim((string) ($client->gym?->address_state ?? $contextGym?->address_state ?? '')),
            ])->filter()->implode(', ');
        }
        $gymAddressForQr = $sanitizeAddressForQr($gymAddressForQr);
        if ($activeQrCredential && $hasWhatsappPhone) {
            $publicCardUrl = \Illuminate\Support\Facades\URL::signedRoute('clients.card.public', ['client' => $client->id]);
            $publicQrImageUrl = \Illuminate\Support\Facades\URL::signedRoute('clients.card.public-qr-image', ['client' => $client->id]);
            $publicQrDownloadUrl = \Illuminate\Support\Facades\URL::signedRoute('clients.card.public-download', ['client' => $client->id]);
            $gymSummaryParts = array_values(array_filter([
                $gymNameForQr !== '' ? 'Gimnasio: '.$gymNameForQr : '',
                $gymSlugForQr !== '' ? 'Código/sede: '.$gymSlugForQr : '',
                $gymAddressForQr !== '' ? 'Dirección: '.$gymAddressForQr : '',
            ], static fn ($line) => $line !== ''));
            $gymSummaryLine = count($gymSummaryParts) > 0
                ? implode(' | ', $gymSummaryParts)
                : 'Gimnasio: no definido';

            $whatsappLines = [
                'Hola '.$client->full_name.'.',
                $gymSummaryLine,
                'Descarga tu QR aquí: '.$publicQrDownloadUrl,
            ];
            $whatsappQrMessage = implode("\n", $whatsappLines);
            $whatsappQrUrl = 'https://wa.me/'.$whatsappDigits.'?text='.rawurlencode($whatsappQrMessage);
        }
    ?>

    <div class="mb-4 flex flex-wrap gap-2">
        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','xOn:click' => 'openRfidModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','x-on:click' => 'openRfidModal()']); ?>Asignar RFID <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
        <form method="POST" action="<?php echo e(route('client-credentials.generate-qr', $client->id)); ?>">
            <?php echo csrf_field(); ?>
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'secondary']); ?>Generar QR <?php echo $__env->renderComponent(); ?>
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

    <?php if(session('generated_qr_value')): ?>
        <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['type' => 'success','class' => 'mb-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'success','class' => 'mb-4']); ?>
            QR generado: <span class="font-mono text-xs"><?php echo e(session('generated_qr_value')); ?></span>
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

    <?php if($activeQrCredential): ?>
        <div class="mb-5 grid gap-4 rounded-xl border border-cyan-500/30 bg-cyan-500/10 p-4 md:grid-cols-[210px_1fr] md:items-center">
            <div class="rounded-lg border border-slate-300 bg-slate-100 p-3 text-center dark:border-white/10 dark:bg-slate-900/60">
                <?php echo $activeQrSvg; ?>

            </div>
            <div class="space-y-3">
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">QR activo para acceso</p>
                <p class="break-all rounded-lg border border-slate-300 bg-slate-100 p-2 font-mono text-xs text-slate-800 dark:border-white/10 dark:bg-slate-900/60 dark:text-slate-200"><?php echo e($activeQrCredential->value); ?></p>
                <div class="flex flex-wrap gap-2">
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'muted','size' => 'sm','xOn:click' => 'copyQr(@js($activeQrCredential->value))']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'muted','size' => 'sm','x-on:click' => 'copyQr(@js($activeQrCredential->value))']); ?>Copiar valor <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.card', $client->id),'target' => '_blank','rel' => 'noopener','variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.card', $client->id)),'target' => '_blank','rel' => 'noopener','variant' => 'ghost','size' => 'sm']); ?>Imprimir tarjeta <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => route('clients.card.pdf', $client->id),'target' => '_blank','rel' => 'noopener','variant' => 'secondary','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('clients.card.pdf', $client->id)),'target' => '_blank','rel' => 'noopener','variant' => 'secondary','size' => 'sm']); ?>Exportar PDF <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                    <?php if($publicCardUrl !== ''): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $publicCardUrl,'target' => '_blank','rel' => 'noopener','variant' => 'ghost','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($publicCardUrl),'target' => '_blank','rel' => 'noopener','variant' => 'ghost','size' => 'sm']); ?>Ver tarjeta pública <?php echo $__env->renderComponent(); ?>
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
                    <?php if($whatsappQrUrl !== ''): ?>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['href' => $whatsappQrUrl,'target' => '_blank','rel' => 'noopener','variant' => 'success','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($whatsappQrUrl),'target' => '_blank','rel' => 'noopener','variant' => 'success','size' => 'sm']); ?>
                            <span class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path d="M20.52 3.48A11.82 11.82 0 0 0 12.08 0C5.5 0 .13 5.37.13 11.94c0 2.1.55 4.14 1.58 5.94L0 24l6.28-1.64a11.84 11.84 0 0 0 5.8 1.49h.01c6.57 0 11.94-5.37 11.94-11.94a11.8 11.8 0 0 0-3.51-8.43ZM12.09 21.8h-.01a9.8 9.8 0 0 1-4.98-1.36l-.36-.21-3.73.98 1-3.63-.24-.37a9.82 9.82 0 0 1-1.5-5.26c0-5.42 4.41-9.83 9.84-9.83 2.62 0 5.08 1.02 6.93 2.87a9.75 9.75 0 0 1 2.88 6.95c0 5.42-4.42 9.83-9.83 9.83Zm5.39-7.34c-.3-.15-1.78-.88-2.06-.98-.27-.1-.47-.15-.67.15-.2.3-.77.98-.95 1.18-.17.2-.35.22-.65.07-.3-.15-1.26-.46-2.41-1.47a9.02 9.02 0 0 1-1.67-2.07c-.17-.3-.02-.45.13-.6.14-.14.3-.35.45-.52.15-.18.2-.3.3-.5.1-.2.05-.37-.03-.52-.07-.15-.67-1.62-.91-2.21-.24-.58-.49-.5-.67-.5h-.57c-.2 0-.52.08-.8.37-.27.3-1.03 1.01-1.03 2.46s1.06 2.86 1.21 3.06c.15.2 2.08 3.18 5.04 4.45.7.3 1.25.48 1.68.62.7.22 1.34.19 1.84.12.56-.08 1.78-.73 2.03-1.43.25-.7.25-1.3.17-1.43-.07-.13-.27-.2-.57-.35Z"/>
                                </svg>
                                <span>Enviar por WhatsApp</span>
                            </span>
                         <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','xOn:click' => 'copyWhatsappMessage(@js($whatsappQrMessage))']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','x-on:click' => 'copyWhatsappMessage(@js($whatsappQrMessage))']); ?>Copiar mensaje WA <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','variant' => 'ghost','size' => 'sm','disabled' => true,'title' => 'Registra un teléfono válido para enviar por WhatsApp.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'ghost','size' => 'sm','disabled' => true,'title' => 'Registra un teléfono válido para enviar por WhatsApp.']); ?>WhatsApp no disponible <?php echo $__env->renderComponent(); ?>
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
                <p class="text-xs text-cyan-700 dark:text-cyan-300" x-text="qrCopyFeedback"></p>
                <p class="text-xs text-emerald-700 dark:text-emerald-300" x-text="whatsappCopyFeedback"></p>
                <?php if($whatsappQrMessage !== ''): ?>
                    <details class="rounded-lg border border-slate-300 bg-slate-50 p-2 text-xs text-slate-700 dark:border-white/10 dark:bg-slate-900/40 dark:text-slate-300">
                        <summary class="cursor-pointer font-semibold">Vista previa del mensaje de WhatsApp</summary>
                        <pre class="mt-2 whitespace-pre-wrap break-words"><?php echo e($whatsappQrMessage); ?></pre>
                    </details>
                <?php endif; ?>
                <?php if(! $hasWhatsappPhone): ?>
                    <p class="text-xs text-amber-700 dark:text-amber-300">
                        Este cliente no tiene teléfono válido para WhatsApp. Guarda el número con código de país (ej: 593...).
                    </p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if($client->credentials->isNotEmpty()): ?>
        <div class="overflow-x-auto rounded-xl border border-slate-300 dark:border-white/10">
            <table class="ui-table min-w-[880px]">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th>Acción</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $client->credentials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $credential): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $displayValue = $credential->value;
                        if ($credential->type === 'rfid' && strlen((string) $credential->value) > 6) {
                            $displayValue = str_repeat('*', 6).' '.substr((string) $credential->value, -6);
                        }
                        $formId = 'deactivate-credential-'.$credential->id;
                        $credentialLabel = strtoupper((string) $credential->type).' #'.$credential->id;
                    ?>
                    <tr>
                        <td>#<?php echo e($credential->id); ?></td>
                        <td class="font-semibold"><?php echo e(strtoupper((string) $credential->type)); ?></td>
                        <td class="font-mono text-xs"><?php echo e($displayValue); ?></td>
                        <td>
                            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['variant' => $credential->status === 'active' ? 'success' : 'muted']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($credential->status === 'active' ? 'success' : 'muted')]); ?><?php echo e($credential->status); ?> <?php echo $__env->renderComponent(); ?>
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
                        <td><?php echo e($credential->created_at?->format('Y-m-d H:i') ?? '-'); ?></td>
                        <td>
                            <?php if($credential->status === 'active'): ?>
                                <form id="<?php echo e($formId); ?>"
                                      method="POST"
                                      action="<?php echo e(route('client-credentials.deactivate', $credential->id)); ?>"
                                      x-on:submit.prevent="requestDeactivate(<?php echo \Illuminate\Support\Js::from($formId)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($credentialLabel)->toHtml() ?>)">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','variant' => 'danger','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'danger','size' => 'sm']); ?>Desactivar <?php echo $__env->renderComponent(); ?>
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
                            <?php else: ?>
                                <span class="text-slate-500 dark:text-slate-400">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-400 bg-slate-50 p-4 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900/30 dark:text-slate-300">
            <div class="mb-2 inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 9h10"/>
                    <path d="M7 13h6"/>
                </svg>
            </div>
            <p class="font-semibold">Sin credenciales registradas.</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Puedes asignar RFID o generar QR para activar el acceso.</p>
            <div class="mt-3 flex flex-wrap gap-2">
                <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'button','size' => 'sm','variant' => 'ghost','xOn:click' => 'openRfidModal()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','size' => 'sm','variant' => 'ghost','x-on:click' => 'openRfidModal()']); ?>Asignar RFID <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                <form method="POST" action="<?php echo e(route('client-credentials.generate-qr', $client->id)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm','variant' => 'secondary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm','variant' => 'secondary']); ?>Generar QR <?php echo $__env->renderComponent(); ?>
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
<?php /**PATH C:\laragon\www\gymsystem\resources\views/clients/partials/_tab_credentials.blade.php ENDPATH**/ ?>
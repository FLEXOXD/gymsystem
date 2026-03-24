<?php
    $contactWhatsappUrl = trim((string) ($content['whatsapp_url'] ?? '#'));
    $contactWhatsappPhoneRaw = trim((string) ($content['whatsapp_phone'] ?? ''));
    $formatMarketingPhone = static function (string $rawPhone): string {
        $digits = preg_replace('/\D+/', '', $rawPhone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '593') && strlen($digits) === 12) {
            return '+593 '.substr($digits, 3, 3).' '.substr($digits, 6, 3).' '.substr($digits, 9, 3);
        }

        return '+'.$digits;
    };
    $contactWhatsappDisplay = $formatMarketingPhone($contactWhatsappPhoneRaw);
    $contactPhoneDigits = preg_replace('/\D+/', '', $contactWhatsappPhoneRaw) ?? '';
    $contactPhoneDisplay = $contactWhatsappDisplay !== '' ? $contactWhatsappDisplay : '+593 995 142 566';
    $contactEmail = 'flexjok.agencia@gmail.com';
    $contactLocation = 'Machachi, canton Mejia, Pichincha, Ecuador';
    $contactHours = 'De 9:00 AM a 7:00 PM';
    $contactStatusTitle = $contactModalType === 'error'
        ? 'Revisemos tu solicitud'
        : 'Recibimos tu mensaje';
    $contactStatusFallback = $contactModalType === 'error'
        ? 'Completa los campos pendientes y vuelve a intentarlo para que podamos responderte mejor.'
        : 'Gracias por escribirnos. Revisaremos tu mensaje y te responderemos lo antes posible.';
    $contactBackgroundImage = 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=2200&q=80';
?>

<section id="contacto" class="shell section">
    <div class="contact-simple-shell" style="--contact-bg-image: url('<?php echo e($contactBackgroundImage); ?>');">
        <div class="contact-simple-grid">
            <div class="contact-simple-copy reveal">
                <p class="contact-simple-kicker">CONTACTANOS</p>
                <h1 class="contact-simple-title">Estamos listos para ayudarte.</h1>
                <p class="contact-simple-text">
                    Escribenos y con gusto te ayudamos con informacion, reservas o cualquier consulta.
                </p>

                <div class="contact-simple-actions">
                    <?php if($contactWhatsappUrl !== '#'): ?>
                        <a href="<?php echo e($contactWhatsappUrl); ?>" target="_blank" rel="noreferrer" class="contact-action-button">
                            Escribir por WhatsApp
                        </a>
                    <?php elseif($contactPhoneDigits !== ''): ?>
                        <a href="tel:+<?php echo e($contactPhoneDigits); ?>" class="contact-action-button">
                            Llamar ahora
                        </a>
                    <?php endif; ?>

                    <a href="mailto:<?php echo e($contactEmail); ?>" class="contact-action-button contact-action-button--secondary">
                        Enviar correo
                    </a>
                </div>

                <div class="contact-detail-grid" aria-label="Datos de contacto">
                    <article class="contact-detail-card">
                        <span class="contact-detail-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <circle cx="12" cy="10" r="2.5" stroke="currentColor" stroke-width="1.8"/>
                            </svg>
                        </span>
                        <span class="contact-detail-label">Ubicacion</span>
                        <strong class="contact-detail-value"><?php echo e($contactLocation); ?></strong>
                        <p class="contact-detail-note">Atencion cercana para tu gimnasio.</p>
                    </article>

                    <article class="contact-detail-card">
                        <span class="contact-detail-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M6.6 4.8h2.2l1.3 3.1-1.7 1.7a13 13 0 0 0 5.5 5.5l1.7-1.7 3.1 1.3v2.2c0 .7-.6 1.3-1.3 1.3A14.7 14.7 0 0 1 5.3 6.1c0-.7.6-1.3 1.3-1.3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="contact-detail-label">Numero de telefono</span>
                        <strong class="contact-detail-value"><?php echo e($contactPhoneDisplay); ?></strong>
                        <p class="contact-detail-note">Disponible por llamada o WhatsApp.</p>
                    </article>

                    <article class="contact-detail-card">
                        <span class="contact-detail-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="6" width="18" height="12" rx="2.5" stroke="currentColor" stroke-width="1.8"/>
                                <path d="m4 8 8 5 8-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="contact-detail-label">Correo electronico</span>
                        <strong class="contact-detail-value"><?php echo e($contactEmail); ?></strong>
                        <p class="contact-detail-note">Ideal para consultas mas detalladas.</p>
                    </article>

                    <article class="contact-detail-card">
                        <span class="contact-detail-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M12 8v4l2.6 1.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="contact-detail-label">Horario de atencion</span>
                        <strong class="contact-detail-value"><?php echo e($contactHours); ?></strong>
                        <p class="contact-detail-note">Respondemos dentro de este horario.</p>
                    </article>
                </div>
            </div>

            <section class="contact-form-card reveal">
                <header class="contact-form-header">
                    <small>Formulario de contacto</small>
                    <h2>Envianos un mensaje</h2>
                    <p>Completa tus datos y te responderemos lo antes posible.</p>
                </header>

                <form id="landing-contact-form"
                      class="contact-form-grid"
                      method="POST"
                      action="<?php echo e(route('landing.contact.store')); ?>">
                    <?php echo csrf_field(); ?>

                    <label class="contact-form-field">
                        <span class="contact-label">Nombre <em>*</em></span>
                        <input type="text"
                               class="contact-input <?php $__errorArgs = ['first_name', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="first_name"
                               value="<?php echo e(old('first_name')); ?>"
                               placeholder="Tu nombre"
                               autocomplete="given-name"
                               required>
                        <?php $__errorArgs = ['first_name', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="contact-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="contact-form-field">
                        <span class="contact-label">Apellido <em>*</em></span>
                        <input type="text"
                               class="contact-input <?php $__errorArgs = ['last_name', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="last_name"
                               value="<?php echo e(old('last_name')); ?>"
                               placeholder="Tu apellido"
                               autocomplete="family-name"
                               required>
                        <?php $__errorArgs = ['last_name', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="contact-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="contact-form-field contact-form-field--full">
                        <span class="contact-label">Correo <em>*</em></span>
                        <input type="email"
                               class="contact-input <?php $__errorArgs = ['email', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="email"
                               value="<?php echo e(old('email')); ?>"
                               placeholder="tucorreo@gmail.com"
                               autocomplete="email"
                               required>
                        <?php $__errorArgs = ['email', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="contact-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="contact-form-field contact-form-field--full">
                        <span class="contact-label">Mensaje <em>*</em></span>
                        <textarea class="contact-input <?php $__errorArgs = ['message', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  name="message"
                                  rows="6"
                                  placeholder="Escribe aqui tu mensaje"
                                  required><?php echo e(old('message')); ?></textarea>
                        <span class="contact-helper">Tambien puedes escribirnos directo por WhatsApp o correo.</span>
                        <?php $__errorArgs = ['message', 'landingContact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="contact-feedback"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <div class="contact-submit">
                        <button type="submit" class="contact-submit-button">Enviar mensaje</button>
                        <p class="contact-submit-note">Tu informacion se usara solo para responder esta solicitud.</p>
                    </div>
                </form>
            </section>
        </div>
    </div>
</section>

<div id="contact-status-backdrop" class="contact-status-backdrop <?php echo e($contactModalMessage !== '' ? 'is-open' : ''); ?>"></div>
<div id="contact-status-modal"
     class="contact-status-modal <?php echo e($contactModalMessage !== '' ? 'is-open' : ''); ?> <?php echo e($contactModalType === 'error' ? 'is-error' : ''); ?>"
     data-variant="<?php echo e($contactModalType); ?>"
     role="dialog"
     aria-modal="true"
     aria-labelledby="contact-status-title">
    <div class="contact-status-inner">
        <span class="contact-status-badge"><?php echo e($contactModalType === 'error' ? 'Revisa los datos' : 'Mensaje recibido'); ?></span>
        <span class="contact-status-icon" aria-hidden="true">
            <?php if($contactModalType === 'error'): ?>
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M12 8v5.5M12 17h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.72 3h16.92a2 2 0 0 0 1.72-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                </svg>
            <?php else: ?>
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="m7 12 3.2 3.2L17.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            <?php endif; ?>
        </span>
        <div>
            <h4 id="contact-status-title"><?php echo e($contactStatusTitle); ?></h4>
            <p><?php echo e($contactModalMessage !== '' ? $contactModalMessage : $contactStatusFallback); ?></p>
        </div>
        <div class="contact-status-actions">
            <?php if($contactModalType !== 'error' && $contactWhatsappUrl !== '#'): ?>
                <a class="contact-status-link" href="<?php echo e($contactWhatsappUrl); ?>" target="_blank" rel="noreferrer">Continuar por WhatsApp</a>
            <?php endif; ?>
            <button id="contact-status-close" type="button" class="contact-status-close"><?php echo e($contactModalType === 'error' ? 'Volver al formulario' : 'Entendido'); ?></button>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/partials/contact-premium.blade.php ENDPATH**/ ?>
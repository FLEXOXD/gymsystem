<div id="quote-request-backdrop" class="quote-modal-backdrop <?php echo e($quoteModalOpen ? 'is-open' : ''); ?>"></div>
<div id="quote-request-modal"
     class="quote-modal <?php echo e($quoteModalOpen ? 'is-open' : ''); ?>"
     role="dialog"
     aria-modal="true"
     aria-labelledby="quote-request-title">
    <button id="quote-request-close" type="button" class="quote-modal-close" aria-label="Cerrar formulario de cotización">
        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
            <path d="M5 5 15 15M15 5 5 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
        </svg>
    </button>

    <div class="quote-modal-shell">
        <section class="quote-modal-side">
            <div>
                <p class="quote-modal-kicker">Cotización personalizada</p>
                <h3 class="quote-modal-title">Recibe una propuesta alineada a la operación real de tu gimnasio.</h3>
                <p class="quote-modal-copy">
                    Analizamos equipo, sedes, recepción, cobros y metas de crecimiento para recomendarte el plan correcto y un siguiente paso claro.
                </p>
            </div>

            <div class="quote-modal-visual" aria-hidden="true">
                <div class="quote-visual-frame">
                    <div class="quote-visual-chip-row">
                        <span class="quote-visual-chip">Recepción</span>
                        <span class="quote-visual-chip">Caja</span>
                        <span class="quote-visual-chip">Membresías</span>
                        <span class="quote-visual-chip">Sucursales</span>
                    </div>

                    <div class="quote-visual-dashboard">
                        <article class="quote-visual-card is-accent">
                            <span class="quote-visual-card-label">Diagnóstico inicial</span>
                            <strong class="quote-visual-card-value">Propuesta clara, sin respuestas genéricas.</strong>
                            <p>Entendemos primero tu contexto para recomendarte el mejor siguiente paso.</p>
                        </article>
                        <article class="quote-visual-card">
                            <span class="quote-visual-card-label">Resultado esperado</span>
                            <strong class="quote-visual-card-value">Plan, alcance y prioridad comercial.</strong>
                            <p>Te guiamos según el tamaño de tu operación y el momento de compra.</p>
                        </article>
                    </div>

                    <ul class="quote-visual-list">
                        <li>Respuesta por correo o WhatsApp, según el canal más conveniente.</li>
                        <li>Recomendación basada en número de colaboradores, país y nivel operativo.</li>
                        <li>Seguimiento pensado para gimnasios, boxes y centros de entrenamiento.</li>
                    </ul>
                </div>

                <div class="quote-modal-stat-grid">
                    <article class="quote-modal-stat-card">
                        <span>Tiempo de respuesta</span>
                        <strong>Primer contacto en horario comercial y con seguimiento humano.</strong>
                    </article>
                    <article class="quote-modal-stat-card">
                        <span>Enfoque</span>
                        <strong>Operación diaria, cobros, membresías, accesos y crecimiento por sedes.</strong>
                    </article>
                </div>
            </div>
        </section>

        <section class="quote-modal-form-panel">
            <header class="quote-form-header">
                <p class="quote-modal-kicker">Solicitud comercial</p>
                <h3 id="quote-request-title">Cuéntanos cómo opera tu gimnasio hoy</h3>
                <p>Con esta información preparamos una propuesta más precisa sobre plan, alcance y acompañamiento inicial.</p>
            </header>

            <div class="quote-form-hint-grid" aria-label="Ventajas de la solicitud comercial">
                <article class="quote-form-hint">
                    <strong>Respuesta humana</strong>
                    <span>Revisamos cada solicitud manualmente.</span>
                </article>
                <article class="quote-form-hint">
                    <strong>Sin compromiso</strong>
                    <span>Puedes cotizar antes de decidir tu siguiente paso.</span>
                </article>
                <article class="quote-form-hint">
                    <strong>Propuesta útil</strong>
                    <span>Adaptada al tamaño real de tu operación.</span>
                </article>
            </div>

            <?php if($quoteModalMessage !== ''): ?>
                <div class="quote-form-alert <?php echo e($quoteModalType === 'error' ? 'is-error' : 'is-success'); ?>">
                    <strong><?php echo e($quoteModalType === 'error' ? 'Revisa la información antes de enviarla.' : 'Tu solicitud ya fue recibida.'); ?></strong>
                    <span><?php echo e($quoteModalMessage); ?></span>
                </div>
            <?php endif; ?>

            <div id="quote-plan-pill" class="quote-plan-pill <?php echo e($quoteSelectedPlanLabel !== '' ? 'is-visible' : ''); ?>">
                <span>Interés principal</span>
                <strong data-quote-plan-label><?php echo e($quoteSelectedPlanLabel !== '' ? $quoteSelectedPlanLabel : 'General'); ?></strong>
            </div>

            <form id="landing-quote-form" method="POST" action="<?php echo e(route('landing.quote.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="quote_requested_plan" value="<?php echo e(old('quote_requested_plan')); ?>" data-quote-plan-input>
                <input type="hidden" name="quote_source" value="<?php echo e(old('quote_source', 'landing_'.$pageMode)); ?>" data-quote-source-input>

                <div class="quote-form-grid">
                    <label class="quote-form-field">
                        <span class="quote-form-label">Nombre <em>*</em></span>
                        <input type="text"
                               class="contact-input <?php $__errorArgs = ['quote_first_name', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="quote_first_name"
                               value="<?php echo e(old('quote_first_name')); ?>"
                               placeholder="Ej: Andrea"
                               autocomplete="given-name"
                               required>
                        <?php $__errorArgs = ['quote_first_name', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="quote-form-field">
                        <span class="quote-form-label">Apellido <em>*</em></span>
                        <input type="text"
                               class="contact-input <?php $__errorArgs = ['quote_last_name', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="quote_last_name"
                               value="<?php echo e(old('quote_last_name')); ?>"
                               placeholder="Ej: Morales"
                               autocomplete="family-name"
                               required>
                        <?php $__errorArgs = ['quote_last_name', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="quote-form-field">
                        <span class="quote-form-label">Teléfono de contacto <em>*</em></span>
                        <span class="quote-form-inline">
                            <select class="contact-input quote-form-prefix <?php $__errorArgs = ['quote_phone_country_code', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="quote_phone_country_code"
                                    data-quote-prefix-select
                                    required>
                                <?php $__currentLoopData = $quotePhonePrefixes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prefix): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($prefix); ?>" <?php if(old('quote_phone_country_code', '+593') === $prefix): echo 'selected'; endif; ?>><?php echo e($prefix); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <input type="tel"
                                   class="contact-input <?php $__errorArgs = ['quote_phone_number', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   name="quote_phone_number"
                                   value="<?php echo e(old('quote_phone_number')); ?>"
                                   placeholder="099 123 4567"
                                   autocomplete="tel"
                                   required>
                        </span>
                        <span class="quote-form-help">Usaremos este número solo para responder a tu solicitud comercial.</span>
                        <?php $__errorArgs = ['quote_phone_country_code', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['quote_phone_number', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="quote-form-field">
                        <span class="quote-form-label">Correo de contacto <em>*</em></span>
                        <input type="email"
                               class="contact-input <?php $__errorArgs = ['quote_email', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="quote_email"
                               value="<?php echo e(old('quote_email')); ?>"
                               placeholder="gerencia@tugimnasio.com"
                               autocomplete="email"
                               required>
                        <?php $__errorArgs = ['quote_email', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="quote-form-field quote-form-field--full">
                        <span class="quote-form-label">País de operación <em>*</em></span>
                        <select class="contact-input <?php $__errorArgs = ['quote_country', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                name="quote_country"
                                data-quote-country-select
                                required>
                            <option value="">Selecciona tu país</option>
                            <?php $__currentLoopData = $quoteCountryPrefixes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country => $prefix): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($country); ?>"
                                        data-phone-prefix="<?php echo e($prefix ?? ''); ?>"
                                        <?php if(old('quote_country') === $country): echo 'selected'; endif; ?>><?php echo e($country); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['quote_country', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="quote-form-field quote-form-field--full">
                        <span class="quote-form-label">¿Cuántas personas forman parte de la operación? <em>*</em></span>
                        <input type="number"
                               class="contact-input <?php $__errorArgs = ['quote_professionals_count', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               name="quote_professionals_count"
                               value="<?php echo e(old('quote_professionals_count')); ?>"
                               min="1"
                               max="5000"
                               placeholder="Ej: 8 personas"
                               required>
                        <span class="quote-form-help">Incluye recepción, entrenadores, administración y personal operativo.</span>
                        <?php $__errorArgs = ['quote_professionals_count', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <label class="quote-form-field quote-form-field--full">
                        <span class="quote-form-label">¿Qué te gustaría ordenar primero?</span>
                        <textarea class="contact-input <?php $__errorArgs = ['quote_notes', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  name="quote_notes"
                                  rows="4"
                                  placeholder="Ej: Tenemos una sede, dos turnos en recepción y queremos ordenar membresías, caja y accesos."><?php echo e(old('quote_notes')); ?></textarea>
                        <?php $__errorArgs = ['quote_notes', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>

                    <div class="quote-form-checkbox quote-form-field--full">
                        <label>
                            <input type="checkbox" name="quote_privacy_accepted" value="1" <?php if(old('quote_privacy_accepted')): echo 'checked'; endif; ?>>
                            <span>Acepto el tratamiento de mis datos para recibir mi cotización y seguimiento comercial.</span>
                        </label>
                        <p class="quote-form-legal">Usaremos esta información solo para preparar tu propuesta y coordinar una respuesta comercial personalizada.</p>
                        <?php $__errorArgs = ['quote_privacy_accepted', 'landingQuote'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="quote-form-error"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <button type="submit" class="btn btn-demo quote-form-submit">Quiero mi propuesta personalizada</button>
            </form>
        </section>
    </div>
</div>
<?php /**PATH C:\laragon\www\gymsystem\resources\views/marketing/partials/quote-request-modal.blade.php ENDPATH**/ ?>
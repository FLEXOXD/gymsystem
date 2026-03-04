<?php if($legalAcceptanceRequired): ?>
    <section id="legal-acceptance-overlay" class="legal-accept-overlay" aria-modal="true" role="dialog" aria-labelledby="legal-accept-title">
        <div class="legal-accept-dialog">
            <header class="legal-accept-header">
                <h2 id="legal-accept-title">Aceptación de condiciones legales</h2>
                <p>Para continuar debes aceptar una sola vez las condiciones legales vigentes. Esta aceptación queda registrada como respaldo legal.</p>
                <p><strong>Versión vigente:</strong> <?php echo e($legalCurrentVersion); ?></p>
            </header>

            <div class="legal-accept-docs">
                <?php $__currentLoopData = $legalTermsDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="legal-accept-doc">
                        <h3><?php echo e($doc['label']); ?></h3>
                        <p><?php echo e($doc['summary']); ?></p>
                        <ul>
                            <?php $__currentLoopData = $doc['points']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $point): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($point); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <form id="legal-accept-form" method="POST" action="<?php echo e($legalAcceptancePostUrl); ?>" class="legal-accept-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="accepted" value="1">
                <input type="hidden" name="terms_version" value="<?php echo e($legalCurrentVersion); ?>">
                <input type="hidden" name="location_permission" id="legal-location-permission" value="skipped">
                <input type="hidden" name="latitude" id="legal-location-latitude" value="">
                <input type="hidden" name="longitude" id="legal-location-longitude" value="">
                <input type="hidden" name="location_accuracy_m" id="legal-location-accuracy" value="">

                <?php if($errors->has('accepted') || $errors->has('terms_version')): ?>
                    <div class="legal-accept-errors">
                        <?php if($errors->has('accepted')): ?>
                            <p><?php echo e($errors->first('accepted')); ?></p>
                        <?php endif; ?>
                        <?php if($errors->has('terms_version')): ?>
                            <p><?php echo e($errors->first('terms_version')); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <label class="legal-accept-check">
                    <input type="checkbox" id="legal-accept-checkbox" required>
                    <span>Confirmo que leí y acepto la Política de privacidad, Condiciones de servicio y Términos comerciales versión <?php echo e($legalCurrentVersion); ?>.</span>
                </label>

                <div class="legal-accept-actions">
                    <button id="legal-accept-submit" type="submit" class="ui-button ui-button-primary" disabled>Aceptar condiciones</button>
                </div>
            </form>
        </div>
    </section>
<?php endif; ?>

<?php /**PATH C:\laragon\www\gymsystem\resources\views/layouts/partials/panel/legal-acceptance-modal.blade.php ENDPATH**/ ?>
<?php $__env->startSection('title', 'Administrar página web'); ?>
<?php $__env->startSection('page-title', 'Administrar página web'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $sectionImageMap = [
            1 => ['file' => 'section_1_image_file', 'path' => 'section_1_image_path', 'url' => 'section_1_image_url', 'label' => 'Imagen sección 1'],
            2 => ['file' => 'section_2_image_file', 'path' => 'section_2_image_path', 'url' => 'section_2_image_url', 'label' => 'Imagen sección 2'],
            3 => ['file' => 'section_3_image_file', 'path' => 'section_3_image_path', 'url' => 'section_3_image_url', 'label' => 'Imagen sección 3'],
        ];
        $sectionModalImageMap = [
            1 => [
                'main_file' => 'section_1_modal_image_file',
                'main_path' => 'section_1_modal_image_path',
                'main_url' => 'section_1_modal_image_url',
                'secondary_file' => 'section_1_modal_secondary_image_file',
                'secondary_path' => 'section_1_modal_secondary_image_path',
                'secondary_url' => 'section_1_modal_secondary_image_url',
            ],
            2 => [
                'main_file' => 'section_2_modal_image_file',
                'main_path' => 'section_2_modal_image_path',
                'main_url' => 'section_2_modal_image_url',
                'secondary_file' => 'section_2_modal_secondary_image_file',
                'secondary_path' => 'section_2_modal_secondary_image_path',
                'secondary_url' => 'section_2_modal_secondary_image_url',
            ],
            3 => [
                'main_file' => 'section_3_modal_image_file',
                'main_path' => 'section_3_modal_image_path',
                'main_url' => 'section_3_modal_image_url',
                'secondary_file' => 'section_3_modal_secondary_image_file',
                'secondary_path' => 'section_3_modal_secondary_image_path',
                'secondary_url' => 'section_3_modal_secondary_image_url',
            ],
        ];
        $sectionCtaDefaults = [
            1 => route('demo.guide'),
            2 => route('demo'),
            3 => route('landing').'#faq-movil',
        ];

        $marqueeLogoMap = [
            1 => ['file' => 'marquee_item_1_logo_file', 'path' => 'marquee_item_1_logo_path', 'url' => 'marquee_item_1_logo_url'],
            2 => ['file' => 'marquee_item_2_logo_file', 'path' => 'marquee_item_2_logo_path', 'url' => 'marquee_item_2_logo_url'],
            3 => ['file' => 'marquee_item_3_logo_file', 'path' => 'marquee_item_3_logo_path', 'url' => 'marquee_item_3_logo_url'],
            4 => ['file' => 'marquee_item_4_logo_file', 'path' => 'marquee_item_4_logo_path', 'url' => 'marquee_item_4_logo_url'],
            5 => ['file' => 'marquee_item_5_logo_file', 'path' => 'marquee_item_5_logo_path', 'url' => 'marquee_item_5_logo_url'],
            6 => ['file' => 'marquee_item_6_logo_file', 'path' => 'marquee_item_6_logo_path', 'url' => 'marquee_item_6_logo_url'],
        ];

        $editorSections = [
            ['id' => 'editor-brand', 'label' => 'Marca'],
            ['id' => 'editor-whatsapp', 'label' => 'WhatsApp'],
            ['id' => 'editor-hero', 'label' => 'Hero'],
            ['id' => 'editor-marquee', 'label' => 'Banda en movimiento'],
            ['id' => 'editor-services', 'label' => 'Interfaces'],
            ['id' => 'editor-footer', 'label' => 'Footer'],
        ];

        $landingPreviewUrl = route('landing', ['preview_guest' => 1]);
        $heroSlidesConfigured = collect(range(1, 4))->filter(fn ($slide) => filled($content['hero_slide_'.$slide.'_url'] ?? null))->count();
        $serviceSectionsConfigured = collect([1, 2, 3])->filter(fn ($section) => filled($content['section_'.$section.'_title'] ?? null) || filled($content['section_'.$section.'_text'] ?? null))->count();
        $serviceImagesConfigured = collect([1, 2, 3])->filter(fn ($section) => filled($content[$sectionImageMap[$section]['url']] ?? null))->count();
        $marqueeLogosConfigured = collect(range(1, 6))->filter(fn ($item) => filled($content[$marqueeLogoMap[$item]['url']] ?? null))->count();
        $brandReady = filled($content['brand_name'] ?? null) && filled($content['demo_button_label'] ?? null);
        $heroReady = filled($content['hero_title'] ?? null) && filled($content['hero_subtitle'] ?? null);
        $footerReady = filled($content['footer_text'] ?? null) && filled($content['footer_contact_email'] ?? null);
        $whatsappReady = filled($content['whatsapp_phone'] ?? null) && filled($content['whatsapp_message'] ?? null);
        $editorReadiness = collect([$brandReady, $heroReady, $footerReady, $whatsappReady])->filter()->count();
    ?>

    <div class="sa-shell">
        <section class="sa-hero">
            <div class="sa-hero-grid">
                <div>
                    <span class="sa-kicker">Editor comercial</span>
                    <h2 class="sa-title">Gestiona la landing como un sistema editorial, no como un formulario largo.</h2>
                    <p class="sa-subtitle">
                        Reorganicé la experiencia para que primero entiendas estado, cobertura y checklist de publicación.
                        El editor sigue siendo el mismo en funcionalidad, pero ahora trabaja con mejor contexto, mejor navegación y acceso más directo al preview.
                    </p>
                    <div class="sa-actions">
                        <button type="button" data-open-live-preview aria-haspopup="dialog" aria-controls="live-preview-modal" class="ui-button ui-button-primary">Abrir vista previa</button>
                        <a href="<?php echo e($landingPreviewUrl); ?>" target="_blank" rel="noreferrer" class="ui-button ui-button-ghost">Abrir landing pública</a>
                        <span class="sa-pill is-info">Edición en vivo con preview</span>
                    </div>
                </div>

                <div class="sa-note-card">
                    <p class="sa-note-label">Flujo editorial recomendado</p>
                    <div class="sa-note-list">
                        <div class="sa-note-item">
                            <strong>1. Ajusta narrativa</strong>
                            <span>Marca, hero y servicios deben contar la propuesta antes de tocar detalles cosméticos.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>2. Válida conversión</strong>
                            <span>WhatsApp, CTA y botones de planes deben quedar listos antes de publicar cambios.</span>
                        </div>
                        <div class="sa-note-item">
                            <strong>3. Revisa en preview</strong>
                            <span>Comprueba desktop, tablet y mobile desde la misma vista antes de guardar.</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sa-stat-grid">
            <article class="sa-stat-card <?php echo e($editorReadiness >= 3 ? 'is-success' : 'is-warning'); ?>">
                <p class="sa-stat-label">Base editorial</p>
                <p class="sa-stat-value"><?php echo e($editorReadiness); ?>/4</p>
                <p class="sa-stat-meta">Marca, WhatsApp, hero y footer con mínimos listos para publicar.</p>
            </article>
            <article class="sa-stat-card is-info">
                <p class="sa-stat-label">Slides hero</p>
                <p class="sa-stat-value"><?php echo e($heroSlidesConfigured); ?>/4</p>
                <p class="sa-stat-meta">Cobertura visual del bloque principal de captación.</p>
            </article>
            <article class="sa-stat-card is-neutral">
                <p class="sa-stat-label">Servicios documentados</p>
                <p class="sa-stat-value"><?php echo e($serviceSectionsConfigured); ?>/3</p>
                <p class="sa-stat-meta"><?php echo e($serviceImagesConfigured); ?>/3 secciones ya tienen imagen cargada.</p>
            </article>
            <article class="sa-stat-card is-warning">
                <p class="sa-stat-label">Logos de banda</p>
                <p class="sa-stat-value"><?php echo e($marqueeLogosConfigured); ?>/6</p>
                <p class="sa-stat-meta">Prueba visual y soporte comercial para la franja de credibilidad.</p>
            </article>
        </section>

        <?php if($errors->any()): ?>
            <div class="ui-alert ui-alert-danger" role="alert" aria-labelledby="site-content-errors-title">
                <p id="site-content-errors-title" class="font-semibold">Hay errores en el contenido web.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="grid items-start gap-6 2xl:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.65fr)]">
        <div class="space-y-4">
            <?php if (isset($component)) { $__componentOriginaldae4cd48acb67888a4631e1ba48f2f93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldae4cd48acb67888a4631e1ba48f2f93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.card','data' => ['title' => 'Contenido comercial','subtitle' => 'Editor visual de landing para editar por secciones y ver la vista previa al instante.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Contenido comercial','subtitle' => 'Editor visual de landing para editar por secciones y ver la vista previa al instante.']); ?>
                <form id="site-content-form" method="POST" action="<?php echo e(route('superadmin.web-page.update')); ?>" enctype="multipart/form-data" class="site-content-editor grid gap-4 text-slate-800 lg:grid-cols-2" aria-describedby="site-content-form-help">
                    <?php echo csrf_field(); ?>

                    <p id="site-content-form-help" class="sr-only">
                        Editor de contenido comercial con navegación rápida, vista previa en vivo y bloques para marca, WhatsApp, hero, banda, servicios y footer.
                    </p>

                    <div class="sa-toolbar lg:col-span-2 space-y-3">
                        <div class="flex flex-col gap-3 xl:flex-row xl:items-end xl:justify-between">
                            <div>
                                <p class="sa-section-title">Panel de edición</p>
                                <p class="sa-section-copy">Muévete por bloques, monitorea cambios sin guardar y abre la vista previa sin salir del flujo.</p>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <button type="button" data-open-live-preview aria-haspopup="dialog" aria-controls="live-preview-modal" class="ui-button ui-button-secondary">Vista previa</button>
                                <a href="<?php echo e($landingPreviewUrl); ?>" target="_blank" rel="noreferrer" class="ui-button ui-button-ghost">Abrir landing</a>
                                <span id="editor-change-indicator"
                                      role="status"
                                      aria-live="polite"
                                      class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                    Sin cambios
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <?php $__currentLoopData = $editorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $editorSection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button type="button"
                                        data-scroll-target="#<?php echo e($editorSection['id']); ?>"
                                        aria-label="Ir a la sección <?php echo e($editorSection['label']); ?>"
                                        class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:border-slate-400 hover:text-slate-900">
                                    <?php echo e($editorSection['label']); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    <section id="editor-brand" tabindex="-1" class="editor-block lg:col-span-2 grid gap-4 lg:grid-cols-2 rounded-xl border border-slate-300/70 bg-slate-50/60 p-3">
                        <div class="lg:col-span-2">
                            <h3 class="sa-section-title">Marca y header</h3>
                            <p class="sa-section-copy">Define cómo se presenta la marca, qué CTA ve el usuario y qué logo se replica en la navegación.</p>
                        </div>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Texto pequeño marca
                            <input type="text" name="brand_kicker" data-preview-field="brand_kicker" class="ui-input" value="<?php echo e(old('brand_kicker', $content['brand_kicker'] ?? '')); ?>">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Nombre marca
                            <input type="text" name="brand_name" data-preview-field="brand_name" class="ui-input" value="<?php echo e(old('brand_name', $content['brand_name'] ?? '')); ?>">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Texto botón login
                            <input type="text" name="login_button_label" data-preview-field="login_button_label" class="ui-input" value="<?php echo e(old('login_button_label', $content['login_button_label'] ?? '')); ?>">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Texto botón demo
                            <input type="text" name="demo_button_label" data-preview-field="demo_button_label" class="ui-input" value="<?php echo e(old('demo_button_label', $content['demo_button_label'] ?? '')); ?>" required>
                        </label>

                        <div class="space-y-2 lg:col-span-2 rounded-xl border border-slate-300 bg-white p-3">
                            <p class="text-xs font-bold uppercase tracking-wide">Logo principal</p>
                            <?php if(!empty($content['brand_logo_url'])): ?>
                                <img src="<?php echo e($content['brand_logo_url']); ?>" alt="Logo principal" class="h-20 w-20 rounded-lg border border-slate-300 object-contain">
                            <?php endif; ?>
                            <input type="file" name="brand_logo_file" data-preview-image="brand_logo_file" accept=".jpg,.jpeg,.png,.webp,.svg" class="ui-input">
                            <label class="inline-flex items-center gap-2 text-xs font-medium">
                                <input type="checkbox" name="remove_brand_logo_path" value="1">
                                Quitar logo actual
                            </label>
                        </div>
                    </section>

                    <section id="editor-whatsapp" tabindex="-1" class="editor-block lg:col-span-2 grid gap-4 lg:grid-cols-2 rounded-xl border border-slate-300/70 bg-slate-50/60 p-3">
                        <div class="lg:col-span-2">
                            <h3 class="sa-section-title">WhatsApp automático</h3>
                            <p class="sa-section-copy">Asegura la salida de conversión principal: teléfono válido, mensaje base y rutas por plan.</p>
                        </div>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Teléfono WhatsApp
                            <input type="text" id="wa-phone" name="whatsapp_phone" class="ui-input" value="<?php echo e(old('whatsapp_phone', $content['whatsapp_phone'] ?? '')); ?>" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Mensaje base del usuario
                            <textarea id="wa-message" name="whatsapp_message" class="ui-input min-h-[96px]" required><?php echo e(old('whatsapp_message', $content['whatsapp_message'] ?? '')); ?></textarea>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                            URL WhatsApp generada automáticamente
                            <input type="text" id="wa-preview" class="ui-input" value="<?php echo e($content['whatsapp_url'] ?? ''); ?>" readonly>
                        </label>

                        <div class="lg:col-span-2 rounded-xl border border-slate-300 bg-white p-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-600">Mensajes por plan (botones de precios)</p>
                            <p class="mt-1 text-xs text-slate-500">Cada plan enviara su propio texto a WhatsApp.</p>

                            <div class="mt-3 grid gap-3 lg:grid-cols-2">
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Mensaje plan básico
                                    <textarea id="wa-plan-basico-message" name="whatsapp_message_plan_basico" class="ui-input min-h-[88px]"><?php echo e(old('whatsapp_message_plan_basico', $content['whatsapp_message_plan_basico'] ?? '')); ?></textarea>
                                </label>
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Mensaje plan profesional
                                    <textarea id="wa-plan-profesional-message" name="whatsapp_message_plan_profesional" class="ui-input min-h-[88px]"><?php echo e(old('whatsapp_message_plan_profesional', $content['whatsapp_message_plan_profesional'] ?? '')); ?></textarea>
                                </label>
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Mensaje plan premium
                                    <textarea id="wa-plan-premium-message" name="whatsapp_message_plan_premium" class="ui-input min-h-[88px]"><?php echo e(old('whatsapp_message_plan_premium', $content['whatsapp_message_plan_premium'] ?? '')); ?></textarea>
                                </label>
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    Mensaje plan sucursales
                                    <textarea id="wa-plan-sucursales-message" name="whatsapp_message_plan_sucursales" class="ui-input min-h-[88px]"><?php echo e(old('whatsapp_message_plan_sucursales', $content['whatsapp_message_plan_sucursales'] ?? '')); ?></textarea>
                                </label>
                            </div>

                            <div class="mt-3 grid gap-3 lg:grid-cols-2">
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    URL plan básico
                                    <input type="text" id="wa-plan-basico-preview" class="ui-input" value="<?php echo e($content['whatsapp_url_plan_basico'] ?? ''); ?>" readonly>
                                </label>
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    URL plan profesional
                                    <input type="text" id="wa-plan-profesional-preview" class="ui-input" value="<?php echo e($content['whatsapp_url_plan_profesional'] ?? ''); ?>" readonly>
                                </label>
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    URL plan premium
                                    <input type="text" id="wa-plan-premium-preview" class="ui-input" value="<?php echo e($content['whatsapp_url_plan_premium'] ?? ''); ?>" readonly>
                                </label>
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                    URL plan sucursales
                                    <input type="text" id="wa-plan-sucursales-preview" class="ui-input" value="<?php echo e($content['whatsapp_url_plan_sucursales'] ?? ''); ?>" readonly>
                                </label>
                            </div>
                        </div>
                    </section>
                    <section id="editor-hero" tabindex="-1" class="editor-block lg:col-span-2 grid gap-4 lg:grid-cols-2 rounded-xl border border-slate-300/70 bg-slate-50/60 p-3">
                        <div class="lg:col-span-2">
                            <h3 class="sa-section-title">Hero principal</h3>
                            <p class="sa-section-copy">Aqué se juega la primera impresión: narrativa, promesa y soporte visual del carrusel inicial.</p>
                        </div>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                            Hero kicker
                            <input type="text" name="hero_kicker" data-preview-field="hero_kicker" class="ui-input" value="<?php echo e(old('hero_kicker', $content['hero_kicker'] ?? '')); ?>" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                            Hero título
                            <input type="text" name="hero_title" data-preview-field="hero_title" class="ui-input" value="<?php echo e(old('hero_title', $content['hero_title'] ?? '')); ?>" required>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                            Hero subtítulo
                            <textarea name="hero_subtitle" data-preview-field="hero_subtitle" class="ui-input min-h-[108px]" required><?php echo e(old('hero_subtitle', $content['hero_subtitle'] ?? '')); ?></textarea>
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Etiqueta sobre carrusel
                            <input type="text" name="hero_media_tag" data-preview-field="hero_media_tag" class="ui-input" value="<?php echo e(old('hero_media_tag', $content['hero_media_tag'] ?? '')); ?>">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                            Texto inferior carrusel
                            <input type="text" name="hero_media_note" data-preview-field="hero_media_note" class="ui-input" value="<?php echo e(old('hero_media_note', $content['hero_media_note'] ?? '')); ?>">
                        </label>

                        <div class="space-y-2 lg:col-span-2 rounded-xl border border-slate-300 bg-white p-3">
                            <p class="text-xs font-bold uppercase tracking-wide">Carrusel hero (4 imágenes)</p>
                            <div class="grid gap-3 lg:grid-cols-4">
                                <?php for($slide = 1; $slide <= 4; $slide++): ?>
                                    <div class="space-y-2 rounded-lg border border-slate-300/70 bg-slate-50/60 p-2">
                                        <p class="text-[11px] font-bold uppercase tracking-wide">Slide <?php echo e($slide); ?></p>
                                        <?php if(!empty($content['hero_slide_'.$slide.'_url'] ?? '')): ?>
                                            <img src="<?php echo e($content['hero_slide_'.$slide.'_url']); ?>" alt="Slide <?php echo e($slide); ?>" class="h-24 w-full rounded-md border border-slate-300 object-cover">
                                        <?php endif; ?>
                                        <input type="file"
                                               name="hero_slide_<?php echo e($slide); ?>_file"
                                               data-preview-image="hero_slide_<?php echo e($slide); ?>_file"
                                               accept=".jpg,.jpeg,.png,.webp,.svg"
                                               class="ui-input">
                                        <label class="inline-flex items-center gap-2 text-xs font-medium">
                                            <input type="checkbox" name="remove_hero_slide_<?php echo e($slide); ?>_path" value="1">
                                            Quitar slide <?php echo e($slide); ?>

                                        </label>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </section>

                    <section id="editor-marquee" tabindex="-1" class="editor-block lg:col-span-2 grid gap-4 rounded-xl border border-slate-300/70 bg-slate-50/60 p-3">
                        <div>
                            <h3 class="sa-section-title">Banda en movimiento</h3>
                            <p class="sa-section-copy">Usa este bloque para reforzar prueba visual, alianzas o mensajes breves de confianza.</p>
                        </div>

                        <?php $__currentLoopData = [1, 2, 3, 4, 5, 6]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="grid gap-3 rounded-xl border border-slate-300 bg-white p-3 lg:grid-cols-2">
                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                    Item <?php echo e($item); ?> texto
                                    <input type="text" name="marquee_item_<?php echo e($item); ?>_text" data-preview-field="marquee_item_<?php echo e($item); ?>_text" class="ui-input" value="<?php echo e(old('marquee_item_'.$item.'_text', $content['marquee_item_'.$item.'_text'] ?? '')); ?>">
                                </label>

                                <div class="space-y-2 lg:col-span-2 rounded-xl border border-slate-300/70 bg-slate-50/70 p-3">
                                    <p class="text-xs font-bold uppercase tracking-wide">Item <?php echo e($item); ?> logo (opcional)</p>
                                    <?php if(!empty($content[$marqueeLogoMap[$item]['url']] ?? '')): ?>
                                        <img src="<?php echo e($content[$marqueeLogoMap[$item]['url']]); ?>" alt="Logo item <?php echo e($item); ?>" class="h-16 w-44 rounded-lg border border-slate-300 bg-slate-950 object-contain">
                                    <?php endif; ?>
                                    <input type="file" name="<?php echo e($marqueeLogoMap[$item]['file']); ?>" data-preview-image="<?php echo e($marqueeLogoMap[$item]['file']); ?>" accept=".jpg,.jpeg,.png,.webp,.svg" class="ui-input">
                                    <label class="inline-flex items-center gap-2 text-xs font-medium">
                                        <input type="checkbox" name="remove_<?php echo e($marqueeLogoMap[$item]['path']); ?>" value="1">
                                        Quitar logo item <?php echo e($item); ?>

                                    </label>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </section>

                    <section id="editor-services" tabindex="-1" class="editor-block lg:col-span-2 grid gap-4 rounded-xl border border-slate-300/70 bg-slate-50/60 p-3">
                        <div>
                            <h3 class="sa-section-title">Interfaces y modales</h3>
                            <p class="text-xs font-medium text-slate-500">Aqui puedes cambiar texto, destinos, contenido del modal e imagenes sin tocar codigo.</p>
                            <p class="sa-section-copy">Organiza beneficios en bloques legibles. Cada sección debe explicar una ventaja concreta y visual.</p>
                        </div>

                        <?php $__currentLoopData = [1, 2, 3]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <details class="rounded-xl border border-slate-300 bg-white p-3" <?php echo e($section === 1 ? 'open' : ''); ?>>
                                <summary class="cursor-pointer text-sm font-bold text-slate-800">Sección <?php echo e($section); ?></summary>

                                <div class="mt-3 grid gap-4 lg:grid-cols-2">
                                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                        Sección <?php echo e($section); ?> título
                                        <input type="text" name="section_<?php echo e($section); ?>_title" data-preview-field="section_<?php echo e($section); ?>_title" class="ui-input" value="<?php echo e(old('section_'.$section.'_title', $content['section_'.$section.'_title'] ?? '')); ?>">
                                    </label>

                                    <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                        Sección <?php echo e($section); ?> texto
                                        <textarea name="section_<?php echo e($section); ?>_text" data-preview-field="section_<?php echo e($section); ?>_text" class="ui-input min-h-[88px]"><?php echo e(old('section_'.$section.'_text', $content['section_'.$section.'_text'] ?? '')); ?></textarea>
                                    </label>

                                    <?php for($item = 1; $item <= 3; $item++): ?>
                                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                            Sección <?php echo e($section); ?> item <?php echo e($item); ?>

                                            <input type="text" name="section_<?php echo e($section); ?>_item_<?php echo e($item); ?>" data-preview-field="section_<?php echo e($section); ?>_item_<?php echo e($item); ?>" class="ui-input" value="<?php echo e(old('section_'.$section.'_item_'.$item, $content['section_'.$section.'_item_'.$item] ?? '')); ?>">
                                        </label>
                                    <?php endfor; ?>

                                    <div class="lg:col-span-2 rounded-xl border border-slate-300/70 bg-slate-50/70 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide">Botones de la interfaz</p>
                                        <div class="mt-3 grid gap-4 lg:grid-cols-2">
                                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                                Texto botÃ³n principal
                                                <input type="text" name="section_<?php echo e($section); ?>_cta_label" class="ui-input" value="<?php echo e(old('section_'.$section.'_cta_label', $content['section_'.$section.'_cta_label'] ?? '')); ?>">
                                            </label>
                                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide">
                                                Texto botÃ³n modal
                                                <input type="text" name="section_<?php echo e($section); ?>_modal_button_label" class="ui-input" value="<?php echo e(old('section_'.$section.'_modal_button_label', $content['section_'.$section.'_modal_button_label'] ?? '')); ?>">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="lg:col-span-2 rounded-xl border border-slate-300/70 bg-slate-50/70 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide">Destino del boton principal</p>
                                        <label class="mt-3 block space-y-1 text-xs font-bold uppercase tracking-wide">
                                            URL boton principal
                                            <input type="text" name="section_<?php echo e($section); ?>_cta_url" class="ui-input" value="<?php echo e(old('section_'.$section.'_cta_url', $content['section_'.$section.'_cta_url'] ?? '')); ?>" placeholder="<?php echo e($sectionCtaDefaults[$section]); ?>">
                                            <span class="text-[11px] font-medium normal-case tracking-normal text-slate-500">
                                                Si lo dejas vacio, usara por defecto: <?php echo e($sectionCtaDefaults[$section]); ?>

                                            </span>
                                        </label>
                                    </div>

                                    <div class="lg:col-span-2 rounded-xl border border-slate-300/70 bg-slate-50/70 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide">Contenido del modal explicativo</p>
                                        <div class="mt-3 grid gap-4 lg:grid-cols-2">
                                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                                Etiqueta pequeÃ±a modal
                                                <input type="text" name="section_<?php echo e($section); ?>_modal_badge" class="ui-input" value="<?php echo e(old('section_'.$section.'_modal_badge', $content['section_'.$section.'_modal_badge'] ?? '')); ?>">
                                            </label>
                                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                                TÃ­tulo modal
                                                <input type="text" name="section_<?php echo e($section); ?>_modal_title" class="ui-input" value="<?php echo e(old('section_'.$section.'_modal_title', $content['section_'.$section.'_modal_title'] ?? '')); ?>">
                                            </label>
                                            <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                                DescripciÃ³n modal
                                                <textarea name="section_<?php echo e($section); ?>_modal_text" class="ui-input min-h-[108px]"><?php echo e(old('section_'.$section.'_modal_text', $content['section_'.$section.'_modal_text'] ?? '')); ?></textarea>
                                            </label>
                                            <?php for($modalItem = 1; $modalItem <= 3; $modalItem++): ?>
                                                <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                                                    Modal item <?php echo e($modalItem); ?>

                                                    <input type="text" name="section_<?php echo e($section); ?>_modal_item_<?php echo e($modalItem); ?>" class="ui-input" value="<?php echo e(old('section_'.$section.'_modal_item_'.$modalItem, $content['section_'.$section.'_modal_item_'.$modalItem] ?? '')); ?>">
                                                </label>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <div class="space-y-2 lg:col-span-2 rounded-xl border border-slate-300/70 bg-slate-50/70 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide"><?php echo e($sectionImageMap[$section]['label']); ?></p>
                                        <?php if(!empty($content[$sectionImageMap[$section]['url']] ?? '')): ?>
                                            <img src="<?php echo e($content[$sectionImageMap[$section]['url']]); ?>" alt="<?php echo e($sectionImageMap[$section]['label']); ?>" class="h-40 w-full rounded-lg border border-slate-300 object-cover">
                                        <?php endif; ?>
                                        <input type="file" name="<?php echo e($sectionImageMap[$section]['file']); ?>" data-preview-image="<?php echo e($sectionImageMap[$section]['file']); ?>" accept=".jpg,.jpeg,.png,.webp,.svg" class="ui-input">
                                        <label class="inline-flex items-center gap-2 text-xs font-medium">
                                            <input type="checkbox" name="remove_<?php echo e($sectionImageMap[$section]['path']); ?>" value="1">
                                            Quitar imagen actual
                                        </label>
                                    </div>

                                    <div class="space-y-2 rounded-xl border border-slate-300/70 bg-slate-50/70 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide">Imagen principal modal</p>
                                        <?php if(!empty($content[$sectionModalImageMap[$section]['main_url']] ?? '')): ?>
                                            <img src="<?php echo e($content[$sectionModalImageMap[$section]['main_url']]); ?>" alt="Imagen principal modal <?php echo e($section); ?>" class="h-40 w-full rounded-lg border border-slate-300 object-cover">
                                        <?php endif; ?>
                                        <input type="file" name="<?php echo e($sectionModalImageMap[$section]['main_file']); ?>" accept=".jpg,.jpeg,.png,.webp,.svg" class="ui-input">
                                        <label class="inline-flex items-center gap-2 text-xs font-medium">
                                            <input type="checkbox" name="remove_<?php echo e($sectionModalImageMap[$section]['main_path']); ?>" value="1">
                                            Quitar imagen principal
                                        </label>
                                    </div>

                                    <div class="space-y-2 rounded-xl border border-slate-300/70 bg-slate-50/70 p-3">
                                        <p class="text-xs font-bold uppercase tracking-wide">Imagen secundaria modal</p>
                                        <?php if(!empty($content[$sectionModalImageMap[$section]['secondary_url']] ?? '')): ?>
                                            <img src="<?php echo e($content[$sectionModalImageMap[$section]['secondary_url']]); ?>" alt="Imagen secundaria modal <?php echo e($section); ?>" class="h-40 w-full rounded-lg border border-slate-300 object-cover">
                                        <?php endif; ?>
                                        <input type="file" name="<?php echo e($sectionModalImageMap[$section]['secondary_file']); ?>" accept=".jpg,.jpeg,.png,.webp,.svg" class="ui-input">
                                        <label class="inline-flex items-center gap-2 text-xs font-medium">
                                            <input type="checkbox" name="remove_<?php echo e($sectionModalImageMap[$section]['secondary_path']); ?>" value="1">
                                            Quitar imagen secundaria
                                        </label>
                                    </div>
                                </div>
                            </details>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </section>
                    <section id="editor-footer" tabindex="-1" class="editor-block lg:col-span-2 grid gap-4 lg:grid-cols-2 rounded-xl border border-slate-300/70 bg-slate-50/60 p-3">
                        <div class="lg:col-span-2">
                            <h3 class="sa-section-title">Footer</h3>
                            <p class="sa-section-copy">Cierra con contacto claro y texto legal consistente. Debe resolver dudas básicas y dar salida de soporte.</p>
                        </div>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                            Footer (usa {year} para el año actual)
                            <input type="text" name="footer_text" data-preview-field="footer_text" class="ui-input" value="<?php echo e(old('footer_text', $content['footer_text'] ?? '')); ?>">
                        </label>

                        <label class="space-y-1 text-xs font-bold uppercase tracking-wide lg:col-span-2">
                            Correo de contacto footer
                            <input type="email" name="footer_contact_email" data-preview-field="footer_contact_email" class="ui-input" value="<?php echo e(old('footer_contact_email', $content['footer_contact_email'] ?? 'soporte@flexgym.local')); ?>">
                        </label>
                    </section>

                    <div class="lg:col-span-2 flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50/80 p-4 dark:border-amber-500/40 dark:bg-amber-900/20 lg:flex-row lg:items-center lg:justify-between">
                        <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">
                            Antes de guardar, revisa preview y CTA de WhatsApp. Son las dos superficies con más impacto comercial inmediato.
                        </p>
                        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>Guardar contenido web <?php echo $__env->renderComponent(); ?>
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
        </div>

        <aside class="space-y-4 2xl:sticky 2xl:top-6">
            <div class="ui-card space-y-4">
                <div>
                    <h3 class="sa-section-title">Checklist de publicación</h3>
                    <p class="sa-section-copy">Estado rápido de los bloques que más afectan percepción y conversión.</p>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/50">
                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Marca y CTA</span>
                        <span class="sa-pill <?php echo e($brandReady ? 'is-success' : 'is-warning'); ?>"><?php echo e($brandReady ? 'Listo' : 'Revisar'); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/50">
                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Hero principal</span>
                        <span class="sa-pill <?php echo e($heroReady ? 'is-success' : 'is-warning'); ?>"><?php echo e($heroReady ? 'Listo' : 'Revisar'); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/50">
                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">WhatsApp</span>
                        <span class="sa-pill <?php echo e($whatsappReady ? 'is-success' : 'is-warning'); ?>"><?php echo e($whatsappReady ? 'Listo' : 'Revisar'); ?></span>
                    </div>
                    <div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/50">
                        <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">Footer</span>
                        <span class="sa-pill <?php echo e($footerReady ? 'is-success' : 'is-warning'); ?>"><?php echo e($footerReady ? 'Listo' : 'Revisar'); ?></span>
                    </div>
                </div>
            </div>

            <div class="ui-card space-y-4">
                <div>
                    <h3 class="sa-section-title">Acciones rápidas</h3>
                    <p class="sa-section-copy">Atajos para validar contenido sin perder contexto de edición.</p>
                </div>
                <div class="flex flex-col gap-2">
                    <button type="button" data-open-live-preview aria-haspopup="dialog" aria-controls="live-preview-modal" class="ui-button ui-button-secondary w-full justify-center">Abrir vista previa</button>
                    <a href="<?php echo e($landingPreviewUrl); ?>" target="_blank" rel="noreferrer" class="ui-button ui-button-ghost w-full justify-center">Abrir landing pública</a>
                </div>
                <div class="space-y-2">
                    <?php $__currentLoopData = $editorSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $editorSection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button"
                                data-scroll-target="#<?php echo e($editorSection['id']); ?>"
                                aria-label="Ir a la sección <?php echo e($editorSection['label']); ?>"
                                class="ui-button ui-button-ghost w-full justify-start">
                            <?php echo e($editorSection['label']); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            <div class="ui-card space-y-4">
                <div>
                    <h3 class="sa-section-title">Criterio editorial</h3>
                    <p class="sa-section-copy">Buenas prácticas aplicadas para que la página principal convierta mejor y se mantenga coherente.</p>
                </div>
                <ul class="sa-check-list">
                    <li>La propuesta principal debe entenderse sin depender del carrusel.</li>
                    <li>Los botones de planes deben abrir WhatsApp con intención clara y específica.</li>
                    <li>Servicios y banda visual deben reforzar credibilidad, no repetir el mismo mensaje.</li>
                    <li>Footer y correo de soporte deben resolver confianza operativa y contacto.</li>
                </ul>
            </div>
        </aside>
    </div>

    <style>
        .theme-dark .site-content-editor {
            color: #e2e8f0;
        }
        .theme-dark .site-content-editor > .rounded-xl,
        .theme-dark .site-content-editor .editor-block {
            border-color: rgba(71, 85, 105, 0.85) !important;
            background: rgba(15, 23, 42, 0.72) !important;
        }
        .theme-dark .site-content-editor [class*="bg-slate-50"],
        .theme-dark .site-content-editor [class*="bg-slate-50\\/"] {
            background: rgba(15, 23, 42, 0.72) !important;
        }
        .theme-dark .site-content-editor [class*="border-slate-300"] {
            border-color: rgba(71, 85, 105, 0.85) !important;
        }
        .theme-dark .site-content-editor .bg-white,
        .theme-dark .site-content-editor details {
            background: rgba(15, 23, 42, 0.9) !important;
        }
        .theme-dark .site-content-editor .text-slate-800,
        .theme-dark .site-content-editor .text-slate-700 {
            color: #e2e8f0 !important;
        }
        .theme-dark .site-content-editor .text-slate-600,
        .theme-dark .site-content-editor .text-slate-500 {
            color: #94a3b8 !important;
        }
        .theme-dark .site-content-editor [data-scroll-target] {
            background: rgba(15, 23, 42, 0.9) !important;
            border-color: rgba(71, 85, 105, 0.85) !important;
            color: #e2e8f0 !important;
        }
        .theme-dark .site-content-editor [data-scroll-target]:hover {
            border-color: rgba(34, 211, 238, 0.65) !important;
            color: #a5f3fc !important;
        }
        .theme-dark .site-content-editor .ui-input {
            background: #020617;
            border-color: #334155;
            color: #f8fafc;
        }
        .theme-dark .site-content-editor .ui-input::placeholder {
            color: #94a3b8;
        }
        .theme-dark .site-content-editor details summary {
            color: #f1f5f9;
        }

        .web-preview-fab {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 9999;
            border: 1px solid #0f172a;
            border-radius: 999px;
            background: #0f172a;
            color: #ffffff;
            padding: .7rem 1rem;
            font-size: .85rem;
            font-weight: 700;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .4);
            cursor: pointer;
        }
        .web-preview-fab:hover { background: #111f38; }

        .web-preview-modal {
            position: fixed;
            inset: 0;
            z-index: 10000;
            display: none;
        }
        .web-preview-modal.is-open { display: block; }
        .web-preview-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(2, 6, 23, .72);
            backdrop-filter: blur(3px);
        }
        .web-preview-dialog {
            position: relative;
            width: min(1420px, calc(100% - 1rem));
            height: calc(100vh - 1rem);
            margin: .5rem auto;
            border: 1px solid #cbd5e1;
            border-radius: 1rem;
            background: #ffffff;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(15, 23, 42, .35);
        }
        .web-preview-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            padding: .7rem .9rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .web-preview-title strong { display: block; font-size: .9rem; color: #0f172a; }
        .web-preview-title span { display: block; font-size: .74rem; color: #64748b; }
        .web-preview-actions { display: inline-flex; align-items: center; gap: .4rem; flex-wrap: wrap; justify-content: flex-end; }
        .web-preview-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: .55rem;
            background: #fff;
            color: #334155;
            padding: .35rem .65rem;
            font-size: .76rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }
        .web-preview-btn.is-active {
            background: #e2e8f0;
            border-color: #94a3b8;
            color: #0f172a;
        }
        .web-preview-body {
            height: calc(100% - 55px);
            padding: .45rem;
            background: #f1f5f9;
            overflow: auto;
        }
        .web-preview-viewport {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            height: 100%;
            border-radius: .6rem;
            overflow: hidden;
        }
        .web-preview-frame {
            width: 100%;
            height: 100%;
            border: 1px solid #cbd5e1;
            border-radius: .6rem;
            background: #fff;
        }
    </style>

    <button id="live-preview-open" type="button" data-open-live-preview aria-haspopup="dialog" aria-controls="live-preview-modal" class="web-preview-fab">Vista previa en vivo</button>

    <div id="live-preview-modal" class="web-preview-modal" aria-hidden="true">
        <div id="live-preview-backdrop" class="web-preview-backdrop"></div>
        <div class="web-preview-dialog" role="dialog" aria-modal="true" aria-labelledby="live-preview-title" aria-describedby="live-preview-description" tabindex="-1">
            <div class="web-preview-head">
                <div class="web-preview-title">
                    <strong id="live-preview-title">Vista previa en vivo</strong>
                    <span id="live-preview-description">Landing pública en vivo mientras editas</span>
                </div>
                <div class="web-preview-actions">
                    <button type="button" data-preview-device="desktop" class="web-preview-btn is-active">Desktop</button>
                    <button type="button" data-preview-device="tablet" class="web-preview-btn">Tablet</button>
                    <button type="button" data-preview-device="mobile" class="web-preview-btn">Mobile</button>
                    <button type="button" id="preview-modal-reload" class="web-preview-btn">Recargar</button>
                    <a href="<?php echo e($landingPreviewUrl); ?>" target="_blank" rel="noreferrer" class="web-preview-btn web-preview-link">Abrir landing</a>
                    <button type="button" id="live-preview-close" class="web-preview-btn">Cerrar</button>
                </div>
            </div>
            <div class="web-preview-body">
                <div id="preview-modal-viewport" class="web-preview-viewport">
                    <iframe id="landing-preview-modal"
                            src="<?php echo e($landingPreviewUrl); ?>"
                            title="Preview landing modal"
                            tabindex="-1"
                            class="web-preview-frame"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('site-content-form');
            const phoneEl = document.getElementById('wa-phone');
            const messageEl = document.getElementById('wa-message');
            const waPreviewEl = document.getElementById('wa-preview');
            const planWhatsappFields = {
                basico: {
                    message: document.getElementById('wa-plan-basico-message'),
                    preview: document.getElementById('wa-plan-basico-preview'),
                },
                profesional: {
                    message: document.getElementById('wa-plan-profesional-message'),
                    preview: document.getElementById('wa-plan-profesional-preview'),
                },
                premium: {
                    message: document.getElementById('wa-plan-premium-message'),
                    preview: document.getElementById('wa-plan-premium-preview'),
                },
                sucursales: {
                    message: document.getElementById('wa-plan-sucursales-message'),
                    preview: document.getElementById('wa-plan-sucursales-preview'),
                },
            };
            const changeIndicator = document.getElementById('editor-change-indicator');
            const modalIframe = document.getElementById('landing-preview-modal');
            const previewViewport = document.getElementById('preview-modal-viewport');
            const modalReloadBtn = document.getElementById('preview-modal-reload');
            const openModalButtons = Array.from(document.querySelectorAll('[data-open-live-preview]'));
            const closeModalBtn = document.getElementById('live-preview-close');
            const modalEl = document.getElementById('live-preview-modal');
            const modalBackdrop = document.getElementById('live-preview-backdrop');
            const modalDialog = modalEl ? modalEl.querySelector('.web-preview-dialog') : null;
            const deviceButtons = Array.from(document.querySelectorAll('[data-preview-device]'));
            const quickNavButtons = Array.from(document.querySelectorAll('[data-scroll-target]'));
            const imageFields = Array.from(document.querySelectorAll('[data-preview-image]'));
            const previewFrames = [modalIframe].filter(Boolean);
            let lastFocusedElement = null;

            if (!form || previewFrames.length === 0 || !previewViewport) {
                return;
            }

            const forEachPreviewDoc = function (callback) {
                previewFrames.forEach(function (frame) {
                    const doc = frame.contentDocument || (frame.contentWindow ? frame.contentWindow.document : null);
                    if (doc) {
                        callback(doc, frame);
                    }
                });
            };

            const normalizeWhatsappPhone = function () {
                return String(phoneEl && phoneEl.value ? phoneEl.value : '').replace(/\D+/g, '');
            };

            const buildWhatsappUrlFromValues = function (phone, message) {
                const normalizedPhone = String(phone || '').trim();
                if (!normalizedPhone) {
                    return '#';
                }

                const normalizedMessage = String(message || '')
                    .replace(/\r\n/g, '\n')
                    .replace(/\r/g, '\n')
                    .trim()
                    .normalize('NFC');
                let url = 'https://api.whatsapp.com/send?phone=' + normalizedPhone;
                if (normalizedMessage) {
                    url += '&text=' + encodeURIComponent(normalizedMessage);
                }

                return url;
            };

            const buildWhatsappUrls = function () {
                const phone = normalizeWhatsappPhone();
                const baseMessage = String(messageEl && messageEl.value ? messageEl.value : '').trim();
                const urls = {
                    base: buildWhatsappUrlFromValues(phone, baseMessage),
                    basico: '#',
                    profesional: '#',
                    premium: '#',
                    sucursales: '#',
                };

                if (waPreviewEl) {
                    waPreviewEl.value = urls.base;
                }

                Object.keys(planWhatsappFields).forEach(function (planKey) {
                    const config = planWhatsappFields[planKey];
                    const planMessage = String(config && config.message && config.message.value ? config.message.value : '').trim();
                    const planUrl = buildWhatsappUrlFromValues(phone, planMessage);
                    urls[planKey] = planUrl;
                    if (config && config.preview) {
                        config.preview.value = planUrl;
                    }
                });

                return urls;
            };

            const serializeForm = function () {
                const snapshot = {};
                const fields = form.querySelectorAll('input,textarea,select');
                fields.forEach(function (field) {
                    if (!field.name || field.type === 'file' || field.type === 'hidden') {
                        return;
                    }

                    if (field.type === 'checkbox') {
                        snapshot[field.name] = field.checked ? '1' : '0';
                        return;
                    }

                    snapshot[field.name] = String(field.value || '');
                });

                return JSON.stringify(snapshot);
            };

            const initialSnapshot = serializeForm();
            const updateChangeIndicator = function () {
                if (!changeIndicator) {
                    return;
                }

                const changed = serializeForm() !== initialSnapshot;
                if (changed) {
                    changeIndicator.classList.remove('border-emerald-300', 'bg-emerald-50', 'text-emerald-700');
                    changeIndicator.classList.add('border-amber-300', 'bg-amber-50', 'text-amber-700');
                    changeIndicator.textContent = 'Cambios sin guardar';
                } else {
                    changeIndicator.classList.remove('border-amber-300', 'bg-amber-50', 'text-amber-700');
                    changeIndicator.classList.add('border-emerald-300', 'bg-emerald-50', 'text-emerald-700');
                    changeIndicator.textContent = 'Sin cambios';
                }
            };

            const textByName = function (name) {
                const field = form.querySelector('[name="' + name + '"]');
                return field ? String(field.value || '').trim() : '';
            };

            const applyTextAt = function (selector, index, value) {
                forEachPreviewDoc(function (doc) {
                    const list = doc.querySelectorAll(selector);
                    if (list[index]) {
                        list[index].textContent = value;
                    }
                });
            };

            const applyWhatsappLinks = function (urls) {
                forEachPreviewDoc(function (doc) {
                    const baseUrl = String(urls && urls.base ? urls.base : '#');
                    const planLinks = doc.querySelectorAll('[data-plan-cta-key]');
                    planLinks.forEach(function (link) {
                        const key = String(link.getAttribute('data-plan-cta-key') || '').trim();
                        const targetUrl = String(urls && urls[key] ? urls[key] : baseUrl);
                        link.setAttribute('href', targetUrl !== '' ? targetUrl : '#');
                    });

                    const links = doc.querySelectorAll('a[href*="wa.me"], .btn-wa');
                    links.forEach(function (link) {
                        if (link.hasAttribute('data-plan-cta-key')) {
                            return;
                        }

                        link.setAttribute('href', baseUrl !== '' ? baseUrl : '#');
                    });
                });
            };

            const ensureImageAt = function (containerSelector, src, preserveContent) {
                forEachPreviewDoc(function (doc) {
                    const container = doc.querySelector(containerSelector);
                    if (!container) {
                        return;
                    }

                    let image = container.querySelector('img');
                    if (!image) {
                        image = doc.createElement('img');
                        if (containerSelector === '.brand') {
                            const fallback = container.querySelector('.brand-fallback');
                            if (fallback) {
                                fallback.replaceWith(image);
                            } else {
                                container.insertBefore(image, container.firstChild);
                            }
                        } else if (!preserveContent) {
                            container.innerHTML = '';
                            container.appendChild(image);
                        } else {
                            container.appendChild(image);
                        }
                    }

                    image.src = src;
                    image.style.width = '100%';
                    image.style.height = '100%';
                    image.style.objectFit = containerSelector === '.brand' ? 'contain' : 'cover';
                    if (containerSelector === '.brand') {
                        image.classList.add('brand-logo');
                    }
                });
            };

            const ensureServiceImageAt = function (index, src) {
                forEachPreviewDoc(function (doc) {
                    const containers = doc.querySelectorAll('.service-media');
                    const container = containers[index];
                    if (!container) {
                        return;
                    }

                    let image = container.querySelector('img');
                    if (!image) {
                        image = doc.createElement('img');
                        container.innerHTML = '';
                        container.appendChild(image);
                    }

                    image.src = src;
                    image.style.width = '100%';
                    image.style.height = '100%';
                    image.style.objectFit = 'cover';
                });
            };

            const ensureMarqueeLogoAt = function (index, src) {
                forEachPreviewDoc(function (doc) {
                    const targets = doc.querySelectorAll('[data-marquee-logo-index="' + index + '"]');
                    targets.forEach(function (image) {
                        image.src = src;
                    });

                    if (targets.length > 0) {
                        return;
                    }

                    const chips = doc.querySelectorAll('[data-marquee-chip-index="' + index + '"]');
                    chips.forEach(function (chip) {
                        const image = doc.createElement('img');
                        image.className = 'brand-chip-logo';
                        image.setAttribute('data-marquee-logo-index', String(index));
                        image.src = src;
                        chip.insertBefore(image, chip.firstChild);
                    });
                });
            };

            const ensureHeroSlideAt = function (index, src) {
                forEachPreviewDoc(function (doc) {
                    const slides = doc.querySelectorAll('[data-hero-slide-index]');
                    const target = Array.from(slides).find(function (slideImage) {
                        return String(slideImage.getAttribute('data-hero-slide-index')) === String(index);
                    });

                    if (target) {
                        target.src = src;
                        return;
                    }

                    const frame = doc.querySelector('.hero-media-frame');
                    if (!frame) {
                        return;
                    }

                    let carousel = frame.querySelector('[data-hero-carousel]');
                    if (!carousel) {
                        carousel = doc.createElement('div');
                        carousel.className = 'hero-carousel';
                        carousel.setAttribute('data-hero-carousel', '');
                        frame.innerHTML = '';
                        frame.appendChild(carousel);
                    }

                    let image = carousel.querySelector('img[data-hero-slide-index="' + index + '"]');
                    if (!image) {
                        const figure = doc.createElement('figure');
                        figure.className = 'hero-slide';
                        figure.setAttribute('data-hero-slide', '');
                        image = doc.createElement('img');
                        image.className = 'hero-slide-image';
                        image.setAttribute('data-hero-slide-index', String(index));
                        figure.appendChild(image);
                        carousel.appendChild(figure);
                    }

                    if (image) {
                        image.src = src;

                        const allSlides = carousel.querySelectorAll('[data-hero-slide]');
                        allSlides.forEach(function (slide) {
                            slide.classList.remove('is-active');
                        });
                        const parentSlide = image.closest('[data-hero-slide]');
                        if (parentSlide) {
                            parentSlide.classList.add('is-active');
                        }
                    }
                });
            };

            const refreshLivePreview = function () {
                applyTextAt('.brand small', 0, textByName('brand_kicker'));
                applyTextAt('.brand strong', 0, textByName('brand_name'));
                applyTextAt('.footer .footer-brand h4', 0, textByName('brand_name'));
                applyTextAt('.nav-actions .btn.btn-outline', 0, textByName('login_button_label'));

                forEachPreviewDoc(function (doc) {
                    doc.querySelectorAll('.btn-demo').forEach(function (btn) {
                        btn.textContent = textByName('demo_button_label');
                    });
                });

                applyTextAt('#inicio .kicker', 0, textByName('hero_kicker'));
                applyTextAt('#inicio h1', 0, textByName('hero_title'));
                applyTextAt('#inicio .reveal p', 1, textByName('hero_subtitle'));
                applyTextAt('.hero-media-tag', 0, textByName('hero_media_tag'));
                applyTextAt('.hero-media-note', 0, textByName('hero_media_note'));

                forEachPreviewDoc(function (doc) {
                    for (let i = 1; i <= 6; i += 1) {
                        const value = textByName('marquee_item_' + i + '_text');
                        const labels = doc.querySelectorAll('[data-marquee-text-index="' + i + '"]');
                        labels.forEach(function (label) {
                            label.textContent = value;
                        });
                    }
                });

                for (let section = 1; section <= 3; section += 1) {
                    applyTextAt('.service h3', section - 1, textByName('section_' + section + '_title'));
                    applyTextAt('.service p', section - 1, textByName('section_' + section + '_text'));
                    for (let item = 1; item <= 3; item += 1) {
                        const index = ((section - 1) * 3) + (item - 1);
                        applyTextAt('.service ul li', index, textByName('section_' + section + '_item_' + item));
                    }
                }

                const footerRaw = textByName('footer_text');
                const footerResolved = footerRaw.replace('{year}', String(new Date().getFullYear()));
                const footerContactEmail = textByName('footer_contact_email').trim() || 'soporte@flexgym.local';
                applyTextAt('.copy', 0, footerResolved);
                applyTextAt('.footer .footer-lead', 0, footerResolved);
                forEachPreviewDoc(function (doc) {
                    const emailLink = doc.querySelector('.footer-contact-email');
                    if (emailLink) {
                        emailLink.textContent = footerContactEmail;
                        emailLink.setAttribute('href', 'mailto:' + footerContactEmail);
                    }
                });

                const whatsappUrls = buildWhatsappUrls();
                applyWhatsappLinks(whatsappUrls);
            };

            let livePreviewTimer = null;
            const queuePreviewRefresh = function () {
                if (livePreviewTimer) {
                    window.clearTimeout(livePreviewTimer);
                }

                livePreviewTimer = window.setTimeout(function () {
                    refreshLivePreview();
                    updateChangeIndicator();
                }, 80);
            };

            quickNavButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const target = document.querySelector(button.getAttribute('data-scroll-target') || '');
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        window.setTimeout(function () {
                            if (typeof target.focus === 'function') {
                                target.focus({ preventScroll: true });
                            }
                        }, 160);
                    }
                });
            });

            const setDevice = function (device) {
                const widths = {
                    desktop: '100%',
                    tablet: '900px',
                    mobile: '430px',
                };

                previewViewport.style.maxWidth = widths[device] || widths.desktop;
                deviceButtons.forEach(function (button) {
                    const active = button.getAttribute('data-preview-device') === device;
                    button.classList.toggle('is-active', active);
                });
            };

            const reloadAllPreviews = function () {
                previewFrames.forEach(function (frame) {
                    if (frame.contentWindow) {
                        frame.contentWindow.location.reload();
                    }
                });
            };

            const openModal = function () {
                if (!modalEl) {
                    return;
                }

                lastFocusedElement = document.activeElement instanceof HTMLElement ? document.activeElement : null;
                modalEl.classList.add('is-open');
                modalEl.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
                window.setTimeout(function () {
                    if (closeModalBtn) {
                        closeModalBtn.focus();
                        return;
                    }

                    modalDialog?.focus();
                }, 0);
                queuePreviewRefresh();
            };

            const closeModal = function () {
                if (!modalEl) {
                    return;
                }

                modalEl.classList.remove('is-open');
                modalEl.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
                if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                    lastFocusedElement.focus();
                }
            };

            setDevice('desktop');
            deviceButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    setDevice(button.getAttribute('data-preview-device') || 'desktop');
                });
            });

            if (modalReloadBtn) {
                modalReloadBtn.addEventListener('click', reloadAllPreviews);
            }

            openModalButtons.forEach(function (button) {
                button.addEventListener('click', openModal);
            });

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', closeModal);
            }

            if (modalBackdrop) {
                modalBackdrop.addEventListener('click', closeModal);
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Tab' && modalEl && modalEl.classList.contains('is-open') && modalDialog) {
                    const focusable = Array.from(modalDialog.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])'))
                        .filter(function (element) {
                            return element instanceof HTMLElement && !element.hasAttribute('hidden');
                        });

                    if (focusable.length > 0) {
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
                            return;
                        }
                    }
                }

                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            form.addEventListener('input', queuePreviewRefresh);
            form.addEventListener('change', queuePreviewRefresh);

            if (phoneEl) {
                phoneEl.addEventListener('input', queuePreviewRefresh);
            }
            if (messageEl) {
                messageEl.addEventListener('input', queuePreviewRefresh);
            }

            imageFields.forEach(function (input) {
                input.addEventListener('change', function () {
                    const file = input.files && input.files[0] ? input.files[0] : null;
                    if (!file) {
                        return;
                    }

                    const localUrl = URL.createObjectURL(file);
                    if (input.name === 'brand_logo_file') {
                        ensureImageAt('.brand', localUrl, true);
                        return;
                    }
                    if (input.name === 'hero_slide_1_file') {
                        ensureHeroSlideAt(1, localUrl);
                        return;
                    }
                    if (input.name === 'hero_slide_2_file') {
                        ensureHeroSlideAt(2, localUrl);
                        return;
                    }
                    if (input.name === 'hero_slide_3_file') {
                        ensureHeroSlideAt(3, localUrl);
                        return;
                    }
                    if (input.name === 'hero_slide_4_file') {
                        ensureHeroSlideAt(4, localUrl);
                        return;
                    }
                    if (input.name === 'marquee_item_1_logo_file') {
                        ensureMarqueeLogoAt(1, localUrl);
                        return;
                    }
                    if (input.name === 'marquee_item_2_logo_file') {
                        ensureMarqueeLogoAt(2, localUrl);
                        return;
                    }
                    if (input.name === 'marquee_item_3_logo_file') {
                        ensureMarqueeLogoAt(3, localUrl);
                        return;
                    }
                    if (input.name === 'marquee_item_4_logo_file') {
                        ensureMarqueeLogoAt(4, localUrl);
                        return;
                    }
                    if (input.name === 'marquee_item_5_logo_file') {
                        ensureMarqueeLogoAt(5, localUrl);
                        return;
                    }
                    if (input.name === 'marquee_item_6_logo_file') {
                        ensureMarqueeLogoAt(6, localUrl);
                        return;
                    }
                    if (input.name === 'section_1_image_file') {
                        ensureServiceImageAt(0, localUrl);
                        return;
                    }
                    if (input.name === 'section_2_image_file') {
                        ensureServiceImageAt(1, localUrl);
                        return;
                    }
                    if (input.name === 'section_3_image_file') {
                        ensureServiceImageAt(2, localUrl);
                    }
                });
            });

            previewFrames.forEach(function (frame) {
                frame.addEventListener('load', function () {
                    queuePreviewRefresh();
                });
            });

            buildWhatsappUrls();
            updateChangeIndicator();
        })();
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\gymsystem\resources\views/superadmin/site-content.blade.php ENDPATH**/ ?>
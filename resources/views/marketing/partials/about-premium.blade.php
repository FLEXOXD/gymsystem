@php
    $aboutProductName = trim((string) ($brandName ?? ''));
    $aboutProductName = $aboutProductName !== '' ? $aboutProductName : 'GymSystem';
    $aboutHeroVisualImage = trim((string) ($aboutHeroImage ?? ''));
    if ($aboutHeroVisualImage === '') {
        $aboutHeroVisualImage = trim((string) ($aboutStoryImage ?? ''));
    }
    $aboutStoryVisualImage = trim((string) ($aboutStoryImage ?? ''));
    if ($aboutStoryVisualImage === '') {
        $aboutStoryVisualImage = $aboutHeroVisualImage;
    }
    $aboutTrustBadges = [
        'Pensado para gimnasios reales',
        'Hecho para Ecuador y Latinoamérica',
        'Soporte cercano y profesional',
    ];
    $aboutStorySteps = [
        ['step' => '01', 'title' => 'El problema era diario, no teórico', 'text' => 'Pagos descontrolados, clientes sin seguimiento y demasiadas decisiones tomadas a ciegas.'],
        ['step' => '02', 'title' => 'La solución tenía que ser práctica', 'text' => 'Rápida para recepción, clara para administración y útil para el dueño desde el primer día.'],
        ['step' => '03', 'title' => 'El objetivo siempre fue el crecimiento', 'text' => 'No solo ordenar procesos, sino ayudarte a cobrar mejor, reducir errores y escalar con control.'],
    ];
    $aboutStoryPains = [
        'Membresías vencidas que nadie detecta a tiempo.',
        'Pagos registrados en varios lugares y sin control real.',
        'Clientes activos sin seguimiento comercial ni operativo.',
        'Demasiadas tareas manuales para un negocio que quiere crecer.',
    ];
    $aboutMissionPoints = [
        ['title' => 'Cobros y renovaciones bajo control', 'text' => 'Para que sepas qué se cobró, qué se vence y qué ingresos están en riesgo.'],
        ['title' => 'Operación ordenada para recepción y caja', 'text' => 'Menos retrabajo, menos errores y más velocidad para atender mejor.'],
        ['title' => 'Visibilidad real desde el celular', 'text' => 'Lo importante disponible donde estés, sin depender de reportes tardíos.'],
    ];
    $aboutDifferentiators = [
        ['icon' => 'spark', 'title' => 'Automatización real', 'text' => 'Recordatorios, vencimientos, cobros y tareas repetitivas que dejan de depender de la memoria.', 'caption' => 'Menos trabajo manual'],
        ['icon' => 'target', 'title' => 'Enfoque en resultados', 'text' => 'Cada función busca que cobres mejor, reduzcas fugas operativas y tomes decisiones con más claridad.', 'caption' => 'Más control para crecer'],
        ['icon' => 'setup', 'title' => 'Implementación sencilla', 'text' => 'Está pensado para empezar rápido y que tu equipo lo entienda sin procesos eternos ni fricción.', 'caption' => 'Rápido de adoptar'],
        ['icon' => 'gym', 'title' => 'Pensado para gimnasios reales', 'text' => 'Recepción, caja, asistencia y seguimiento conectados en un flujo que sí hace sentido en la operación.', 'caption' => 'Diseñado para el día a día'],
    ];
    $aboutOutcomeCards = [
        ['icon' => 'money', 'title' => 'Saber quién te debe dinero', 'text' => 'Ves pendientes, vencimientos y renovaciones sin revisar chats, cuadernos o varias hojas.', 'tag' => 'Vencimientos claros'],
        ['icon' => 'access', 'title' => 'Controlar asistencia en segundos', 'text' => 'La entrada de clientes deja de ser una adivinanza y se convierte en información útil.', 'tag' => 'Check-in ágil'],
        ['icon' => 'shield', 'title' => 'Reducir errores operativos', 'text' => 'Menos procesos manuales significa menos olvidos, menos retrabajo y una operación más confiable.', 'tag' => 'Menos caos diario'],
        ['icon' => 'mobile', 'title' => 'Administrar todo desde el celular', 'text' => 'Consulta lo importante de tu gimnasio sin depender de estar frente a un escritorio.', 'tag' => 'Control móvil'],
    ];
    $aboutFounderTags = ['Producto con criterio', 'Automatización útil', 'Enfoque en negocios reales'];
    $aboutFounderPrinciples = [
        'Resolver primero lo que más afecta ingresos y orden operativo.',
        'Mantener la experiencia simple para recepción, administración y gerencia.',
        'Construir un sistema serio, claro y útil para operar todos los días.',
    ];
    $aboutTrustCards = [
        ['icon' => 'clock', 'title' => 'Disponibilidad', 'text' => 'Acompañamiento para resolver dudas, aterrizar decisiones y avanzar sin sentirte solo.'],
        ['icon' => 'support', 'title' => 'Soporte cercano', 'text' => 'Respuestas claras, humanas y enfocadas en tu operación real, no en frases genéricas.'],
        ['icon' => 'shield', 'title' => 'Enfoque profesional', 'text' => 'Diseño, producto y experiencia alineados para que tu sistema también transmita seriedad.'],
        ['icon' => 'growth', 'title' => 'Pensado para crecer contigo', 'text' => 'Desde una sede hasta una operación más grande, sin volver al desorden cada vez que creces.'],
    ];
@endphp

<section id="nosotros" class="section about-section about-premium">
    <div class="shell">
        <div class="about-premium-hero reveal" @if ($aboutHeroVisualImage !== '') style="--about-premium-hero-image: url('{{ $aboutHeroVisualImage }}');" @endif>
            <div class="about-premium-hero-copy">
                <span class="about-premium-kicker">Nosotros</span>
                <h1 class="about-premium-hero-title">Creamos tecnología para que los gimnasios crezcan de verdad</h1>
                <p class="about-premium-hero-lead">En {{ $aboutProductName }} ayudamos a dueños de gimnasios de Ecuador y Latinoamérica a ordenar pagos, asistencia y operación diaria con una plataforma clara, rápida y pensada para vender mejor.</p>
                <div class="about-premium-badge-row">
                    @foreach ($aboutTrustBadges as $badge)
                        <span class="about-premium-badge">{{ $badge }}</span>
                    @endforeach
                </div>
                <div class="about-premium-actions">
                    <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                        @csrf
                        <button class="btn btn-demo" type="submit">Solicitar demo gratis</button>
                    </form>
                    <a href="{{ $pricingHref }}" class="btn btn-ghost">Ver planes</a>
                </div>
            </div>
            <div class="about-premium-hero-panel">
                <article class="about-premium-panel-card about-premium-panel-card--primary">
                    <span class="about-premium-panel-label">Control visible desde el día uno</span>
                    <strong class="about-premium-panel-title">Una sola plataforma para cobros, asistencia, caja y seguimiento.</strong>
                    <p class="about-premium-panel-copy">Menos tiempo persiguiendo información. Más tiempo tomando decisiones con claridad y foco comercial.</p>
                    <div class="about-premium-panel-pill-row">
                        <span class="about-premium-panel-pill">Cobros claros</span>
                        <span class="about-premium-panel-pill">Renovaciones visibles</span>
                        <span class="about-premium-panel-pill">Caja ordenada</span>
                        <span class="about-premium-panel-pill">Control móvil</span>
                    </div>
                </article>
                <div class="about-premium-panel-grid">
                    <article class="about-premium-mini-card">
                        <span class="about-premium-icon-slot">@include('marketing.partials.about-icon', ['name' => 'control'])</span>
                        <strong>Pagos bajo control</strong>
                        <p>Detecta pendientes, vencimientos y renovaciones antes de que afecten tus ingresos.</p>
                    </article>
                    <article class="about-premium-mini-card">
                        <span class="about-premium-icon-slot">@include('marketing.partials.about-icon', ['name' => 'access'])</span>
                        <strong>Asistencia sin fricción</strong>
                        <p>Revisa movimiento, entradas y actividad diaria sin depender de registros dispersos.</p>
                    </article>
                    <article class="about-premium-mini-card">
                        <span class="about-premium-icon-slot">@include('marketing.partials.about-icon', ['name' => 'mobile'])</span>
                        <strong>Gestión móvil</strong>
                        <p>Consulta lo importante desde el celular o escritorio, según cómo operas tu negocio.</p>
                    </article>
                </div>
            </div>
        </div>
    </div>

    <div class="shell about-premium-story-grid">
        <article class="about-premium-story-card reveal">
            <span class="about-premium-kicker">Nuestra historia</span>
            <h2 class="about-premium-section-title">{{ $aboutProductName }} nació viendo el mismo problema una y otra vez</h2>
            <p class="about-premium-copy">Muchos gimnasios tienen energía, clientes y ganas de crecer, pero siguen operando con cuadernos, Excel, chats y demasiadas tareas manuales.</p>
            <p class="about-premium-copy">Cuando nadie sabe con claridad quién debe, qué membresía vence hoy o cómo cerró la caja, el desgaste aparece rápido. Y con el desgaste, también se frena el crecimiento.</p>
            <p class="about-premium-copy">Por eso creamos {{ $aboutProductName }}: para devolverle al dueño control total, automatizar lo repetitivo y convertir la operación en una ventaja real.</p>
            <div class="about-premium-story-steps">
                @foreach ($aboutStorySteps as $storyStep)
                    <div class="about-premium-story-step">
                        <span class="about-premium-story-step-count">{{ $storyStep['step'] }}</span>
                        <div>
                            <strong>{{ $storyStep['title'] }}</strong>
                            <p>{{ $storyStep['text'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
        <aside class="about-premium-story-visual reveal">
            @if ($aboutStoryVisualImage !== '')
                <img src="{{ $aboutStoryVisualImage }}" alt="Operación moderna de gimnasio enfocada en control y crecimiento" loading="lazy">
            @else
                <div class="about-premium-story-placeholder">Agrega una imagen real del producto o de la operación del gimnasio para reforzar esta historia.</div>
            @endif
            <div class="about-premium-story-overlay">
                <span class="about-premium-story-overlay-label">Problemas que resolvemos</span>
                <ul class="about-premium-story-pains">
                    @foreach ($aboutStoryPains as $pain)
                        <li>{{ $pain }}</li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>

    <div class="shell">
        <article class="about-premium-mission reveal">
            <div>
                <span class="about-premium-kicker">Misión</span>
                <h2 class="about-premium-section-title">Ayudar a gimnasios a tener control total, automatizar procesos y aumentar ingresos.</h2>
                <p class="about-premium-copy">No hablamos de “ser líderes”. Hablamos de que el dueño pueda cobrar mejor, ver su operación en tiempo real y crecer sin volver al caos.</p>
            </div>
            <div class="about-premium-mission-points">
                @foreach ($aboutMissionPoints as $missionPoint)
                    <div class="about-premium-mission-point">
                        <div>
                            <strong>{{ $missionPoint['title'] }}</strong>
                            <span>{{ $missionPoint['text'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </div>

    <header class="shell heading reveal about-premium-heading">
        <small>No somos solo un sistema</small>
        <h2>Diseñado para la realidad de un gimnasio, no para una demo bonita</h2>
        <p>Cada módulo fue pensado para que el software se sienta útil desde recepción hasta la gerencia, con impacto real sobre la operación y los ingresos.</p>
    </header>

    <div class="shell about-premium-feature-grid">
        @foreach ($aboutDifferentiators as $item)
            <article class="about-premium-feature-card reveal">
                <span class="about-premium-icon-slot">@include('marketing.partials.about-icon', ['name' => $item['icon']])</span>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['text'] }}</p>
                <span class="about-premium-card-caption">{{ $item['caption'] }}</span>
            </article>
        @endforeach
    </div>

    <header class="shell heading reveal about-premium-heading">
        <small>Beneficios</small>
        <h2>Lo que logramos contigo</h2>
        <p>Resultados prácticos que un dueño siente en la operación diaria y en la tranquilidad con la que gestiona su gimnasio.</p>
    </header>

    <div class="shell about-premium-outcome-grid">
        @foreach ($aboutOutcomeCards as $item)
            <article class="about-premium-outcome-card reveal">
                <span class="about-premium-icon-slot">@include('marketing.partials.about-icon', ['name' => $item['icon']])</span>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['text'] }}</p>
                <span class="about-premium-outcome-pill">{{ $item['tag'] }}</span>
            </article>
        @endforeach
    </div>

    <div class="shell">
        <section class="about-premium-founder reveal">
            <div class="about-premium-founder-identity">
                <div class="about-premium-founder-avatar" aria-hidden="true">
                    <span class="about-premium-founder-avatar-core">DQ</span>
                </div>
                <span class="about-premium-founder-badge">Fundador de {{ $aboutProductName }}</span>
            </div>
            <div class="about-premium-founder-copy">
                <span class="about-premium-kicker">Fundador</span>
                <h2 class="about-premium-section-title">David Quintana</h2>
                <p class="about-premium-founder-role">Desarrollador enfocado en soluciones para negocios reales.</p>
                <p class="about-premium-copy">David impulsa {{ $aboutProductName }} con una idea simple: el software para gimnasios no debe complicar la operación, debe ordenar cobros, reducir errores y darle al dueño visibilidad real para crecer con más seguridad.</p>
                <div class="about-premium-founder-tags">
                    @foreach ($aboutFounderTags as $tag)
                        <span class="about-premium-founder-tag">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
            <aside class="about-premium-founder-side">
                <span class="about-premium-founder-side-label">Lo que guía el producto</span>
                <h4>Principios que se reflejan en cada módulo</h4>
                <ul class="about-premium-soft-list">
                    @foreach ($aboutFounderPrinciples as $principle)
                        <li>{{ $principle }}</li>
                    @endforeach
                </ul>
            </aside>
        </section>
    </div>

    <header class="shell heading reveal about-premium-heading">
        <small>Confianza</small>
        <h2>La confianza se gana con cómo trabajamos</h2>
        <p>En vez de llenar esta página con testimonios inventados, preferimos mostrarte el respaldo real que vas a encontrar: disponibilidad, criterio, acompañamiento y enfoque profesional.</p>
    </header>

    <div class="shell about-premium-trust-grid">
        @foreach ($aboutTrustCards as $item)
            <article class="about-premium-trust-card reveal">
                <span class="about-premium-icon-slot">@include('marketing.partials.about-icon', ['name' => $item['icon']])</span>
                <h3>{{ $item['title'] }}</h3>
                <p>{{ $item['text'] }}</p>
            </article>
        @endforeach
    </div>

    <section class="shell about-premium-cta reveal">
        <div>
            <span class="about-premium-kicker">Siguiente paso</span>
            <h2 class="about-premium-cta-title">Empieza a transformar tu gimnasio hoy</h2>
            <p class="about-premium-cta-copy">Prueba cómo se siente operar con más control, menos errores y una plataforma pensada para acompañar el crecimiento de tu gimnasio.</p>
        </div>
        <div class="about-premium-cta-actions">
            <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                @csrf
                <button class="btn btn-demo" type="submit">Solicitar demo gratis</button>
            </form>
            <a href="{{ $pricingHref }}" class="btn btn-ghost">Ver planes</a>
        </div>
    </section>
</section>

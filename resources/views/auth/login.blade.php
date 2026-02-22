<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Iniciar sesion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 ui-text dark:bg-slate-950">
<main class="mx-auto flex min-h-screen w-full max-w-md items-center px-4 py-8">
    <section class="ui-card w-full p-6">
        <h1 class="ui-heading text-2xl font-extrabold">Ingreso</h1>
        <p class="ui-muted mt-2 text-sm">Accede con tu cuenta de recepción.</p>

        @if ($errors->any())
            <div class="ui-alert ui-alert-danger mt-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="mt-5 space-y-4">
            @csrf
            <div>
                <label class="ui-muted mb-1 block text-sm font-semibold" for="email">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autofocus
                    value="{{ old('email') }}"
                    class="ui-input h-11">
            </div>

            <div>
                <label class="ui-muted mb-1 block text-sm font-semibold" for="password">Contrasena</label>
                <div class="relative">
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="ui-input h-11 pr-12">
                    <button
                        type="button"
                        id="toggle-password-visibility"
                        class="ui-muted absolute inset-y-0 right-0 inline-flex w-11 items-center justify-center"
                        aria-label="Mostrar contrasena"
                        aria-controls="password"
                        aria-pressed="false">
                        <svg id="password-eye-open" class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" stroke="currentColor" stroke-width="1.8"/>
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                        <svg id="password-eye-closed" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M3 3l18 18" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M10.5 6.3A11 11 0 0 1 12 6c6.5 0 10 6 10 6a17 17 0 0 1-4.1 4.8M6.6 8.1C3.8 10 2 12 2 12s3.5 6 10 6a11 11 0 0 0 4.3-.8" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </button>
                </div>
            </div>

            <label class="ui-muted flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600" @checked(old('remember'))>
                <span>Recordarme</span>
            </label>

            <button type="submit" class="ui-button ui-button-primary h-11 w-full text-base font-bold">
                Entrar
            </button>
        </form>
    </section>
</main>
<script>
    (function () {
        const toggle = document.getElementById('toggle-password-visibility');
        const input = document.getElementById('password');
        const eyeOpen = document.getElementById('password-eye-open');
        const eyeClosed = document.getElementById('password-eye-closed');

        if (!toggle || !input || !eyeOpen || !eyeClosed) {
            return;
        }

        const syncPasswordIconState = function () {
            const isVisible = input.type === 'text';
            eyeOpen.classList.toggle('hidden', !isVisible);
            eyeClosed.classList.toggle('hidden', isVisible);
            toggle.setAttribute('aria-pressed', isVisible ? 'true' : 'false');
            toggle.setAttribute('aria-label', isVisible ? 'Ocultar contrasena' : 'Mostrar contrasena');
        };

        syncPasswordIconState();

        toggle.addEventListener('click', function () {
            input.type = input.type === 'text' ? 'password' : 'text';
            syncPasswordIconState();
        });
    })();
</script>
</body>
</html>

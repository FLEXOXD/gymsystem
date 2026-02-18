<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 ui-text dark:bg-slate-950">
<main class="mx-auto flex min-h-screen w-full max-w-md items-center px-4 py-8">
    <section class="ui-card w-full p-6">
        <h1 class="ui-heading text-2xl font-extrabold">Ingreso</h1>
        <p class="ui-muted mt-2 text-sm">Accede con tu cuenta de recepcion.</p>

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
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="ui-input h-11">
            </div>

            <label class="ui-muted flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-cyan-600">
                <span>Recordarme</span>
            </label>

            <button type="submit" class="ui-button ui-button-primary h-11 w-full text-base font-bold">
                Entrar
            </button>
        </form>
    </section>
</main>
</body>
</html>

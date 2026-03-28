<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Panel de Administración - Salud Maternal. Acceso exclusivo para administradores.">
    <title>Administración | Salud Maternal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #6c63ff;
            --primary-dark: #5548e0;
            --accent: #ff6584;
            --bg: #0f0e1a;
            --surface: rgba(255,255,255,0.05);
            --border: rgba(255,255,255,0.08);
            --text: #f0eeff;
            --muted: #9b9ab5;
            --error: #ff5b79;
            --success: #4ecdc4;
            --input-bg: rgba(255,255,255,0.07);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Fondo animado */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 20% 50%, rgba(108,99,255,0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(255,101,132,0.10) 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 80%, rgba(78,205,196,0.08) 0%, transparent 50%);
            animation: bgAnim 12s ease-in-out infinite alternate;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes bgAnim {
            0%   { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(3%, 2%) rotate(2deg); }
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1rem;
            box-shadow: 0 8px 32px rgba(108,99,255,0.4);
        }

        .brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text);
            letter-spacing: -0.02em;
        }

        .brand p {
            color: var(--muted);
            font-size: 0.85rem;
            margin-top: 0.3rem;
        }

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 2.5rem 2rem;
            backdrop-filter: blur(20px);
            box-shadow: 0 24px 64px rgba(0,0,0,0.4);
        }

        .card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.4rem;
        }

        .card .subtitle {
            color: var(--muted);
            font-size: 0.85rem;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 0.5rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(108,99,255,0.18);
        }

        input::placeholder { color: var(--muted); }

        .password-wrap {
            position: relative;
        }

        .toggle-pass {
            position: absolute;
            right: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .toggle-pass:hover { color: var(--primary); }

        .alert {
            border-radius: 10px;
            padding: 0.8rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .alert-error {
            background: rgba(255,91,121,0.12);
            border: 1px solid rgba(255,91,121,0.3);
            color: #ff8fab;
        }

        .alert-success {
            background: rgba(78,205,196,0.12);
            border: 1px solid rgba(78,205,196,0.3);
            color: var(--success);
        }

        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.15s, opacity 0.15s;
            box-shadow: 0 4px 20px rgba(108,99,255,0.4);
            letter-spacing: 0.01em;
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(108,99,255,0.55);
        }

        .btn-login:active {
            transform: translateY(0);
            opacity: 0.9;
        }

        .badge-admin {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(108,99,255,0.15);
            border: 1px solid rgba(108,99,255,0.3);
            color: #a89dff;
            border-radius: 20px;
            padding: 0.3rem 0.8rem;
            font-size: 0.75rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            background: var(--primary);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.5; transform: scale(0.8); }
        }

        .field-error {
            color: var(--error);
            font-size: 0.78rem;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="brand">
            <div class="brand-logo">🏥</div>
            <h1>Salud Maternal</h1>
            <p>Sistema de Gestión Médica</p>
        </div>

        <div class="card">
            <div class="badge-admin">
                <span class="badge-dot"></span>
                Panel Administrativo
            </div>

            <h2>Iniciar sesión</h2>
            <p class="subtitle">Acceso exclusivo para administradores del sistema</p>

            {{-- Mensaje de éxito (ej. después de logout) --}}
            @if(session('success'))
                <div class="alert alert-success">
                     {{ session('success') }}
                </div>
            @endif

            {{-- Errores generales de la API --}}
            @if($errors->has('correo') && !$errors->has('contrasena'))
                <div class="alert alert-error">
                     {{ $errors->first('correo') }}
                </div>
            @endif

            <form id="login-form" method="POST" action="{{ route('admin.login.post') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        placeholder="admin@salud.com"
                        value="{{ old('correo') }}"
                        autocomplete="email"
                        required
                    >
                    @error('correo')
                        <p class="field-error">⚠ {{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <div class="password-wrap">
                        <input
                            type="password"
                            id="contrasena"
                            name="contrasena"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="toggle-pass" id="toggle-pass-btn" aria-label="Mostrar contraseña">
                            👁
                        </button>
                    </div>
                    @error('contrasena')
                        <p class="field-error">⚠ {{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-login" id="btn-submit">
                    Ingresar al panel
                </button>
            </form>
        </div>
    </div>

    <script>
        // Toggle mostrar/ocultar contraseña
        const btn  = document.getElementById('toggle-pass-btn');
        const pass = document.getElementById('contrasena');
        btn.addEventListener('click', () => {
            const isPass = pass.type === 'password';
            pass.type = isPass ? 'text' : 'password';
            btn.textContent = isPass ? '👁' : '👁';
        });

        // Deshabilitar botón al enviar para evitar doble clic
        document.getElementById('login-form').addEventListener('submit', function () {
            const submitBtn = document.getElementById('btn-submit');
            submitBtn.textContent = 'Verificando…';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>

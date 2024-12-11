<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST" action="{{ route('register') }}" id="registroForm">
                @csrf
                <h1>Crear Cuenta</h1>
                <div class="social-icons">
                    <a href="#" class="icon"
                    ><i class="fa-brands fa-google-plus-g"></i
                    ></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"
                    ><i class="fa-brands fa-linkedin-in"></i
                    ></a>
                </div>
                <span>o usa tu correo para registrarte</span>
                <input type="text" name="username" placeholder="Nombre de usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
                <button type="submit">Registrarse</button>
            </form>
        </div>

        <div class="form-container sign-in">
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf
                @if($errors->any())
                    <div class="alert alert-danger" style="color: red; margin-bottom: 10px;">
                        {{ $errors->first() }}
                    </div>
                @endif
                <h1>Iniciar Sesión</h1>
                <div class="social-icons">
                    <a href="#" class="icon"
                    ><i class="fa-brands fa-google-plus-g"></i
                    ></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"
                    ><i class="fa-brands fa-linkedin-in"></i
                    ></a>
                </div>
                <span>o usa tu correo y contraseña</span>
                <input type="text" name="username" placeholder="Usuario" required>
                <input type="password" name="password" placeholder="Contraseña" required>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <button type="submit">Ingresar</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>¡Bienvenido de nuevo!</h1>
                    <p>Para mantenerte conectado, por favor inicia sesión con tu información personal</p>
                    <button class="hidden" id="login">Iniciar Sesión</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>¡Hola, Amigo!</h1>
                    <p>Ingresa tus datos personales y empieza tu aventura con nosotros</p>
                    <button class="hidden" id="register">Registrarse</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>

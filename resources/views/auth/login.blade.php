<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Gestión Hotelera - Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS (ya está configurado en AdminLTE) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #e0e0e0, #f5f5f5);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .login-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }

        .login-header {
            background: #343a40;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .login-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group .input-group {
            height: 45px;
            /* Ajustamos la altura para que coincida con la captura */
        }

        .form-group .input-group-text {
            background-color: #f8f9fa;
            /* Fondo claro para el ícono */
            border: 1px solid #ccc;
            border-right: none;
            color: #666;
            font-size: 1.1rem;
            /* Tamaño del ícono */
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 0 4px 4px 0;
            /* Bordes redondeados solo a la derecha */
            font-size: 1rem;
            background-color: #f8f9fa;
            /* Fondo más claro como en la captura */
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: #28a745;
            /* Borde verde al enfocar */
            outline: none;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-login:hover {
            background-color: #218838;
        }

        .login-footer {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            SISTEMA GESTIÓN HOTELERA 4
        </div>
        <div class="login-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="email">Correo</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" required autofocus
                            placeholder="Ingrese su Correo Electrónico">
                    </div>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input id="password" type="password" name="password"
                            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" required
                            placeholder="Ingrese su Contraseña">
                    </div>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn-login">Acceder</button>
            </form>
        </div>
        <div class="login-footer">
            MORRONE PABLO - 2025
        </div>
    </div>

    <!-- Bootstrap JS (opcional, si necesitas funcionalidad de Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

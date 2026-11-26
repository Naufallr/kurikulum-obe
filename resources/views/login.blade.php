<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/blade-animations.css') }}">
</head>
<body>
    <div class="login-container">
        <div class="background" style="background-image: url('{{ asset('storage/ukri.jpg') }}');">
        </div>

        <div class="login-form-wrapper">
            <form class="login-form" method="POST" action="{{ route('login') }}">
                @csrf

                <div class="logo-container">
                    <img src="{{ asset('storage/logo ukri.png') }}" alt="Logo UKRI" class="logo">
                </div>

                <h1 class="welcome-text">SELAMAT DATANG</h1>

                @if ($errors->any())
                    <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
                        @foreach ($errors->all() as $error)
                            <p style="margin: 0;">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" id="username" placeholder="Username" class="form-input" value="{{ old('username') }}" required autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="Password" class="form-input" required>
                    </div>
                </div>
                <button type="submit" class="login-button">Masuk</button>
            </form>
        </div>
        @include('partials.page-footer', ['footerClass' => 'page-footer--login'])
    </div>
    <script src="{{ asset('js/blade-animations.js') }}" defer></script>
</body>
</html>









<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="background" style="background-image: url('{{ asset('storage/ukri.jpg') }}');">
        </div>

        <div class="login-form-wrapper">
            <form class="login-form" method="POST" action="#">
                @csrf

                <div class="logo-container">
                    <img src="{{ asset('storage/logo ukri.png') }}" alt="Logo UKRI" class="logo">
                </div>

                <h1 class="welcome-text">SELAMAT DATANG</h1>

                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" name="username" id="username" placeholder="Username" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="password" id="password" placeholder="Password" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="captcha-container">
                        <div class="captcha-image" id="captchaImage">
                            <svg width="160" height="48" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 60">
                                <rect width="200" height="60" fill="#ffffff"/>
                                <text x="50%" y="50%" font-family="Arial, sans-serif" font-size="28" font-weight="bold" fill="#ff9999" text-anchor="middle" dominant-baseline="middle">SsDTP</text>
                                <path d="M10,25 Q60,15 110,25 T210,25" stroke="#ff6b6b" stroke-width="1.5" fill="none"/>
                                <path d="M5,35 Q55,28 105,35 T205,35" stroke="#ff6b6b" stroke-width="1.5" fill="none"/>
                                <path d="M15,45 Q65,38 115,45 T215,45" stroke="#ff6b6b" stroke-width="1.5" fill="none"/>
                                <path d="M8,18 Q58,12 108,18 T208,18" stroke="#ff6b6b" stroke-width="1" fill="none"/>
                                <path d="M12,52 Q62,48 112,52 T212,52" stroke="#ff6b6b" stroke-width="1" fill="none"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" name="captcha" id="captcha" placeholder="Masukkan Kode Keamanan" class="form-input" required>
                    </div>
                </div>

                <button type="submit" class="login-button">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>

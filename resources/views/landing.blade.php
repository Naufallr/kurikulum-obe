<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurikulum OBE</title>
    <link rel="stylesheet" href="{{ asset('https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css') }}">
    <img src="{{asset ('storage/app/public/logo ukri.jpg')}}" alt="user">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: rgba(255, 255, 255, 0.8);
        }
        .login-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="background">
        <div class="container">
            <h1>Universitas Kebangsaan Republik Indonesia</h1>
            <div class="login-form">
                <h2>SELAMAT DATANG</h2>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <input type="text" name="username" placeholder="Username" required><br><br>
                    <input type="password" name="password" placeholder="Password" required><br><br>
                    <input type="text" name="kode_keamanan" placeholder="Masukkan Kode Keamanan" required><br><br>
                    <button type="submit">Masuk</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
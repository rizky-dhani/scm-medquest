<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akses Ditolak</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7fafc;
            color: #4a5568;
            text-align: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            padding: 2rem;
        }
        .logo {
            margin-bottom: 2rem;
        }
        .logo img {
            max-width: 200px;
        }
        h1 {
            font-size: 2.5rem;
            color: #e53e3e;
            margin-bottom: 1rem;
        }
        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        .btn {
            display: inline-block;
            background-color: #3182ce;
            color: white;
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            border-radius: 0.375rem;
            font-weight: 600;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #2c5282;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="{{ asset('assets/images/LOGO-MEDQUEST-HD.png') }}" alt="Logo Perusahaan">
        </div>
        <h1>Akses Ditolak</h1>
        <p>Akun Anda saat ini tidak aktif. Silakan hubungi administrator sistem untuk mengaktifkan kembali akun Anda.</p>
        <p>Jika Anda merasa ini adalah kesalahan, silakan hubungi tim IT Anda.</p>
        
        <form method="POST" action="{{ route('logout.403') }}" style="display: inline;">
            @csrf
            <button type="submit" class="btn">Kembali ke Beranda</button>
        </form>
    </div>
</body>
</html>
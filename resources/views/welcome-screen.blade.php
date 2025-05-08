<!DOCTYPE html>
<html lang="id" x-data="{
    show: localStorage.getItem('welcomeShown') !== 'yes',
    masuk() {
        localStorage.setItem('welcomeShown', 'yes');
        window.location.href = '{{ route('home') }}';
    }
}" x-init="
    if (!show) {
        window.location.href = '{{ route('home') }}';
    }
">
<head>
    <meta charset="UTF-8">
    <title>Selamat Datang</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="//unpkg.com/alpinejs" defer></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #f8f9fa, #e0e0e0);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .welcome-box {
            text-align: center;
            background: white;
            padding: 40px 20px;
            border-radius: 20px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .welcome-box img {
            width: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="welcome-box">
        <img src="{{ asset('favicon.png') }}" alt="Logo Cafe">
        <h1 class="fw-bold mb-2">Raihaan Coffee Corner</h1>
        <p class="text-muted mb-4">Minuman hangat langsung ke rumahmu 🍵</p>
        <button class="btn btn-dark px-4 rounded-pill" @click="masuk()">Masuk</button>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') · {{ config('app.name', 'Raihaan Coffee Corner') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #011a0f, #006a3e); color: #1f2937; padding: 16px;
        }
        .card { background: #fff; border-radius: 20px; padding: 2rem 1.5rem; max-width: 380px; width: 100%; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,.25); }
        .code { font-size: 3rem; font-weight: 800; color: #006a3e; line-height: 1; margin-bottom: .5rem; }
        h1 { font-size: 1.2rem; margin: 0 0 .5rem; }
        p { color: #6b7280; font-size: .92rem; margin: 0 0 1.5rem; line-height: 1.5; }
        a.btn { display: inline-block; background: #006a3e; color: #fff; text-decoration: none; padding: .75rem 1.5rem; border-radius: 12px; font-weight: 600; }
        a.link { display: block; margin-top: .9rem; color: #6b7280; font-size: .85rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a class="btn" href="{{ url('/home') }}">Kembali ke Menu</a>
        <a class="link" href="javascript:history.back()">Kembali ke halaman sebelumnya</a>
    </div>
</body>
</html>

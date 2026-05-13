<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Selesai</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f0fdf4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: #fff;
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .icon {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2.25rem;
        }
        h1 {
            font-size: 1.375rem;
            font-weight: 700;
            color: #15803d;
            margin-bottom: 0.5rem;
        }
        .order-number {
            display: inline-block;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            margin: 0.75rem 0;
            letter-spacing: 0.02em;
        }
        p {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-top: 0.5rem;
        }
        .already {
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">✅</div>
        <h1>Pesanan Selesai!</h1>
        <div class="order-number">{{ $pesanan->nomor_pesanan }}</div>
        <p>
            Pesanan atas nama <strong>{{ $pesanan->nama_pemesan }}</strong>
            telah ditandai selesai.
        </p>
        @if($pesanan->waktu_selesai)
            <p class="already">Diselesaikan pada {{ $pesanan->waktu_selesai->format('d M Y, H:i') }} WIB</p>
        @endif
    </div>
</body>
</html>

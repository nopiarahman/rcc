<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kami Sedang Tutup - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md text-center">
            <div class="mb-6">
                @php
                    $settings = \App\Models\WebSetting::first();
                    $logoPath = $settings->logo_path ? asset('storage/' . $settings->logo_path) : asset('images/logo.png');
                @endphp
                <img src="{{ $logoPath }}" alt="Logo" class="h-20 mx-auto mb-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    @if($settings->is_temporarily_closed==true)
                        Kami Sedang Tutup Sementara
                    @else
                        Kami Sedang Tutup
                    @endif
                </h1>
                
                @if($settings->temporary_closure_message)
                    <p class="mt-4 text-gray-600">{{ $settings->temporary_closure_message }}</p>
                @else
                    <p class="mt-4 text-gray-600">Kami sedang tutup sementara. Silakan kunjungi kembali nanti.</p>
                @endif
                
                @if($settings->opening_time && $settings->closing_time)
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600">Jam Operasional:</p>
                        <p class="font-medium">
                            {{ \Carbon\Carbon::parse($settings->opening_time)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($settings->closing_time)->format('H:i') }} WIB
                        </p>
                    </div>
                @endif
                
                @if(!auth()->check() || !auth()->user()->is_admin)
                    
                @else
                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                            Ke Dashboard
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="text-sm text-gray-500 mt-8">
                &copy; {{ date('Y') }} {{ $settings->site_name ?? config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>
</body>
</html>

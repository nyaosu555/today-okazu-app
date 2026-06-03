<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '今日のおかずなんにしよ') }}</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="792x901">

        <!-- Fonts -->
        {{-- <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{-- 全体の背景 --}}
        <div class="relative min-h-screen bg-cover bg-center flex items-center justify-center flex-col p-4"
                style="background-image: url('{{ asset('images/background-image.png') }}');"
        >
            {{-- 背景を暗くするオーバーレイ --}}
            <div class="absolute inset-0 bg-black/50 backrdop-blur-sm"></div>

            {{-- 共通タイトル --}}
            <div class="relative a-10 text-center mb-8">
                <h1 class="text-[#ffffff] text-3xl md:text-5xl font-bold mb-8 drop-shadow-sm">
                    今日のおかずなんにしよ
                    <div class="h-1.5 w-[245px] bg-[#F0BA32] mx-auto mt-4 rounded-full"></div>
                </h1>
            </div>

            {{-- 中央のカード（ここが各画面の中身に置き換わる） --}}
            <div class="relative z-10 w-full max-w-md bg-[#fee5a5] rounded-[2em] shadow-2xl p-8 md:p-10 text-center">
                {{ $slot }}
            </div>
        </div>


        {{-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div> --}}
    </body>
</html>

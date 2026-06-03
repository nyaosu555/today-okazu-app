<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', '今日のおかずなんにしよ') }}</title>

        <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" sizes="792x901">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        {{-- @stack('scripts') --}}

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- 全体の背景 --}}
        <div class="relative min-h-screen bg-cover bg-center flex items-center justify-start flex-col p-4"
                style="background-image: url('{{ asset('images/background-image.png') }}');"
        >
            {{-- 背景を暗くするオーバーレイ --}}
            <div class="absolute inset-0 bg-black/50 backrdop-blur-sm"></div>

            {{-- 共通タイトル --}}
            <div class="relative z-10 text-center my-10 md:my-20 md:mb-16">
                <h1 class="text-[#ffffff] text-3xl md:text-5xl font-bold mb-8 drop-shadow-sm">
                    今日のおかずなんにしよ
                    <div class="h-1.5 w-[245px] bg-[#F0BA32] mx-auto mt-4 rounded-full"></div>
                </h1>
            </div>

            {{-- 中央のカード（ここが各画面の中身に置き換わる） --}}
            <!-- Page Content -->
            <main class="relative z-10 w-full max-w-6xl mx-auto px-4 pb-20">
                {{ $slot }}
            </main>
        </div>
        <x-message-modal />

        @stack('scripts')
    </body>
</html>

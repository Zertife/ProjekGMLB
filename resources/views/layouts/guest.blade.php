<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .required { color: #dc2626; margin-left: 0.15rem; }
            body { background: #f8fafc; color: #1e293b; }
            .min-h-screen { background: #f8fafc !important; }
            .min-h-screen .sm\:max-w-md {
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                box-shadow: none !important;
            }
            input:focus, select:focus, textarea:focus {
                border-color: #2563eb !important;
                outline: 2px solid #bfdbfe !important;
                outline-offset: 1px;
                box-shadow: none !important;
            }
            a:focus-visible, button:focus-visible {
                outline: 2px solid #93c5fd;
                outline-offset: 2px;
                box-shadow: none !important;
            }
            button, .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl { box-shadow: none !important; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

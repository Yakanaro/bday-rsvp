@props(['title'])

<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="robots" content="noindex, nofollow">
        <meta name="theme-color" content="#C8102E">
        <title>{{ $title }} · {{ config('invitation.host') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500&family=Oswald:wght@400;500;600&display=swap">

        @vite(['resources/css/host.css'])
    </head>
    <body>
        <main {{ $attributes->merge(['class' => 'host-page']) }}>
            {{ $slot }}
        </main>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body class="antialiased">
    <div
        class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
          @if (isset($token))
          <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                Token : <br>
                <textarea style="width:80%;">{{$token}}</textarea>
              </div>
                @endif
                <a class="btn" href="{{ url('auth/google') }}"
                    style="background: #1500ff; color: #ffffff; padding: 10px; width: 10%; margin: 20% 45%; text-align: center; display: block; border-radius:20px;">
                    Login with Google
                </a>
                <a class="btn" href="{{ url('auth/youtube') }}"
                    style="background: red; color: #ffffff; padding: 10px; width: 10%; margin: 20% 45%; text-align: center; display: block; border-radius:20px;">
                    YouTUBE code generator
                </a>

                <a class="btn" href="{{ $instaAuthUrl }}"
                    style="background: red; color: #ffffff; padding: 10px; width: 10%; margin: 20% 45%; text-align: center; display: block; border-radius:20px;">
                    Login with Instagram
                </a>

    </div>
</div>
</body>

</html>

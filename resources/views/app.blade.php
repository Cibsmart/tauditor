<!DOCTYPE html>
<html  class="h-full bg-gray-100">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet" />
    <script src="{{ mix('/js/app.js') }}" defer></script>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    @routes
  </head>

  <body class="font-sans leading-none text-gray-700 antialiased">
    @inertia
  </body>
</html>
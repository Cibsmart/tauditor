<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

    <title>@yield('title')</title>
</head>
    <div class="container-xl h-100">
        <div class="text-center">
            <h3>@yield('heading')</h3>
        </div>

        <div>
            <h4>@yield('sub_heading')</h4>
        </div>
        <hr style="" />
        @yield('body')
    </div>
<body>

</body>
</html>

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Booj Book List</title>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>

        <!-- Scripts -->
            <script type="text/javascript">var _site = "{{ url('books') }}";  </script>
            <script type="text/javascript">var _spinner = "{{ asset('images/ajax-loader.gif') }}"; </script>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>

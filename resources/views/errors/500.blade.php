<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>500 Error</title>
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <div class="flex-1 flex items-center justify-center">
            <main class="w-full">
                <div class="container max-w-2xl mx-auto px-4">
                    <div class="text-center mt-4">
                        <h1 class="text-6xl font-bold text-gray-900">500</h1>
                        <p class="text-xl text-gray-700 mt-2">Error de servidor interno</p>
                        <a href="{{route('panel')}}" class="inline-flex items-center mt-4 text-blue-600 hover:text-blue-700">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Quiero salir de aquí
                        </a>
                    </div>
                </div>
            </main>
        </div>
        <div class="flex items-end w-full">
            @include('layouts.include.footer')
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
</body>

</html>

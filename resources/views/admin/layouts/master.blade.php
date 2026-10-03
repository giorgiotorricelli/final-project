<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <header class="admin-header px-3">

        
        <div class="dropdown">
            <button class="btn admin-nav-btn dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-list"></i>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="{{ route('admin.index') }}">Home</a></li>
                <li><a class="dropdown-item" href="{{ route('admin.movies.index') }}">All Movies</a></li>
            </ul>
        </div>
        <h1 class='future-font m-0'>Admin</h1>
        <a class="d-inline nav-link" href="{{ route('dashboard') }}">Dashboard</a>
    </header>
    @yield('content')
</body>
</html>
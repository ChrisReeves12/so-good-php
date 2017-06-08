<!DOCTYPE html>
<html>
<head>
    <title>{{ business('store_name') }} | Administration</title>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="/css/admin/combined.min.css?v={{ business('static_resource_version') }}" type="text/css" rel="stylesheet">
    @yield('head-scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
</head>
<body>
<header>
    <nav class="top-nav">
        <div class="logo-section"></div>
        <ul>
            <li>
                <a href="/admin">
                    <img class="icon" src="/assets/img/layout/admin/home_icon.png">
                    <div class="label">Home</div>
                </a>
            </li>
            <li>
                <a target="_blank" href="/admin/reports">
                    <img class="icon" src="/assets/img/layout/admin/reports_icon.png">
                    <div class="label">Reports</div>
                </a>
            </li>
            <li>
                <a target="_blank" href="/admin/logs">
                    <img class="icon" src="/assets/img/layout/admin/bug_icon.png">
                    <div class="label">Logs</div>
                </a>
            </li>
            <li>
                <a target="_blank" href="/">
                    <img class="icon" src="/assets/img/layout/admin/view_store_icon.png">
                    <div class="label">Go To Store</div>
                </a>
            </li>
        </ul>
    </nav>
</header>
<div id="side_menu_bar"></div>
<div class="main">
    @yield('content')
</div>
@yield('page-bottom')
<script>
    window.sogood = window.sogood || {};
    window.sogood.reactjs = window.sogood.reactjs || {};
</script>
@yield('javascript-globals')
<script
        src="https://code.jquery.com/jquery-3.1.1.min.js"
        integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="
        crossorigin="anonymous"></script>
<script
        src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"
        integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU="
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="/js/admin/combined.min.js?v={{ business('static_resource_version') }}"></script>
@yield('footer-scripts')
</body>
</html>
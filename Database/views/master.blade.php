<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Comparator</title>
    <link href="{{ asset('services/database/bootstrap.min.css') }}" rel="stylesheet">

    <style>
        .card {
            height: 250px;
            overflow-y: scroll;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            @yield('content')
        </div>
    </div>
</div>
<input id="cpy-me" type="hidden">

<script src="{{ asset('services/database/jquery.min.js') }}"></script>
<script src="{{ asset('services/database/clipboard.min.js') }}"></script>

<script>

    var clipboard = new ClipboardJS('.cpy');

    clipboard.on('success', function(e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        e.clearSelection();
    });

    clipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });



</script>
</body>
</html>

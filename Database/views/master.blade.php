<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Comparator</title>
    <?php
        $base_assets = asset('/');
    ?>
    <link href="{{ $base_assets . 'services/database/bootstrap.min.css' }}" rel="stylesheet">

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

<script src="{{ $base_assets . 'services/database/jquery.min.js' }}"></script>
<script src="{{ $base_assets . 'services/database/clipboard.min.js' }}"></script>

<script>

    var clipboard = new ClipboardJS('.cpy');

    clipboard.on('success', function(e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        e.clearSelection();
        alert('Copied!');
    });

    clipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });

    $(document).on('click', '#do-compare', function () {
        var This = $(this);

        if (This.attr('disabled') == 'disabled') {
            return false;
        }
        This.attr('disabled', 'disabled');
        This.html('Loading....');
        $('#form-compare').submit();

        return false;
    })


</script>
</body>
</html>

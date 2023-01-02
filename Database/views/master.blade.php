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

        .checkbox:focus {
            box-shadow: unset !important;
        }
        .apply {
            padding: 10px;
            border: 2px solid #eee;
            background: mintcream;
            width: fit-content;
            font-weight: bold;
            @if($autoupdate == true) display:none; @endif
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

    clipboard.on('success', function (e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);

        e.clearSelection();
        alert('Copied!');
    });

    clipboard.on('error', function (e) {
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
    $(document).find('#radios').hide();
    $(document).on('change', 'input', function () {

        var sourceUpdate = $(document).find('#source-auto');
        var currentUpdate = $(document).find('#current-auto');

        var radios = $(document).find('#radios');

        var noRadio = $(document).find('#no-datatype-update-div');
        var radioSource = $(document).find('#source-datatype-update-div');
        var radioCurrent = $(document).find('#current-datatype-update-div');

        radios.hide();
        radioSource.show();
        radioCurrent.show();

        if (sourceUpdate.is(':checked') && !currentUpdate.is(':checked')) {
            radios.show();
            radioCurrent.hide();
        }
        if (currentUpdate.is(':checked') && !sourceUpdate.is(':checked')) {
            radios.show();
            radioSource.hide();
        }
        if (!sourceUpdate.is(':checked') && !currentUpdate.is(':checked')) {
            radios.show();
            radios.hide();
        }
        if (sourceUpdate.is(':checked') && currentUpdate.is(':checked')) {
            radios.show();
            radioSource.show();
            radioCurrent.show();
        }
    })

    $(document).on('click', '.apply', function () {

        var This = $(this);
        if (This.attr('disabled') == 'disabled') {
            return false;
        }
        var id = This.data('id');
        var db = This.data('db');

        var _token = '{{ csrf_token() }}'
        var content = $(document).find('#' + id).html();
        var URL = '{{ route('db-compare-apply') }}';
        $.ajax({
            url: URL,
            type: 'POST',
            async: false,
            data: {_token, content, db},
            beforeSend: function () {
                This.attr('disabled', 'disabled');
                This.html('Loading....');
            },
            success: function (response) {
                console.log({response});
                This.removeAttr('disabled');
                This.html('<span class="alert alert-success">Applied</span>');
                $('#' + id).html('<center class="text-center text-success h5">Changes are Successfully Applied</center>');
            },
            error: function (xhr) {
                This.removeAttr('disabled');
                This.html('<span class="alert alert-danger">Error!</span>');
                setTimeout(function () {
                    This.html('Apply');
                }, 2000);
            }

        });
        console.log(content);
        return false;
    })

</script>
</body>
</html>

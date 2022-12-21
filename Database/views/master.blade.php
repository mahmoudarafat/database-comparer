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

<script>

    $(document).on('click', '.cpy', function(){
        var id = $(this).data('id');
        copyToClipboard(id)
    });
    $('.cpy').hide();

    function copyToClipboard(id) {
        return false;
        var $temp = $("#cpy-me");
        var temp = document.getElementById('cpy-me');
        var toCpy = document.getElementById('q-' + id).innerHTML
        temp.value = toCpy;

        temp.select();
        document.execCommand("copy");
        // navigator.clipboard.writeText(toCpy);

        alert('copied!')
        return false;
    }


</script>
</body>
</html>

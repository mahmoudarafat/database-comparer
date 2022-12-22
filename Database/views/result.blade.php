@extends('Comparer::master')

@section('content')
    <div class="text-center">
        <h1 class="text-primary pager" style="margin-top:50px;">Compare Results</h1>
    </div>
    <hr>
    <div class="row">

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Add tables to source DB <a class="btn btn-info h5 cpy" data-id="source_create" data-clipboard-action="copy" data-clipboard-target="#q-source_create" href="javascript:void(0)">Copy</a></h3>
            <div class="card">
                <code id="q-source_create">
                    <?php
                    $qu3 = trim( rtrim(str_replace(';', ';<br>', $query['reverseCreate'])));
                    ?>
                    {!! strlen($qu3) ? $qu3 : '<center class="text-center text-muted h5">No tables to Add to SOURCE</center>'  !!}
                </code>
            </div>
        </div>

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Add tables to Your DB <a class="btn btn-info h5 cpy" data-id="current_create" data-clipboard-action="copy" data-clipboard-target="#q-current_create" href="javascript:void(0)">Copy</a></h3>
            <div class="well">
                <div class="card">
                    <code id="q-current_create">
                        <?php
                        $qu1 = trim( rtrim(str_replace(';', ';<br>', $query['currentCreate'])) );
                        ?>
                        {!! strlen($qu1) ? $qu1 : '<center class="text-center text-muted h5">No tables to Add to YOUR DB</center>'  !!}
                    </code>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Update tables in source DB <a class="btn btn-info h5 cpy" data-id="source_update" data-clipboard-action="copy" data-clipboard-target="#q-source_update"  href="javascript:void(0)">Copy</a></h3>
            <div class="card">
                <code id="q-source_update">
                    <?php
                    $qu4 = trim( rtrim(str_replace(';', ';<br>', $query['reverseUpdate']) ));
                    ?>
                    {!! strlen($qu4) ? $qu4 : '<center class="text-center text-muted h5">No tables to be updated in SOURCE</center>'  !!}
                </code>
            </div>
        </div>

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Update tables in Your DB <a class="btn btn-info h5 cpy"  data-clipboard-action="copy" data-clipboard-target="#q-current_updates"   href="javascript:void(0)" data-id="current_updates">Copy</a></h3>
            <div class="card">
                <code id="q-current_updates">
                    <?php
                    $qu2 = trim( rtrim(str_replace(';', ';<br>', $query['currentUpdate'])));
                    ?>
                    {!! strlen($qu2) ? $qu2 : '<center class="text-center text-muted h5">No tables to be updated in YOUR DB</center>'  !!}
                </code>
            </div>
        </div>


    </div>

@stop


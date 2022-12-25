@extends('Comparer::master')

@section('content')
    <div class="text-center">
        <h1 class="text-primary pager" style="margin-top:50px;">Compare Results</h1>
    </div>
    <hr>
    <div class="row">

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Add tables to <u class="text-success">{{ $db['source'] }}</u> DB <a
                    class="btn btn-info h5 cpy" data-id="source_create" data-clipboard-action="copy"
                    data-clipboard-target="#q-source_create" href="javascript:void(0)">Copy</a></h3>
            <div class="card">
                <code id="q-source_create">
                    <?php
                    $qu3 = trim(rtrim(str_replace(';', ';<br>', $query['reverseCreate'])));
                    ?>
                    {!! strlen($qu3) ? $qu3 : '<center class="text-center text-muted h5">No tables to Add to SOURCE</center>'  !!}
                </code>
            </div>
        </div>

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Add tables to <u class="text-danger">{{ $db['current'] }}</u> DB <a
                    class="btn btn-info h5 cpy" data-id="current_create" data-clipboard-action="copy"
                    data-clipboard-target="#q-current_create" href="javascript:void(0)">Copy</a></h3>
            <div class="well">
                <div class="card">
                    <code id="q-current_create">
                        <?php
                        $qu1 = trim(rtrim(str_replace(';', ';<br>', $query['currentCreate'])));
                        ?>
                        {!! strlen($qu1) ? $qu1 : '<center class="text-center text-muted h5">No tables to Add to YOUR DB</center>'  !!}
                    </code>
                </div>
            </div>
        </div>

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Update tables in <u class="text-success">{{ $db['source'] }}</u> DB <a
                    class="btn btn-info h5 cpy" data-id="source_update" data-clipboard-action="copy"
                    data-clipboard-target="#q-source_update" href="javascript:void(0)">Copy</a></h3>
            <div class="card">
                <code id="q-source_update">
                    <?php
                    $qu4 = trim(rtrim(str_replace(';', ';<br>', $query['reverseUpdate'])));
                    ?>
                    {!! strlen($qu4) ? $qu4 : '<center class="text-center text-muted h5">No tables to be updated in SOURCE</center>'  !!}
                </code>
            </div>
        </div>

        <div class="col-sm-6">
            <br>
            <h3 class="text-info">Update tables in <u class="text-danger">{{ $db['current'] }}</u> DB <a
                    class="btn btn-info h5 cpy" data-clipboard-action="copy" data-clipboard-target="#q-current_updates"
                    href="javascript:void(0)" data-id="current_updates">Copy</a></h3>
            <div class="card">
                <code id="q-current_updates">
                    <?php
                    $qu2 = trim(rtrim(str_replace(';', ';<br>', $query['currentUpdate'])));
                    ?>
                    {!! strlen($qu2) ? $qu2 : '<center class="text-center text-muted h5">No tables to be updated in YOUR DB</center>'  !!}
                </code>
            </div>
        </div>

        <div class="col-sm-6">
            <br>

            <h3 class="text-primary text-center">Changed Columns</h3>
            <hr>
            <table class="table table-bordered text-center">
                <thead>
                <th>Table</th>
                <th>Column</th>
                <th><span class="text-success">{{ $db['source'] }}</span> DB</th>
                <th><span class="text-danger">{{ $db['current'] }}</span> DB</th>
                </thead>
                <tbody>

                @forelse($changedColumns as $table => $data)
                    <tr>
                        <td rowspan="{{ sizeof($data) }}">{{ $table }}</td>
                        @foreach($data as $col => $dt)
                            @if($loop->iteration == 1 )
                                <td>{{ $col }}</td>
                                <td>{{ $dt['source']->Type }}</td>
                                <td>{{ $dt['current']->Type }}</td>
                            @endif
                        @endforeach
                    </tr>
                    @foreach($data as $col => $dt)
                        @if($loop->iteration > 1 )
                            <tr @if($loop->iteration % 2 == 0) class="info" @endif>
                                <td>{{ $col }}</td>
                                <td>{{ $dt['source']->Type }}</td>
                                <td>{{ $dt['current']->Type }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="10"></td>
                    </tr>
                @empty
                    <tr><td colspan="10"></td></tr>
                    <tr>
                        <td colspan="10" class="h3 text-muted">No Changes...</td>
                    </tr>
                    <tr><td colspan="10"></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="col-sm-6">
            <br>
            <h3 class="text-info text-center">Changes Query</h3>
            <hr>
            <table class="table table-bordered">
                <thead>
                <th>Update</th>
                <th>Query</th>
                </thead>
                <tbody>

                <tr>
                    <td><u class="text-success">{{ $db['source'] }}</u> DB
                        <a
                            class="btn btn-info cpy" data-id="dismatch_query" data-clipboard-action="copy"
                            data-clipboard-target="#q-dismatch_query" href="javascript:void(0)">Copy</a>
                    </td>
                    <td>

                    <code id="q-dismatch_query">
                        <?php
                        $qu5 = trim(rtrim(str_replace(';', ';<br>', $query['updateSource'])));
                        ?>
                        {!! $query['misSource'] == true ? $qu5 : '<center class="text-center text-muted h5">No Changes</center>'  !!}
                    </code>
                    </td>
                </tr>

                <tr>
                    <td><u class="text-danger">{{ $db['current'] }}</u> DB
                        <a
                            class="btn btn-info cpy" data-id="rev_dismatch_query" data-clipboard-action="copy"
                            data-clipboard-target="#q-rev_dismatch_query" href="javascript:void(0)">Copy</a>
                    </td>
                    <?php
                        $qu6 = trim(rtrim(str_replace(';', ';<br>', $query['updateCurrent'])));
                    ?>
                    <td>
                        <code id="q-rev_dismatch_query">
                        {!! $query['misCurrent'] == true ? $qu6 : '<center class="text-center text-muted h5">No Changes</center>'  !!}
                        </code>
                    </td>

                </tr>

                </tbody>

            </table>
        </div>
    </div>

@stop


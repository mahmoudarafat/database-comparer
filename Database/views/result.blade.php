@extends('Comparer::master')

@section('content')

    <?php
        $source = json_encode($sourceDB);
        $current = json_encode($currentDB);
    ?>
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
                <a data-id="q-source_create" href="javascript:void(0)" class="apply" data-db="{{ $source }}">Apply</a>
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
                    <a data-id="q-current_create" href="javascript:void(0)" class="apply" data-db="{{ $current }}">Apply</a>
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
                <a data-id="q-source_update" href="javascript:void(0)" class="apply" data-db="{{ $source }}">Apply</a>
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
                <a data-id="q-current_updates" href="javascript:void(0)" class="apply" data-db="{{ $current }}">Apply</a>
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

            <h3 class="text-center text-primary">Changed Columns</h3>
            <hr>
            <table class="table text-center table-bordered">
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
            <h3 class="text-center text-info">Changes Query</h3>
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
                        <a data-id="q-dismatch_query" href="javascript:void(0)" class="apply" data-db="{{ $source }}">Apply</a>
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
                        <a data-id="q-rev_dismatch_query" href="javascript:void(0)" class="apply" data-db="{{ $current }}">Apply</a>
                        <code id="q-rev_dismatch_query">
                        {!! $query['misCurrent'] == true ? $qu6 : '<center class="text-center text-muted h5">No Changes</center>'  !!}
                        </code>
                    </td>

                </tr>

                </tbody>

            </table>
        </div>
    </div>

    <!-- Migration Sections -->
    <div class="row">
        <div class="col-sm-12">
            <br>
            <h3 class="text-center text-success">Laravel Migration Files</h3>
            <hr>
            
            @if(!empty($migrations))
                @foreach($migrations as $key => $migration)
                    <div class="mb-4 card" style="height:auto;">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <span class="badge badge-primary">{{ ucwords(str_replace('_', ' ', $migration['type'])) }}</span>
                                <code>{{ $migration['filename'] }}</code>
                                <div class="float-right">
                                    <a class="btn btn-success btn-sm cpy" 
                                       data-id="migration_{{ $key }}" 
                                       data-clipboard-action="copy"
                                       data-clipboard-target="#migration-{{ $key }}" 
                                       href="javascript:void(0)">
                                        <i class="fa fa-copy"></i> Copy
                                    </a>
                                    <a class="btn btn-primary btn-sm download-migration" 
                                       data-filename="{{ $migration['filename'] }}"
                                       data-content="{{ base64_encode($migration['content']) }}"
                                       href="javascript:void(0)">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </div>
                            </h5>
                            <small class="text-muted">{{ $migration['description'] }}</small>
                        </div>
                        <div class="card-body">
                            <pre id="migration-{{ $key }}" style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;  overflow-y: auto;"><code>{{ $migration['content'] }}</code></pre>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card">
                    <div class="text-center card-body">
                        <h5 class="text-muted">No migrations needed - databases are already in sync!</h5>
                    </div>
                </div>
            @endif
        </div>
    </div>

@stop


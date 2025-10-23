@extends('Comparer::master')
@section('title')
    Database Comparer
@endsection
@section('content')

    <div class="text-center">
        <h1 class="text-primary pager" style="margin-top:50px;">Database Comparator</h1>
        <hr>
        <form id="form-compare" method="post" action="{{ request()->url() }}">
            {{ csrf_field() }}
            <table class="table table-bordered">
                <thead>
                <th>Source Database</th>
                <th>Your Database</th>
                </thead>
                <tbody>
                <tr>
                    @foreach(['source', 'current'] as $item)
                    <td>
                        <ul class="list-unstyled">
                            <li>
                                <label>Host: </label><input required name="{{ $item }}[host]" class="form-control" value="127.0.0.1" />
                            </li>
                            <li>
                                <label>PORT: </label><input required name="{{ $item }}[port]" class="form-control" value="3306" />
                            </li>
                            <li>
                                <label>Database: </label><input required name="{{ $item }}[db]" class="form-control" value="" />
                            </li>
                            <li>
                                <label>Username: </label><input required name="{{ $item }}[user]" class="form-control" value="homestead" />
                            </li>
                            <li>
                                <label>Password: </label><input name="{{ $item }}[pass]" class="form-control" value="secret" />
                            </li>
                            <li class="row" style="margin-top: 10px;">
                                <label for="{{ $item }}-auto" class="col-sm-6">Auto Update Tables:</label>
                                <input  type="checkbox" id="{{ $item}}-auto" name="{{ $item }}[auto-update]" class="checkbox col-sm-2" value="1" />
                            </li>
                        </ul>
                    </td>
                    @endforeach
                </tr>

                <tr>
                    <td colspan="3" id="radios">
                        <h6 class="text-danger">Works only if auto update is Enabled!</h6>
                        <ul class="list-unstyled" style="display: flex">
                            <li style="padding-right: 30px;"><b>Data Types:</b></li>
                            <li style="display: contents;" id="no-datatype-update-div">
                                <label for="no-datatype-update" class="col-sm-3">Don't Update:
                                <input  type="radio" id="no-datatype-update" name="datatype-update" checked value="no" /></label>
                            </li>
                            <li style="    display: contents;" id="source-datatype-update-div">
                                <label for="source-datatype-update" class="col-sm-3">Update Source:
                                    <input  type="radio" id="source-datatype-update" name="datatype-update" value="source" /></label>
                            </li>
                            <li style="    display: contents;" id="current-datatype-update-div">
                                <label for="current-datatype-update" class="col-sm-3">Update Current:
                                    <input  type="radio" id="current-datatype-update" name="datatype-update" value="current" /></label>
                            </li>
                        </ul>
                    </td>
                </tr>


                <tr>
                    <td colspan="3" id="default-values">
                        <h6 class="text-danger">What to do with columns without default values</h6>
                        <ul class="list-unstyled" style="display: flex">
                            <li style="padding-right: 30px;"><b>Update to:</b></li>
                            <li style="display: contents;" id="no-default-update-div">
                                <label for="no-default-update" class="col-sm-3">Don't Update:
                                    <input  type="radio" id="no-default-update" name="default-update" checked value="no" /></label>
                            </li>
                            <li style="    display: contents;" id="source-datatype-update-div">
                                <label for="null-default-update" class="col-sm-3">Update To <b>NULL</b>:
                                    <input  type="radio" id="null-default-update" name="default-update" value="null" /></label>
                            </li>
                            <li style="display: contents;" id="string-default-update-div">
                                <label for="string-default-update" class="col-sm-3">Update to <b>Empty String</b>:
                                    <input  type="radio" id="string-default-update" name="default-update" value="string" /></label>
                            </li>
                        </ul>
                    </td>
                </tr>

                </tbody>
            </table>
        </form>

        <a href="javascript:void(0)" id="do-compare" class="btn btn-primary" >Compare</a>

    </div>

@endsection


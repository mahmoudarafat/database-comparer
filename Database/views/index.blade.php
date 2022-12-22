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
                    <td>
                        <ul class="list-unstyled">
                            <li>
                                <label>Host: </label><input required name="source[host]" class="form-control" value="127.0.0.1" />
                            </li>
                            <li>
                                <label>PORT: </label><input required name="source[port]" class="form-control" value="3306" />
                            </li>
                            <li>
                                <label>Database: </label><input required name="source[db]" class="form-control" value="" />
                            </li>
                            <li>
                                <label>Username: </label><input required name="source[user]" class="form-control" value="root" />
                            </li>
                            <li>
                                <label>Password: </label><input name="source[pass]" class="form-control" value="" />
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="list-unstyled">
                            <li>
                                <label>Host: </label><input required name="current[host]" class="form-control" value="127.0.0.1" />
                            </li>
                            <li>
                                <label>PORT: </label><input required name="current[port]" class="form-control" value="3306" />
                            </li>
                            <li>
                                <label>Database: </label><input required name="current[db]" class="form-control" value="" />
                            </li>
                            <li>
                                <label>Username: </label><input required name="current[user]" class="form-control" value="root" />
                            </li>
                            <li>
                                <label>Password: </label><input name="current[pass]" class="form-control" value="" />
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


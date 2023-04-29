@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <form  method="POST" action='{{route('client.import_csv')}}' enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input type="file" class="form-control" data-icon="false" name='uploaded_file'  accept=".csv">
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Import</button>
                </form>
            </div>
        </div>
    </div>
@endsection

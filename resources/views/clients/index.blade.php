@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-8">
                <h1>Clients</h1>
                <a class="btn btn-info col-2 mb-2" href='{{url("clients/create")}}'>Import Clients</a>
                <table class="table  table-bordered table-hover">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Category</th>
                        <th scope="col">Firstname</th>
                        <th scope="col">Lastname</th>
                        <th scope="col">Email</th>
                        <th scope="col">Gender</th>
                        <th scope="col">BirthDate</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($clients) && count($clients) > 0)
                        @foreach($clients as $client)
                            <tr>
                                <th scope="row">{{$client->id}}</th>
                                <td>{{$client->category}}</td>
                                <td>{{$client->firstname}}</td>
                                <td>{{$client->lastname}}</td>
                                <td>{{$client->email}}</td>
                                <td>{{$client->gender}}</td>
                                <td>{{$client->birthDate}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="10">There is no clients.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                {{ $clients->appends(request()->all())->links() }}
                @if(isset($clients) && count($clients) > 0)
                    <form action="{{route('client.export')}}" method="post">
                        @csrf
                     <?php
                        $currentRequest = request()->all();
                     ?>
                        <input type="hidden" value="{{$currentRequest && isset($currentRequest['filter_by_category']) ? $currentRequest['filter_by_category']: ''}}" name="filter_by_category">
                        <input type="hidden" value="{{$currentRequest && isset($currentRequest['filter_by_gender']) ? $currentRequest['filter_by_gender']: ''}}" name="filter_by_gender">
                        <input type="hidden" value="{{$currentRequest && isset($currentRequest['filter_by_birthDate']) ? $currentRequest['filter_by_birthDate']: ''}}" name="filter_by_birthDate">
                        <input type="hidden" value="{{$currentRequest && isset($currentRequest['filter_by_age']) ? $currentRequest['filter_by_age']: ''}}" name="filter_by_age">
                        <input type="hidden" value="{{$currentRequest && isset($currentRequest['filter_by_age_range']) ? $currentRequest['filter_by_age_range']: ''}}" name="filter_by_age_range">
                        <button class="btn btn-info col-2 mb-2" id="exportCSV" type="submit">Export as CSV</button>
                    </form>
                @endif
            </div>
            <div class="col-4">
                <h1>Filters</h1>
                <form action="{{ route('client.filter') }}" method="get">
                    <div class="form-group mb-2">
                        <label for="categoryFilter">
                            Category filter
                            <input name="filter_by_category" id="categoryFilter" type="text" class="form-control" value="{{ request()->get('filter_by_category') }}">
                        </label>
                    </div>
                    <div class="form-group mb-2">
                        <label for="genderFilter">
                            Gender filter
                            <input name="filter_by_gender" id="genderFilter" type="text" class="form-control"  value="{{ request()->get('filter_by_gender') }}">
                        </label>
                    </div>
                    <div class="form-group mb-2">
                        <label for="birthDayFilter">
                            Birthday filter
                            <input name="filter_by_birthDate" id="birthDayFilter" type="date" class="form-control"  value="{{ request()->get('filter_by_birthDate') }}">
                        </label>
                    </div>
                    <div class="form-group mb-2">
                        <label for="ageFilter">
                            Age filter
                            <input name="filter_by_age" id="ageFilter" type="number" class="form-control" min="1" max="100"  value="{{ request()->get('filter_by_age') }}">
                        </label>
                    </div>
                    <label>Age range filter</label>
                    <div class="row">
                        <div class="form-group col-md-4">
                            <input class="form-control" type="number" name="first_age" min="1" max="100" value="{{ request()->get('first_age') }}" placeholder="From" >
                        </div>
                        <div class="form-group col-md-4">
                            <input class="form-control" type="number" name="second_age" min="1" max="100" value="{{ request()->get('second_age') }}" placeholder="To">
                        </div>
                    </div>
                    <button class="btn btn-info btn-block mt-2" type="submit">Apply filters</button>
                </form>
            </div>
        </div>
    </div>
@endsection

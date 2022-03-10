@extends('layouts.user')
@section('title', __('User Transactions'))


@section('content')

@section('content')

@include('layouts.messages')

<div class="card content-area content-area-mh">
    <div class="card-innr">
        <div class="card-head">
            <h4 class="card-title">{{__('Token list')}}</h4>
        </div>
        <div class="gaps-1x"></div>

            <div class="table-responsive">
                <table class="data-table dt-filter-init">

                    <!-- Table Headings -->
                    <thead>
                    <tr class="data-item data-head">
                        <th class="data-col">Select Token</th>
                        <th class="data-col">Symbol</th>
                        <th class="data-col">Token Name</th>
                        <th class="data-col">Short Description</th>
                        <th class="data-col">Description</th>
                        <th class="data-col">More Info</th>
                    </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($tkns as $tkn)
                            <tr>
                                <td class="data-col">
                                    <a type="button" class="btn btn-auto btn-primary btn-sm" href="{{ route('token', $tkn->token_symbol) }}"><span>Use This Token</span></a>
                                </td>

                                <td class="data-col">
                                    <div>{{ $tkn->token_symbol }}</div>
                                </td>

                                <td class="data-col">
                                    <div>{{ $tkn->name }}</div>
                                </td>
                                <td class="data-col" style="word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                    <div>{{ $tkn->short_description }}</div>
                                </td>
                                <td class="data-col" style="word-wrap: break-word;min-width: 160px;max-width: 160px;">
                                    <span class="sub ">
                                    {{ $tkn->description }}
                                    </span>
                                </td>
                                <td class="data-col" >
                                    <div><a href="{{ $tkn->url_more_info }}">{{ $tkn->url_more_info }}</a></div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>{{-- .card-innr --}}
</div>{{-- .card --}}
@endsection
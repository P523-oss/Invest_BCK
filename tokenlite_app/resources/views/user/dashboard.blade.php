@extends('layouts.user')
@section('title', __('User Dashboard'))
@php
$has_sidebar = false;
$base_currency = base_currency();
@endphp

@section('content')
<div class="content-area user-account-dashboard">
    @include('layouts.messages')
    @if(session()->has('currentsymbol'))
        <div class="row">
            {{-- <div class="col-lg-4 col-md-12"> --}}
            {{-- <div class="col-lg-4 col-md-6"> --}}
            {{-- <div class="col-lg-4 col-md-6"> --}}
            <div class="col-lg-4 col-md-12">
                {!! UserPanel::user_balance_card($contribution, ['vers' => 'side', 'class'=> 'card-full-height']) !!}
            </div>
            <div class="col-lg-4 col-md-6">
                {!! UserPanel::user_token_block('', ['vers' => 'buy']) !!}
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="account-info card card-full-height">
                    <div class="card-innr">
                        {!! UserPanel::user_account_status() !!}
                        <div class="gaps-2x"></div>
                        {!! UserPanel::user_account_wallet() !!}
                    </div>
                </div>
            </div>
            {{-- <div class="col-lg-4 col-md-6">
                {!! UserPanel::token_sales_progress('',  ['class' => 'card-full-height']) !!}
            </div> --}}
        </div>
    @endif     
    <div class="row">
        @if(get_page('home_top', 'status') == 'active')
            {{-- <div class="col-12 col-lg-7"> --}}
            <div class="col-12 col-lg-12">
                @if(session()->has('currentsymbol'))
                    {!! UserPanel::content_block('token_info', ['image' => 'welcome.png', 'class' => 'card-full-height']) !!}
                @else
                    {!! UserPanel::content_block('welcome', ['image' => 'welcome.png', 'class' => 'card-full-height']) !!}
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
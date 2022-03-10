@extends('layouts.user')
@section('title', __('Self Certification Completed'))
@php
($has_sidebar = false)
@endphp
@section('content')
<div class="content-area card">
    <div class="card-innr">
        @include('layouts.messages')
        <div class="card-head d-flex justify-content-between">
            <h4 class="card-title card-title-md">{{__('Self Certification Completed')}}</h4>
        </div>
        <div class="card-text" style="min-height:200px;">
            <div class="alert alert-success" role="alert">
                Congratulations. You have completed the self certification process.
              </div>
            <p>{{__('Thanks for filling our self certification form.')}} </p>
        </div>
        <div class="gaps-1x"></div>
    </div>{{-- .card-innr --}}
</div>{{-- .card --}}
@endsection

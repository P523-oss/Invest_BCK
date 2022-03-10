@extends('layouts.user')
@section('title', __('Agreement Signage'))
@php
$has_sidebar = false;

$signed_title = ($signed_status !== NULL && isset($_GET['thank_you'])) ? __('Begin your Agreement Signage') : __('Agreement Signage');
$signed_desc = ($signed_status !== NULL && isset($_GET['thank_you'])) ? __('Sign the Agreement.') :
    __('To comply with this Seller terms and conditions each participant is required to digitally sing an Agreement Contract. Please, complete our fast and secure signing process to participate in token offerings.');
@endphp

@section('content')
<div class="page-header page-header-kyc">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7 text-center">
            <h2 class="page-title">{{ $signed_title }}</h2>
            <p class="large">{{ $signed_desc }}</p>
        </div>
    </div>
</div>
<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-9">
        <div class="content-area card user-account-pages page-kyc">
            <div class="card-innr">
                @include('layouts.messages')
                <div class="kyc-status card mx-lg-4">
                    <div class="card-innr">
                        Status: {{ $signed_status}}
                        {{-- IF NOT SUBMITED --}}
                        @if($signed_status == NULL || $signed_status == '')
                        <form class="eg" action="" method="post" data-busy="form">
                            @csrf
                            <div class="status status-empty">
                                <div class="status-icon">
                                    <em class="ti ti-files"></em>
                                </div>
                                <span class="status-text text-dark">{{__('You have not digitally signed the Agreement Contract for this Token.')}}{{ (token('before_kyc')=='1') ? __('In order to purchase our tokens, please verify your identity.') : ''}}</span>
                                <p class="px-md-5">{{__('Submit this form to e-sign it. If you have any question, please feel free to contact our support team.')}}</p>
                                <p class="px-md-5">{{__('We are using this data you entered in our system:')}}</p>

                                <div class="form-group">
                                    <div class="input-item input-with-label">
                                        <label for="signer_email">Signer Email</label>
                                        <div class="input-wrap">
                                            <input type="email" aria-disabled="true" disabled class="form-control input-bordered valid" id="signer_email" name="signer_email"
                                            value="{{ Auth::user()->email }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-item input-with-label">
                                        <label for="signer_name">Signer Name</label>
                                        <div class="input-wrap">
                                            <input type="text" aria-disabled="true" disabled class="form-control input-bordered valid" id="signer_name" name="signer_name"
                                            value="{{ Auth::user()->name }}">
                                        </div>
                                    </div>
                                </div>
                            {{-- <input type="hidden" name="csrf_token" value="{{ csrf_token() }}"/> --}}
                            <button type="submit" class="btn btn-primary">{{__('Start digital signage process')}}</button>
                            <div><br /><br />
                                <a href="{{ route('user.account') }}" class="">{{__('Go to Edit your Profile')}}</a>
                            </div>
                        </div>
                        </form>
                        @endif
                        {{-- IF SUBMITED @Thanks --}}
                        @if($signed_status == 'submited'))
                        <div class="status status-thank px-md-5">
                            <div class="status-icon">
                                <em class="ti ti-check"></em>
                            </div>
                            <span class="status-text large text-dark">{{__('The digital signage has been started')}}</span>
                            <p class="px-md-5">{{__('Please go to your email and follow the instructions. Once our team verified your digital signage, you will be notified by email. ')}}</p>
                        </div>
                        @endif

                        {{-- IF SUBMIT ERROR --}}
                        @if($signed_status == 'submit-error')
                        <div class="status status-process">
                            <div class="status-icon">
                                <em class="ti ti-infinite"></em>
                            </div>
                            <span class="status-text text-dark">{{__('Error:')}}</p>
                                <span class="status-text text-dark">{{ $msg_error }}</p>
                        </div>
                        @endif

                        {{-- IF REJECTED/MISSING --}}
                        @if($signed_status !== NULL && ($signed_status == 'missing' || $signed_status == 'rejected') && !isset($_GET['thank_you']))
                        <div class="status status{{ ($signed_status == 'missing') ? '-warnning' : '-canceled' }}">
                            <div class="status-icon">
                                <em class="ti ti-na"></em>
                            </div>
                            <span class="status-text text-dark">
                                {{ $signed_status == 'missing' ? __('We found some information to be missing.') : __('Sorry! Your application was rejected.') }}
                            </span>
                            <p class="px-md-5">{{__('In our verification process, we found information that is incorrect or missing. Please resubmit the form. In case of any issues with the submission please contact our support team.')}}</p>
                            <a href="{{ route('user.kyc.application') }}?state={{ $signed_status == 'missing' ? 'missing' : 'resubmit' }}" class="btn btn-primary">{{__('Submit Again')}}</a>
                        </div>
                        @endif

                        {{-- IF VERIFIED --}}
                        @if($signed_status !== NULL && $signed_status == 'approved' && !isset($_GET['thank_you']))
                        <div class="status status-verified">
                            <div class="status-icon">
                                <em class="ti ti-files"></em>
                            </div>
                            <span class="status-text text-dark">{{__('Your identity verified successfully.')}}</span>
                            <p class="px-md-5">{{__('One of our team members verified your identity. Now you can participate in our token sale. Thank you.')}}</p>
                            <div class="gaps-2x"></div>
                            <a href="{{ route('user.token') }}" class="btn btn-primary">{{__('Purchase Token')}}</a>
                        </div>
                        @endif

                    </div>
                </div>{{-- .card --}}
            </div>
        </div>
        {!! UserPanel::kyc_footer_info() !!}
    </div>
</div>
@endsection

@extends('layouts.user')
@section('title', __('User Self Certification'))

@php
$has_sidebar = true;

@endphp

@section('content')
@include('layouts.messages')
<div class="card content-area content-area-mh">
    <div class="card-innr">
        <div class="card-head">
            <h4 class="card-title">{{__('User Self Certification')}}</h4>
        </div>
        <div class="gaps-1x"></div>
        <form class="register-form" method="POST" action="{{ route('user.selfcertify1') }}" id="selfcertify1">
            @csrf
            @include('layouts.messages')
            <div class="alert alert-warning" role="alert">
                <strong>This is the final stage of your account creation process.</strong>
            </div>
            <div class='small border p-3'>
                <p>
                Self certification to create your account.
                </p><p>
                Please complete the information below and agree to the relevant certification criterion.<br/>
                </p><p>
                Before any investment options are enabled on this account you will be required to complete
                KYC/AML and investor status validation process.<br/>
                </p>
            </div>
            <div class="input-item mt-3">
                Hello, <strong>{{ Auth::user()->name}}</strong>. Your user type is <strong>{{$userTypeDescription}}</strong>. Your Tokenizer ID is {{ Auth::id() }}.
            </div>

            <div class="input-item">
                <label for="title">Country of residence <small>(where you are living)</small></label>
                <select name="country_residence" id="country_residence" class="input-bordered">
                    @foreach($countries as $country)
                    <option value="{{ ($country->id) }}"> {{ $country->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-item">
                <label for="title">Country of citizenship <small>(from your passport, if you have more than one passport and one is from United States, please enter United States)</small></label>
                <select name="country_citizenship" id="country_citizenship" class="input-bordered">
                    @foreach($countries as $country)
                    <option value="{{ ($country->id) }}"> {{ $country->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="input-item">
                <label for="title">Street address</label>
                <input type="text" placeholder="{{__('Street address')}}" class="input-bordered{{ $errors->has('address') ? ' input-error' : '' }}" name="address" value="{{ old('address') }}"data-msg-required="{{ __('Required.') }}" data-msg-required="{{ __('Enter valid street address.') }}" required>
            </div>
            <div class="input-item">
                <label for="title">City</label>
                <input type="text" placeholder="{{__('City')}}" class="input-bordered{{ $errors->has('city') ? ' input-error' : '' }}" name="city" value="{{ old('city') }}"data-msg-required="{{ __('Required.') }}" data-msg-required="{{ __('Enter valid city.') }}" required>
            </div>
            <div class="input-item">
                <label for="title">State/Province</label>
                <input type="text" placeholder="{{__('State/Province')}}" class="input-bordered{{ $errors->has('state') ? ' input-error' : '' }}" name="state" value="{{ old('state') }}"data-msg-required="{{ __('Required.') }}" data-msg-required="{{ __('Enter valid State/Province.') }}" required>
            </div>
            <div class="input-item">
                <label for="title">Postal Code</label>
                <input type="text" placeholder="{{__('Postal Code')}}" class="input-bordered{{ $errors->has('postalcode') ? ' input-error' : '' }}" name="postalcode" value="{{ old('postalcode') }}"data-msg-required="{{ __('Required.') }}" data-msg-required="{{ __('Enter valid Postal code.') }}" required>
            </div>
{{--            <div class="border pt-3 pl-3 pr-3 mb-3">--}}
{{--                <div class="input-item">--}}
{{--                    <textarea class="small input-bordered" rows="6" disabled readonly>{{$message}}</textarea>--}}
{{--                </div>--}}

{{--                <div class="input-item text-left">--}}
{{--                    <input name="agree" class="input-checkbox input-checkbox-md" id="agree" type="checkbox" required="required" data-msg-required="{{ __("You should accept this condition.") }}">--}}
{{--                    <label for="agree">{{ __('I agree.') }}</label>--}}
{{--                </div>--}}
{{--            </div>--}}

                <div class="gaps-1x"> </div>
                <p>This information is needed for us to comply with SEC and state securities regulations. We ask the following questions to determine if the amount you may invest is limited by law.
                </p>
                <br>
                <p>Are you an "accredited" investor (meaning do you earn over $200,000 per year, have a net worth of $1m or more, or are an institutional investor)?</p>
                <div style="float:left;width:50px;"><input class="accradio" type="radio" id="accredited1" name="accredited" value="1" checked>
                    <label for="accredited">Yes</label></div>
                <div style="float:left;width:50px;"><input class="accradio" type="radio" id="accredited0" name="accredited" value="0">
                    <label for="accredited0">No</label></div><br>



                <div id="yes_accredited" style="float:left;width:100%;display:none;">

                    <p><strong>I qualify as an accredited investor as follows:</strong></p>
                    <div style="width:100%;">
                        <li>
                            <input type="checkbox" id="qualified1" name="qualified1" value="qualified1">
                            <label style="display:inline;" for="qualified1">I have an individual net worth, or joint net worth with my spouse, that exceeds $1 million including retirement accounts, but excluding the net value of my primary residence.</label>
                        </li>
                    </div>
                    <div style="width:100%;">
                        <li>
                            <input type="checkbox" id="qualified2" name="qualified2" value="qualified2">
                            <label style="display:inline;" for="qualified2">I am an individual with income of over $200,000 in each of the last two years, or joint income with my spouse exceeding $300,000 in those years, and I reasonably expect at least the same this year.</label>
                        </li>
                    </div>
                    <div style="width:100%;">
                        <li>
                            <input type="checkbox" id="qualified3" name="qualified3" value="qualified3">
                            <label style="display:inline;" for="qualified3">I am an individual with certifications or credentials issued by an accredited educational institution including, but not limited to, order holders in good standing of the FINRA Series 7, Series 65 and Series 82 licenses.
                            </label>
                        </li>
                    </div>
                    <div style="width:100%;">
                        <li>
                            <input type="checkbox" id="qualified4" name="qualified4" value="qualified4">
                            <label style="display:inline;" for="qualified4">This is a private fund and I am a knowledgeable employee of this fund.</label>
                        </li>
                    </div>
                    <div style="width:100%;">
                        <li>
                            <input type="checkbox" id="qualified5" name="qualified5" value="qualified5">
                            <label style="display:inline;" for="qualified5">I have pooled my finances with my spousal equivalent and qualify as an accredited investor.
                            </label>
                        </li>
                    </div>

                </div>
                <div id="no_accredited" style="float:left;width:100%;display:none;">

                    <p>As you are not an "accredited investor" the law limits the total amount you can invest based on your annual income and your net worth. Please provide these so that we may determine if the amount you wish to invest is within these limitations.
                    </p>
                    <div style="width:100%;">
                        <div class="input-item input-with-label">
                            <label for="annual_income" class="input-item-label">Annual Income</label>
                            <div class="input-wrap">
                                <input value="0" style="max-width:300px;" class="input-bordered valid" type="text" id="annual_income" name="annual_income" required="required" placeholder="$">
                            </div>
                        </div>
                    </div>
                    <div style="width:100%;">
                        <div class="input-item input-with-label">
                            <label for="net_worth" class="input-item-label">Net Worth</label>
                            <div class="input-wrap">
                                <input value="0" style="max-width:300px;" class="input-bordered valid" type="text" id="net_worth" name="net_worth" required="required" placeholder="$">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="width:100%;float:left;padding:15px 0px;">
                    <h3>Substitute Form W-9 Statement</h3>
                </div>

                <div style="width:100%;float:left;">
                    <p>Under penalty of perjury, by accepting the agreement below I certify that I have provided my correct taxpayer identification number, and:
                            </p>
                    <div style="float:left;width:100%;">
                        <li>
                            <input type="radio" id="us1" name="us" value="1" checked>
                            <label for="accredited">I am a US citizen, US resident, or other US person.</label>
                        </li>
                    </div>

                    <div style="float:left;width:100%;">
                        <li>
                            <input type="radio" id="us0" name="us" value="0">
                            <label for="accredited0">I am not a US citizen, US resident, or other US person.</label>
                        </li>
                    </div><br>

                    <p style="float:left;width:100%;">And:</p>
                    <div style="float:left;">
                        <li>
                            <input type="radio" id="backup0" name="backup" value="0" checked>
                            <label for="accredited">I am exempt from backup withholding.</label>
                        </li>
                    </div>
                    <div style="float:left;">
                        <li>
                            <input type="radio" id="backup1" name="backup" value="1">
                            <label style="display:inline;"  for="accredited0">I am subject to backup withholding. (Only check this option if you've been notified by the IRS that you are subject to backup withholding.)
                            </label>
                        </li>
                    </div><br>
                </div>


            @if( gws('referral_info_show')==1 && get_refer_id() )
            <div class="input-item">
                <input type="text" class="input-bordered" value="{{ __('Your were invited by :userid', ['userid' => get_refer_id(true)]) }}" disabled readonly>
            </div>
            @endif
            <div class="gaps-1x" style="float:left;width:100%;"> </div>
            <button type="submit" class="btn btn-primary btn-block">{{  __('Continue') }}</button>
        </form>
    </div>
</div>
@endsection

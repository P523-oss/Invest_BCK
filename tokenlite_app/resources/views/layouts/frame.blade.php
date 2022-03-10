<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="js">
<head>
    <meta charset="utf-8">
    <meta name="apps" content="{{ site_whitelabel('apps') }}">
    <meta name="author" content="{{ site_whitelabel('author') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="site-token" content="{{ site_token() }}">
    <link rel="shortcut icon" href="{{ site_favicon() }}">
    <title>@yield('title') | {{ site_whitelabel('title') }}</title>
    <link rel="stylesheet" href="{{ asset(style_theme('vendor')) }}">
    <link rel="stylesheet" href="{{ asset(style_theme('user')) }}">
    @stack('header')
@if(get_setting('site_header_code', false))
    {{ html_string(get_setting('site_header_code')) }}
@endif

<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/defi-ventures/tokenizer-common-wrapper@latest/dist/tokenizer-common/tokenizer-common.css">
<script src="https://cdn.jsdelivr.net/gh/defi-ventures/tokenizer-common-wrapper@latest/dist/tokenizer-common.js"></script>
</head>
<body class="user-dashboard page-user theme-modern">
    <tok-main>
        <div>
            <tok-content>

    <div class="topbar-wrap mb-0">
        <div class="topbar is-sticky">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center">
                    <ul class="topbar-nav d-lg-none">
                        <li class="topbar-nav-item relative">
                            <a class="toggle-nav" href="#">
                                <div class="toggle-icon">
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                    <span class="toggle-line"></span>
                                </div>
                            </a>
                        </li>{{-- .topbar-nav-item --}}
                    </ul>{{-- .topbar-nav --}}

                    <a class="topbar-logo" href="{{ url('/') }}">
                        <img height="40" src="{{ site_whitelabel('logo-light') }}" srcset="{{ site_whitelabel('logo-light2x') }}" alt="{{ site_whitelabel('name') }}">
                    </a>
                    <ul class="topbar-nav">
                        <li class="topbar-nav-item relative">
                            @if(session()->has('currentsymbol'))
                                <span class="user-welcome d-none d-lg-inline-block">
                                    <a class="btn btn-success" href="{{ route('user.token-list') }}" data-toggle="tooltip" data-placement="bottom" title="Click to select another Token Symbol">
                                        <span class="user-welcome d-none d-lg-inline-block">Selected Token: </span>
                                        {{-- <div class="token-balance-icon">
                                        <img src="{{ asset('images/symbol/'. unserialize(session('currenttoken'))->logo) }}" alt=""></div> --}}
                                        <span class="">{{session('currentsymbol') }}</span>
                                    </a>
                                </span>
                            @else
                                <span class="user-welcome d-none d-lg-inline-block">
                                    <a class="btn btn-info" href="{{ route('user.token-list') }}" data-toggle="tooltip" data-placement="bottom" title="Click to select an Token Symbol">
                                        View Token Offers
                                    </a>
                                </span>
                            @endif

                            <span class="user-welcome d-none d-lg-inline-block">{{__('Welcome!')}} {{ auth()->user()->name }}</span>
                            <a class="toggle-tigger user-thumb" href="#"><em class="ti ti-user"></em></a>
                            <div class="toggle-class dropdown-content dropdown-content-right dropdown-arrow-right user-dropdown">
                                <span class=" text-muted">{!! UserPanel::user_balance() !!}</span>
                                {!! UserPanel::user_menu_links() !!}
                                {!! UserPanel::user_logout_link() !!}
                            </div>
                        </li>{{-- .topbar-nav-item --}}
                    </ul>{{-- .topbar-nav --}}
                </div>
            </div>{{-- .container --}}
        </div>{{-- .topbar --}}

        <div class="navbar">
            <div class="container">
                <div class="navbar-innr">
                    <ul class="navbar-menu" id="main-nav">
                        <li><a href="{{ route('user.home') }}"><em class="ikon ikon-dashboard"></em> {{__('Dashboard')}}</a></li>
                        <li><a href="{{ route('user.listing') }}"><em class=" ti-briefcase mr-2"></em> {{__('Projects')}}</a></li>
                        <li><a href="{{ route('user.token') }}"><em class="ikon ikon-coins"></em> {{__('Buy Token')}}</a></li>
{{--                        @if(get_page('distribution', 'status') == 'active')--}}
{{--                        <li><a href="{{ route('public.pages', 'distribution') }}"><em class="ikon ikon-distribution"></em> {{ get_page('distribution', 'title') }}</a></li>--}}
{{--                        @endif--}}
                        <li><a href="{{ route('user.transactions') }}"><em class="ikon ikon-transactions"></em> {{__('Transactions')}}</a></li>
                        @if(nio_module()->has('Withdraw') && has_route('withdraw:user.index'))
                        <li {!! ((is_page('withdraw'))? ' class="active"' : '') !!}>
                            <a href="{{ route('withdraw:user.index') }}"><em class="ikon ikon-wallet"></em> Withdraw</a>
                        </li>
                        @endif
                        {{-- <li><a href="{{ route('user.account') }}"><em class="ikon ikon-user"></em> {{__('Profile')}}</a></li> --}}
                        @if(gws('user_mytoken_page') == 1)
                        <li><a href="{{ route('user.token.balance') }}"><em class="ikon ikon-my-token"></em> {{ __('My Token') }}</a></li>
                        @endif
{{--                        @if(gws('main_website_url') != NULL)--}}
{{--                        <li><a href="{{gws('main_website_url')}}" target="_blank"><em class="ikon ikon-home-link"></em> {{__('Main Site')}}</a></li>--}}
{{--                        @endif--}}
                    </ul>
                    @if(!is_kyc_hide())
                    <ul class="navbar-btns">
                        @if(isset(Auth::user()->kyc_info->status) && Auth::user()->kyc_info->status == 'approved')
                        <li><span class="badge badge-outline badge-success badge-lg"><em class="text-success ti ti-files mgr-1x"></em><span class="text-success">{{__('KYC Approved')}}</span></span></li>
                        @else
                        <li><a href="javascript:void(0)" data-toggle="modal" data-target="#select-kyc" class="btn btn-sm btn-outline btn-light"><em class="text-primary ti ti-files"></em><span>{{__('KYC Application')}}</span></a></li>
{{--                        <li><a href="{{ route('user.kyc') }}" class="btn btn-sm btn-outline btn-light"><em class="text-primary ti ti-files"></em><span>{{__('KYC Application')}}</span></a></li>--}}
{{--                            <a href="javascript:void(0)" data-toggle="modal" data-target="#edit-wallet" class="user-wallet' . $btn_cls . '">' . ($user->walletAddress != null ? __('Edit') : __('Add')) . '</a>--}}
                        @endif
                    </ul>
                    @endif
                </div>{{-- .navbar-innr --}}
            </div>{{-- .container --}}
        </div>{{-- .navbar --}}
    </div>{{-- .topbar-wrap --}}

    <div class="page-content">
        <div class="xcontainer">
            <div class="row">
                <div class="col-sm-12">

                @yield('content')

                </div>
            </div>
        </div>{{-- .container --}}
    </div>{{-- .page-content --}}

    <div class="footer-bar">
        <div class="container">
            @if(is_show_social('site'))
            <div class="row justify-content-center">
                <div class="col-lg-5 text-center order-lg-last text-lg-right pdb-2x pb-lg-0">
                    {!! UserPanel::social_links() !!}
                </div>
                <div class="col-lg-7">
                    <div class="d-flex align-items-center justify-content-center justify-content-lg-start guttar-15px pdb-1-5x pb-lg-2">
                        {!! UserPanel::copyrights('div') !!}
                        {!! UserPanel::language_switcher() !!}
                    </div>
                    {!! UserPanel::footer_links(null, ['class'=>'align-items-center justify-content-center justify-content-lg-start']) !!}
                </div>
            </div>{{-- .row --}}
            @else
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-7">
                    {!! UserPanel::footer_links(null, ['class'=>'guttar-20px']) !!}
                </div>
                <div class="col-lg-5 mt-2 mt-sm-0">
                    <div class="d-flex justify-content-between justify-content-lg-end align-items-center guttar-15px">
                        {!! UserPanel::copyrights('div') !!}
                        {!! UserPanel::language_switcher() !!}
                    </div>
                </div>
            </div>{{-- .row --}}
            @endif
        </div>{{-- .container --}}
    </div>{{-- .footer-bar --}}
    @yield('modals')
    <div id="ajax-modal"></div>
    <div class="page-overlay">
        <div class="spinner"><span class="sp sp1"></span><span class="sp sp2"></span><span class="sp sp3"></span></div>
    </div>

@if(gws('theme_custom'))
    <link rel="stylesheet" href="{{ asset(style_theme('custom')) }}">
@endif
    <script>
        var base_url = "{{ url('/') }}",
        {!! (has_route('transfer:user.send')) ? 'user_token_send = "'.route('transfer:user.send').'",' : '' !!}
        {!! (has_route('withdraw:user.request')) ? 'user_token_withdraw = "'.route('withdraw:user.request').'",' : '' !!}
        {!! (has_route('user.ajax.account.wallet')) ? 'user_wallet_address = "'.route('user.ajax.account.wallet').'",' : '' !!}
        csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    </script>
    <script src="{{ asset('assets/js/jquery.bundle.js').css_js_ver() }}"></script>
    <script src="{{ asset('assets/js/script.js').css_js_ver() }}"></script>
    <script src="{{ asset('assets/js/app.js').css_js_ver() }}"></script>
    @stack('footer')
    <script type="text/javascript">
        @if (session('resent'))
        show_toast("success","{{ __('A fresh verification link has been sent to your email address.') }}");
        @endif
    </script>
    @if(get_setting('site_footer_code', false))
    {{ html_string(get_setting('site_footer_code')) }}
    @endif

        </tok-content>
        <tok-apps-side-menu></tok-apps-side-menu>
        <tok-footer></tok-footer>
    </div>
</tok-main>
</body>
</html>

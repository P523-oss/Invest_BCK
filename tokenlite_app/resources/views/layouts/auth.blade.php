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

	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="website" />
	<meta property="og:title" content="Tokenizer : Investement Banking on Blockchain with Asset-backed-Tokens" />
	<meta property="og:description" content="Tokenizer is a blockchain investment banking platform with an end-to-end DeFi infrastructure for fundraising, investing, and trading Asset-Backed tokens." />
	<meta property="og:url" content="https://invest.tokenizer.cc/" />
	<meta property="og:site_name" content="Tokenizer - Investment Banking on Blockchain" />
	<meta property="article:modified_time" content="2020-05-26T11:25:37+00:00" />
	<meta property="og:image" content="https://invest.tokenizer.cc/images/welcome2.png" />
	<meta property="og:image:width" content="400" />
	<meta property="og:image:height" content="400" />
	<meta name="twitter:card" content="summary_large_image" />

    <title>@yield('title') | {{ site_whitelabel('title') }}</title>
    <link rel="stylesheet" href="{{ asset(style_theme('vendor')) }}">
    <link rel="stylesheet" href="{{ asset(style_theme('user')) }}">
    @if( recaptcha() )
    <script src="https://www.google.com/recaptcha/api.js?render={{ recaptcha('site') }}"></script>
    @endif
    @stack('header')
    @if(get_setting('site_header_code', false))
    {{ html_string(get_setting('site_header_code')) }}
    @endif
    <style >
        tok-main>div.with-apps-side-menu {
            position: static !important;
            margin-left: var(--apps-side-menu-width) !important;
        }
        @media (max-width: 600px) {
            tok-main>div.with-apps-side-menu {
                margin-left: var(--apps-side-menu-mobile-width) !important;
            }
        }
    </style>

<!-- Tokenizer sidebar -->
<script type="module" src="https://cdn.jsdelivr.net/gh/defi-ventures/tokenizer-common-wrapper@latest/dist/tokenizer-common/tokenizer-common.esm.js"></script>
<script nomodule="" src="https://cdn.jsdelivr.net/gh/defi-ventures/tokenizer-common-wrapper@latest/dist/tokenizer-common/tokenizer-common.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/defi-ventures/tokenizer-common-wrapper@latest/dist/tokenizer-common/tokenizer-common.css">



</head>
@php
$auth_layout = (gws('theme_auth_layout', 'default'));
$logo_light = ($auth_layout=='center-dark') ? 'logo-light' : 'logo';
$body_class = ($auth_layout=='center-dark'||$auth_layout=='center-light') ? ' page-ath-alt' : '';
$body_bgc   = ($auth_layout=='center-dark') ? ' bg-secondary' : '';
$wrap_class = ($auth_layout=='default') ? ' flex-row-reverse' : '';

$header_logo = '<div class="page-ath-header"><a href="'.url('/').'" class="page-ath-logo"><img class="page-ath-logo-img" src="'. site_whitelabel($logo_light) .'" srcset="'. site_whitelabel($logo_light.'2x') .'" alt="'. site_whitelabel('name') .'"></a></div>';
@endphp
<body class="page-ath theme-modern page-ath-modern{{ $body_class.$body_bgc }}">
{{--    <tok-main>--}}
        <div>
            <tok-content>


    <div class="page-ath-wrap{{ $wrap_class }}">
        <div class="page-ath-content">
            {!! $header_logo !!}
            @yield('content')

            <div class="page-ath-footer">
                @if(is_show_social('login'))
                    {!! UserPanel::social_links('', ['class' => 'mb-3']) !!}
                    {!! UserPanel::footer_links(['lang' => true], ['class' => 'guttar-20px align-items-center']) !!}
                    {!! UserPanel::copyrights('div') !!}
                @else
                    {!! UserPanel::footer_links(['lang' => true, 'copyright'=>true], ['class' => 'guttar-20px align-items-center']) !!}
                @endif
            </div>
        </div>
        @if ($auth_layout=='default' || $auth_layout=='alter')
        <div class="page-ath-gfx" style="background-image: url({{ asset('images/ath-gfx.png') }});">
            <div class="w-100 d-flex justify-content-center">
                <div class="col-md-8 col-xl-5">
                    {{-- <img src="{{ asset('images/intro.png') }}" alt=""> --}}
                </div>
            </div>
        </div>
        @endif
    </div>

@if(gws('theme_custom'))
    <link rel="stylesheet" hrefx="{{ asset(style_theme('custom')) }}">
@endif
    <script>
        var base_url = "{{ url('/') }}",
        csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        layouts_style = "modern";
    </script>
    <script src="{{ asset('assets/js/jquery.bundle.js').css_js_ver() }}"></script>
    <script src="{{ asset('assets/js/script.js').css_js_ver() }}"></script>
    <script type="text/javascript">
        jQuery(function(){
            var $frv = jQuery('.validate');
            if($frv.length > 0){ $frv.validate({ errorClass: "input-bordered-error error" }); }
        });
    </script>
    @stack('footer')

    @if(get_setting('site_footer_code', false))
    {{ html_string(get_setting('site_footer_code')) }}
    @endif

        <!-- Tokenizer sidebar -->
        </tok-content>
{{--        <tok-apps-side-menu></tok-apps-side-menu>--}}
        <tok-footer></tok-footer>
    </div>
{{--</tok-main>--}}


</body>
</html>

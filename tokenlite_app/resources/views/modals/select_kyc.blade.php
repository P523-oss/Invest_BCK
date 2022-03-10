@php
@endphp
<div class="modal fade" id="modal-select-kyc" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h4 class="popup-title">{{__('Choose KYC')}}</h4>

                <div class="gaps-1x">
                    <p><a href="{{ route('user.kyc') }}" class="btn btn-sm btn-outline btn-light"><em class="text-primary ti ti-files"></em><span>Internal KYC Process</span></a></p>
                </div>
            </div>
        </div>{{-- .modal-content --}}
    </div>{{-- .modal-dialog --}}
</div>
{{-- Modal End --}}
{{--<script type="text/javascript">--}}
{{--    (function($) {--}}
{{--        var $nio_user_wallet = $('#nio-user-wallet-update, #nio-user-wallet-request');--}}
{{--        if ($nio_user_wallet.length > 0) { ajax_form_submit($nio_user_wallet, true, 'ti ti-alert', true); }--}}
{{--    })(jQuery);--}}
{{--</script>--}}

<style>
    #upload-logo, #upload-update-logo{display: none;}
</style>
@extends('layouts.admin')
@section('title', __('Token Setup'))


@section('content')

<div class="page-content">
    <div class="container">
        @include('layouts.messages')

<div class="card content-area content-area-mh">
    <div class="card-innr">
        <div class="card-head has-aside">
            <h4 class="card-title">{{__('Token Sales List')}}</h4>

            <div class="relative d-inline-block d-md-none">
                <a href="#" class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em class="ti ti-more-alt"></em></a>
                <div class="toggle-class dropdown-content dropdown-content-center-left pd-2x">
                    <div class="card-opt data-action-list">
                        <ul class="btn-grp btn-grp-block guttar-20px guttar-vr-10px">
                            <li>
                                <a href="#" class="btn btn-auto btn-sm btn-primary" data-toggle="modal" data-target="#addToken">
                                    <em class="fas fa-plus-circle"> </em>
                                    <span>Add <span class="d-none d-md-inline-block">Token</span></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-opt data-action-list d-none d-md-inline-flex">
                <ul class="btn-grp btn-grp-block guttar-20px">
                    <li>
                        <a href="#" class="btn btn-auto btn-sm btn-primary" data-toggle="modal" data-target="#addToken">
                            <em class="fas fa-plus-circle"> </em><span>Add <span class="d-none d-md-inline-block">Token</span></span>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
        <div class="gaps-1x"></div>

            <div class="table-responsive">
                <table class="data-table dt-filter-init">

                    <!-- Table Headings -->
                    <thead>
                    <tr class="data-item data-head">
                        <th class="data-col">Symbol</th>
                        <th class="data-col">Token Name</th>
                        <th class="data-col">Logo</th>
                        <th class="data-col">Short Description</th>
                        <th class="data-col">Description</th>
                        <th class="data-col">More Info Link</th>
                        <th class="data-col dt-status">Status</th>
                        <th class="data-col dt-status">Hide Token</th>
                        <th class="data-col">More Options</th>
                    </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody>
                        @foreach ($tkns as $tkn)
                            <tr>
                                <td class="data-col">
                                    <div>{{ $tkn->token_symbol }}</div>
                                </td>
                                <td class="data-col">
                                    <div>{{ $tkn->name }}</div>
                                </td>
                                <td class="data-col">
                                    @if($tkn->logo)
                                        <div><img id="original" src="{{ url('/images/symbol/'.$tkn->logo) }}" height="64" width="64"></div>
                                    @endif
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
                                <td class="data-col dt-status">
                                    <span class="dt-status-md badge badge-outline badge-md badge-{{ __status($tkn->status,'status') }}">{{ __status($tkn->status,'text') }}</span>
                                    {{-- <span class="dt-status-sm badge badge-sq badge-outline badge-md badge-{{ __status($tkn->status,'status') }}">{{ substr(__status($tkn->status,'text'), 0, 1) }}</span> --}}
                                </td>
                                <td class="data-col dt-status">
                                    <span>{{$tkn->hidden == 1 ? 'Yes':'No'}}</span>
                                    {{-- <span class="dt-status-sm badge badge-sq badge-outline badge-md badge-{{ __status($tkn->status,'status') }}">{{ substr(__status($tkn->status,'text'), 0, 1) }}</span> --}}
                                </td>
                                <td class="data-col text-right">
                                    <div class="relative d-inline-block">
                                        <a href="#" class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em class="ti ti-more-alt"></em></a>
                                        <div class="toggle-class dropdown-content dropdown-content-top-left">
                                            <ul class="dropdown-list more-menu-{{$tkn->id}}">
                                                <li><a href="{{ route('admin.stages', [$tkn->id] ) }}"><em class="far fa-eye"></em> View Token Sale Stages</a></li>
                                                <li><a href="#" class="token-action" data-toggle="modal" data-target="#editToken" data-token="{{ $tkn->id }}" data-modal="edit" data-actions="update" onclick="viewToken({{ $tkn->id }})"><em class="far fa-edit"></em> Edit</a></li>
                                                
                                                @if ($tkn->hidden == 0)
                                                    <li><a href="#" data-uid="{{ $tkn->id }}" class="token-action" onclick="hideToken({{ $tkn->id }})"><em class="far fa-eye-slash"></em> Hide Token</a></li>
                                                @else
                                                    <li><a href="#" data-uid="{{ $tkn->id }}" class="token-action" onclick="showToken({{ $tkn->id }})"><em class="far fa-eye"></em> Show Token</a></li>
                                                @endif
                                                

                                                {{-- @if(Auth::id() != $tkn->id && $tkn->id != save_gmeta('site_super_admin')->value)  --}}
                                                @if($tkn->status != 'suspend')
                                                <li><a href="#" data-uid="{{ $tkn->id }}" data-type="suspend_token" class="token-action front"><em class="fas fa-ban"></em>Suspend</a></li>
                                                @else
                                                <li><a href="#" id="front" data-uid="{{ $tkn->id }}" data-type="active_token" class="token-action"><em class="fas fa-ban"></em>Active</a></li>
                                                @endif
                                                {{-- @endif --}}
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
    </div>{{-- .card-innr --}}
</div>{{-- .card --}}
</div>
</div>
@endsection


@section('modals')
<div class="modal fade" id="addToken" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h3 class="popup-title">Add New Token</h3>
                <form action="{{ route('admin.ajax.tokens.add') }}" method="POST" class="addtoken-form validate-modern" id="addTokenForm" autocomplete="false">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Token Name</label>
                                <div class="input-wrap">
                                    <input name="name" id="token-name" class="input-bordered" minlength="3" required="required" type="text" placeholder="Token name">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Symbol</label>
                                <div class="input-wrap">
                                    <input name="token_symbol" class="input-bordered" minlength="3" required="required" type="text" placeholder="Token symbol">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Short Description</label>
                        <div class="input-wrap">
                            <input name="short_description" class="input-bordered" minlength="3" required="required" type="text" placeholder="Short Description">
                        </div>
                    </div>
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Description</label>
                        <div class="input-wrap">
                            <textarea  name="description" class="input-bordered" minlength="3" required="required" type="text" placeholder="Description">
                            </textarea>
                        </div>
                    </div>
                    {{-- CLIENT USER --}}
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Client User</label>
                        <div class="input-wrap">
                            <select name="client_id" class="form-control" id="client_user">
                                @foreach ($clients as $client)
                                    <option value="{{$client->id}}">{{$client->name}}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>
                    {{-- HEX COLOR --}}
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Color</label>
                        <div class="input-wrap">
                            <input name="color" class="input-bordered" maxlength="6" type="text" placeholder="Hex Color">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="doc-upload doc-upload-d1">
                                <h6 class="font-mid doc-type-title">{!! __('Upload Here The Logo Image') !!}</h6>
                                <div class="upload-box">
                                    <div class="upload-zone document_one">
                                        <label id="logo-con" for="upload-logo" class="dz-message dz-clickable" style="display: block" data-dz-message>
                                            <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                            <span class="dz-message-text">{{__('(recommended size: 64x64)')}}</span>
                                            <span class="dz-message-or">{{__('or')}}</span>
                                            {{-- <button type="button" class="btn btn-primary">{{__('Select')}}</button> --}}
                                            <label for="upload-logo"  class="btn btn-primary">{{__('Select')}}</label>
                                        </label>
                                    </div>
                                    <input type="file" id="upload-logo" name="logo" accept="image/png">
                                    <input type="hidden" class="logo_base" value="" name="logo_base">
                                    <input type="hidden" name="document_one" accept="application/png" />
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">More Info Url</label>
                                <div class="input-wrap">
                                    <input name="url_more_info" class="input-bordered" minlength="3" required="required" type="text" placeholder="https://">
                                </div>
                            </div>
                            <div class="input-item">
                                <input checked class="input-checkbox input-checkbox-sm" name="status" id="status" type="checkbox">
                                <label for="status">Is the Token Active?</label>
                            </div>
                        </div>
                    </div>
                    <div class="gaps-1x"></div>
                    <div class="text-center">
                        <button class="btn btn-md btn-primary" type="submit">Add Token</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- .modal-content --}}
    </div>
    {{-- .modal-dialog --}}
</div>

<div class="modal fade" id="editToken" tabindex="-1">
    <div class="modal-dialog modal-dialog-md modal-dialog-centered">
        <div class="modal-content">
            <a href="#" class="modal-close" data-dismiss="modal" aria-label="Close"><em class="ti ti-close"></em></a>
            <div class="popup-body popup-body-md">
                <h3 class="popup-title">Edit Token</h3>
                <form action="{{ route('admin.ajax.tokens.add') }}" method="POST" class="addtoken-form validate-modern" id="editTokenForm" autocomplete="false">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Token Name</label>
                                <div class="input-wrap">
                                    <input name="name" id="u_name" class="input-bordered" minlength="3" required="required" type="text" placeholder="Token name">
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">Symbol</label>
                                <div class="input-wrap">
                                    <input name="token_symbol" id="u_symbol" class="input-bordered" minlength="3" required="required" type="text" placeholder="Token symbol">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Short Description</label>
                        <div class="input-wrap">
                            <input name="short_description" id="u_short_desc" class="input-bordered" minlength="3" required="required" type="text" placeholder="Short Description">
                        </div>
                    </div>
                    <div class="input-item input-with-label">
                        <label class="input-item-label">Description</label>
                        <div class="input-wrap">
                            <textarea  name="description" id="u_desc" class="input-bordered" minlength="3" required="required" type="text" placeholder="Description">
                            </textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="doc-upload doc-upload-d1">
                                <h6 class="font-mid doc-type-title">{!! __('Upload Here The Logo Image') !!}</h6>
                                <div class="upload-box">
                                    <div class="upload-zone document_one">
                                        <label id="u-logo-con" for="upload-update-logo" class="dz-message dz-clickable" style="display: block" data-dz-message>
                                            <span class="dz-message-text">{{__('Drag and drop file')}}</span>
                                            <span class="dz-message-text">{{__('(recommended size: 64x64)')}}</span>
                                            <span class="dz-message-or">{{__('or')}}</span>
                                            {{-- <button type="button" class="btn btn-primary">{{__('Select')}}</button> --}}
                                            <label for="upload-update-logo"  class="btn btn-primary">{{__('Select')}}</label>
                                        </label>
                                    </div>
                                    <input type="file" id="upload-update-logo" name="logo" accept="image/png">
                                    <input type="hidden" class="logo_base" value="" name="logo_base">
                                    <input type="hidden" name="document_one" accept="application/png" />
                                </div>
                                <small>Accept : pdf</small>
                                <div class="hiddenFiles"></div>
                                <div class="pt-3">
                                    @if(get_setting('site_white_paper') != '')
                                    <strong>White paper : </strong><a href="{{ route('public.white.paper') }}" target="_blank" >{{get_setting('site_white_paper')}}</a>
                                    @else
                                    <p class="text-light">No file uploaded yet!</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-item input-with-label">
                                <label class="input-item-label">More Info Url</label>
                                <div class="input-wrap">
                                    <input name="url_more_info" id="u_url" class="input-bordered" minlength="3" required="required" type="text" placeholder="https://">
                                </div>
                            </div>
                            <div class="input-item">
                                <input checked class="input-checkbox input-checkbox-sm" name="status" id="status" type="checkbox">
                                <label for="status">Is the Token Active?</label>
                            </div>
                        </div>
                    </div>
                    <div class="gaps-1x"></div>
                    <div class="text-center">
                        <button class="btn btn-md btn-primary" type="submit">Update Token</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- .modal-content --}}
    </div>
    {{-- .modal-dialog --}}
</div>
@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    const tokens = @json($tkns);

    function viewToken(toknID){
        let tokn = tokens.find(data => data.id == toknID)
        $('#editTokenForm').attr('action',`${base_url}/admin/tokens/editToken/${tokn.id}`)
        $('#u_name').val(tokn.name);
        $('#u_symbol').val(tokn.token_symbol);
        $('#u_short_desc').val(tokn.short_description);
        $('#u_desc').val(tokn.description);
        $('#u_url').val(tokn.url_more_info);
        // $('#upload-logo').val(tokn.logo)
        $('#u-logo-con').html( `
            <img style="height:70px;" src="{{asset('images/symbol/${tokn.logo}')}}">
        ` );
    }

    function readURL(input,type='') {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                if(type=='add'){
                    $('#logo-con').html( `
                        <img style="height:70px;" src="${e.target.result}">
                    ` );
                }else if(type=='edit'){
                    $('#u-logo-con').html( `
                        <img style="height:70px;" src="${e.target.result}">
                    ` );
                }
                $(".logo_base").val(e.target.result)
               
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $(document).ready(function(){
        $("#upload-logo").on('change',function(){
            readURL(this,'add');
        });
        $("#upload-update-logo").on('change',function(){
            readURL(this,'edit');
        });
    })

    function hideToken (token_id){
        swal({
            title: "Are you sure?",
            text: "This token will be hidden.",
            icon: "warning",
            buttons: !0,
            dangerMode: !0
        }).then(e => {
            if (e) {
                $.ajax({
                    type:'POST',
                    url:base_url+'/admin/tokens/updateToken',
                    data:{id:token_id,hidden:1},
                    success: function(res){
                        if(res.code == 201){
                            window.location.reload()
                        }
                    }
                })
               
            }
        })
    }
    function showToken(token_id){
        swal({
            title: "Are you sure?",
            text: "This token will be displayed.",
            icon: "warning",
            buttons: !0,
            dangerMode: !0
        }).then(e => {
            if (e) {
                $.ajax({
                    type:'POST',
                    url:base_url+'/admin/tokens/updateToken',
                    data:{id:token_id,hidden:0},
                    success: function(res){
                        if(res.code == 201){
                            window.location.reload()
                        }
                    }
                })
               
            }
        })
    }
   
</script>

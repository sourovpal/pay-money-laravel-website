@extends('user_dashboard.layouts.app')

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('public/plugins/intl-tel-input-17.0.19/css/intlTelInput.min.css') }}">
@endsection

@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid" id="user-profile">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Settings') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="mt-5 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/profile') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active">{{ __('Profile') }}</p>
                        </div>
                    </a>

                    @if ($two_step_verification != 'disabled')
                    <a href="{{ url('/profile/2fa') }}">
                        <div class="mr-4">
                            <p class="text-16 font-weight-400 text-gray-500"> {{ __('2-FA') }} </p>
                        </div>
                    </a>
                    @endif

                    <a href="{{ url('/profile/personal-id') }}">
                        <div class="mr-4">
                            <span class="text-16 font-weight-400 text-gray-500">{{ __('Identity verification') }}</span>
                            @if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' )
                            (<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>{{ __('Verified') }}</span>) 
                            @endif
                        </div>
                    </a>

                    <a href="{{ url('/profile/personal-address') }}">
                        <div class="mr-4">
                            <span class="text-16 font-weight-400 text-gray-500">{{ __('Address verfication') }}</span>
                            @if( !empty(getAuthUserAddress()) && getAuthUserAddress()->status == 'approved' )(<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) @endif
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">{{ __('User profile') }}</h3>
                        <p class="text-gray-500 text-16"> {{ __('Mange your profile') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-11">
                            @include('user_dashboard.layouts.common.alert')
                            <div class="bg-secondary mt-3 shadow p-4">
                                <div class="row">

                                    <!-- avatar -->
                                    <div class="col-lg-12 mt-2">
                                        <div class="row px-4 justify-content-between">
                                            <div class="d-flex flex-wrap">
                                                <div class="pr-3">
                                                    @if(!empty(Auth::user()->picture))
                                                        <img src="{{url('public/user_dashboard/profile/'.Auth::user()->picture)}}" class="w-50p rounded-circle" id="profileImage">
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-50p w-50p" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                      </svg>
                                                    @endif
                                                </div>

                                                <div>
                                                    <h4 class="font-weight-600 text-16">@lang('message.dashboard.setting.change-avatar')</h4>
                                                    <p>@lang('message.dashboard.setting.change-avatar-here')</p>
                                                    <p class="font-weight-600 text-12">*{{__('Recommended Dimension')}}: 100 px * 100 px</p>

                                                    <input type="file" id="file" style="display: none"/>
                                                    <input type="hidden" id="file_name"/>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="uploadAvatar text-md-right">
                                                    <a href="javascript:changeProfile()" id="changePicture"
                                                        class="btn btn-light w-160p btn-border btn-sm mt-2">
                                                        <i class="fa fa-camera" aria-hidden="true"></i>
                                                        &nbsp; @lang('message.dashboard.button.change-picture')
                                                    </a>
                                                    <p id="file-error" style="display: none;"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Change Password -->
                                    <div class="col-lg-12 mt-4">
                                        <div class="row px-4 justify-content-between">
                                            <div class="d-flex flex-wrap">
                                                <div class="pr-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-50p w-50p" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                </div>

                                                <div>
                                                    <h4 class="font-weight-600 text-16">@lang('message.dashboard.setting.change-password')</h4>
                                                    <p>@lang('message.dashboard.setting.change-password-here')</p>
                                                </div>
                                            </div>

                                            <div>
                                                <div class=" text-md-right">
                                                    <button type="button" class="btn w-160p btn-profile mt-2 text-14" data-toggle="modal"
                                                            data-target="#myModal">
                                                            <i class="fas fa-key"></i> @lang('message.dashboard.button.change-password')
                                                    </button>
                                                </div>
                                                <!-- The Modal -->
                                                <div class="modal" id="myModal">
                                                    <div class="modal-dialog modal-lg">
                                                        <form method="post" action="{{url('prifile/update_password')}}" id="reset_password">
                                                            {{ csrf_field() }}

                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title text-18 font-weight-600">@lang('message.dashboard.setting.change-password')</h4>
                                                                    <button type="button" class="close"
                                                                            data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body px-4">

                                                                    <div class="form-group">
                                                                        <label>@lang('message.dashboard.setting.old-password')</label>
                                                                        <input class="form-control" name="old_password" id="old_password" type="password"
                                                                        required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')">
                                                                        @if($errors->has('old_password'))
                                                                            <span class="error">{{ $errors->first('old_password') }}</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="clearfix"></div>

                                                                    <div class="form-group">
                                                                        <label>@lang('message.dashboard.setting.new-password')</label>
                                                                        <input class="form-control" name="password" id="password" type="password"
                                                                        required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                                        minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => '6']) }}">
                                                                        @if($errors->has('password'))
                                                                            <span class="error">{{ $errors->first('password') }}</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="clearfix"></div>
                                                                    <div class="form-group">
                                                                        <label>@lang('message.dashboard.setting.confirm-password')</label>
                                                                        <input class="form-control" name="confirm_password" id="confirm_password" type="password"
                                                                        required oninvalid="this.setCustomValidity('{{ __('This field is required.') }}')"
                                                                        minlength="6" data-min-length="{{ __(':x should contain at least :y characters.', ['x' => __('Password'), 'y' => '6']) }}">
                                                                        @if($errors->has('confirm_password'))
                                                                            <span class="error">{{ $errors->first('confirm_password') }}</span>
                                                                        @endif
                                                                    </div>

                                                                    <div class="mt-1  mb-2">
                                                                        <div class="row m-0">
                                                                            <div>
                                                                                <button type="submit" class="btn btn-primary px-4 py-2" id="password-submit-btn">
                                                                                    <i class="spinner fa fa-spinner fa-spin d-none"></i> <span id="password-submit-btn-text">@lang('message.dashboard.button.submit')</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Modal footer -->

                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if (empty($user->phone))
                                    <div class="row mt-4">
                                        <div class="col-lg-12 mt-2">
                                            <div class="row px-4 justify-content-between">
                                                <div class="d-flex flex-wrap">
                                                    <div class="pr-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-50p w-50p" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                    </div>

                                                    <div>
                                                        <h4 class="font-weight-600 text-16">@lang('message.dashboard.setting.add-phone')</h4>
                                                        <p class="addPhoneBody">@lang('message.dashboard.setting.add-phone-subhead1') <b>+</b> @lang('message.dashboard.setting.add-phone-subhead2')</p>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="uploadAvatar text-md-right">
                                                        <button type="button" class="btn btn-profile btn-border w-160p btn-sm add mt-2" data-toggle="modal" data-target="#add">
                                                            <i class="fa fa-plus" id="modalTextSymbol"></i>
                                                            <span class="modalText">&nbsp; @lang('message.dashboard.setting.add-phone')</span>
                                                        </button>
                                                    </div>

                                                    <!-- Add Phone Modal -->
                                                    <div class="modal" id="add">
                                                        <div class="modal-dialog modal-lg">
                                                            <form method="POST" action="{{ url('profile/complete-phone-verification')}}" id="complete-phone-verification">
                                                                {{ csrf_field() }}
                                                                <input type="hidden" value="{{ $is_sms_env_enabled }}" name="is_sms_env_enabled" id="is_sms_env_enabled" />
                                                                <input type="hidden" value="{{ $checkPhoneVerification }}" name="checkPhoneVerification" id="checkPhoneVerification" />

                                                                <input type="hidden" value="{{ $user->id }}" name="user_id" id="user_id" />
                                                                <input type="hidden" name="hasVerificationCode" id="hasVerificationCode" />

                                                                <input type="hidden" name="defaultCountry" id="defaultCountry" class="form-control">
                                                                <input type="hidden" name="carrierCode" id="carrierCode" class="form-control">
                                                                <input type="hidden" name="countryName" id="countryName" class="form-control">

                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title text-18 font-weight-600">@lang('message.dashboard.setting.add-phone')</h4>
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>

                                                                    <div class="modal-body px-4">
                                                                        <div class="alert text-center" id="message" style="display: none"></div>
                                                                        <div class="form-group">
                                                                            <label id="subheader_text">@lang('message.dashboard.setting.add-phone-subheadertext')</label>
                                                                            <br>
                                                                            <div class="phone_group">
                                                                                <input type="tel" class="form-control" id="phone" name="phone">
                                                                            </div>
                                                                            <span id="phone-number-error"></span>
                                                                            <span id="tel-number-error"></span>

                                                                        </div>
                                                                        <div class="clearfix"></div>

                                                                        <div class="form-group">
                                                                            <label></label>
                                                                            <input id="phone_verification_code" type="text" maxlength="6" class="form-control" name="phone_verification_code"
                                                                            style="display: none;width: 46%;">
                                                                        </div>
                                                                        <div class="clearfix"></div>

                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <div style="margin-top: 6px;">
                                                                                    <span id="static_phone_show" class="static_phone_show" style="display: none;"></span>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-md-2">
                                                                                <button type="button" class="btn btn-profile edit" style="display: none;"><i class="fa fa-edit"></i></button>
                                                                            </div>
                                                                        </div>

                                                                            <!-- Modal footer -->
                                                                        <div class="mb-2">
                                                                            <div class="row justify-content-center">
                                                                                <div>
                                                                                    <button type="button" class="btn btn-primary px-4 py-2 next" id="common_button">@lang('message.dashboard.button.next')</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mt-4">
                                            <div class="row px-4 justify-content-between">
                                                <div>
                                                    <div class="preloader" style="display: none;">
                                                        <div class="preloader-img"></div>
                                                    </div>
                                                    <div class="user-profile-qr-code p-6">
                                                    </div>
                                                </div>

                                                <div>
                                                    <button type="button" class="btn btn-profile btn-border w-160p btn-sm mt-2 update-qr-code" id="qr-code-btn">
                                                        @lang('message.dashboard.button.update-qr-code')
                                                    </button>
                                                    <br>
                                                    <br>
                                                    <button type="button" class="btn btn-border btn-profile btn-sm mt-2 w-160p" id="print-qr-code-btn" style="display: none;">
                                                        {{ __('Print QR Code') }}
                                                    </button>
                                                    <!-- The Modal -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="row mt-4">
                                        <div class="col-lg-12 mt-2">
                                            <div class="row px-4 justify-content-between">
                                                <div class="d-flex flex-wrap">
                                                    <div class="pr-3">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-50p w-50p" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                    </div>

                                                    <div>
                                                        <h4 class="font-weight-600 text-16">@lang('message.dashboard.setting.phone-number')</h4>
                                                        <p class="editPhoneBody">{{ auth()->user()->phone }}</p>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="uploadAvatar">
                                                        <button type="button" class="btn btn-profile btn-border w-160p btn-sm editModal mt-2" data-toggle="modal" data-target="#editModal">
                                                            <i class="fa fa-edit"></i>
                                                            <span>&nbsp; @lang('message.dashboard.setting.edit-phone')</span>
                                                        </button>

                                                    </div>
                                                    <!-- The Modal -->
                                                    <div class="modal" id="editModal">
                                                        <div class="modal-dialog modal-lg">

                                                            <form method="POST" action="{{ url('profile/update-phone-number')}}" id="update-phone-number">
                                                                {{ csrf_field() }}
                                                                <input type="hidden" value="{{ $is_sms_env_enabled }}" name="is_sms_env_enabled" id="is_sms_env_enabled">
                                                                <input type="hidden" value="{{ $user->id }}" name="user_id" id="user_id">

                                                                <input type="hidden" value="{{ $checkPhoneVerification }}" name="editCheckPhoneVerification" id="editCheckPhoneVerification" />
                                                                <input type="hidden" name="editHasVerificationCode" id="editHasVerificationCode" />

                                                                <input type="hidden" name="edit_defaultCountry" id="edit_defaultCountry" value="{{ $user->defaultCountry }}">
                                                                <input type="hidden" name="edit_carrierCode" id="edit_carrierCode" value="{{ $user->carrierCode }}">

                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title text-18 font-weight-600">@lang('message.dashboard.setting.edit-phone')</h4>
                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                    </div>

                                                                    <div class="modal-body px-4 editModalBody">
                                                                        <div class="alert text-center" id="message" style="display: none"></div>

                                                                        <div class="form-group">
                                                                            <label id="subheader_edit_text">@lang('message.dashboard.setting.add-phone-subheadertext')</label>
                                                                            <br>
                                                                            <div class="phone_group">
                                                                                <input type="tel" class="form-control" id="edit_phone" name="edit_phone" value="{{ '+'.$user->carrierCode.$user->phone }}">
                                                                            </div>
                                                                            <span id="edit-phone-number-error"></span>
                                                                            <span id="edit-tel-number-error"></span>
                                                                        </div>
                                                                        <div class="clearfix"></div>

                                                                        <div class="form-group">
                                                                            <label></label>
                                                                            <input id="edit_phone_verification_code" type="text" maxlength="6" class="form-control" name="edit_phone_verification_code"
                                                                            style="display: none;width: 46%;">
                                                                        </div>
                                                                        <div class="clearfix"></div>

                                                                        <div class="row">
                                                                            <div class="col-md-5">
                                                                                <div style="margin-top: 6px;">
                                                                                    <span id="edit_static_phone_show" class="edit_static_phone_show" style="display: none;"></span>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <button type="button" class="btn btn-sm btn-primary px-4 py-2 edit_button_edit" style="display: none;"><i class="fa fa-edit"></i></button>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row justify-content-end pb-2">
                                                                            <div class="pr-4">
                                                                                <button type="button" class="btn btn-cancel px-4 py-2" data-dismiss="modal" id="close">@lang('message.form.cancel')</button>
                                                                            </div>

                                                                            <div class="pr-3">
                                                                                @php
                                                                                    $bothDisabled = ($is_sms_env_enabled == false && $checkPhoneVerification == "Disabled");
                                                                                @endphp

                                                                                @if ($bothDisabled || $checkPhoneVerification == "Disabled")
                                                                                    <button type="button" class="btn btn-primary px-4 py-2 edit_form_submit" id="common_button_update">@lang('message.form.update')</button>
                                                                                @else
                                                                                    <button type="button" class="btn btn-primary px-4 py-2 update" id="common_button_update">@lang('message.dashboard.button.next')</button>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-4">
                                            <div class="row px-4 justify-content-between">
                                                <div>
                                                    <div class="preloader" style="display: none;">
                                                        <div class="preloader-img"></div>
                                                    </div>
                                                    <div class="user-profile-qr-code p-6">
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="mt-4">
                                                        <button type="button" class="btn btn-profile btn-border w-160p btn-sm update-qr-code" id="qr-code-btn">
                                                            @lang('message.dashboard.button.update-qr-code')
                                                        </button>
                                                    </div>

                                                    <div class="mt-4">
                                                        <button type="button" class="btn btn-border btn-profile btn-sm w-160p" id="print-qr-code-btn" style="display: none;">
                                                            {{ __('Print QR Code') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="row mt-5 pl-3 pr-3">
                                    <div class="col-lg-12">
                                        <!-- Profile information -->
                                        <h3 class="sub-title">@lang('message.dashboard.setting.profile-information')</h3>
                                        <form method="post" action="{{ url('prifile/update') }}" id="profile_update_form">
                                            {{csrf_field()}}

                                            <input type="hidden" value="{{$user->id}}" name="id" id="id" />

                                            <div class="row mt-4">
                                                <!-- First Name -->
                                                <div class="form-group col-md-6">
                                                    <label for="first_name">@lang('message.dashboard.setting.first-name')<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="first_name" id="first_name" value="{{ $user->first_name }}"
                                                    required data-value-missing="{{ __('This field is required.') }}"
                                                    maxlength="30" data-max-length="{{__(':x length should be maximum :y charcters.', ['x' => __('First name'), 'y' => __('30')]) }}">
                                                    @if($errors->has('first_name'))
                                                        <span class="error">{{ $errors->first('first_name') }}</span>
                                                    @endif
                                                </div>
                                                <!-- Last Name -->
                                                <div class="form-group col-md-6">
                                                    <label for="last_name">@lang('message.dashboard.setting.last-name')<span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="last_name" id="last_name"value="{{ $user->last_name }}"
                                                    required data-value-missing="{{ __('This field is required.') }}"
                                                    maxlength="30" data-max-length="{{ __(':x length should be maximum :y charcters.', ['x' => __('Last name'), 'y' => __('30')]) }}">
                                                    @if($errors->has('last_name'))
                                                        <span class="error">{{ $errors->first('last_name') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Email -->
                                                <div class="form-group col-md-6">
                                                    <label for="email">@lang('message.dashboard.setting.email')<span class="text-danger">*</span></label>
                                                    <input type="text" id="email" class="form-control" value="{{ $user->email }}" readonly>
                                                </div>
                                                <!-- Default Wallet -->
                                                <div class="form-group col-md-6">
                                                    <label for="email">@lang('message.dashboard.setting.default-wallet')</label>
                                                    <select class="form-control" name="default_wallet" id="default_wallet">
                                                        @foreach($wallets as $wallet)
                                                            <option value="{{$wallet->id}}" {{$wallet->is_default == 'Yes' ? 'Selected' : ''}}>{{$wallet->currency->code}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- address_1 -->
                                                <div class="form-group col-md-6">
                                                    <label for="address_1">@lang('message.dashboard.setting.address1')</label>
                                                    <textarea class="form-control" name="address_1" id="address_1">{{ $user->user_detail->address_1 }}</textarea>
                                                    @if($errors->has('address_1'))
                                                        <span class="error">{{ $errors->first('address_1') }}</span>
                                                    @endif
                                                </div>
                                                <!-- address_2 -->
                                                <div class="form-group col-md-6">
                                                    <label for="address_2">@lang('message.dashboard.setting.address2')</label>
                                                    <textarea class="form-control" name="address_2" id="address_2">{{ $user->user_detail->address_2 }}</textarea>
                                                    @if($errors->has('address_2'))
                                                        <span class="error">{{ $errors->first('address_2') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- City -->
                                                <div class="form-group col-md-6">
                                                    <label for="city">@lang('message.dashboard.setting.city')</label>
                                                    <input type="text" class="form-control" name="city" id="city" value="{{ $user->user_detail->city }}">
                                                    @if($errors->has('city'))
                                                        <span class="error"> {{ $errors->first('city') }}</span>
                                                    @endif
                                                </div>

                                                <!-- State -->
                                                <div class="form-group col-md-6">
                                                    <label for="state">@lang('message.dashboard.setting.state')</label>
                                                    <input type="text" class="form-control" name="state" id="state" value="{{ $user->user_detail->state }}">
                                                    @if($errors->has('state'))
                                                        <span class="error">{{ $errors->first('state') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <!-- Country -->
                                                <div class="form-group col-md-6">
                                                    <label for="country_id">@lang('message.dashboard.setting.country')</label>
                                                    <select class="form-control" name="country_id" id="country_id">
                                                        @foreach($countries as $country)
                                                            <option value="{{$country->id}}" <?= ($user->user_detail->country_id == $country->id) ? 'selected' : '' ?> >{{$country->name}}</option>
                                                        @endforeach
                                                    </select>
                                                    @if($errors->has('country_id'))
                                                        <span class="error">{{ $errors->first('country_id') }}</span>
                                                    @endif
                                                </div>
                                                <!-- timezone -->
                                                <div class="form-group col-md-6">
                                                    <label for="timezone">@lang('message.dashboard.setting.timezone')</label>
                                                    <select class="form-control" name="timezone" id="timezone">
                                                        @foreach($timezones as $timezone)
                                                            <option value="{{ $timezone['zone'] }}" {{ ($user->user_detail->timezone == $timezone['zone']) ? 'selected' : '' }}>
                                                            {{ $timezone['diff_from_GMT'] . ' - ' . $timezone['zone'] }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    @if($errors->has('timezone'))
                                                        <span class="error">{{ $errors->first('timezone') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row mt-1">
                                                <div class="form-group mb-0 col-md-12">
                                                    <button type="submit" class="btn btn-primary px-4 py-2" id="users_profile">
                                                        <i class="spinner fa fa-spinner fa-spin d-none"></i> <span id="users_profile_text">@lang('message.dashboard.button.submit')</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script src="{{ asset('public/plugins/html-validation-1.0.0/validation.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('public/plugins/intl-tel-input-17.0.19/js/intlTelInput-jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/isValidPhoneNumber.min.js') }}" type="text/javascript"></script>
<script src="{{ theme_asset('public/js/sweetalert/sweetalert-unpkg.min.js') }}" type="text/javascript"></script>
<script>
    'use strict';
    var utilsScriptLoadingPath = '{{ asset("public/plugins/intl-tel-input-17.0.19/js/utils.min.js") }}';
    var userId = '{{ auth()->user()->id }}';
    var OrginalUsercarrierCode = '{{ $user->carrierCode }}';
    var OrginalUserphone = '{{ $user->phone }}';
    var carrierCode = '{{ !empty($user->carrierCode) ? $user->carrierCode : NULL }}';
    var defaultCountry = '{{ !empty($user->defaultCountry) ? $user->defaultCountry : NULL }}';
    var formattedPhone = '{{ !empty($user->formattedPhone) ? $user->formattedPhone : NULL }}';
    var csrfToken = '{{ csrf_token() }}';
    var profileImageUploadUrl = "{{ url('profile-image-upload') }}";
    var profileImageSourceUrl = '{{ asset("public/user_dashboard/profile") }}';
    var nameError = "{{ __('Please enter only alphabet and spaces') }}";
    var nameLengthError = "{{ __('Name can not be more than 30 characters') }}";
    var pleaseWaitText = "{{ __('Please Wait') }}";
    var loadingText = '{{ __("Loading...") }}';
    var QrCodeSecret = '{{ !empty($QrCodeSecret) ? $QrCodeSecret : '' }}';
    var updateQrCodeText = '{{ __("Update QR Code") }}';
    var addQrCodeText = '{{ __("Add QR Code") }}';
    var errorText = '{{ __("Error!") }}';
    var countryShortCode = '{{ getDefaultCountry() }}';
    var validPhoneNumberErrorText = '{{ __("Please enter a valid international phone number.") }}';
    var fieldRequiredText = '{{ __("This field is required.") }}';
    var submitText = '{{ __("Submit") }}';
    var nextText = '{{ __("Next") }}';
    var getCodeText = '{{ __("Get Code") }}';
    var verificationCodeText = '{{ __("To make sure this number is yours, we will send you a verification code.") }}';
    var phoneNumberText = '{{ __("Enter the number you’d like to use") }}';
    var smsCodeSentText = smsCodeSentText ;
    var smsCodeSubmitText = '{{ __("Enter it to verify your phone.") }}';
    var verifyText = '{{ __("Verify") }}';
    var verifyPhoneText = '{{ __("Verify Phone") }}'
    var minPasswordCharacterLength = '{{ __("Please enter at least :x characters.") }}';
    var passwordSameValue = '{{ __("Please enter the same value again.") }}';
    var submittingText = '{{ __("Submitting...") }}';
</script>
<script src="{{ asset('public/user_dashboard/js/profile/profile.min.js')}}" type="text/javascript"></script>
@endsection


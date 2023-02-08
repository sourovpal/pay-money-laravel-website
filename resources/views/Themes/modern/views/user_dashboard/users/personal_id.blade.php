@extends('user_dashboard.layouts.app')

@section('css')

@endsection

@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Settings') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="mt-5 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/profile') }}">
                        <div class="mr-4">
                            <p class="text-16 font-weight-400">{{ __('Profile') }}</p>
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
                        <div class="mr-4 border-bottom-active pb-3">
                            <span class="text-16 font-weight-600 text-active">{{ __('Identity verification') }}</span>
                            @if( !empty(getAuthUserIdentity()) && getAuthUserIdentity()->status == 'approved' )
                            (<span style="color: green"><i class="fa fa-check" aria-hidden="true"></i>Verified</span>) 
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
                        <h3 class="sub-title">{{ __('Identitiy verification') }}</h3>
                        <p class="text-gray-500 text-16"> {{ __('Verify your idetification') }}</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="p-4">
                                @include('user_dashboard.layouts.common.alert')
                                <div>
                                    <div class="row">
                                        <div class="col-lg-12 bg-secondary rounded mt-3 shadow p-35">
                                            <!-- form -->
                                            <form action="{{ url('profile/personal-id-update') }}" method="POST" class="form-horizontal" id="personal_id" enctype="multipart/form-data">
                                                {{ csrf_field() }}

                                                <input type="hidden" value="{{ $user->id }}" name="user_id" id="user_id" />

                                                <input type="hidden" value="{{ isset($documentVerification->file_id) ? $documentVerification->file_id : '' }}" name="existingIdentityFileID" id="existingIdentityFileID" />

                                                <!-- Identity Type -->
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="identity_type">@lang('message.personal-id.identity-type')</label>
                                                        <select name="identity_type" id="identity_type" class="form-control">
                                                            <option value="">@lang('message.personal-id.select-type')</option>
                                                            <option value="driving_license"
                                                            {{ !empty($documentVerification->identity_type) && $documentVerification->identity_type == 'driving_license' ? 'selected' : '' }}>
                                                                @lang('message.personal-id.driving-license')
                                                            </option>
                                                            <option value="passport" {{ !empty($documentVerification->identity_type) && $documentVerification->identity_type == 'passport' ? 'selected' : '' }}>@lang('message.personal-id.passport')</option>
                                                            <option value="national_id" {{ !empty($documentVerification->identity_type) && $documentVerification->identity_type == 'national_id' ? 'selected' : '' }}>@lang('message.personal-id.national-id')</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                <!-- Identity Number -->
                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="identity_number">@lang('message.personal-id.identity-number')</label>
                                                        <input type="text" name="identity_number" class="form-control" value="{{ !empty($documentVerification->identity_number) ? $documentVerification->identity_number : '' }}">
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>

                                                <div class="row">
                                                    <div class="form-group col-md-12">
                                                        <label for="identity_file">@lang('message.personal-id.upload-identity-proof')</label>
                                                        <input type="file" name="identity_file" class="form-control input-file-field">
                                                    </div>
                                                </div>

                                                <!-- Attached file -->
                                                @if (!empty($documentVerification->file))
                                                    <h5>
                                                        <a class="text-info" href="{{ url('public/uploads/user-documents/identity-proof-files').'/'.$documentVerification->file->filename }}"><i class="fa fa-download"></i>
                                                            {{ $documentVerification->file->originalname }}
                                                        </a>
                                                    </h5>
                                                    <br>
                                                @endif

                                                <div class="row">
                                                    <div class="form-group mb-0 col-md-12 mt-1">
                                                        <button type="submit" class="btn btn-primary px-4 py-2" id="personal_id_submit">
                                                            <i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="personal_id_submit_text">@lang('message.dashboard.button.submit')</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <!-- /form -->
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
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

<script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>
<script src="{{theme_asset('public/js/additional-methods.min.js')}}" type="text/javascript"></script>

<script type="text/javascript">

    jQuery.extend(jQuery.validator.messages, {
        required: "{{ __('This field is required.') }}",
        number: "{{ __("Please enter a valid number.") }}",
    })

    $('#personal_id').validate({
        rules: {
            identity_type: {
                required: true,
            },
            identity_number: {
                required: true,
            },
            identity_file: {
                required: true,
                extension: "pdf|png|jpg|jpeg|gif|bmp",
            },
        },
        messages: {
            identity_file: {
                extension: "{{ __("Please select (pdf, png, jpg, jpeg, gif or bmp) file!") }}"
            }
        },
        submitHandler: function(form)
        {
            $("#personal_id_submit").attr("disabled", true);
            $(".spinner").show();
            $("#personal_id_submit_text").text('Submitting...');
            form.submit();
        }
    });
</script>
@endsection

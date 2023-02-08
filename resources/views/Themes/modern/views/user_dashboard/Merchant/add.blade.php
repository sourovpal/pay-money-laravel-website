@extends('user_dashboard.layouts.app')
@section('content')
<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div class="d-flex justify-content-between">
                <div>
                    <h3 class="page-title">{{ __('Merchants') }}</h3>
                </div>
            </div>
            <!-- Page title end-->

            <div class="mt-4 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/merchants') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active">@lang('message.dashboard.merchant.menu.merchant')</p>
                        </div>
                    </a>

                    <a href="{{ url('/merchant/payments') }}">
                        <div class="mr-4">
                            <p class="text-16 font-weight-400 text-gray-500">@lang('message.dashboard.merchant.menu.payment')</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-5 row m-0 justify-content-between">
                        <div>
                            <h3 class="sub-title">{{ __('New Merchant') }}</h3>
							<p class="text-gray-500 text-justify">{{ __('Merchant account will allow your business to accept payments from your customers. When a customer pays for a product or service, money will be added to your wallets. You can create both standard and Express merchants providing proper information. Once a merchant is approved by the administrator, the merchant account will be ready to accept payments.') }}</p>
                        </div>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-lg-8 mt-2">
                    <div class="row">
                        <div class="col-md-12 col-xl-10">
                            <div class="bg-secondary p-35 rounded mt-3 mb-30 shadow">
                                @include('user_dashboard.layouts.common.alert')

                                <form action="{{url('merchant/store')}}"  method="post" enctype="multipart/form-data" accept-charset="utf-8" id="merchant_add_form">
									<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">

										<div>
											<!-- Name -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.add.name')</label>
												<input value="{{ old('business_name') }}" class="form-control" name="business_name" id="business_name"  type="text">
												@if($errors->has('business_name'))
												<span class="help-block">
													<strong class="text-danger">{{ $errors->first('business_name') }}</strong>
												</span>
												@endif
											</div>

											<!-- Site Url -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.add.site-url')</label>
												<input value="{{ old('site_url') }}" class="form-control" name="site_url" id="site_url"  placeholder="http://www.example.com" type="url">
												@if($errors->has('site_url'))
												<span class="help-block">
													<strong class="text-danger">{{ $errors->first('site_url') }}</strong>
												</span>
												@endif
											</div>

											<!-- Site Url -->
											<div class="form-group">
												<label for="exampleInputPassword1">@lang('message.dashboard.send-request.common.currency')</label>
												<select class="form-control" name="currency_id">
													@foreach($activeCurrencies as $result)
															<option value="{{ $result->id }}" {{ $defaultWallet->currency_id == $result->id ? 'selected="selected"' : '' }}>{{ $result->code }}</option>
													@endforeach
												</select>
											</div>

											<!-- Type -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.add.type')</label>
												<select class="form-control" name="type" id="type">
													<option <?= old('type')=='standard'?'selected':''?> value="standard">{{ __('Standard') }}</option>
													<option <?= old('type')=='express'?'selected':''?> value="express">{{ __('Express') }}</option>
												</select>
												@if($errors->has('type'))
												<span class="help-block">
													<strong class="text-danger">{{ $errors->first('type') }}</strong>
												</span>
												@endif
											</div>

											<!-- Comment for administration -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.edit.comment-for-administration')</label>
												<textarea name="note" class="form-control" id="note">{{ old('note') }}</textarea>
												@if($errors->has('note'))
													<span class="help-block">
														<strong class="text-danger">{{ $errors->first('note') }}</strong>
													</span>
												@endif
											</div>

											<!-- Logo -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.add.logo')</label>
												<input class="form-control" name="logo" id="logo" type="file">
												@if($errors->has('logo'))
												<span class="help-block">
													<strong class="text-danger">{{ $errors->first('logo') }}</strong>
												</span>
												@endif
												<div class="clearfix"></div>
												<small class="form-text text-muted"><strong>{{ allowedImageDimension(100,80,'user') }}</strong></small>

												<p style="width: 100px !important;"><img src='{{ url('public/uploads/userPic/default-image.png') }}' width="100" height="80" id="merchant-demo-logo-preview"></p>
											</div>
										</div>

										<!-- Buttons -->
										<div class="row m-0 mt-4 justify-content-between">
											<div>
												<a href="#" class="merchant-back-link">
													<p class="py-2 text-active text-underline merchant-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
												</a>
											</div>
	
											<div>
												<button type="submit" class="btn btn-primary px-5 py-2" id="merchant_create">
													<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="merchant_create_text">@lang('message.dashboard.button.submit')</span>
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
</section>
@endsection

@section('js')

<script src="{{theme_asset('public/js/jquery.validate.min.js')}}" type="text/javascript"></script>

<script src="{{theme_asset('public/js/additional-methods.min.js')}}" type="text/javascript"></script>

<!-- read-file-on-change -->
@include('common.read-file-on-change')

<script>
	 function merchantBack()
    {
        window.localStorage.setItem("depositConfirmPreviousUrl",document.URL);
        window.history.back();
    }

	 //Only go back by back button, if submit button is not clicked
	 $(document).on('click', '.merchant-back-btn', function (e)
    {
        e.preventDefault();
        merchantBack();
    });


	jQuery.extend(jQuery.validator.messages, {
	    required: "{{ __('This field is required.') }}",
	    url: "{{ __('Please enter a valid URL.') }}",
	})

	// preview currency logo on change
    $(document).on('change','#logo', function()
    {
        let orginalSource = '{{ url('public/uploads/userPic/default-image.png') }}';
        readFileOnChange(this, $('#merchant-demo-logo-preview'), orginalSource);
    });

	$('#merchant_add_form').validate({
		rules: {
			business_name: {
				required: true,
			},
			site_url: {
				required: true,
				url: true,
			},
			type: {
				required: true,
			},
			note: {
				required: true,
			},
			logo: {
	            extension: "png|jpg|jpeg|gif|bmp",
	        },
		},
		messages: {
	      logo: {
	        extension: "{{ __('Please select (png, jpg, jpeg, gif or bmp) file!') }}"
	      }
	    },
		submitHandler: function(form)
	    {
	        $("#merchant_create").attr("disabled", true);
	        $(".spinner").show();
	        $("#merchant_create_text").text("{{ __('Submitting...') }}");
	        form.submit();
	    }
	});

</script>
@endsection

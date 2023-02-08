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

                <div>
                    <a href="{{url('/merchant/add')}}" class="btn btn-primary px-4 py-2 ticket-btn"><i class="fa fa-user"></i>&nbsp;
                        @lang('message.dashboard.button.new-merchant')</a>
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
                            <h3 class="sub-title">{{ __('Edit Merchant') }}</h3>
							<p class="text-gray-500">{{ __('Update merchants information.') }}</p>
                        </div>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-lg-8 mt-2">
                    <div class="row">
                        <div class="col-md-12 col-xl-10">
                            <div class="bg-secondary rounded mt-3 mb-30 p-35 shadow">
                                @include('user_dashboard.layouts.common.alert')

								<form action="{{url('merchant/update')}}"  method="post" accept-charset="utf-8" id="EditForm" enctype="multipart/form-data">
									<input type="hidden" value="{{csrf_token()}}" name="_token" id="token">
									<input type="hidden" value="{{$merchant->id}}" name="id" id="id">
									<div>
										<div>
											<!-- Name -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.add.name')</label>
												<input class="form-control" name="business_name" id="business_name"  type="text" value="{{$merchant->business_name}}">

												@if($errors->has('business_name'))
												<span class="help-block">
													<strong class="text-danger">{{ $errors->first('business_name') }}</strong>
												</span>
												@endif
											</div>

											<!-- Site Url -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.add.site-url')</label>
												<input class="form-control" name="site_url" id="site_url"  placeholder="http://www.example.com" type="text" value="{{$merchant->site_url}}">

												@if($errors->has('site_url'))
												<span class="help-block">
													<strong class="text-danger">{{ $errors->first('site_url') }}</strong>
												</span>
												@endif
											</div>

											<!-- Currency -->
											<div class="form-group">
												<label>@lang('message.dashboard.send-request.common.currency')</label>
												<select class="form-control" name="currency_id" id="currency">
													@foreach($activeCurrencies as $result)
														@if (!empty($merchant->currency_id))
															<option value="{{ $result->id }}" {{ $merchant->currency_id == $result->id ? 'selected="selected"' : ''}}>{{ $result->code }}</option>
															@else
															<option value="{{ $result->id }}" {{ $defaultWallet->currency_id == $result->id ? 'selected="selected"' : ''}}>{{ $result->code }}</option>
														@endif
													@endforeach
												</select>
												<span class="currency-change-warning d-none error">{{ __('Changing currency will put the merchant status again in admin moderation.') }}</span>
											</div>

											<!-- Type -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.add.type')</label>
												<input readonly class="form-control" value="{{ucfirst($merchant->type)}}">
											</div>

											<!-- Comment for administration -->
											<div class="form-group">
												<label>@lang('message.dashboard.merchant.edit.comment-for-administration')</label>
												<textarea name="note" class="form-control" id="note">{{$merchant->note}}</textarea>
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

												@if (!empty($merchant->logo))
													<p style="width: 100px !important;"><img src="{{url('public/user_dashboard/merchant/'.$merchant->logo)}}" width="100" height="80" id="merchant-logo-preview"></p>
												@else
													<p style="width: 100px !important;"><img src='{{ url('public/uploads/userPic/default-image.png') }}' width="100" height="80" id="merchant-demo-logo-preview"></p>
												@endif
											</div>

										</div>

										<!-- Button -->
										<div class="row m-0 mt-4 justify-content-between">
											<div>
												<a href="#" class="merchant-back-link">
													<p class="py-2 text-active text-underline merchant-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
												</a>
											</div>
	
											<div>
												<button type="submit" class="btn btn-primary px-4 py-2" id="merchant_update">
													<i class="spinner fa fa-spinner fa-spin" style="display: none;"></i> <span id="merchant_update_text">@lang('message.dashboard.button.update')</span>
												</button>
											</div>
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
    	let merchantLogo = '{{ !empty($merchant->logo) ? $merchant->logo : null }}';
    	if (merchantLogo != null) {
    		readFileOnChange(this, $('#merchant-logo-preview'), orginalSource);
    	}
        readFileOnChange(this, $('#merchant-demo-logo-preview'), orginalSource);
	});

	$('#currency').on('change', function() {
		let currentStatus = '{{ $merchant->status }}';
		let currencyCurrencyId = '{{ $merchant->currency_id }}';
		let currencyId = $(this).val();

		if (currentStatus == 'Approved' && currencyCurrencyId != currencyId) {
			$('.currency-change-warning').removeClass('d-none');
		} else {
			$('.currency-change-warning').addClass('d-none');
		}
	});

	$('#EditForm').validate({
		rules: {
			business_name: {
				required: true,
			},
			site_url: {
				required: true,
				url: true,
			},
			password: {
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
	        $("#merchant_update").attr("disabled", true);
	        $(".spinner").show();
	        $("#merchant_update_text").text("{{ __('Updating...') }}");
	        form.submit();
	    }
	});

</script>

@endsection

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
                            <h3 class="sub-title">{{ __('Merchant details') }}</h3>
							<p class="text-gray-500">{{ __('Here you can see merchants details.') }}</p>
                        </div>
                    </div>
                    <!-- Sub title end-->
                </div>

                <div class="col-lg-8 mt-2">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="bg-secondary rounded mt-3 p-35 shadow">
                                <div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p class="font-weight-600">@lang('message.dashboard.merchant.details.merchant-id')</p>
                                                <p>{{ $merchant->merchant_uuid }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p class="font-weight-600">@lang('message.dashboard.merchant.details.business-name')</p>
                                                <p>{{ $merchant->business_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p class="font-weight-600">@lang('message.dashboard.merchant.details.site-url')</p>
                                                <p>{{ $merchant->site_url }}</p>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p class="font-weight-600">@lang('message.form.currency')</p>
                                                <p>{{ !empty($merchant->currency->code) ? $merchant->currency->code : $defaultWallet->currency->code }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p class="font-weight-600">@lang('message.dashboard.merchant.details.status')</p>
                                                <p>
                                                    @if ($merchant->status == 'Moderation')
                                                        <span class="badge badge-warning">@lang('message.dashboard.merchant.table.moderation')</span>
                                                    @elseif ($merchant->status == 'Disapproved')
                                                        <span class="badge badge-danger">@lang('message.dashboard.merchant.table.disapproved')</span>
                                                    @elseif ($merchant->status == 'Approved')
                                                        <span class="badge badge-success">@lang('message.dashboard.merchant.table.approved')</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p class="font-weight-600">@lang('message.dashboard.merchant.details.note')</p>
                                                <p>{{ $merchant->note }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <p class="font-weight-600">@lang('message.dashboard.merchant.details.date')</p>
                                                <p>{{ dateFormat($merchant->create_at) }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row m-0 mt-4 justify-content-between">
                                        <div>
                                            <a href="#" class="merchant-back-link">
                                                <p class="py-2 text-active text-underline merchant-back-btn mt-2"><u><i class="fas fa-long-arrow-alt-left"></i> @lang('message.dashboard.button.back')</u></p>
                                            </a>
                                        </div>

                                        <div>
                                            <a class="btn btn-primary px-4 py-2" href="{{url('merchant/edit/'.$merchant->id)}}">@lang('message.form.edit')</a>
                                        </div>
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
<!--End Section-->
@endsection

@section('js')
<script type="">
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
</script>
@endsection

@extends('user_dashboard.layouts.app')
@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">Deposit Fund</h3>
            </div>
            <!-- Page title end-->
            <div class="row mt-4">
                <div class="col-xl-4">
                    <!-- Sub title start -->
                    <div class="mt-5">
                        <h3 class="sub-title">Create Deposit</h3>
                        <p class="text-gray-500 text-16"> Enter your deposit amount, currency and payment method</p>
                    </div>
                    <!-- Sub title end-->
                </div>
                <div class="col-xl-8">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="d-flex w-100 mt-4">
                                <ol class="breadcrumb w-100">
                                    <li class="breadcrumb-active"><a href="#">Create</a></li>
                                    <li class="breadcrumb-first"><a href="#">Confirmation</a></li>
                                    <li class="active">Success</li>
                                </ol>
                            </div>
                            <div class="bg-secondary mt-5 shadow p-35">
                                @include('user_dashboard.layouts.common.alert')
								<div>
                                    <div id="paypal-button-container"></div>
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include('user_dashboard.layouts.common.help')
@endsection
@section('js')
<script src="https://www.paypal.com/sdk/js?client-id={{ $clientId }}&disable-funding=paylater&currency={{ $currencyCode }}"></script> 
<script>
    paypal.Buttons({
        createOrder: function (data, actions) {
            // This function sets up the details of the transaction, including the amount and line item details.
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: "{!! $amount !!}"
                    }
                }]
            });
        },
        onApprove: function (data, actions) {
            // This function captures the funds from the transaction.
            return actions.order.capture().then(function (details) {
                // This function shows a transaction success message to your buyer.
                // alert('Transaction completed by ' + details.payer.name.given_name);
                window.location.replace(SITE_URL + "/deposit/paypal-payment/success/" + btoa(details.purchase_units[0].amount.value));
            });
        }
    }).render('#paypal-button-container');
</script>
@endsection
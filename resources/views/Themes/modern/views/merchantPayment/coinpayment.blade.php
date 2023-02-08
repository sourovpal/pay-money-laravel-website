
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ meta(Route::current()->uri(), 'description') }}">
    <meta name="keywords" content="{{ meta(Route::current()->uri(), 'keywords') }}">
    <title>{{ meta(Route::current()->uri(), 'title') }}<?= isset($additionalTitle) ? ' | '.$additionalTitle : '' ?></title>
    <!--css styles-->
    <link rel="stylesheet" type="text/css" href="{{ theme_asset('public/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/dashboard.css')}}">
    <link rel="stylesheet" type="text/css" href="{{theme_asset('public/css/fontawesome/css/all.min.css')}}">

    <!---title logo icon-->
    <link rel="javascript" href="{{theme_asset('public/js/respond.js')}}">
    <!---favicon-->
    @if (!empty(settings('favicon')))
    <link rel="shortcut icon" href="{{theme_asset('public/images/logos/'.settings('favicon'))}}" />
    @endif

    <style type="text/css">
        .coin-main-div:focus {
            border: 2px solid #635BFF!important;
            background: #dee2e6;
        }
    </style>
</head>
<body>
    <section class="min-vh-100">
        <div class="p-2 my-3">
            <div class="container-fluid">
                <!-- Coin payment section start-->
                <div class="row mt-4">
                    <div class="col-md-8 col-xl-8">
                        @include('user_dashboard.layouts.common.alert')
                        <div class="shadow bg-white p-4">
                            <div class="row">
                                <div class="col-md-12" id="coin-search">
                                    <form>
                                        <div class="input-group">
                                            <input type="text" id="search-coin" class="form-control search" placeholder="Search ">
                                            <div class="input-group-append search-append">
                                                <button class="btn btn-primary search-btn" type="button">
                                                    <i class="fa fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row mt-4" id="coin-list">
                                @foreach ($coin_accept as $coin)
                                <div class="col-md-6 mt-4 coin-div" coin-iso="{{ $coin['iso'] }}">
                                    <div class="d-flex border p-2 coin-main-div" tabindex="-1">
                                        <div class="pr-2">
                                            <img class="w-50p" style="width: 50px;" src="{{ $coin['icon'] }}" alt="icon">
                                        </div>

                                        <div class="pr-2">
                                            <p class="font-weight-600">{{$coin['name']}}</p>
                                            <p class="font-weight-500" coin-rate="{{$coin['rate']}}">{{$coin['rate']}}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 col-xl-4">
                        <div class="shadow bg-white py-4">
                            <form action="{{ url('payment/coinpayments/make-transaction') }}" method="POST">
                                @csrf
                                <input type="hidden" name="selected_coin" value="" id="input-selected-coin">

                                <div class="p-4">
                                    {!! getSystemLogo() !!}
                                </div>

                                <div class="d-flex justify-content-between border-top">
                                    <div class="px-4 py-2">
                                        <p class="font-weight-600"> {{ __('Total Amount') }} ({{$currencyCode}})</p>
                                    </div>

                                    <div class="px-4 py-2">
                                        <p>{{ $amount }} {{$currencyCode}}</p>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between border-top">
                                    <div class="px-4 py-2">
                                        <p class="font-weight-600">{{ __('Payment method') }}</p>
                                    </div>

                                    <div class="px-4 py-2">
                                        <p id="selected-coin"></p>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between border-top">
                                    <div class="px-4 py-2">
                                        <p class="font-weight-600">{{ __('Total amount') }} <span id="selected-iso"></span></p>
                                    </div>

                                    <div class="px-4 py-2">
                                        <p id="selected-coin-rate"></p>
                                    </div>
                                </div>

                                <div class="mt-4 px-4">
                                    <button class="btn btn-primary px-4 py-2 w-100 coinpayment-submit-button">{{ __('Pay Now') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!--coin payment section end -->
            </div>
        </div>
    </section>


    @include('user_dashboard.layouts.common.help')

    <script src="{{theme_asset('public/js/jquery.min.js')}}" type="text/javascript"></script>

    <script src="{{theme_asset('public/js/jquery.ba-throttle-debounce.js')}}" type="text/javascript"></script>

    <script type="text/javascript">

        let encoded_coin_accept = @json($encoded_coin_accept);
        let coinList = JSON.parse(encoded_coin_accept);

        $('#search-coin').on('keyup', $.debounce(500, function(){

            let coinName = $(this).val();

            let filteredCoins =  coinList.filter( function ( coin ) {
                return (coin.name).match(new RegExp("[^,]*"+coinName+"[^,]*",'ig'));
            });

            let coinListHTML = '';

            $( filteredCoins ).each( function ( index, filteredCoin ) {
                coinListHTML += `
                <div class="col-md-6 mt-4 coin-div" coin-iso="${filteredCoin.iso}">
                    <div class="d-flex border p-2 coin-main-div">
                        <div class="pr-2">
                            <img class="w-50p" style="width: 50px;" src="${filteredCoin.icon}" alt="icon">
                        </div>

                        <div class="pr-2">
                            <p class="font-weight-600">${filteredCoin.name}</p>
                            <p class="font-weight-500" coin-rate="${filteredCoin.rate}">${filteredCoin.rate}</p>
                        </div>
                    </div>
                </div>
                `;
            });

            $("#coin-list").empty();
            $("#coin-list").html(coinListHTML);
        }));

        $(document).on('click', '.coin-div', function(){
            let coinIso = $(this).attr('coin-iso');
            let coinRate = $(this).find('p').last().attr('coin-rate');

            console.log(coinIso, coinRate);

            $('#selected-coin').text(coinIso);
            $('#input-selected-coin').val(coinIso);
            $('#selected-iso').text(coinIso);
            $('#selected-coin-rate').text(coinRate);
            $('.coin-main-div').click(function() {
                $('.class-toggle').toggle();
                $('.class-toggle').toggleClass('add-class');
            })
        });

        $('.coinpayment-submit-button').click(function() {
            $(this).attr('disabled', 'disabled');
            $(this).parents('form').submit();
        });

    </script>
</body>
</html>

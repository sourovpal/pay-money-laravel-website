@extends('user_dashboard.layouts.app')
@section('content')

<section class="min-vh-100">
    <div class="my-30">
        <div class="container-fluid">
            <!-- Page title start -->
            <div>
                <h3 class="page-title">{{ __('Disputes') }}</h3>
            </div>
            <!-- Page title end-->

            <div class="mt-5 border-bottom">
                <div class="d-flex flex-wrap">
                    <a href="{{ url('/disputes') }}">
                        <div class="mr-4 border-bottom-active pb-3">
                            <p class="text-16 font-weight-600 text-active">{{ __('Dispute list') }}</p>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-lg-4">
                    <!-- Sub title start -->
                    <div class="mt-2">
                        <h3 class="sub-title">{{ __('Latest disputes') }}</h3>
                        <p class="text-gray-500 text-16">{{ __('Make conversation with admin') }}.</p>
                    </div>
                    <!-- Sub title end-->
                </div>

				<div class="col-lg-8">
					  <!--dispute list section start-->
						@if($list->count() > 0)
							@foreach($list as $result)
								<div class="row bg-secondary rounded m-0 mt-4 p-4 shadow">
									<div class="col-md-10">
										<div class="mb-2">
											<h3 class="font-weight-600 text-18"> {{$result->title}} </h3>
										</div>
										<p>@lang('message.dashboard.dispute.dispute-id'): {{ isset($result->code) ? $result->code :"-" }}</p>
										<p>@lang('message.dashboard.dispute.transaction-id'): {{ isset($result->transaction) ? $result->transaction->uuid :"-" }}</p>

										@if(Auth::user()->id != $result->claimant_id)
											<p>
												@lang('message.dashboard.dispute.claimant') :
												{{ $result->claimant->first_name .' '.$result->claimant->last_name}}
											</p>
										@endif

										@if(Auth::user()->id != $result->defendant_id)
											<p>
												@lang('message.dashboard.dispute.defendant') :
												{{ $result->defendant->first_name .' '.$result->defendant->last_name }}
											</p>
										@endif

										<p>
											@lang('message.dashboard.dispute.created-at') : {{ dateFormat($result->created_at) }}
										</p>
										<p>
											@lang('message.dashboard.dispute.status') :
											@php
												echo getStatusBadge($result->status);
											@endphp
										</p>
									</div>

									<div class="col-md-2">
										<p class="text-right text-active text-underline">
											<a href='{{url("dispute/discussion/$result->id") }}' class="text-active font-weight-600">
												<u>@lang('message.dashboard.button.details')</u>
											</a>
										</p>
									</div>
								</div>

							@endforeach
						@else
							<div class="row bg-secondary rounded m-0 mt-4 p-4 shadow">
								<div class="col-md-12">
									<div class="p-5 text-center">
										<img src="{{ theme_asset('public/images/banner/notfound.svg') }}" alt="notfound">
										<p class="mt-4">{{ __('Sorry!') }}  @lang('message.dashboard.dispute.no-dispute')</p>
									</div>
								</div>
							</div>
						@endif
				</div>
            </div>
			<div>
				{{ $list->links('vendor.pagination.bootstrap-4') }}
			</div>
            <!--dispute list end-->
        </div>
    </div>
</section>
@endsection

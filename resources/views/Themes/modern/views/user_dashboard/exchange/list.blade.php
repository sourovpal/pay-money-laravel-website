@extends('user_dashboard.layouts.app')
@section('content')
    <section class="min-vh-100">
        <div class="container mt-5">
            <div class="row justify-content-center">
				<div class="col-md-12 col-xs-12">
					@include('user_dashboard.layouts.common.alert')
					<div class="card">
						<div class="card-header">
							<div class="chart-list float-left">
								<ul>
									<li class="active"><a href="{{url('/exchanges')}}">{{ __('list') }}</a></li>
									<li><a href="{{url('/exchange')}}">{{ __('New Exchange') }}</a></li>
								</ul>
							</div>
						</div>

						<div class="wap-wed mt20 mb20">
							<div class="card-body" style="overflow: auto;">
								@if($list->count() > 0)
									<table class="table table-striped table-hover">
										<thead>
											<tr>
											<th>{{ __('Exchange From') }}</th>
											<th>{{ __('Exchange To') }}</th>
											<th>{{ __('Exchange Rate') }}</th>
											<th>{{ __('Amount') }}</th>
											<th>{{ __('Date') }}</th>
											<th>{{ __('Action') }}</th>
											</tr>
										</thead>

										<tbody>
											@foreach($list as $result)
											<tr>
											@if($result->type == 'Out')
											<td>
												{{$defaultCurrency->code}}
											</td>
											@else
											<td>
												{{$result->code}}
											</td>
											@endif
											@if($result->type == 'In')
											<td>
												{{$defaultCurrency->code}}
											</td>
											@else
											<td>
												{{$result->code}}
											</td>
											@endif
											<td>{{ decimalFormat($result->exchange_rate) }}</td>
											<td>
												@if($result->type == 'Out')
													{{ moneyFormat($defaultCurrency->symbol, decimalFormat($result->amount)) }}
												@else
													{{ moneyFormat($result->symbol, decimalFormat($result->amount)) }}
												@endif
											</td>
											<td>{{ dateFormat($result->created_at) }}</td>
											<td><a href="{{url('exchange/view/'.$result->id)}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a></td>
											</tr>
											@endforeach
										</tbody>
									</table>
								@else
									<h4>{{ __('Data not found') }}!</h4>
								@endif
							</div>
						</div>

						<div class="card-footer">
							{{ $list->links('vendor.pagination.bootstrap-4') }}
						</div>
					</div>
				</div>
            </div>
        </div>
    </section>
@endsection

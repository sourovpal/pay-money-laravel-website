@extends('frontend.layouts.app')
@section('content')
    <div class="min-vh-100">
        <section class="bg-image mt-93">
            <div class="bg-dark">
                <div class="container">
                    <div class="row py-5">
                        <div class="col-md-12">
                            <h2 class="text-white font-weight-bold text-28">{{ $pageInfo->name }} </h2>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--End banner Section-->

        <!--Start Section-->
        <section class="mt-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        {!! $pageInfo->content !!}
                    </div>
                    <!--/col-->
                </div>
                <!--/row-->
            </div>
        </section>
    </div>
@endsection
@section('js')
<script>

</script>
@endsection

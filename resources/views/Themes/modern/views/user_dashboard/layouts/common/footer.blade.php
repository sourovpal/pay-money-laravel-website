<section class="bg-image footer text-white mt-5">
    <div class="bg-dark">
        <div class="container pt-3 pb-3">
            <p class="text-center mt-4">{{ __('Follow us') }}</p>
            <div class="d-flex justify-content-center">
                <div class="d-flex flex-wrap social-icons mt-5 mb-4">
                    @foreach($socialList as $social)
                        @if (!empty($social->url))
                            <div class="p-2">
                                <a href="{{ $social->url }}">{!! $social->icon !!}</a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <div class="d-flex flex-wrap justify-content-center mt-4">
                <div class="p-2 pl-4 pr-4">
                    <a href="{{ url('/') }}" class="text-white">@lang('message.home.title-bar.home')</a>
                </div>

                <div class="p-2 pl-4 pr-4">
                    <a href="{{ url('/send-money') }}" class="text-white" >@lang('message.home.title-bar.send')</a>
                </div>

                <div class="p-2 pl-4 pr-4">
                    <a href="{{ url('/request-money') }}" class="text-white" >@lang('message.home.title-bar.request')</a>
                </div>

                @if(!empty($menusFooter))
                    @foreach($menusFooter as $footer_navbar)
                    <div class="p-2 pl-4 pr-4">
                        <a href="{{url($footer_navbar->url)}}" class="text-white"> {{ $footer_navbar->name }}</a>
                    </div>
                    @endforeach
                @endif

                <div class="p-2 pl-4 pr-4">
                    <a href="{{ url('/developer') }}" class="text-white">@lang('message.home.title-bar.developer')</a>
                </div>
            </div>

            <div class="d-flex justify-content-center pt-4">

                @foreach(getAppStoreLinkFrontEnd() as $app)
                    @if (!empty($app->logo))
                        <div class="p-2 pl-4 pr-4">
                            <a href="{{$app->link}}"><img src="{{url('public/uploads/app-store-logos/'.$app->logo)}}" class="img-responsive" width="125" height="50"/></a>
                        </div>
                    @else
                        <div class="p-2 pl-4 pr-4">
                            <a href="#"><img src='{{ url('public/uploads/app-store-logos/default-logo.jpg') }}' class="img-responsive" width="120" height="90"/></a>
                        </div>
                    @endif
                @endforeach
            </div>


            <hr>
            <div class="d-flex justify-content-between">
                <div>
                    <?php
                        $company_name = settings('name');
                    ?>
                    <p class="copyright">@lang('message.footer.copyright')&nbsp;© {{date('Y')}} &nbsp;&nbsp; {{ $company_name }} | @lang('message.footer.copyright-text')</p>
                </div>

                <div>
                    <div class="container-select d-flex">
                        <div>
                            <i class="fa fa-globe"></i>
                        </div>

                        <div>
                            <select class="select-custom" id="lang">
                                @foreach (getLanguagesListAtFooterFrontEnd() as $lang)
                                <option {{ Session::get('dflt_lang') == $lang->short_name ? 'selected' : '' }} value='{{ $lang->short_name }}'> {{ $lang->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</section>


<!-- Delete Modal -->
<div class="modal fade" id="delete-warning-modal" role="dialog" style="z-index:1060; color: light blue;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:100%;height:100%; background-color: aliceblue;">
            <div style="display: block" class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ __('Confirm Delete') }}</h4>
            </div>

            <div class="modal-body">
                <p><strong>{{ __('Are you sure you want to delete this Data ?') }}</strong></p>
            </div>

            <div class="modal-footer">
                <a class="btn btn-danger" id="delete-modal-yes" href="javascript:void(0)">@lang('message.form.yes')</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('message.form.no')</button>
            </div>
        </div>
    </div>
</div>


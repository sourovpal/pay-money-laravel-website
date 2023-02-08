@extends('admin.layouts.master')
@section('title', __('SMS Templates'))

@section('head_style')
  <!-- wysihtml5 -->
  <link rel="stylesheet" type="text/css" href="{{  asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}">
@endsection


@section('page_content')
  <div class="row">
    <div class="col-md-3">
       @include('admin.common.sms_menu')
    </div>
    <!-- /.col -->

    <div class="col-md-9">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">
            @if($tempId == 6)
              {{ __('Compose Transfer Status Change Template') }}
            @elseif($tempId == 8)
              {{ __('Compose Request Payment Status Change Template') }}
            @elseif($tempId == 10)
              {{ __('Compose Payout Status Change Template') }}
            @elseif($tempId == 14)
              {{ __('Compose Merchant Payment Status Change Template') }}
            @elseif($tempId == 16)
              {{ __('Compose Request Payment Status Change Template') }}
            @else
            {{ templateHeaderText($temp_Data[0]->subject) }}
            @endif
          </h3>
        </div>

        <form action='{{ url(\Config::get('adminPrefix').'/sms-template/update/'.$tempId) }}' method="post" id="sms-template">
          {!! csrf_field() !!}

          <!-- /.box-header -->

          <!-- English -->
          <div class="box-body">
            <div class="form-group">
                <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                <input class="form-control f-14" name="en[subject]" type="text" value="{{$temp_Data[0]->subject}}">
                @if ($errors->has('en[subject]'))
                      <span class="help-block">
                          <strong>{{ $errors->first('en[subject]') }}</strong>
                      </span>
                @endif
              </div>

            <div class="form-group">
                <textarea name="en[body]" class="form-control f-14 editor h-180" id="editor">{{$temp_Data[0]->body}}</textarea>
                @if ($errors->has('en[body]'))
                    <span class="help-block">
                        <strong>{{ $errors->first('en[body]') }}</strong>
                    </span>
                @endif
            </div>

            <!-- Other Languages -->
            <div class="box-group" id="accordion">
              <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
              <div class="panel box box-primary">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseOne" aria-expanded="false" class="collapsed">
                      {{ __('Arabic') }}
                    </a>
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse h-auto" aria-expanded="false">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                      <input class="form-control f-14" name="ar[subject]" type="text" value="{{$temp_Data[1]->subject}}">
                      @if ($errors->has('ar[subject]'))
                          <span class="help-block">
                              <strong>{{ $errors->first('ar[subject]') }}</strong>
                          </span>
                      @endif
                    </div>
                    <div class="form-group">
                        <textarea name="ar[body]" class="form-control f-14 editor 180">
                          {{$temp_Data[1]->body}}
                        </textarea>
                        @if ($errors->has('ar[body]'))
                          <span class="help-block">
                              <strong>{{ $errors->first('ar[body]') }}</strong>
                          </span>
                        @endif
                    </div>

                  </div>
                </div>
              </div>

              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="false">
                      {{ __('French') }}
                    </a>
                  </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse h-auto" aria-expanded="false">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                      <input class="form-control f-14" name="fr[subject]" type="text" value="{{$temp_Data[2]->subject}}">
                    </div>
                    <div class="form-group">
                        <textarea name="fr[body]" class="form-control f-14 editor 180">
                          {{$temp_Data[2]->body}}
                        </textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="false">
                      {{ __('Português') }}
                    </a>
                  </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse h-auto" aria-expanded="false">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                      <input class="form-control f-14" name="pt[subject]" type="text" value="{{$temp_Data[3]->subject}}">
                    </div>
                    <div class="form-group">
                        <textarea name="pt[body]" class="form-control f-14 editor 180">
                          {{$temp_Data[3]->body}}
                        </textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseFour" class="collapsed" aria-expanded="false">
                      {{ __('Russian') }}
                    </a>
                  </h4>
                </div>
                <div id="collapseFour" class="panel-collapse collapse h-auto" aria-expanded="false">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                      <input class="form-control f-14" name="ru[subject]" type="text" value="{{$temp_Data[4]->subject}}">
                    </div>
                    <div class="form-group">
                      <textarea name="ru[body]" class="form-control f-14 editor 180">
                        {{$temp_Data[4]->body}}
                      </textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseFive" class="collapsed" aria-expanded="false">
                      {{ __('Spanish') }}
                    </a>
                  </h4>
                </div>
                <div id="collapseFive" class="panel-collapse collapse h-auto" aria-expanded="false">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                      <input class="form-control f-14" name="es[subject]" type="text" value="{{$temp_Data[5]->subject}}">
                    </div>
                    <div class="form-group">
                        <textarea name="es[body]" class="form-control f-14 editor 180">
                          {{$temp_Data[5]->body}}
                        </textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseSix" class="collapsed" aria-expanded="false">
                      {{ __('Turkish') }}
                    </a>
                  </h4>
                </div>
                <div id="collapseSix" class="panel-collapse collapse h-auto" aria-expanded="false">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                      <input class="form-control f-14" name="tr[subject]" type="text" value="{{$temp_Data[6]->subject}}">
                    </div>
                    <div class="form-group">
                        <textarea name="tr[body]" class="form-control f-14 editor 180">
                        {{$temp_Data[6]->body}}
                        </textarea>
                    </div>
                  </div>
                </div>
              </div>

              <div class="panel box box-success">
                <div class="box-header with-border">
                  <h4 class="box-title">
                    <a data-bs-toggle="collapse" data-bs-parent="#accordion" href="#collapseSeven" class="collapsed" aria-expanded="false">
                      {{ __('Chinese') }}
                    </a>
                  </h4>
                </div>
                <div id="collapseSeven" class="panel-collapse collapse h-auto" aria-expanded="false">
                  <div class="box-body">
                    <div class="form-group">
                      <label class="f-14 fw-bold mb-1" for="Subject">{{ __('Subject') }}</label>
                      <input class="form-control f-14" name="ch[subject]" type="text" value="{{$temp_Data[7]->subject}}">
                    </div>
                    <div class="form-group">
                        <textarea name="ch[body]" class="form-control f-14 editor 180">
                        {{$temp_Data[7]->body}}
                        </textarea>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /.box-body -->

          <div class="box-footer">
            <div class="pull-right">

              @if(Common::has_permission(\Auth::guard('admin')->user()->id, 'edit_sms_template'))
                <button type="submit" class="btn btn-theme f-14" id="sms_edit">
                    <i class="fa fa-spinner fa-spin d-none"></i> <span id="sms_edit_text">{{ __('Update') }}</span>
                </button>
              @endif

            </div>
          </div>
        </form>
        <!-- /.box-footer -->
      </div>
      <!-- /.nav-tabs-custom -->
    </div>
    <!-- /.col -->
  </div>
  <!-- /.row -->

@endsection

@push('extra_body_scripts')

<!-- jquery.validate -->
<script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>

<script src="{{ asset('public/backend/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}" type="text/javascript"></script>

<script>
    $(function () {
      $(".editor").wysihtml5();
    });

    $('#sms-template').validate({
        rules: {
            subject: {
                required: true
            },
            content:{
               required: true
            }
        },
        submitHandler: function(form)
        {
            $("#sms_edit").attr("disabled", true);
            $(".fa-spin").removeClass("d-none");
            $("#sms_edit_text").text('Updating...');
            form.submit();
        }
    });
</script>

@endpush

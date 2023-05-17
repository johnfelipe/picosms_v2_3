@extends('layouts.customer')

@section('title','Compose | SmsBox')

@section('extra-css')
    <link rel="stylesheet" href="{{asset('plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
@endsection

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans('customer.compose')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a
                                href="{{route('customer.smsbox.inbox')}}">{{trans('customer.smsbox')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('customer.inbox')}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <a href="{{route('customer.smsbox.inbox')}}"
                   class="btn btn-primary btn-block mb-3">{{trans('customer.back_to_inbox')}}</a>
                @include('customer.smsbox.common')
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card card-primary card-outline">
                    <form id="compose_form" action="{{route('customer.smsbox.compose.sent')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="card-header">
                            <h3 class="card-title">{{trans('customer.compose_new_message')}}</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <div id="pre_draft">
                                @isset($draft)
                                    <input type='hidden' id='draft_id' name='draft_id' value='{{$draft->id}}'/>
                                @endisset
                            </div>
                            <div class="form-group d-flex">
                                <label for="fromNumber">{{trans('customer.from')}}:</label>
                                <select name="from_number" id="fromNumber"
                                        class="select2bs4 form-control compose-select"
                                        data-placeholder="{{trans('customer.from')}}:">

                                    @foreach(auth('customer')->user()->numbers as $key=>$number)
                                        <option {{isset($draft) && $draft->formatted_number_from==$number->number?'selected':($key==0?'selected':'')}}>{{$number->number}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-12">

                                        <select name="to_numbers[]" id="toNumbers" class="select2 compose-select"
                                                multiple="multiple"
                                                data-placeholder="{{trans('customer.recipient')}}:">

                                            @if(isset($draft) && $draft->formatted_number_to)
                                                @foreach($draft->formatted_number_to_array as $to)
                                                    <option selected value="{{$to}}">{{$to}}</option>
                                                @endforeach
                                            @endif
                                            @isset($users_to_contacts)
                                                <optgroup label="Contacts">
                                                    @foreach($users_to_contacts as $to)
                                                        <option value="{{json_encode($to)}}">{{$to['value']}}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endisset

                                            @isset($users_to_groups)
                                                <optgroup label="Groups">
                                                    @foreach($users_to_groups as $to)
                                                        <option value="{{json_encode($to)}}">{{$to['value']}}</option>
                                                    @endforeach
                                                </optgroup>
                                            @endisset
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                            <textarea name="body" id="compose-textarea" class="form-control compose-body"
                                      placeholder="{{trans('customer.enter_message')}}">{{isset($draft)?$draft->body:''}}</textarea>
                            </div>
                            <div class="form-group d-none">
                                <label for="mms_files">{{trans('customer.choose_file')}}:</label>
                                <input type="file" accept="image/*" id="mms_files" class="form-control" name="mms_files[]" multiple>
                            </div>
                            <div class="form-group">

                                <div class="icheck-success d-inline">
                                    <input {{isset($draft) && $draft->schedule_datetime?'checked':''}} name="isSchedule"
                                           type="checkbox" id="isScheduled">
                                    <label for="isScheduled">
                                        {{trans('customer.schedule')}}
                                    </label>
                                </div>

                                <input style="display: {{isset($draft) && $draft->schedule_datetime?'block':'none'}}"
                                       name="schedule"
                                       value="{{isset($draft) && $draft->schedule_datetime?$draft->schedule_datetime->format('m/d/Y h:i A'):''}}"
                                       id="schedule" type='text'
                                       class="form-control"/>
                            </div>

                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="float-right">
                                <button id="draft" type="button" class="btn btn-default"><i
                                        class="fas fa-pencil-alt"></i> {{trans('customer.draft')}}
                                </button>
                                <button type="submit" class="btn btn-primary"><i
                                        class="far fa-envelope"></i> {{trans('customer.send')}}
                                </button>
                            </div>
                            <button id="reset" type="button" class="btn btn-default"><i
                                    class="fas fa-times"></i> {{trans('customer.reset')}}
                            </button>
                        </div>
                        <!-- /.card-footer -->
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->


@endsection

@section('extra-scripts')
    <script src="{{asset('plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/daterangepicker/moment.min.js')}}"></script>
    <script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>

    <script !src="">
        "use strict";
        var select2 = $('#toNumbers').select2({
            minimumInputLength: 1,
            tags: true,
            tokenSeparators: [",", " "],
        })

        $('#fromNumber').select2({
            theme: 'bootstrap4'
        });

        $(function () {
            "use strict";
            $('#schedule').daterangepicker({
                autoUpdateInput: true,
                singleDatePicker: true,
                timePicker: true,
                locale: {
                    format: 'MM/DD/YYYY hh:mm A'
                }
            });
        });

        $('#isScheduled').on('change', function (e) {
            const checked = $(this).is(':checked');
            if (checked) {
                $('#schedule').show();
            } else {
                $('#schedule').hide();
            }
        })

        $('#reset').on('click', function (e) {
            e.preventDefault();
            $(select2).val('').trigger('change');
            $("#compose-textarea").val('');
            let checked = $("#isScheduled").is(':checked');
            if (checked) {
                $('#isScheduled').click().prop("checked", false);
            }
        })

        $('#draft').on('click', function (e) {
            e.preventDefault();
            const from = $('#fromNumber').val();
            const to = $('#toNumbers').val();
            const body = $('#compose-textarea').val();
            const checked = $("#isScheduled").is(':checked');
            const draft_id = $("#draft_id").val();
            let schedule = '';
            if (checked) {
                schedule = $('#schedule').val();
            }
            $.ajax({
                method: 'post',
                url: '{{route('customer.smsbox.draft.store')}}',
                data: {_token: '{{csrf_token()}}', from, to, body, checked, schedule, draft_id},
                success: function (res) {
                    if (res.status == 'success') {
                        notify('success', res.message);
                        var id = res.data.id;
                        $('#pre_draft').html("<input type='hidden' id='draft_id' name='draft_id' value='" + id + "'/>");

                    } else {
                        notify('danger', res.message);
                    }
                }
            })

        })
    </script>
@endsection


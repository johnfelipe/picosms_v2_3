@extends('layouts.customer')

@section('title','Settings')

@section('extra-css')

@endsection

@section('content')
    <section class="content-header">

    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mx-auto col-sm-10">
                <!-- Custom Tabs -->
                <div class="card">

                    <div class="card-header d-flex p-0">
                        <h2 class="card-title p-3"><a href="{{route('customer.settings.index')}}">{{trans('customer.settings')}}</a></h2>
                        <ul class="nav nav-pills ml-auto p-2">
                            <li class="nav-item"><a class="nav-link active" href="#profile_tab" data-toggle="tab">{{trans('customer.profile')}}</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#password_tab" data-toggle="tab">{{trans('customer.password')}}</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#notification_tab" data-toggle="tab">{{trans('customer.notification')}}</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="profile_tab">
                                <form method="post" role="form" id="profile_form"
                                      action="{{route('customer.settings.profile_update')}}" enctype="multipart/form-data">
                                    @csrf
                                    @include('customer.settings.profile_form')

                                    <button type="submit" class="btn btn-primary">{{trans('customer.submit')}}</button>
                                </form>
                            </div>

                            <div class="tab-pane" id="password_tab">
                                <form method="post" role="form" id="password_form"
                                      action="{{route('customer.settings.password_update')}}">
                                    @csrf
                                    @include('customer.settings.password_form')

                                    <button type="submit" class="btn btn-primary">{{trans('customer.submit')}}</button>
                                </form>
                            </div>
                            <div class="tab-pane" id="notification_tab">
                                <div class="row">
                                    <div class="col-sm-6 ml-2">

                                @include('customer.settings.notification_form')

                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.tab-content -->
                    </div><!-- /.card-body -->
                </div>
                <!-- ./card -->


            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection

@section('extra-scripts')
    <script src="{{asset('plugins/jquery-validation/jquery.validate.min.js')}}"></script>

    <script src="{{asset('plugins/bs-custom-file-input/bs-custom-file-input.js')}}"></script>

    <script !src="">
        "use strict";
        let $validate;
        $validate = $('#profile_form').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
            },
            messages: {
                email: {
                    required: "Please enter a email address",
                    email: "Please enter a vaild email address"
                },
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long"
                },
                first_name: {required: "Please provide first name"},
                last_name: {required: "Please provide last name"}
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
        $('#notification_switch').on('change',function(e){
            const isChecked=$(this).is(':checked');
            $.ajax({
                method:'post',
                url:'{{route('customer.settings.notification_update')}}',
                data:{_token:'{{csrf_token()}}',isChecked},
                success:function(res){
                    notify('success',res.message);
                }
            })
        })

        $(document).ready(function () {
            bsCustomFileInput.init();
        });

    </script>


@endsection


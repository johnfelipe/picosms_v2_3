@extends('layouts.admin')

@section('title','Edit Customers')

@section('extra-css')

@endsection

@section('content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 mx-auto col-sm-10 mt-3">
                <!-- Custom Tabs -->
                <div class="card">

                    <div class="card-header d-flex p-0">
                        <h2 class="card-title p-3"><a href="{{route('admin.customers.index')}}">@lang('admin.customers.customer')</a></h2>
                        <ul class="nav nav-pills ml-auto p-2">
                            <li class="nav-item"><a class="nav-link active" href="#edit_tab" data-toggle="tab">@lang('admin.customers.edit')</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#phone_tab" data-toggle="tab">@lang('admin.customers.phone_number')</a>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="#plan_tab" data-toggle="tab">@lang('admin.customers.plan')</a></li>
                        </ul>
                    </div><!-- /.card-header -->
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="edit_tab">
                                <form method="post" role="form" id="customerForm"
                                      action="{{route('admin.customers.update',[$customer])}}">
                                    @csrf
                                    @method('put')
                                    @include('admin.customers.form')

                                    <button type="submit" class="btn btn-primary">@lang('admin.form.button.submit')</button>
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="phone_tab">

                                <div class="card" id="numberListSection">
                                    <div class="card-header">
                                        <h3 class="card-title">@lang('admin.customers.assign_numbers')</h3>
                                        <button onclick="toggleSection('#numberListSection','#assignNumberSection')"
                                                class="btn btn-sm btn-primary float-right"><span
                                                class="fa fa-plus"></span> @lang('admin.form.button.new')
                                        </button>

                                        <div class="card-tools d-none">
                                            <ul class="pagination pagination-sm float-right">
                                                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>@lang('admin.table.number')</th>
                                                <th>@lang('admin.table.cost')</th>
                                                <th class="table-action">@lang('admin.table.action')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($customer->numbers->isEmpty())
                                                <tr>
                                                    <td colspan="3" class="text-center">@lang('admin.table.empty')</td>
                                                </tr>
                                            @endif
                                            @foreach($customer->numbers as $number)
                                                <tr>
                                                    <td>{{$number->number}}</td>
                                                    <td>{{$number->cost}}</td>
                                                    <td>
                                                        <button
                                                            data-message="@lang('admin.message.remove_number',['number'=>$number->number])"
                                                            data-action="{{route('admin.customer.number.remove')}}"
                                                            data-input='{"id":"{{$number->number_id}}","customer_id":"{{$customer->id}}"}'
                                                            data-toggle="modal" data-target="#modal-confirm"
                                                            id="removeNumber" class="btn btn-danger btn-sm"
                                                            type="button">Remove
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>

                                <div class="card" style="display: none" id="assignNumberSection">
                                    <div class="card-header">
                                        <h3 class="card-title">@lang('admin.customers.choose_number')</h3>
                                        <button onclick="toggleSection('#assignNumberSection','#numberListSection')"
                                                class="btn btn-sm btn-primary float-right"><span
                                                class="fa fa-list"></span> @lang('admin.form.button.list')
                                        </button>
                                        <div class="card-tools d-none">
                                            <ul class="pagination pagination-sm float-right">
                                                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>@lang('admin.table.number')</th>
                                                <th>@lang('admin.table.cost')</th>
                                                <th class="table-action">@lang('admin.table.action')</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if($availableNumbers->isEmpty())
                                                <tr>
                                                    <td colspan="3" class="text-center">@lang('admin.table.empty')</td>
                                                </tr>
                                            @endif

                                            @foreach($availableNumbers as $number)
                                                <tr>
                                                    <td>{{$number->number}}</td>
                                                    <td>{{$number->sell_price}}</td>
                                                    <td>
                                                        <button
                                                            data-message="@lang('admin.message.assign_number',['number'=>$number->number])"
                                                            data-action="{{route('admin.customer.number.assign')}}"
                                                            data-input='{"id":"{{$number->id}}","customer_id":"{{$customer->id}}"}'
                                                            data-toggle="modal" data-target="#modal-confirm"
                                                            id="assignNumber" class="btn btn-info btn-sm" type="button">
                                                            Assign
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.card-body -->
                                </div>


                                <!-- /.card -->
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="plan_tab">

                                <div class="card" id="planListSection">
                                    <div class="card-header">
                                        <h3 class="card-title">@lang('admin.customers.plan')</h3>
                                        <div class="card-tools d-none">
                                            <ul class="pagination pagination-sm float-right">
                                                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                                                <li class="page-item"><a class="page-link" href="#">1</a></li>
                                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- /.card-header -->
                                    <div class="card-body p-0">
                                        <div class="row">
                                            @isset($customer->plan->plan)
                                            <div class="col-sm-5">
                                                <ul class="current-plan list-inline mt-2 mt-sm-5">

                                                    <li>
                                                        <div class="title">@lang('admin.table.current')</div>
                                                        <div class="value">{{$customer->plan->plan->title}}</div>
                                                    </li>
                                                    <li>
                                                        <div class="title">@lang('admin.table.cost')($)</div>
                                                        <div class="value">{{$customer->plan->price}}</div>
                                                    </li>
                                                    <li>
                                                        <div class="title">@lang('admin.table.limit')</div>
                                                        <div class="value">{{$customer->plan->sms_limit}}</div>
                                                    </li>
                                                    <li>
                                                        <div class="title">@lang('admin.table.remain')</div>
                                                        <div class="value">{{$customer->plan->sms_limit}}</div>
                                                    </li>
                                                    @if(!$customer->plan->sms_limit)
                                                        <div class="no-value">@lang('admin.customers.no_plan')</div>
                                                    @endif
                                                </ul>

                                            </div>
                                            @endisset
                                            <div class="col-sm-7">
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th>@lang('admin.table.title')</th>
                                                        <th>@lang('admin.table.limit')</th>
                                                        <th>@lang('admin.table.cost') ($)</th>
                                                        <th class="table-action">@lang('admin.table.action')</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @if($activePlans->isEmpty())
                                                        <tr>
                                                            <td colspan="3" class="text-center">@lang('admin.table.empty')</td>
                                                        </tr>
                                                    @endif
                                                    @php $planId=isset($customer->plan)?$customer->plan->plan_id:''; @endphp
                                                    @foreach($activePlans as $plan)
                                                        <tr>
                                                            <td>{{$plan->title}} @if($planId==$plan->id) (@lang('admin.table.current') @endif</td>
                                                            <td>{{$plan->sms_limit}}</td>
                                                            <td>{{$plan->price}}</td>
                                                            <td>
                                                                <button
                                                                    data-message="{!! trans('admin.message.assign_plan',['plan'=>'<b> '.$plan->title.'</b>']) !!}<br/> <span class='text-sm text-muted'>@lang('admin.message.plan_nb')</span>"
                                                                    data-action="{{route('admin.customer.plan.change')}}"
                                                                    data-input='{"id":"{{$plan->id}}","customer_id":"{{$customer->id}}"}'
                                                                    data-toggle="modal" data-target="#modal-confirm"
                                                                    id="changePlan" class="btn btn-primary btn-sm"
                                                                    type="button">@lang('admin.form.button.change')
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->
                                </div>

                            </div>
                            <!-- /.tab-pane -->
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
    <script !src="">
        "use strict";
        let $validate;
        $validate = $('#customerForm').validate({
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
    </script>
    @if(!isset($customer))
        <script !src="">
            "use strict";
            $validate.rules('add', {
                password: {
                    required: true,
                    minlength: 5
                },
            })
        </script>
    @endif
@endsection


@extends('layouts.customer')

@section('title','Trash | SmsBox')

@section('content')

        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{trans('customer.trash')}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('customer.smsbox.inbox')}}">{{trans('customer.smsbox')}}</a></li>
                            <li class="breadcrumb-item active">{{trans('customer.trash')}}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    <a href="{{route('customer.smsbox.compose')}}" class="btn btn-primary btn-block mb-3">{{trans('customer.compose')}}</a>

                    @include('customer.smsbox.common')

                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">{{trans('customer.trash')}}</h3>

                            <div class="card-tools d-none">
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" placeholder="{{trans('customer.search_mail')}}">
                                    <div class="input-group-append">
                                        <div class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card-tools -->
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body p-0">
                            <div class="mailbox-controls">
                                <!-- Check all button -->
                                <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i class="far fa-trash-alt"></i></button>
                                </div>
                                <!-- /.btn-group -->
                                <button type="button" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i></button>
                                <div class="float-right d-none">
                                    1-50/200
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i></button>
                                        <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-right"></i></button>
                                    </div>
                                    <!-- /.btn-group -->
                                </div>
                                <!-- /.float-right -->
                            </div>
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-hover table-striped">
                                    <thead>
                                    <td>
                                    <th>{{trans('customer.number')}}</th>
                                    <th>{{trans('customer.type')}}</th>
                                    <th>{{trans('customer.message')}}</th>
                                    <th>{{trans('customer.schedule_at')}}</th>
                                    </td>
                                    </thead>
                                    <tbody>
                                    @foreach($trashes as $message)
                                        <tr>
                                            <td>
                                                <div class="icheck-primary">
                                                    <input class="check-single" data-id="{{$message->id}}" type="checkbox"
                                                           id="check-{{$message->id}}">
                                                    <label for="check-{{$message->id}}"></label>
                                                </div>
                                            </td>
                                            <td class="mailbox-name">{{$message->formatted_number_to}}</td>
                                            <td class="mailbox-name">{{$message->type}}</td>
                                            <td class="mailbox-subject">{{$message->body}}</td>
                                            <td class="mailbox-subject">{{$message->schedule_datetime}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <!-- /.table -->
                            </div>
                            <!-- /.mail-box-messages -->
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->


@endsection


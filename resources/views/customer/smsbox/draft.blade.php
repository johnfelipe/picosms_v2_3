@extends('layouts.customer')

@section('title','Draft | SmsBox')

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans('customer.draft')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('customer.smsbox.inbox')}}">{{trans('customer.smsbox')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('customer.draft')}}</li>
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
            <!-- /.card -->

                <!-- /.card -->
            </div>
            <!-- /.col -->
            <div class="col-md-9">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">{{trans('customer.draft')}}</h3>

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
                            <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i
                                    class="far fa-square"></i>
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm"><i class="far fa-trash-alt"></i>
                                </button>
                            </div>
                            <!-- /.btn-group -->
                            <button type="button" class="d-none btn btn-default btn-sm"><i class="fas fa-sync-alt"></i>
                            </button>
                            <div class="float-right d-none">
                                1-50/200
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-left"></i></button>
                                    <button type="button" class="btn btn-default btn-sm"><i
                                            class="fas fa-chevron-right"></i></button>
                                </div>
                                <!-- /.btn-group -->
                            </div>
                            <!-- /.float-right -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <td>
                                        <th>{{trans('customer.from')}}</th>
                                        <th>{{trans('customer.to')}}</th>
                                        <th>{{trans('customer.message')}}</th>
                                        <th>{{trans('customer.schedule_at')}}</th>
                                        <th>{{trans('customer.action')}}</th>
                                    </td>
                                </thead>
                                <tbody>
                                @foreach($drafts as $message)
                                    <tr>
                                        <td>
                                            <div class="icheck-primary">
                                                <input type="checkbox" value="" id="check1">
                                                <label for="check1"></label>
                                            </div>
                                        </td>
                                        <td class="mailbox-name">{{$message->formatted_number_from}}</td>
                                        <td class="mailbox-name">{{$message->formatted_number_to}}</td>
                                        <td class="mailbox-subject">{{$message->body}}</td>
                                        <td class="mailbox-subject">{{$message->schedule_datetime}}</td>
                                        <td class="mailbox-name">
                                            <a class="btn btn-primary btn-sm" href="{{route('customer.smsbox.compose',['draft'=>$message->id])}}">Resume</a>
                                            <button
                                                data-message="{{trans('customer.messages.delete_draft')}} <br/> <span class='text-sm text-muted'>{{trans('customer.messages.delete_draft_nb')}}</span>"
                                                data-action="{{route('customer.smsbox.draft.delete')}}"
                                                data-input='{"id":"{{$message->id}}"}'
                                                data-toggle="modal" data-target="#modal-confirm"
                                                id="deleteDraft" class="btn btn-danger btn-sm"
                                                type="button">Delete
                                            </button>
                                        </td>
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


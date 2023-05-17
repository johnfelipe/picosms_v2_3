@extends('layouts.customer')

@section('title') Dashboard @endsection

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">

    </section>
    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <!-- /.col-md-6 -->
                <div class="col-lg-10 mx-auto">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="m-0">{{trans('customer.billing')}}</h5>
                        </div>
                        <div class="card-body">

                            <table class="w-100">
                                <tr>
                                    <td>
                                        <div class="card-title float-none">{{trans('customer.your_plan')}}</div>
                                        <h2>{{$customer_plan->plan->title}}</h2></td>
                                    <td class="text-right">
                                        <button type="button"
                                                class="btn btn-primary d-none">{{trans('customer.update_plan')}}</button>
                                    </td>
                                </tr>
                            </table>

                            <table class="w-50">
                                <tr>
                                    <td class="plan-des-title">{{trans('customer.sms_limit')}}</td>
                                    <td class="plan-des-value">{{$customer_plan->sms_limit}}</td>
                                </tr>
                                <tr>
                                    <td class="plan-des-title">{{trans('customer.usage')}}</td>
                                    <td class="plan-des-value">{{$customer_plan->sms_limit-$customer_plan->available_sms}}</td>
                                </tr>
                                <tr>
                                    <td class="plan-des-title">{{trans('customer.cost')}}</td>
                                    <td class="plan-des-value">${{$customer_plan->price}}</td>
                                </tr>
                            </table>

                            <div id="plans" class="plans-wrapper mt-3">
                                @foreach($plans as $plan)
                                    <div
                                        class="columns {{$customer_plan->plan_id==$plan->id?'plan-active':''}}">
                                        <ul class="price">
                                            <li class="grey">{{$plan->title}} <span
                                                    class="plan-title-current">{{$customer_plan->plan_id==$plan->id?'(Current)':''}}</span>
                                            </li>
                                            <li class="price-tag">$ {{$plan->price}}</li>
                                            <li>{{$plan->sms_limit}} {{trans('customer.sms')}}</li>
                                            <li>{{trans('customer.unlimited_support')}}</li>
                                            <li>{{trans('customer.cancel_anytime')}}</li>
                                            <li>
                                                @if($customer_plan->plan_id!=$plan->id)
                                                    @if(Module::has('PaymentGateway') && Module::find('PaymentGateway')->isEnabled())
                                                        <button
                                                            data-message="{!! trans('customer.messages.update_plan',['plan'=>$plan->title]) !!} <br/> <span class='text-sm text-muted'>{{trans('customer.messages.update_plan_nb')}}</span>"
                                                            data-action="{{route('paymentgateway::process')}}"
                                                            data-input='{"id":"{{$plan->id}}"}'
                                                            data-toggle="modal" data-target="#modal-confirm"
                                                            type="button"
                                                            class="btn btn-primary btn-sm">{{trans('customer.choose')}}
                                                        </button>
                                                    @else
                                                        <button
                                                            data-message="{!! trans('customer.messages.update_plan',['plan'=>$plan->title]) !!} <br/> <span class='text-sm text-muted'>{{trans('customer.messages.update_plan_nb')}}</span>"
                                                            data-action="{{route('customer.billing.update')}}"
                                                            data-input='{"id":"{{$plan->id}}"}'
                                                            data-toggle="modal" data-target="#modal-confirm"
                                                            type="button"
                                                            class="btn btn-primary btn-sm">{{trans('customer.choose')}}
                                                        </button>
                                                    @endif
                                                @endif
                                            </li>

                                        </ul>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
@endsection


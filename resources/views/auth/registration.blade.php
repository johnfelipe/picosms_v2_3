@extends('layouts.auth_customer')

@section('title','Sign up')

@section('content')
    <div class="card-body login-card-body">
        <p class="login-box-msg">@lang('auth.registration.title')</p>

        <form action="{{route('signup')}}" method="post">
            @csrf
            <div class="input-group mb-3">
                <input name="first_name" type="text" class="form-control" placeholder="@lang('auth.registration.form.first_name')">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input name="last_name" type="text" class="form-control" placeholder="@lang('auth.registration.form.last_name')">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input name="email" type="email" class="form-control" placeholder="@lang('auth.registration.form.email')">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input name="password" type="password" class="form-control" placeholder="@lang('auth.registration.form.password')">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8 text-sm">
                {!! trans('auth.terms_condition',['terms'=>'<a href="#">Terms and Condition</a>']) !!}
                </div>
                <!-- /.col -->
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">@lang('auth.form.button.sign_up')</button>
                </div>
                <!-- /.col -->
            </div>
        </form>
        <!-- /.social-auth-links -->
<div class="row mt-3">
    <div class="col-6">
        <p class="mb-1">
            <a href="{{route('password.request')}}">@lang('auth.form.forget_password')</a>
        </p>
    </div>
    <div class="col-6">
        <p class="mb-0">
            <a href="{{route('login')}}" class="text-center">@lang('auth.form.sign_in')</a>
        </p>
    </div>
</div>


    </div>

@endsection

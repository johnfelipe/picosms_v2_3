@extends('layouts.auth_customer')

@section('title','Login')

@section('content')
    <div class="card-body login-card-body">
        <p class="login-box-msg">@lang('auth.login.title')</p>

        <form action="{{route('authenticate')}}" method="post">
            @csrf
            <div class="input-group mb-3">
                <input name="email" type="email" class="form-control" placeholder="@lang('auth.login.form.email')">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-envelope"></span>
                    </div>
                </div>
            </div>
            <div class="input-group mb-3">
                <input name="password" type="password" class="form-control" placeholder="@lang('auth.login.form.password')">
                <div class="input-group-append">
                    <div class="input-group-text">
                        <span class="fas fa-lock"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-8">
                    <div class="icheck-primary">
                        <input name="remember_me" type="checkbox" id="remember">
                        <label for="remember">
                            @lang('auth.login.form.remember_me')
                        </label>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-4">
                    <button type="submit" class="btn btn-primary btn-block">@lang('auth.form.button.sign_in')</button>
                </div>
                <!-- /.col -->
            </div>
        </form>

        <!-- /.social-auth-links -->
        <div class="row mt-3">
            <div class="col-8">
                <p class="mb-1">
                    <a href="{{route('password.request')}}">@lang('auth.form.forget_password')</a>
                </p>
            </div>
            <div class="col-4">
                <p class="mb-0">
                    <a href="{{route('signup')}}" class="text-center">@lang('auth.form.registration')</a>
                </p>
            </div>
        </div>
    </div>

@endsection

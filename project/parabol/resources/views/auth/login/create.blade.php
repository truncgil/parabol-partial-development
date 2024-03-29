@extends('layouts.auth')

@section('title', trans('auth.login'))

@section('message', trans('auth.login_to'))

@section('content')
    <div role="alert" class="alert alert-danger d-none" :class="(form.response.error) ? 'show' : ''" v-if="form.response.error" v-html="form.response.message"></div>

    {!! Form::open([
        'route' => 'login',
        'id' => 'login',
        '@submit.prevent' => 'onSubmit',
        '@keydown' => 'form.errors.clear($event.target.name)',
        'files' => true,
        'role' => 'form',
        'class' => 'form-loading-button',
        'novalidate' => true
    ]) !!}
        <div class="text-center">
            <div id="g_id_onload"
                data-client_id="135274556225-sg54p0a7klonf0crkh90i6ell9elklhi.apps.googleusercontent.com"
                data-login_uri="https://app.parabol.truncgil.com/auth/create?ekle&_token={{csrf_token()}}"
                data-auto_prompt="false"
                >
            </div>
            <div class="g_id_signin"
                data-type="standard"
                data-size="large"
                data-theme="outline"
                data-text="sign_in_with"
                data-shape="rectangular"
                data-logo_alignment="left">
                </div>
         </div>
        <br>
        {{ Form::emailGroup('email', false, 'envelope', ['placeholder' => trans('general.email')], null, 'has-feedback', 'input-group-alternative') }}

        {{ Form::passwordGroup('password', false, 'unlock-alt', ['placeholder' => trans('install.database.password')], 'has-feedback', 'input-group-alternative') }}

        <div class="row align-items-center">
            @stack('remember_input_start')
                <div class="col-xs-12 col-sm-8">
                    <div class="custom-control custom-control-alternative custom-checkbox">
                        {{ Form::checkbox('remember', 1, null, [
                            'id' => 'checkbox-remember',
                            'class' => 'custom-control-input',
                            'v-model' => 'form.remember'
                        ]) }}
                        <label class="custom-control-label" for="checkbox-remember">
                            <span class="text-white">{{ trans('auth.remember_me') }}</span>
                        </label>
                    </div>
                </div>
            @stack('remember_input_end')

            <div class="col-xs-12 col-sm-4">
                {!! Form::button(
                '<div class="aka-loader"></div> <span>' . trans('auth.login') . '</span>',
                [':disabled' => 'form.loading', 'type' => 'submit', 'class' => 'btn btn-success float-right', 'data-loading-text' => trans('general.loading')]) !!}
            </div>
        </div>

        @stack('forgotten-password-start')
            <div class="mt-5 mb--4">
                <a href="{{ route('forgot') }}" class="text-white"><small>{{ trans('auth.forgot_password') }}</small></a>
            </div>
            <div class="mt-5">
                <a href="{{ url('auth/create')  }}" class="btn btn-info btn-block">Yeni Hesap Oluştur</a>
            </div>
        @stack('forgotten-password-end')
    {!! Form::close() !!}
@endsection

@push('scripts_start')
    <script src="{{ asset('public/js/auth/login.js?v=' . version('short')) }}"></script>
@endpush

@extends('layouts.auth-master')

@section('content')
<div class="card card-primary">
  <div class="card-header"><h4>Iniciar sesión</h4></div>

  <div class="card-body">
    <form method="POST" action="{{ route('login') }}">
        @csrf
      <div class="form-group">
        <label for="email">{{ __('E-Mail Address') }}</label>
        <input aria-describedby="emailHelpBlock" id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" placeholder="correo@ejemplo.com" tabindex="1" value="{{ old('email') }}" autocomplete="off" autofocus>
        <div class="invalid-feedback">
          {{ $errors->first('email') }}
        </div>
        @if(App::environment('demo'))
        <small id="emailHelpBlock" class="form-text text-muted">
            Demo Email: admin@example.com
        </small>
        @endif
      </div>

      <div class="form-group">
        <div class="d-block">
            <label for="password" class="control-label">{{ __('Password') }}</label>
          <div class="float-right">
            <a href="{{ route('password.request') }}" class="text-small">
              {{ __('Forgot Your Password?') }}
            </a>
          </div>
        </div>
        <input aria-describedby="passwordHelpBlock" id="password" type="password" placeholder="Debe contener al menos 8 caracteres" class="form-control{{ $errors->has('password') ? ' is-invalid': '' }}" name="password" tabindex="2" autocomplete="off">
        <div class="invalid-feedback">
          {{ $errors->first('password') }}
        </div>
        @if(App::environment('demo'))
        <small id="passwordHelpBlock" class="form-text text-muted">
            Demo Password: 1234
        </small>
        @endif
      </div>

      <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember"{{ old('remember') ? ' checked': '' }}>
          <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label>
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
            {{ __('Login') }}
        </button>
      </div>
    </form>
  </div>
</div>
<div class="mt-5 text-muted text-center d-none">
  {{ __("Don't have an account?") }} <a href="{{ route('register') }}"> {{ __('Create One')}} </a>
</div>
@endsection

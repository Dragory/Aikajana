<div id="login-area">
    <h1>{{ __('admin.login_header') }}</h1>
    <br>
    {{ Form::start(URL::to_route('admin_login_post')) }}
        <input name="username" type="text" class="input-block-level" placeholder="{{ __('admin.login_username') }}">
        <input name="password" type="password" class="input-block-level" placeholder="{{ __('admin.login_password') }}">
        <label>
            <input name="stay-logged-in" type="checkbox"> {{ __('admin.login_stay_logged_in') }}
        </label>
        <br>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.login_log_in') }}">
    {{ Form::end() }}
</div>
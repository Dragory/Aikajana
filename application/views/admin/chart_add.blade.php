<div class="content-area">
    <h1>{{ __('admin.chart_add_heading') }}</h1>
    <p>
        {{ __('admin.chart_add_description') }}
    </p>

    <h2>{{ __('admin.chart_info') }}</h2>
    {{ Form::start(URL::to_route('admin_chart_add_post')) }}

    {{ View::make('admin.partials.chart_form', ['chart' => new stdClass]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.chart_add') }}">
    </p>
    {{ Form::end() }}
</div>
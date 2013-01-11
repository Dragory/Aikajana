<div class="content-area">
    <h1>{{ __('admin.group_add_heading') }}</h1>
    <p>
        {{ __('admin.group_add_description') }}
    </p>

    <h2>{{ __('admin.group_info') }}</h2>
    {{ Form::start(URL::to_route('admin_group_add_post', [$chart->chart_url])) }}
    <input type="hidden" name="id_chart" value="{{ $chart->id_chart }}">

    {{ View::make('admin.partials.group_form', ['group' => new stdClass]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.group_add') }}">
    </p>
    {{ Form::end() }}
</div>
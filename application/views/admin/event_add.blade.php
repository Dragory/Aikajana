<div class="content-area">
    <h1>{{ __('admin.event_add_heading') }}</h1>
    <p>
        {{ __('admin.event_add_description') }}
    </p>

    <h2>{{ __('admin.event_info') }}</h2>
    {{ Form::start(URL::to_route('admin_event_add_post', [$chart->chart_url, $group->id_group])) }}
    <input type="hidden" name="id_group" value="{{ $group->id_group }}">

    {{ View::make('admin.partials.event_form', ['event' => new stdClass]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.event_add') }}">
    </p>
    {{ Form::end() }}
</div>
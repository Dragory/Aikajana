<div class="content-area">
    <h1>{{ $event->event_name }}</h1>
    <p>
        {{ __('admin.event_description') }}
    </p>

    <h2>{{ __('admin.event_info') }}</h2>
    {{ Form::start(URL::to_route('admin_event_save_post', [$chart->chart_url, $group->id_group, $event->id_event])) }}
    <input type="hidden" name="id_event" value="{{ $event->id_event }}">

    {{ View::make('admin.partials.event_form', ['event' => $event]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.group_save') }}">
    </p>
    {{ Form::end() }}
</div>
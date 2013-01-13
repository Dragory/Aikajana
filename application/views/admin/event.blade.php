<div class="content-area">
    <h1>{{ $event->event_name }}</h1>
    <p>
        {{ __('admin.event_description') }}
    </p>

    <h2>{{ __('admin.event_info') }}</h2>
    {{ Form::start(URL::to_route('admin_event_save_post', $event->id_event)) }}
    <input type="hidden" name="id_event" value="{{ $event->id_event }}">

    {{ View::make('admin.partials.event_form', ['event' => $event, 'colours' => $colours]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.event_save') }}">
    </p>
    {{ Form::end() }}
</div>
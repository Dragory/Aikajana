<div class="content-area">
    <h1>{{ $colour->colour_name }}</h1>
    <p>
        {{ __('admin.colour_description') }}
    </p>

    <h2>{{ __('admin.colour_info') }}</h2>
    {{ Form::start(URL::to_route('admin_colour_save_post', $colour->id_colour)) }}
    <input type="hidden" name="id_colour" value="{{ $colour->id_colour }}">

    {{ View::make('admin.partials.colour_form', ['colour' => $colour]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.colour_save') }}">
    </p>
    {{ Form::end() }}
</div>
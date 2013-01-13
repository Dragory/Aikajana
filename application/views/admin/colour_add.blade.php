<div class="content-area">
    <h1>{{ __('admin.colour_add_heading') }}</h1>
    <p>
        {{ __('admin.colour_add_description') }}
    </p>

    <h2>{{ __('admin.colour_info') }}</h2>
    {{ Form::start(URL::to_route('admin_colour_add_post', $group->id_group)) }}
    <input type="hidden" name="id_group" value="{{ $group->id_group }}">

    {{ View::make('admin.partials.colour_form', ['colour' => new stdClass]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.colour_add') }}">
    </p>
    {{ Form::end() }}
</div>
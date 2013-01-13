<div class="content-area">
    <h1>{{ $group->group_name }}</h1>
    <p>
        {{ __('admin.group_description') }}
    </p>

    <h2>{{ __('admin.group_info') }}</h2>
    {{ Form::start(URL::to_route('admin_group_save_post', [$chart->chart_url, $group->id_group])) }}
    <input type="hidden" name="id_group" value="{{ $group->id_group }}">

    {{ View::make('admin.partials.group_form', ['group' => $group]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.group_save') }}">
    </p>
    {{ Form::end() }}

    <hr>

    <h2>{{ __('admin.group_colours') }}</h2>
    <table class="table table-striped table-bordered table-charts">
        <thead>
            <tr>
                <th>{{ __('admin.group_colours_header_name') }}</th>
                <th>{{ __('admin.group_colours_header_preview') }}</th>
                <th>{{ __('admin.group_colours_header_hex') }}</th>
                <th>{{ __('admin.group_colours_header_rgb') }}</th>
                <th>{{ __('admin.group_colours_header_actions') }}</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ($colours as $colour)
    {
        $editUrl = URL::to_route('admin_colour', $colour->id_colour);
        $deleteUrl = URL::to_route('admin_colour_delete', $colour->id_colour);

        echo '<tr>'.
                '<td><a href="'.$editUrl.'">'.$colour->colour_name.'</a></td>'.
                '<td><div style="width: 32px; height: 32px; border-radius: 4px; background-color: '.$colour->colour_hex.'"></div></td>'.
                '<td>'.$colour->colour_hex.'</td>'.
                '<td>'.$colour->colour_rgb.'</td>'.
                '<td>'.
                    '<a href="'.$editUrl.'"><button class="btn">'.__('admin.group_colour_edit').'</button></a>'.
                    ' <div style="display: inline-block;">'.Form::start($deleteUrl).
                        '<input class="btn btn-danger" type="submit" onclick="if (!window.confirm(\'Poista?\')) return false;" value="'.__('admin.group_colour_delete').'">'.
                    Form::end().'</div>'.
                '</td>'.
             '</tr>';
    }
?>
        </tbody>
    </table>

    <p>
        <a href="{{ URL::to_route('admin_colour_add', $group->id_group) }}">
            <button class="btn btn-primary">{{ __('admin.group_add_colour') }}</button>
        </a>
    </p>

    <hr>

    <h2>{{ __('admin.group_events') }}</h2>
    <table class="table table-striped table-bordered table-charts">
        <thead>
            <tr>
                <th>{{ __('admin.group_events_header_name') }}</th>
                <th>{{ __('admin.group_events_header_colour') }}</th>
                <th>{{ __('admin.group_events_header_time_start') }}</th>
                <th>{{ __('admin.group_events_header_time_end') }}</th>
                <th>{{ __('admin.group_events_header_actions') }}</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ($events as $event)
    {
        $editUrl = URL::to_route('admin_event', $event->id_event);
        $deleteUrl = URL::to_route('admin_event_delete', $event->id_event);

        echo '<tr>'.
                '<td><a href="'.$editUrl.'">'.$event->event_name.'</a></td>'.
                '<td><span style="border-radius: 4px; font-weight: bold; color: #fff; padding: 4px; background-color: '.$event->colour_hex.'">'.$event->colour_name.'</div></td>'.
                '<td>'.date('d.m.Y', strtotime($event->event_time_start)).'</td>'.
                '<td>'.date('d.m.Y', strtotime($event->event_time_end)).'</td>'.
                '<td>'.
                    '<a href="'.$editUrl.'"><button class="btn">'.__('admin.group_event_edit').'</button></a>'.
                    ' <div style="display: inline-block;">'.Form::start($deleteUrl).
                        '<input class="btn btn-danger" type="submit" onclick="if (!window.confirm(\'Poista?\')) return false;" value="'.__('admin.chart_group_delete').'">'.
                    Form::end().'</div>'.
                '</td>'.
             '</tr>';
    }
?>
        </tbody>
    </table>

    <a href="{{ URL::to_route('admin_event_add', $group->id_group) }}">
        <button class="btn btn-primary">{{ __('admin.group_add_event') }}</button>
    </a>
</div>
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
        $editUrl = URL::to_route('admin_event', [$chart->chart_url, $group->id_group, $event->id_event]);
        $deleteUrl = URL::to_route('admin_event_delete', [$chart->chart_url, $group->id_group, $event->id_event]);

        echo '<tr>'.
                '<td><a href="'.$editUrl.'">'.$event->event_name.'</a></td>'.
                '<td><div style="width: 32px; height: 32px; border-radius: 4px; background-color: '.$event->event_colour.'"></div></td>'.
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

    <a href="{{ URL::to_route('admin_event_add', [$chart->chart_url, $group->id_group]) }}">
        <button class="btn btn-primary">{{ __('admin.group_add_event') }}</button>
    </a>
</div>
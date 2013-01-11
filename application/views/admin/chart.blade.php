<div class="content-area">
    <h1>{{ $chart->chart_name }}</h1>
    <p>
        {{ __('admin.chart_description') }}
    </p>

    <h2>{{ __('admin.chart_info') }}</h2>
    {{ Form::start(URL::to_route('admin_chart_save_post', [$chart->chart_url])) }}
    <input type="hidden" name="id_chart" value="{{ $chart->id_chart }}">
    {{ View::make('admin.partials.chart_form', ['chart' => $chart]) }}

    <p>
        <input type="submit" class="btn btn-primary" value="{{ __('admin.chart_save') }}">
    </p>
    {{ Form::end() }}

    <h2>{{ __('admin.chart_groups') }}</h2>
    <table class="table table-striped table-bordered table-charts">
        <thead>
            <tr>
                <th>{{ __('admin.chart_groups_header_name') }}</th>
                <th>{{ __('admin.chart_groups_header_colour') }}</th>
                <th>{{ __('admin.chart_groups_header_actions') }}</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ($groups as $group)
    {
        $eventUrl = URL::to_route('admin_group', [$chart->chart_url, $group->id_group]);
        $deleteUrl = URL::to_route('admin_group_delete', [$chart->chart_url, $group->id_group]);

        echo '<tr>'.
                '<td><a href="'.$eventUrl.'">'.$group->group_name.'</a></td>'.
                '<td><div style="width: 32px; height: 32px; border-radius: 4px; background-color: '.$group->group_colour.'"></div></td>'.
                '<td>'.
                    '<a href="'.$eventUrl.'"><button class="btn">'.__('admin.chart_group_edit').'</button></a>'.
                    ' <div style="display: inline-block;">'.Form::start($deleteUrl).
                        '<input class="btn btn-danger" type="submit" onclick="if (!window.confirm(\'Poista?\')) return false;" value="'.__('admin.chart_group_delete').'">'.
                    Form::end().'</div>'.
                '</td>'.
             '</tr>';
    }
?>
        </tbody>
    </table>

    <a href="{{ URL::to_route('admin_group_add', [$chart->chart_url]) }}">
        <button class="btn btn-primary">{{ __('admin.chart_add_group') }}</button>
    </a>
</div>
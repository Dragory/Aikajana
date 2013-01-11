<div class="content-area">
    <h1>{{ __('admin.charts_heading') }}</h1>
    <p>
        {{ __('admin.charts_description') }}
    </p>

    <h2>{{ __('admin.charts_charts') }}</h2>
    <table class="table table-striped table-bordered table-charts">
        <thead>
            <tr>
                <th>{{ __('admin.charts_header_name') }}</th>
                <th>{{ __('admin.charts_header_link') }}</th>
                <th>{{ __('admin.charts_header_actions') }}</th>
            </tr>
        </thead>
        <tbody>
<?php
    foreach ($charts as $chart)
    {
        $publicUrl = URL::to_route('chart', $chart->chart_url);
        $chartUrl = URL::to_route('admin_chart', $chart->chart_url);
        $deleteUrl = URL::to_route('admin_chart_delete', $chart->chart_url).'?csrf_token='.Session::token();

        echo '<tr>'.
                '<td><a href="'.$chartUrl.'">'.$chart->chart_name.'</a></td>'.
                '<td><a href="'.$publicUrl.'">'.__('admin.charts_link').'</a></td>'.
                '<td>'.
                    '<a href="'.$chartUrl.'"><button class="btn">'.__('admin.charts_edit').'</button></a>'.
                    ' <div style="display: inline-block;">'.Form::start($deleteUrl).
                        '<input class="btn btn-danger" type="submit" onclick="if (!window.confirm(\'Poista?\')) return false;" value="'.__('admin.charts_delete').'">'.
                    Form::end().'</div>'.
                '</td>'.
             '</tr>';
    }
?>
        </tbody>
    </table>

    <a href="{{ URL::to_route('admin_chart_add') }}">
        <button class="btn btn-primary">{{ __('admin.charts_add_new') }}</button>
    </a>
</div>
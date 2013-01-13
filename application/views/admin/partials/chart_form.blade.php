<table class="table-editable" style="width: 50%;">
    <tr>
        <td colspan="2">
            <strong>Kaavion nimi</strong><br>
            <input type="text" name="chart_name" value="{{ \GenericHelpers\objectVal($chart, 'chart_name')}}">
        </td>
    </tr>
    <tr>
        <td>
            <strong>Nimi valikossa</strong><br>
            <input type="text" name="chart_name_short" value="{{ \GenericHelpers\objectVal($chart, 'chart_name_short')}}">
        </td>
        <td>
            <strong>Nimi osoitteessa</strong><br>
            <input type="text" name="chart_url" value="{{ \GenericHelpers\objectVal($chart, 'chart_url')}}">
        </td>
    </tr>
    <tr>
        <td>
            <strong>Kaavion alkuhetki</strong><br>
            <input class="datepicker" type="text" name="chart_time_start" value="{{ \GenericHelpers\objectVal($chart, 'chart_time_start', '0000-00-00')}}">
        </td>
        <td>
            <strong>Kaavion loppuhetki</strong><br>
            <input class="datepicker" type="text" name="chart_time_end" value="{{ \GenericHelpers\objectVal($chart, 'chart_time_end', '0000-00-00')}}">
        </td>
    </tr>
</table>
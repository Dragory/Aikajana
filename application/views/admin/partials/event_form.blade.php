<table class="table-editable" style="width: 80%;">
    <tr>
        <td style="vertical-align: top;">
            <strong>Tapahtuman nimi</strong><br>
            <input type="text" name="event_name" value="{{ \GenericHelpers\objectVal($event, 'event_name') }}">
        </td>
        <td style="vertical-align: top;">
            <strong>Lyhyempi nimi</strong><br>
            <input type="text" name="event_name_short" value="{{ \GenericHelpers\objectVal($event, 'event_name_short') }}">
        </td>
        <td style="vertical-align: top;">
            <strong>Väri</strong><br>
            <select name="id_colour">
<?php
    foreach ($colours as $colour)
    {
        echo '<option'.
                ' class="option-colour"'.
                ' style="background-color: '.$colour->colour_hex.';"'.
                ($colour->id_colour == \GenericHelpers\objectVal($event, 'id_colour') ? ' selected="selected"' : '').
                ' value="'.$colour->id_colour.'">'.
                $colour->colour_name.
             '</option>';
    }
?>
            </select>
            <label>
                <input type="hidden" name="event_colour_inherit" value="0">
                <input type="checkbox" name="event_colour_inherit" value="1"{{ (\GenericHelpers\objectVal($event, 'event_colour_inherit') ? ' checked="checked"' : '') }}> Käytä ryhmän väriä
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr></td>
    </tr>
    <tr>
        <td>
            <strong>Alkamisaika</strong><br>
            <input class="datepicker" type="text" name="event_time_start" value="{{ \GenericHelpers\objectVal($event, 'event_time_start') }}">
        </td>
        <td>
            <strong>Päättymisaika</strong><br>
            <input class="datepicker" type="text" name="event_time_end" value="{{ \GenericHelpers\objectVal($event, 'event_time_end') }}">
        </td>
    </tr>
    <tr>
        <td>
            <strong>Näytetty alkamisaika</strong><br>
            <input type="text" name="event_start" value="{{ \GenericHelpers\objectVal($event, 'event_start') }}">
        </td>
        <td>
            <strong>Näytetty päättymisaika</strong><br>
            <input type="text" name="event_end" value="{{ \GenericHelpers\objectVal($event, 'event_end') }}">
        </td>
    </tr>
    <tr>
        <td>
            <label>
                <input type="hidden" name="event_start_unsure" value="0">
                <input type="checkbox" name="event_start_unsure" value="1"{{ (\GenericHelpers\objectVal($event, 'event_start_unsure') ? ' checked="checked"' : '') }}> Epävarma alkamisaika
            </label>
        </td>
        <td>
            <label>
                <input type="hidden" name="event_end_unsure" value="0">
                <input type="checkbox" name="event_end_unsure" value="1"{{ (\GenericHelpers\objectVal($event, 'event_end_unsure') ? ' checked="checked"' : '') }}> Epävarma loppumisaika
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr></td>
    </tr>
    <tr>
        <td>
            <strong>Tapahtumapaikka</strong><br>
            <textarea name="event_location">{{ \GenericHelpers\objectVal($event, 'event_location') }}</textarea>
        </td>
        <td>
            <strong>Casus belli</strong><br>
            <textarea name="event_casusbelli">{{ \GenericHelpers\objectVal($event, 'event_casusbelli') }}</textarea>
        </td>
        <td>
            <strong>Lopputulos</strong><br>
            <textarea name="event_result">{{ \GenericHelpers\objectVal($event, 'event_result') }}</textarea>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr></td>
    </tr>
    <tr>
        <td>
            <strong>Osapuoli #1</strong><br>
            <textarea name="event_side1">{{ \GenericHelpers\objectVal($event, 'event_side1') }}</textarea>
        </td>
        <td>
            <strong>Osapuoli #2</strong><br>
            <textarea name="event_side2">{{ \GenericHelpers\objectVal($event, 'event_side2') }}</textarea>
        </td>
    </tr>
    <tr>
        <td>
            <strong>Osapuolen #1 vahvuus</strong><br>
            <textarea name="event_strength1">{{ \GenericHelpers\objectVal($event, 'event_strength1') }}</textarea>
        </td>
        <td>
            <strong>Osapuolen #2 vahvuus</strong><br>
            <textarea name="event_strength2">{{ \GenericHelpers\objectVal($event, 'event_strength2') }}</textarea>
        </td>
    </tr>
    <tr>
        <td>
            <strong>Osapuolen #1 kaatuneita</strong><br>
            <textarea name="event_dead1">{{ \GenericHelpers\objectVal($event, 'event_dead1') }}</textarea>
        </td>
        <td>
            <strong>Osapuolen #2 kaatuneita</strong><br>
            <textarea name="event_dead2">{{ \GenericHelpers\objectVal($event, 'event_dead2') }}</textarea>
        </td>
    </tr>
    <tr>
        <td>
            <strong>Osapuolen #1 haavoittuneita</strong><br>
            <textarea name="event_injured1">{{ \GenericHelpers\objectVal($event, 'event_injured1') }}</textarea>
        </td>
        <td>
            <strong>Osapuolen #2 haavoittuneita</strong><br>
            <textarea name="event_injured2">{{ \GenericHelpers\objectVal($event, 'event_injured2') }}</textarea>
        </td>
    </tr>
    <tr>
        <td colspan="3"><hr></td>
    </tr>
    <tr>
        <td colspan="3">
            <strong>Lisätietoja</strong><br>
            <textarea name="event_info">{{ \GenericHelpers\objectVal($event, 'event_info') }}</textarea>
        </td>
    </tr>
</table>
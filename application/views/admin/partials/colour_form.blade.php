<table class="table-editable" style="width: 50%;">
    <tr>
        <td>
            <strong>Värin nimi</strong><br>
            <input type="text" name="colour_name" value="{{ \GenericHelpers\objectVal($colour, 'colour_name')}}">
        </td>
        <td>
            <strong>Hex</strong><br>
            <input class="colorpicker" type="text" name="colour_hex" value="{{ \GenericHelpers\objectVal($colour, 'colour_hex')}}">
        </td>
    </tr>
</table>
<table style="width: 50%;">
    <tr>
        <td>
            <strong>Ryhmän nimi</strong><br>
            <input type="text" name="group_name" value="{{ \GenericHelpers\objectVal($group, 'group_name')}}">
        </td>
        <td>
            <strong>Ryhmän väri</strong><br>
            <input class="colorpicker" type="text" name="group_colour" value="{{ \GenericHelpers\objectVal($group, 'group_colour')}}">
        </td>
    </tr>
</table>
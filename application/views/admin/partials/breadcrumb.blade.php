<ul class="breadcrumb">
    <li style="padding-right: 16px;">
        {{ Form::start(URL::to_route('admin_logout')) }}
        <button class="btn">Kirjaudu ulos</button>
        {{ Form::end() }}
    </li>
<?php
    $num = count($parts);
    $i = 0;
    foreach ($parts as $part)
    {
        $i++;

        $url = $part[0];
        $name = $part[1];

        if ($url)
            echo '<li><a href="'.$url.'">'.$name.'</a>';
        else
            echo '<li>'.$name;

        if ($i < $num)
            echo '<span class="divider">&raquo;</span>';

        echo '</li>';
    }
?>
</ul>
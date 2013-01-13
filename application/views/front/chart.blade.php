<h1>{{ $chart->chart_name }}</h1>
<?php
    $chartDrawer = new ChartDrawer($chart->chart_time_start, $chart->chart_time_end);

    $chartDrawer->setEvents($events);
    $chartDrawer->setGroups($groups);

    echo $chartDrawer->getChartHtml();

    /*$groupMult = 0;
    foreach ($ordered as $group => &$events)
    {
        $y = 

        $groupMult++;
    }*/
?>

<?php

/*var_dump($chart);
var_dump($groups);
var_dump($events);
var_dump($ordered);*/
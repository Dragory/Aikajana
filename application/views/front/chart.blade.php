<h1>{{ $chart->chart_name }}</h1>
<?php
    $chartDrawer = new ChartDrawer(1900, 2012);

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
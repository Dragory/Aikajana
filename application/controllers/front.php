<?php

class Front_Controller extends Base_Controller
{
    public $layout = 'front.__layout';

    protected function loadPage($view, $data = [])
    {
        $this->layout->nest('menu', 'front.__menu');
        $this->layout->nest('content', 'front.'.$view, $data);
    }

    public function action_index()
    {
        $this->loadPage('index');
    }

    public function action_chart()
    {
        $chart  = new Chart;
        $info   = $chart->getChart(1);
        $groups = $chart->getGroups(1);
        $events = $chart->getEvents(1);

        $ordered = $chart->orderEvents($events);

        $this->loadPage('chart', ['chart' => $info, 'groups' => $groups, 'events' => $events, 'ordered' => $ordered]);
    }
}
<?php

class Front_Controller extends Base_Controller
{
    public $layout = 'layouts.front';

    protected function loadPage($view, $data = [])
    {
        $this->layout->nest('menu', 'front.__menu');
        $this->layout->nest('content', 'front.'.$view, $data);
    }

    public function action_index()
    {
        $this->loadPage('index');
    }

    public function action_chart($chart_url)
    {
        $chartModel  = new Chart;

        // Get the requested chart and show an error
        // if it's not found.
        $chart  = $chartModel->getChartByUrl($chart_url);

        if (!$chart) return Response::error('404');

        // Get the chart's groups and events
        $groups = $chartModel->getGroupsByChartId($chart->id_chart);
        $events = $chartModel->getEventsByChartId($chart->id_chart);

        // Calculate the events' depth and place them in an array
        // keyed by the events' groups' IDs.
        $ordered = $chartModel->orderEvents($events);

        // Show the chart page
        $this->loadPage('chart', ['chart' => $chart, 'groups' => $groups, 'events' => $events, 'ordered' => $ordered]);
    }
}
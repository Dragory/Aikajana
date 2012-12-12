<?php

class Front_Controller extends Base_Controller
{
    public $layout = 'front.__layout';

    protected function loadPage($view, $data = [])
    {
        $this->layout->nest('content', 'front.'.$view, $data);
    }

    public function action_index()
    {
        $this->loadPage('index');
    }

    public function action_chart()
    {
        $this->loadPage('chart');
    }
}
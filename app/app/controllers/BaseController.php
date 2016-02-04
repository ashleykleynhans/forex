<?php

use Phalcon\Mvc\Controller,
    Phalcon\Mvc\View\Simple as SimpleView;

class BaseController extends Controller
{
    public $view;
    
    public function initialize()
    {
        $this->view = new SimpleView();
        $this->view->setViewsDir(realpath(__DIR__ . '/../views/') . '/');
    }

}

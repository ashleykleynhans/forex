<?php

class IndexController extends BaseController
{

    /**
     * Display the landing page
     */
    public function indexAction()
    {
        echo $this->view->render('index');
    }

}

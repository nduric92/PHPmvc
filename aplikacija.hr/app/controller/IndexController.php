<?php

class IndexController
{
    public function index()
    {
        $view = new View();
        $view->render('index');
    }

    public function route1()
    {
        $view = new View();
        $view->render('example1');
    }

}
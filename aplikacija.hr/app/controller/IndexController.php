<?php

class IndexController extends Controller
{
    public function index()
    {
        $this->view->render('index');
    }

    public function route1()
    {
        $view = new View();
        $view->render('example1d');
    }

    public function contact()
    {
        $this->view->render('contact');
    }

}
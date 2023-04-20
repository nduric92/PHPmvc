<?php

class IndexController extends Controller
{
    public function index()
    {
        $this->view->render('index');
    }

    public function contact()
    {
        $this->view->render('contact');
    }

    public function aboutus()
    {
        $this->view->render('aboutus');
    }

    public function aboutapp()
    {
        $this->view->render('aboutapp');
    }

    public function login()
    {
        $this->view->render('login',[
            'message'=>'',
            'email'=>''
        ]);
    }

    public function logout()
    {
        unset($_SESSION['auth']);
        session_destroy();
        header('location:' . App::config('url'));
    }




}
<?php


class CycleController extends AuthorizationController
{
    private $viewPath = 'private' . 
    DIRECTORY_SEPARATOR . 'cycles' . 
    DIRECTORY_SEPARATOR;
    private $e; //start data
    private $messages=[];

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        $this->view->render($this->viewPath . 'index',[
            'data'=>$this->adjustData(Cycle::read()),
            'css'=>'cycle.css'
        ]);
    }


    private function adjustData($cycle)
    {
        foreach($cycle as $c)
        {
            if($c->date==null || $c->date=='0000-00-00'){
                $c->date = 'Not defined';
            }  
            if($c->product==null || $c->product==''){
                $c->product = 'Not defined';
            }
        }
        return $cycle;
    }

    

    

    

    

    private function startData()
    {
        $e = new stdClass();
        $e->worker_shift='';
        $e->product='';
        $e->amount='';
        $e->date='';
        return $e;
    }
    

    public function delete($id=0){
        $id=(int)$id;
        if($id===0){
            header('location: ' . App::config('url') . 'index/logout');
            return;
        }
        Cycle::delete($id);
        header('location: ' . App::config('url') . 'cycle/index');
    }


}
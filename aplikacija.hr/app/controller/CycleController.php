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







    public function new()
    {
        $this->change(Cycle::create([
            'worker_shift'=>'',
            'product'=>'',
            'amount'=>'',
            'date'=>''
        ]));
    }




    public function change($id='')
    {
        if($_SERVER['REQUEST_METHOD']==='GET'){
            $this->change_GET($id);
            return;
        }


        $this->e = (object)$_POST;

        try {
            $this->e->id=$id;
            $this->controll();
            $this->prepareBase();
            Cycle::update((array)$this->e);
            header('location:' . App::config('url') . 'cycle');
           } catch (\Exception $th) {
            $this->view->render($this->viewPutanja .
            'details',[
                'messages'=>$this->messages,
                'e'=>$this->e
            ]);
           }        

    }

    private function change_GET($id)
    {
        $this->e = Cycle::readOne($id);
       $products = [];
       $p = new stdClass();
       $p->id=0;
       $p->name='Select';
       $products[]=$p;
       foreach(Product::read() as $product){
        $products[]=$product;
       }

       if($this->e->date!=null){
        $this->e->date = date('Y-m-d',strtotime($this->e->date));
       }
       $this->view->render($this->viewPath. 
       'details',[
           'e'=>$this->e,
           'workers'=>Worker::read(),
           'products'=>$products
       ]); 
    }







    private function callView($parameters)
    {
        $this->view->render($this->viewPath . 
       'new',$parameters);  
    }

    public function prepareView()
    {
        $this->e = (object)$_POST;
    }

    public function controllNew()
    {
        
    }

    public function prepareBase()
    {

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
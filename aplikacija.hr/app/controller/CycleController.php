<?php


class CycleController extends AuthorizationController
{
    private $viewPath = 'private' . 
    DIRECTORY_SEPARATOR . 'cycles' . 
    DIRECTORY_SEPARATOR;
    private $e; //start data
    private $message='';

    public function __construct()
    {
        parent::__construct();
    }


    public function index()
    {
        if(isset($_GET['condition'])){
            $condition = trim($_GET['condition']);
        }else{
            $condition='';
        }

        if(isset($_GET['page'])){
            $page = (int)$_GET['page'];
            if($page<1){
                $page=1;
            }
        }else{
            $page=1;
        }

        $tc = Cycle::totalCycles($condition);

        $last = (int)ceil($tc/App::config('brps'));

        $this->view->render($this->viewPath . 'index',[
            'data'=>$this->adjustData(Cycle::read($condition,$page)),
            'condition'=>$condition,
            'page'=>$page,
            'last'=>$last,
            'css'=>'cycle.css',
            'message'=>$this->message
        ]);
    }


    public function new()
    {
        $productId=Product::firstProduct();
        if($productId==0){
            header('location: ' . App::config('url') . 'product?p=1');
        }

        $workerId=Worker::firstWorker();
        if($workerId==0){
            header('location: ' . App::config('url') . 'worker?p=1');
        }


        $this->change(Cycle::create([
            'product'=>$productId,
            'worker'=>$workerId,
            'amount'=>'',
            'date'=>''
        ]));
    }

    public function abort($id='')
    {
        $e=Cycle::readOne($id);

        if($e->amount=='' || $e->amount==0 ){
            Cycle::delete($e->id);
        }
        header('location: ' . App::config('url') . 'cycle');

    }

    public function change($id='')
    {
        if($_SERVER['REQUEST_METHOD']==='GET'){
            if(strlen(trim($id))===0){
                header('location: ' . App::config('url') . 'index/logout');
                return;
            } 
        }

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
            $this->view->render($this->viewPath .
            'details',[
                'message'=>$this->message,
                'products'=>Product::readf(),
                'workers'=>Worker::readf(),
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
        
       

       $this->view->render($this->viewPath. 
       'details',[
           'e'=>$this->e,
           'message'=>'',
           'products'=>Product::readf(),
           'workers'=>Worker::readf()
       ]); 
    }

    

    public function prepareView()
    {
        
    }

    public function controllNew()
    {
        
    }

    public function prepareBase()
    {
        if($this->e->amount==''){
            $this->e->amount=null;
        }
    }

    public function controll()
    {
        $this->controllAmount();
        $this->controllDate();
    }

    private function controllAmount()
    {
        
        $e = $this->e->amount;
        if(strlen(trim($e))===0 || strlen(trim($e))=='' || strlen(trim($e))==0){
            $this->message='Amount mandatory';
            throw new Exception();
        }
        
    }

    private function controllDate()
    {
        

        $e = $this->e->date;
        if(strlen(trim($e))===0 || strlen(trim($e))=='' || strlen(trim($e))==null){
            $this->message='Date mandatory';
            throw new Exception();
        }
        
    }


    private function adjustData($cycle)
    {
        foreach($cycle as $c)
        {
            if($c->date==null || $c->date=='0000-00-00'){
                $c->date = 'Not defined';
            }else{
                $c->date=date('d.m.Y' , strtotime($c->date));
            }

            if($c->amount==null || $c->amount==0){
                $c->amount = '-';
            }
            
            
        }
        return $cycle;
    }
    

    

    private function startData()
    {
        $e = new stdClass();
        $e->product='';
        $e->worker='';
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
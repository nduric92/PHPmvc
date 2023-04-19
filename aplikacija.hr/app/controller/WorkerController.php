<?php


class WorkerController extends AdminController
{
    private $viewPath = 'private' . 
    DIRECTORY_SEPARATOR . 'workers' . 
    DIRECTORY_SEPARATOR;
    private $e; //start data
    private $message=[];
    


    public function index()
    {
        if(isset($_GET['condition'])){
            $condition=trim($_GET['condition']);
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

        $tw = Worker::totalWorkers($condition);

        $last = (int)ceil($tw/App::config('brps'));
        
        $this->view->render($this->viewPath . 'index',[
            'data'=>$this->adjustData(Worker::read($condition,$page)),
            'condition'=>$condition,
            'page'=>$page,
            'last'=>$last,
            'css'=>'worker.css',
            'message'=>'message'
        ]);
    }

    public function new()
    {

        $shiftId=Shift::firstShift();
        if($shiftId==0){
            header('location: ' . App::config('url') . 'shift?p=1');
        }

        
                
        $this->change(Worker::create([
            'name'=>'',
            'surname'=>'',
            'oib'=>'',
            'contractnumber'=>'',
            'iban'=>'',
            'shift'=>$shiftId
        ]));
        
                
    }
    

    public function abort($id='')
    {
        $e=Worker::readOne($id);

        if($e->name=='' && 
        $e->surname=='' ){
            Worker::delete($e->id);
            
        }
        header('location: ' . App::config('url') . 'worker');

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
            Worker::update((array)$this->e);
            header('location:' . App::config('url') . 'worker');
           } catch (\Exception $th) {
            $this->view->render($this->viewPath .
            'details',[
                'message'=>$this->message,
                'e'=>$this->e,
                'shifts'=>Shift::read()
            ]);
        } 


    }

    private function callView($parameters)
    {
        $this->view->render($this->viewPath . 
       'details',$parameters);  
    }

    private function change_GET($id)
    {
        $this->e = Worker::readOne($id);
        $p = new stdClass();
        $p->id=0;
        $p->name='Select';
             
        

       $this->view->render($this->viewPath. 
       'details',[
        'e'=>$this->e,
        'message'=>'',
        'shifts'=>Shift::read()
       ]); 
    }

    public function delete($id=0){
        $id=(int)$id;
        if($id===0){
            header('location: ' . App::config('url') . 'index/logout');
            return;
        }
        Worker::delete($id);
        header('location: ' . App::config('url') . 'worker/index');
    }

    
    private function startData()
    {
        $e = new stdClass();
        $e->name='';
        $e->surname='';
        $e->oib='';
        $e->contractnumber='';
        $e->iban='';
        $e->shift='';
        return $e;
    }

    private function prepareView()
    {
        $this->e = (object)$_POST;
    }
    

    private function controllName()
    {

        $e = $this->e->name;
        if(strlen(trim($e))===0){
            $this->message='Name mandatory';
            throw new Exception();
        }

        if(strlen(trim($e))>50){
            $this->message='The name must be less than 50 characters';
            throw new Exception();
        }

        
    }

    private function controllSurname()
    {
        
        $s = $this->e->surname;
        if(strlen(trim($s))===0){
            $this->message='Surname mandatory';
            throw new Exception();
        }

        if(strlen(trim($s))>50){
            $this->message='Surname must be less than 50 characters';
            throw new Exception();
        }

        
    }

    private function prepareBase()
    {
        
    }
    

    private function adjustData($workers)
    {
        foreach($workers as $w)
        {
            $w->title=$w->name;
            if(strlen($w->name)>25){
                $w->name = substr($p->name,0,23) . '...';
            }
            $w->title=$w->oib;
            if($w->oib==null){
                $w->oib = '-';
            }

            $w->title=$w->contractnumber;
            if($w->contractnumber==null){
                $w->contractnumber = '-';
            }

            $w->title=$w->iban;
            if($w->iban==null){
                $w->iban = '-';
            }
        }
        return $workers;
    }

    public function controll()
    {
        $this->controllName(); 
        $this->controllSurname();
    }

}
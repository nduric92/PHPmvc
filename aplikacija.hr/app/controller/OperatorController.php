<?php


class OperatorController extends AdminController
{
    private $viewPath = 'private' . 
    DIRECTORY_SEPARATOR . 'operators' . 
    DIRECTORY_SEPARATOR;
    private $e;//srart data
    private $message='';


    public function index()
    {
        $this->view->render($this->viewPath . 'index',[
            'data'=>$this->adjustData(Operator::read()),
            'css'=>'operator.css'
        ]);
    }

    public function new()
    {
        if($_SERVER['REQUEST_METHOD']==='GET'){
            $this->callView([
                'e'=>$this->startData(),
                'message'=>$this->message
            ]);
            return;
        }
        $this->prepareView();
        if(!$this->controllNew()){
            $this->callView([
                'e'=>$this->e,
                'message'=>$this->message
            ]);
            return;
        }
        Operator::create((array)$this->e);  //ako je sve OK spremiti u bazu
        $this->callView([
            'e'=>$this->startData(),
            'message'=>'Successfully saved'
        ]);
    }




    public function change($id='')
    {
        if($_SERVER['REQUEST_METHOD']==='GET'){
            if(strlen(trim($id))===0){
                header('location: ' . App::config('url') . 'index/logout');
                return;
            }

            $id=(int)$id;
            if($id===0){
                header('location: ' . App::config('url') . 'index/logout');
                return;
            }

            $this->e = Operator::readOne($id);

            if($this->e==null){
                header('location: ' . App::config('url') . 'index/logout');
                return;
            }


            $this->view->render($this->viewPath . 
            'change',[
                'e'=>$this->e,
                'message'=>''
            ]);  
            return;
        }

        // ovdje je POST
        $this->prepareView();
        if(!$this->controllChange()){// kontrolirati podatke, ako nešto ne valja vratiti na view s porukom 
            $this->view->render($this->viewPath . 
            'change',[
                'e'=>$this->e,
                'message'=>$this->message
            ]);  
         return;
        }

        $this->e->id=$id;
        $this->prepareBase(); // priprema za bazu
        Operator::update((array)$this->e);
        $this->view->render($this->viewPath . 
        'change',[
            'e'=>$this->e,
            'message'=>'Succesfully updated'
        ]);  
    }


   public function delete($id=0)
   {
        $id=(int)$id;
        if($id===0){
            header('location: ' . App::config('url') . 'index/logout');
            return;
        }
        Operator::delete($id);
        header('location: ' . App::config('url') . 'operator/index');
    }


   private function controllChange()
   {
       return $this->controllName() && $this->controllPassword() ;
   }


   private function prepareBase()
   {
        
   }


    private function controllNew()
    {
        return $this->controllName() && $this->controllSurname() && $this->controllPassword() && $this->controllSameEmail();
    }

    private function callView($parameters)
    {
        $this->view->render($this->viewPath . 
       'new',$parameters);  
    }

    private function prepareView()
    {
        $this->e = (object)$_POST;
    }

    private function controllName()
    {
        $s = $this->e->name;
        if(strlen(trim($s))===0){
            $this->message='Name mandatory';
            return false;
        }

        if(strlen(trim($s))>50){
            $this->message='The name must be less than 50 characters';
            return false;
        }

        return true;
    }

    private function controllSurname()
    {
        $s = $this->e->surname;
        if(strlen(trim($s))===0){
            $this->message='Surname mandatory';
            return false;
        }

        if(strlen(trim($s))>50){
            $this->message='Surname must be less than 50 characters';
            return false;
        }

        return true;
    }


    private function startData()
    {
        $e = new stdClass();
        $e->name='';
        $e->surname='';
        $e->email='';
        $e->role='';
        $e->password='';
        return $e;
    }


    private function adjustData($operators)
    {
        foreach($operators as $o)
        {            
            $o->title=$o->email;
            if($o->email==null){
                $o->email = 'Not set';
            }
        }
        return $operators;
    }


    private function controllPassword()
    {
        $pw = $this->e->password;
        $pww = $this->e->confirmpw;

        if(strlen(trim($pw))===0)
        {
            $this->message='Password mandatory!';
            return false;
        }
        if(strlen(trim($pww))===0)
        {
            $this->message='Confirm Password mandatory!';
            return false;
        }

        if(strlen(trim($pw))>50)
        {
            $this->message='Password can not be longer than 50 chars!';
            return false;
        }
        if(strlen(trim($pww))>50)
        {
            $this->message='Password can not be longer than 50 chars!';
            return false;
        }
        if(strlen(trim($pw))<8)
        {
            $this->message='Password cannot be shorter than 8 chars!';
            return false;
        }
        if(strlen(trim($pww))<8)
        {
            $this->message='Password cannot be shorter than 8 chars!';
            return false;
        }

        if(!($pw===$pww))
        {
            $this->message='Password does not match!';
            return false;
        }

        return true;
    }

    private function controllSameEmail($id='')
    {
        if(Operator::sameEmail($this->e->email,$id))
        {
            $this->message='Email already exists';
            return false;
        }
        return true;
    }

    



}
<?php

class Operator{
    
    public static function read()
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        select 	id,
                name,
                surname,
                email,
                password,
                `role` 
        from operator 
        group by 	id,
                    name,
                    surname ,
                    email ,
                    password ,
                    `role` 
        order by name asc
        ');
        $expression->execute();
        return $expression->fetchAll();
    }

    public static function readOne($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
        select 	id,
                name,
                surname,
                email,
                password,
                `role` 
        from operator
        where id=:id
        order by name asc
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        return $expression->fetch();
    }

    
    public static function authorize($email,$password)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        select * from operator where email=:email
        ');
        $expression->execute(['email'=>$email]);

        $operator = $expression->fetch();

        if($operator==null){
            return null;
        }

        if(!password_verify($password,$operator->password)){
            return null;
        }

        unset($operator->password);

        return $operator;
    }

    public static function create($parameters)
    {//Log::info($parameters);
        $parameters['password']=password_hash($parameters['password'],PASSWORD_BCRYPT);
        unset($parameters['confirmpw']);
        try{
            $conection = DB::getInstance();
            $expression = $conection->prepare('
            
                insert into operator(name,surname,email,role,password)
                values (:name,:surname,:email,:role,:password);
            
            ');
            $expression->execute($parameters);
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
    }

    public static function update($parameters)
    {//Log::info($parameters);
        $parameters['password']=password_hash($parameters['password'],PASSWORD_BCRYPT);
        unset($parameters['confirmpw']);
        try{
            $conection = DB::getInstance();
            $expression = $conection->prepare('
            
                update operator set
                name=:name,
                surname=:surname,
                email=:email,
                role=:role,
                password=:password
                where id=:id
            
            ');
            $expression->execute($parameters);
        }
        catch(Exception $e){
            echo $e->getMessage();
        }
                
    }

    public static function updatePassword($parameters)
    {
        $parameters['password']=password_hash($parameters['password'],PASSWORD_BCRYPT);
        try{
            $connection=DB::getInstance();
            $expression=$connection->prepare('
            update operator set
            password=:password
            where id=:id
            ');
            $expression->execute($parameters);
            }
            catch(Exception $e)
            {
                echo $e->getMessage();
            }
    }

    public static function delete($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            delete from operator
            where id=:id
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        $expression->execute();
    }

    public static function sameName($s)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            select id from operator
            where name=:name
        
        ');
        $expression->execute([
            'name'=>$s
        ]);
        $id=$expression->fetchColumn();
        return $id>0;
    }

    public static function sameEmail($s,$id='')
    {
        $conection=DB::getInstance();
        if($id!==''){
            $expression=$conection->prepare('

                select count(id) from operator
                where email=:email
                and id!=:id

            ');
            $expression->execute([
                'email'=>$s,
                'id'=>$id
            ]);
        }
        else{
            $expression=$conection->prepare('

                select count(id) from operator
                where email=:email

            ');
            $expression->execute([
                'email'=>$s
            ]);
        }
        $id=$expression->fetchColumn();
        return $id>0;
    }

}
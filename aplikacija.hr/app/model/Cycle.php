<?php

class Cycle{
    
    public static function read()
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        select  wsp.id,
                a.name,
                a.surname,
                c.name as product,
                wsp.amount, 
                wsp.date 
        from worker a
        inner join worker_shift ws on a.id = ws.worker 
        inner join shift b on b.id = ws.shift
        inner join cycle wsp on ws.id =wsp.worker_shift 
        left join product c on wsp.product = c.id
        group by 
                wsp.id,
                a.name,
                a.surname, 
                c.name,
                wsp.amount, 
                wsp.date
        order by date desc;
        ');
        $expression->execute();
        return $expression->fetchAll();
    }

    public static function readOne($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
        select  wsp.id,
                a.name,
                a.surname,
                c.name as product,
                wsp.amount, 
                wsp.date 
        from worker a
        inner join worker_shift ws on a.id = ws.worker 
        inner join shift b on b.id = ws.shift
        inner join cycle wsp on ws.id =wsp.worker_shift 
        left join product c on wsp.product = c.id
        where wsp.id=:id
        order by date desc;
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        return $expression->fetch();
    }


    public static function update($parameters)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            update cycle set
            worker=:worker,
            product=:product,
            amount=:amount,
            date=:date
            where id=:id
        
        ');
        $expression->execute($parameters);
    }
    

    

    
    
    public static function delete($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            delete from cycle
            where id=:id
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        $expression->execute();
    }

    
    
}
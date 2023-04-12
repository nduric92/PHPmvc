<?php

class Cycle{
    
    public static function read($condition='',$page=1)
    {
        $condition = '%' . $condition . '%';
        $brps = App::config('brps');
        $start = ($page * $brps) - $brps;

        $conection = DB::getInstance();
        $expression = $conection->prepare('
        select 	a.id,
                b.name,
                b.surname,
                c.name as product,
                a.amount,
                a.date
        from `cycle` a
        inner join worker b on a.worker = b.id 
        inner join product c on c.id = a.product 
        where concat(b.name, \' \', b.surname, \' \', c.name)
        like :condition
        group by 	a.id,
                    b.name,
                    b.surname,
                    c.name,
                    a.amount,
                    a.`date`
        order by date desc
        limit :start, :brps
        ');
        $expression->bindValue('start',$start,PDO::PARAM_INT);
        $expression->bindValue('brps',$brps,PDO::PARAM_INT);
        $expression->bindParam('condition',$condition);

        $expression->execute();
        return $expression->fetchAll();
    }

    

    public static function readOne($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('

        select * from cycle
        where id=:id
        
        ');
        $expression->execute([
            'id'=>$id
        ]);

        $cycle=$expression->fetch();
        return $cycle;
    }

    public static function create($parameters)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            insert into cycle
            (product,worker,amount,
            date) 
            values
            (:product,:worker,:amount,
            :date);
        
        ');
        $expression->execute($parameters);
        return $conection->lastInsertId();
    }


    public static function update($parameters)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            update cycle set
            product=:product,
            worker=:worker,
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

    public static function totalCycles($condition='')
    {

        $condition = '%' . $condition . '%';
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
        select 	count(*)
        from `cycle` a
        inner join worker b on a.worker = b.id 
        inner join product c on c.id = a.product 
        where concat(b.name, \' \', b.surname, \' \', c.name)
        like :condition;
        
        ');
        $expression->execute([
            'condition'=>$condition
        ]);
        return $expression->fetchColumn();
    }
    
}
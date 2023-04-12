<?php

class Worker{
    
    public static function read($condition='',$page=1)
    {
        $condition='%' . $condition . '%';
        $brps = App::config('brps');
        $start = ($page * $brps) - $brps;

        $conection = DB::getInstance();
        $expression = $conection->prepare('
            select 	a.id,
                    a.name,
                    a.surname,
                    a.oib,
                    a.contractnumber,
                    a.iban,
                    b.name as shift,
                    count(c.id) as cycles
            from worker a
            left join shift b on a.shift = b.id 
            left join cycle c on a.id = c.worker
            where concat(a.name, \' \', a.surname, \' \', ifnull(a.oib,\'\'))
            like :condition
            group by 	a.id,
                        a.name,
                        a.surname,
                        a.oib,
                        a.contractnumber,
                        a.iban,
                        b.name
            order by a.surname asc
            limit :start, :brps
        ');

        $expression->bindValue('start',$start,PDO::PARAM_INT);
        $expression->bindValue('brps',$brps,PDO::PARAM_INT);
        $expression->bindParam('condition',$condition);
        /*$expression->execute([
            'condition'=>$condition
        ]);*/
        $expression->execute();
        return $expression->fetchAll();
    }

    public static function readf()
    {

        $conection = DB::getInstance();
        $expression = $conection->prepare('
            select 	a.id,
                    a.name,
                    a.surname,
                    a.oib,
                    a.contractnumber,
                    a.iban,
                    b.name as shift,
                    count(c.id) as cycles
            from worker a
            left join shift b on a.shift = b.id 
            left join cycle c on a.id = c.worker
            group by 	a.id,
                        a.name,
                        a.surname,
                        a.oib,
                        a.contractnumber,
                        a.iban,
                        b.name
            order by a.surname asc
        ');
        /*$expression->execute([
            'condition'=>$condition
        ]);*/
        $expression->execute();
        return $expression->fetchAll();
    }

    public static function readOne($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
        select * from worker
        where id=:id
        
        
        ');
        
        $expression->execute([
            'id'=>$id
        ]);
        $worker=$expression->fetch();
        return $worker;
    }

    public static function create($parameters)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            insert into worker(name,surname,oib,
            contractnumber,iban,shift) values
            (:name,:surname,:oib,
            :contractnumber,:iban,:shift);
        
        ');
        $expression->execute($parameters);
        return $conection->lastInsertId();
    }

    public static function update($parameters)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            update worker set
            name=:name,
            surname=:surname,
            oib=:oib,
            contractnumber=:contractnumber,
            iban=:iban,
            shift=:shift
            where id=:id
        ');
        $expression->execute($parameters);
    }
    
    public static function delete($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            delete from worker
            where id=:id;
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        $expression->execute();
    }


    public static function firstWorker()
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            select id from worker
            order by id limit 1
        
        ');
        $expression->execute();
        $id=$expression->fetchColumn();
        return $id;
    
    }

    public static function totalWorkers($condition='')
    {

        $condition = '%' . $condition . '%';
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
        select 	count(*)
        from 
        worker   
        where concat(name, \' \', surname, \' \', 
        ifnull(oib,\'\'))
        like :condition;
        
        ');
        $expression->execute([
            'condition'=>$condition
        ]);
        return $expression->fetchColumn();
    }

    
    
}
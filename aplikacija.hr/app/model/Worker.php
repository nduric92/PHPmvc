<?php

class Worker{
    
    public static function read()
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        select  a.id,
                a.name,
                a.surname,
                a.oib ,
                a.contractnumber ,
                a.iban,
                c.name as shift
        from worker a
        left join worker_shift b on a.id = b.worker 
        left join shift c on c.id =b.shift
        group by    a.id,
                    a.name,
                    a.surname,
                    a.oib ,
                    a.contractnumber ,
                    a.iban,
                    c.name
                    order by a.name, a.surname asc;
        ');
        $expression->execute();
        return $expression->fetchAll();
    }

    public static function readOne($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
        select  a.id,
                a.name,
                a.surname,
                a.oib ,
                a.contractnumber ,
                a.iban
        from worker a
        left join worker_shift b on a.id = b.worker
        where a.id=:id
        order by a.name,a.surname;
        
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        return $expression->fetch();
    }

    public static function create($parameters)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            insert into worker(name,surname,oib,
            contractnumber,iban) values
            (:name,:surname,:oib,
            :contractnumber,:iban);
        
        ');
        $expression->execute($parameters);
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
            iban=:iban
            where id=:id
        ');
        $expression->execute($parameters);
    }
    
    public static function delete($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            delete from worker
            where id=:id
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        $expression->execute();
    }
    
}
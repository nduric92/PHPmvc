<?php

class Product{
    
    public static function read($condition='',$page=1)
    {
        $condition='%' . $condition . '%';
        $brps = App::config('brps');
        $start = ($page * $brps) - $brps;

        $conection = DB::getInstance();
        $expression = $conection->prepare('
        select 	a.id,
                a.name,
                a.color,
                a.price,
                a.customer,
                count(b.id)as cycles
        from product a
        left join `cycle` b on a.id = b.product 
        where a.name
        like :condition
        group by a.id,
                a.name,
                a.color,
                a.price,
                a.customer
        order by a.name asc
        limit :start, :brps
        ');
        $expression->bindValue('start',$start,PDO::PARAM_INT);
        $expression->bindValue('brps',$brps,PDO::PARAM_INT);
        $expression->bindParam('condition',$condition);

        $expression->execute();
        return $expression->fetchAll();
    }

    public static function readf()
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        select 	a.id,
                a.name,
                a.color,
                a.price,
                a.customer,
                count(b.id)as cycles
        from product a
        left join `cycle` b on a.id = b.product 
        group by a.id,
                a.name,
                a.color,
                a.price,
                a.customer
        order by a.name asc
        ');

        $expression->execute();
        return $expression->fetchAll();
    }

    public static function readOne($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            select 	a.id,
                    a.name,
                    a.color,
                    a.price,
                    a.customer
            from product a
            where id=:id
            order by a.name asc;
        
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
        
            insert into product(name,color,price,
            customer) values
            (:name,:color,:price,
            :customer);
        
        ');
        $expression->execute($parameters);
    }

    public static function update($parameters)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            update product set
            name=:name,
            color=:color,
            price=:price,
            customer=:customer
            where id=:id
        ');
        $expression->execute($parameters);
    }

    public static function sameName($s)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            select id from product
            where name=:name
        
        ');
        $expression->execute([
            'name'=>$s
        ]);
        $id=$expression->fetchColumn();
        return $id>0;
    } 
    
    public static function delete($id)
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            delete from product
            where id=:id
        
        ');
        $expression->execute([
            'id'=>$id
        ]);
        $expression->execute();
    }

    public static function firstProduct()
    {
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
            select id from product
            order by id limit 1
        
        ');
        $expression->execute();
        $id=$expression->fetchColumn();
        return $id;
    }

    public static function totalProducts($condition='')
    {

        $condition = '%' . $condition . '%';
        $conection = DB::getInstance();
        $expression = $conection->prepare('
        
        select 	count(*)
        from product 
        where name
        like :condition;
        
        ');
        $expression->execute([
            'condition'=>$condition
        ]);
        return $expression->fetchColumn();
    }
    
}
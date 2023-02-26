<?php

// ova datoteka će definirati temeljne preduvjete
// i napraviti autoloading

define('BP',__DIR__ . DIRECTORY_SEPARATOR);
define('BP_APP', BP . 'app' . DIRECTORY_SEPARATOR);

$forAutoload=[
    BP_APP . 'controller',
    BP_APP . 'core',
    BP_APP . 'model'
]; // view ne ide u autoload jer su u njemu phtml datoteke


$paths = implode(PATH_SEPARATOR,$forAutoload);

set_include_path($paths);

spl_autoload_register(function($class)
{
    //echo 'u spl_autoload, tražim klasu ' . $klasa . '<br>';
    $paths = explode(PATH_SEPARATOR,get_include_path());
    foreach($paths as $path){
        //echo $putanja . '<br>';
        $file = $path . DIRECTORY_SEPARATOR .
                        $class . '.php';
        //echo $datoteka, '<br>';
        if(file_exists($file)){
            require_once $file;
            break;
        }
    }
});

App::start();

//$o = new Osoba();
//echo $o->getIme();
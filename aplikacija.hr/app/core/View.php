<?php

class View
{
    private $template;

    public function __construct($template='template')
    {
        $this->template = $template;
    }

    public function render($phtmlPage,$parameters=[])
    {
        $cssFile = BP . 'public' .
        DIRECTORY_SEPARATOR . 'css' .
        DIRECTORY_SEPARATOR . $phtmlPage . '.css';
        if(file_exists($cssFile)){
            $css=str_replace('\\','/',$phtmlPage) . '.css';
        }

        $jsFile = BP . 'public' .
        DIRECTORY_SEPARATOR . 'js' .
        DIRECTORY_SEPARATOR . $phtmlPage . '.js';
        if(file_exists($jsFile)){
            $js=str_replace('\\','/',$phtmlPage) . '.js';
        }

        $viewFile = BP_APP . 'view' . DIRECTORY_SEPARATOR . 
        $phtmlPage . '.phtml';
        ob_start();
        extract($parameters);
        if(file_exists($viewFile))
        {
            include_once $viewFile;
        }else{
            include_once BP_APP . 'view' . DIRECTORY_SEPARATOR . 'errorViewFile.phtml';
        }
        $content = ob_get_clean();
        include_once BP_APP . 'view' . DIRECTORY_SEPARATOR . 
        $this->template . '.phtml';
    }

}
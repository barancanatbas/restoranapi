<?php

class Autoload{

    protected $controller = 'masalar';
    protected $method = "index";
    protected $parametre = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        if (isset($url[0]))
        {
            if (file_exists("controller/".$url[0].".php"))
            {
                $this->controller = $url[0];
                unset($url[0]);
            }
        }
        //include_once 'controller/controller.php';
        include_once 'controller/'.$this->controller.'.php';
        $this->controller = new $this->controller();

        if (isset($url[1]))
        {
            if (method_exists($this->controller,$url[1]))
            {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        $this->parametre = $url ? array_values($url) : [];
        call_user_func_array([$this->controller,$this->method],$this->parametre);
    }

    public function parseUrl()
    {
        if (isset($_GET["url"]))
        {
            return $url = explode('/',filter_var(rtrim($_GET["url"],'/'),FILTER_SANITIZE_URL));
        }
    }
}

?>
<?php
include_once 'autoload.php';
header('Access-Control-Allow-Origin: *'); // gelen bütün istekleri kabul eder
header("Content-type: application/json; charset=utf-8"); // uygulamayı bir json olarak görür ve karakter setini atar


$autoload = new Autoload();


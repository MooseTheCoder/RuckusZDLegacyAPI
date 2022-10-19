<?php

class HTTPHelper{
    public static function isPost(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            return true;
        }
        return false;
    }

    public static function isGet(){
        if($_SERVER['REQUEST_METHOD'] === 'GET'){
            return true;
        }
        return false;
    }

    public static function isDelete(){
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            return true;
        }
        return false;
    }

    public static function getJsonAsArray(){
        return json_decode(file_get_contents('php://input'), true);
    }

    public static function method(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function headerJson(){
        header('Content-Type: application/json; charset=utf-8');
    }

    public static function responseCode($code){
        http_response_code($code);
    }

    public static function closeConnection(){
        exit;
    }
}
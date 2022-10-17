<?php

class Logger{
    public static function Log($Log){
        error_log($Log);
    }
}
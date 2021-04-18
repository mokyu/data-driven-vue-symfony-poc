<?php


namespace App\Enum;


use ReflectionClass;

class GenreEnum
{
    const Roman = 1;
    const Science_Fiction = 2;
    const Non_Fiction = 3;
    const Fantasy = 4;

    static function getAsList(): array
    {
        $oClass = new ReflectionClass(__CLASS__);
        $data = [];
        foreach($oClass->getConstants() as $key=>$value) {
            $data[] = ['value' => \str_replace("_", " ", $key), 'key' => $value]; // invert pair because its more logical how its used data wise
        }
        return $data;
    }
}
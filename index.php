<?php

use LuanHimmlisch\Biseccion\Biseccion;

require_once __DIR__ . '/vendor/autoload.php';

$biseccion = Biseccion::make()
    ->setXi((float) readline('Set initial Xi: '))
    ->setXu((float) readline('Set initial Xu: '))
    ->setFunction(readline('Set function string (use \'x\' as the incognita): '))
    ->execute();

$biseccion->export(__DIR__ . '/out');

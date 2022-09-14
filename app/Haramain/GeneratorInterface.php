<?php namespace App\Haramain;

interface GeneratorInterface
{
    public function __construct();

    public function cleanup();

    public function generate();
}

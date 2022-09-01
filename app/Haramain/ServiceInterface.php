<?php namespace App\Haramain;

interface ServiceInterface
{
    public function __construct();
    public function handleGetData($id);
    public function handleStore($data);
    public function handleUpdate($data);
    public function handleDestroy($id);
}

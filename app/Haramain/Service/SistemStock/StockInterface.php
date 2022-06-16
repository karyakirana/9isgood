<?php namespace App\Haramain\Service\SistemStock;

interface StockInterface
{
    public function __construct();
    public function create();
    public function update();
    public function destroy($id);
}

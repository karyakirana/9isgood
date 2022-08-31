<?php namespace App\Haramain\SistemStock;

interface StockTransaksiInterface
{
    public function getDataById($stockableType, $stockableId);
    public function getDataAll($activeCash = true);
    public function store($data, $stockableType, $stockableId);
    public function update($data, $stockableType, $stockableId);
    public function rollback($stockableType, $stockableId);
    public function destory($stockableType, $stockableId);
}

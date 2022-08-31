<?php namespace App\Haramain\SistemPenjualan;

interface PenjualanInterface
{
    public function getDataById(int $id);
    public function getDataAll(bool $closedCash = true);
    public function store(object|array $data);
    public function update(object|array $data);
    public function rollback(int $id);
    public function destroy(int $id);
}

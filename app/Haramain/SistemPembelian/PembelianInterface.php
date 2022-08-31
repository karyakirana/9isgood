<?php namespace App\Haramain\SistemPembelian;

interface PembelianInterface
{
    public function getDataById(int $pembelianId);
    public function getDataAll(bool $closedCash = true);
    public function store(object|array $data);
    public function update(object|array $data);
    public function rollback(int $pembelianId);
    public function destroy(int $pembelianId);
}

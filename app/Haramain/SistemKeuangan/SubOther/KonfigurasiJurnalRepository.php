<?php namespace App\Haramain\SistemKeuangan\SubOther;

use App\Models\KonfigurasiJurnal;

class KonfigurasiJurnalRepository
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public static function build($config)
    {
        return new static($config);
    }

    public function getAkun()
    {
        return KonfigurasiJurnal::firstWhere('config', $this->config)->akun_id;
    }
}

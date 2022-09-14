<?php

namespace App\Models\Keuangan;

use App\Models\Master\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalPiutangPegawai extends Model
{
    use HasFactory;
    protected $table = 'haramain_keuangan.jurnal_piutang_pegawai';
    protected $fillable = [
        'kode',
        'customer_id',
        'jenis', // hutang atau piutang
        'nominal',
        'keterangan'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

<?php namespace App\Haramain\SistemKeuangan\SubKasir;

use App\Models\Keuangan\Payment;

class PaymentRepository
{
    protected $paymentableType;
    protected $paymentableId;
    protected $akunId;
    protected $nominal;

    protected $paymentClass;
    protected $dataPayment;

    public function __construct(array $dataPayment, $paymentClass)
    {
        //
    }

    public static function build(...$params)
    {
        return new static(...$params);
    }

    public function store()
    {
        return Payment::create([
            'paymentable_type' => $this->paymentableType,
            'paymentable_id' => $this->paymentableId,
            'akun_id' => $this->akunId,
            'nominal' => $this->akunId
        ]);
    }

    public function rollback()
    {
        return $this->model->paymentable()->delete();
    }
}

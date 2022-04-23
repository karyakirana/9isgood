<div>
    <x-molecules.card-tab title="laporan Penjualan">
        <x-slot name="tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_by_1">Periode</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_by_2">Customer</a>
            </li>
        </x-slot>
        <div class="tab-pane fade show active" id="tab_by_1" role="tabpanel">
            <form>
                <div class="row">
                    <div class="col-4">
                        <x-atoms.input.group-horizontal label="Tgl Awal" name="tglAwal">
                            <x-atoms.input.singledaterange id="tglAwal" wire:model.defer="tglAwal" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-4">
                        <x-atoms.input.group-horizontal label="Tgl Akhir" name="tglAkhir">
                            <x-atoms.input.singledaterange id="tglAkhir" wire:model.defer="tglAkhir" />
                        </x-atoms.input.group-horizontal>
                    </div>
                    <div class="col-4">
                        <x-atoms.button.btn-primary wire:click="setTanggal">Set</x-atoms.button.btn-primary>
                        <x-atoms.button.btn-danger wire:click="printPeriode">PRINT</x-atoms.button.btn-danger>
                    </div>
                </div>
            </form>
            <livewire:datatables.penjualan.penjualan-report-by-date-table />
        </div>
        <div class="tab-pane fade" id="tab_by_2" role="tabpanel">b</div>
    </x-molecules.card-tab>

    @push('custom-scripts')
        <script>
            $('#tglAwal').on('change', function (e) {
                let date = $(this).data("#tglAwal");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tglAwal = e.target.value;
            })

            $('#tglAkhir').on('change', function (e) {
                let date = $(this).data("#tglAkhir");
                // eval(date).set('tglLahir', $('#tglLahir').val())
                console.log(e.target.value);
                @this.tglAkhir = e.target.value;
            })
        </script>
    @endpush
</div>

<x-metronics-layout>

    <x-molecules.card title="PIUTANG {{ucwords($customer->nama)}}">
        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1">Belum Bayar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Sudah Bayar</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3">All</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                <livewire:datatable.piutang-penjualan-belum :customer_id="$customer->id" />
            </div>
            <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                <livewire:datatable.piutang-penjualan-sudah :customer_id="$customer->id" />
            </div>
            <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel">
                <livewire:datatable.piutang-penjualan-all :customer_id="$customer->id" />
            </div>
        </div>

    </x-molecules.card>
</x-metronics-layout>

<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="{{ public_path('css\bootstrap.css') }}" media="all" />
</head>
<body>
<div class="text-center">
    <h2 class="text-capitalize" style="font-weight: bolder">Daftar Retur</h2>
    {{--    <h3><strong>Periode 2021-2022</strong></h3>--}}
</div>
<table class="table table-bordered" style="margin-top: 20px">
    <tr>
        <th class="text-center" width="10%">Id</th>
        <th class="text-center" width="15%">Customer</th>
        <th class="text-center" width="15%">Tgl Nota</th>
        <th class="text-center" width="15%">Tgl Tempo</th>
        <th class="text-center" width="15%">Jenis</th>
        <th class="text-center" width="15%">Status</th>
        <th class="text-center" width="15%">Total</th>
    </tr>
    @foreach($penjualan as $item)
        <tr>
            <td class="text-center">
                {{$item->kode}}
            </td>

            <td class="text-center">
                {{$item->customer->nama}}
            </td>

            <td class="text-center">
                {{tanggalan_format($item->tgl_nota)}}
            </td>

            <td class="text-center">
                @if($item->tgl_tempo)
                    {{tanggalan_format($item->tgl_tempo)}}
                @endif
            </td>

            <td class="text-center">
                {{ucfirst($item->jenis_bayar)}}
            </td>

            <td class="text-center">
                {{ucfirst($item->status_bayar)}}
            </td>

            <x-atoms.table.td class="text-right">
                {{rupiah_format($item->total_bayar)}}
            </x-atoms.table.td>
        </tr>
    @endforeach
</table>
</body>
</html>

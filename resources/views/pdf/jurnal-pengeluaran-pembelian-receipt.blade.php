<!DOCTYPE html>
<html lang="en">
<head>
    <title>Nota Nomor {{$pengeluaran_pembelian->kode}}</title>
    <meta charset="utf-8">
    .<style>
        @font-face {
            font-family: Junge;
            /*src: url(Junge-Regular.ttf);*/
        }

        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }

        a {
            color: #001028;
            text-decoration: none;
        }

        body {
            font-family: Junge;
            position: relative;
            width: 21cm;
            height: 29.7cm;
            margin: 0 auto;
            color: #001028;
            background: #FFFFFF;
            font-size: 18px;
        }

        .arrow {
            margin-bottom: 4px;
        }

        .arrow.back {
            text-align: right;
        }

        .inner-arrow {
            padding-right: 10px;
            height: 30px;
            display: inline-block;
            /*background-color: rgb(233, 125, 49);*/
            text-align: center;

            line-height: 30px;
            vertical-align: middle;
        }

        .arrow.back .inner-arrow {
            /*background-color: rgb(233, 217, 49);*/
            padding-right: 0;
            padding-left: 10px;
        }

        .arrow:before,
        .arrow:after {
            content:'';
            display: inline-block;
            width: 0; height: 0;
            border: 15px solid transparent;
            vertical-align: middle;
        }

        .arrow:before {
            /*border-top-color: rgb(233, 125, 49);*/
            /*border-bottom-color: rgb(233, 125, 49);*/
            /*border-right-color: rgb(233, 125, 49);*/
        }

        .arrow.back:before {
            /*border-top-color: transparent;*/
            /*border-bottom-color: transparent;*/
            /*border-right-color: rgb(233, 217, 49);*/
            /*border-left-color: transparent;*/
        }

        .arrow:after {
            /*border-left-color: rgb(233, 125, 49);*/
        }

        .arrow.back:after {
            /*border-left-color: rgb(233, 217, 49);*/
            /*border-top-color: rgb(233, 217, 49);*/
            /*border-bottom-color: rgb(233, 217, 49);*/
            /*border-right-color: transparent;*/
        }

        .arrow span {
            display: inline-block;
            width: 80px;
            margin-right: 20px;
            text-align: right;
        }

        .arrow.back span {
            margin-right: 0;
            margin-left: 20px;
            text-align: left;
        }

        h1 {
            color: #5D6975;
            font-family: Junge;
            font-size: 2.4em;
            line-height: 1.4em;
            font-weight: normal;
            text-align: center;
            border-top: 1px solid #5D6975;
            border-bottom: 1px solid #5D6975;
            margin: 0 0 2em 0;
        }

        h1 small {
            font-size: 0.45em;
            line-height: 1.5em;
            float: left;
        }

        h1 small:last-child {
            float: right;
        }

        #project {
            float: left;
        }

        #company {
            float: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 30px;
        }

        table th,
        table td {
            text-align: center;
        }

        table th {
            padding: 5px 20px;
            color: #5D6975;
            border-bottom: 1px solid #C1CED9;
            white-space: nowrap;
            font-weight: normal;
        }

        table .service,
        table .desc {
            text-align: left;
        }

        table td {
            padding: 15px;
            text-align: right;
        }

        table td.service,
        table td.desc {
            vertical-align: top;
        }

        table td.unit,
        table td.qty,
        table td.total {
            font-size: 1.2em;
        }

        table td.sub {
            border-top: 1px solid #C1CED9;
        }

        table td.grand {
            border-top: 1px solid #5D6975;
        }

        table tr:nth-child(2n-1) td {
            /*background: #EEEEEE;*/
        }

        table tr:last-child td {
            background: #DDDDDD;
        }

        #details {
            margin-bottom: 30px;
        }

        footer {
            color: #5D6975;
            width: 100%;
            height: 30px;
            position: absolute;
            bottom: 0;
            border-top: 1px solid #C1CED9;
            padding: 8px 0;
            text-align: center;
        }
    </style>
</head>
<body>
<main>
    <h1  class="clearfix"><small><br/><span>No. </span>{{$pengeluaran_pembelian->kode}}</small><strong> KAS KELUAR </strong><small><br /><span>Surabaya, </span>{{tanggalan_format($pengeluaran_pembelian->tgl_penerimaan)}}</small></h1>
    <table>
        <thead>
        <tr>

        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="service">Untuk</td>
            <td class="desc">: {{$pengeluaran_pembelian->supplier->nama?? ''}}</td>
            <td class="unit"></td>
            <td class="qty"></td>
            <td class="total"></td>
        </tr>
        <tr>
            <td class="service">Keperluan</td>
            <td class="desc">: {{$pengeluaran_pembelian->keterangan}}</td>
            <td class="unit"></td>
            <td class="qty"></td>
            <td class="total"></td>
        </tr>
        <tr>
            <td class="service">Terbilang</td>
            <td class="desc">: <strong>" {{ucwords(terbilang($pengeluaran_pembelian->total_pengeluaran))}} Rupiah " </strong></td>
            <td class="unit"></td>
            <td >Jumlah :</td>
            <td class="total">Rp.{{rupiah_format($pengeluaran_pembelian->total_pengeluaran)}}</td>
        </tr>
{{--        <tr>--}}
{{--            <td class="service">Training</td>--}}
{{--            <td class="desc">Initial training sessions for staff responsible for uploading web content</td>--}}
{{--            <td class="unit">$40.00</td>--}}
{{--            <td class="qty">4</td>--}}
{{--            <td class="total">$160.00</td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td colspan="4" class="sub"></td>--}}
{{--            <td class="sub total"></td>--}}
{{--        </tr>--}}
{{--        <tr>--}}
{{--            <td colspan="4">TAX 25%</td>--}}
{{--            <td class="total">$1,300.00</td>--}}
{{--        </tr>--}}
        <tr>
            <td colspan="4" class="grand total"><strong>Total</strong></td>
            <td class="grand total"><strong>Rp.{{rupiah_format($pengeluaran_pembelian->total_pengeluaran)}}</strong></td>
        </tr>
        </tbody>
    </table>
    <div id="details" class="clearfix">
        <div id="project">
            <div class="arrow"><div class="inner-arrow"><span>Pembayaran</span>: Utang sek bos</div></div>
            <div class="arrow"><div class="inner-arrow"><span></span></div></div>
            <div class="arrow"><div class="inner-arrow"><span></span> Mengetahui,</div></div>
            <div class="arrow"><div class="inner-arrow"><span></span></div></div>
        </div>
        <div id="company">
            <div class="arrow back"><div class="inner-arrow"><span></span></div></div>
            <div class="arrow back"><div class="inner-arrow"><span></span></div></div>
            <div class="arrow back"><div class="inner-arrow"><span>Penerima,</span><span></span></div></div>
            <div class="arrow back"><div class="inner-arrow"><span></span></div></div>
        </div>
    </div>
{{--    <div id="notices">--}}
{{--        <div>NOTICE:</div>--}}
{{--        <div class="notice">A finance charge of 1.5% will be made on unpaid balances after 30 days.</div>--}}
{{--    </div>--}}
</main>
{{--<footer>--}}
{{--    Invoice was created on a computer and is valid without the signature and seal.--}}
{{--</footer>--}}
</body>
</html>

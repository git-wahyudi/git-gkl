<!DOCTYPE html>
<html lang="en" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Angsuran</title>
    <link href="{{asset('vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" />
    <style>
    	/*table { page-break-inside:auto }
	    tr{ page-break-inside:avoid; page-break-before:auto;}
	    thead { display:table-header-group;margin-bottom: 100px; }
	    tfoot { display:table-footer-group }*/
        @font-face {
            font-family: Cambria;
            src: url("{{ asset('fonts/Cambria.ttf') }}");
            font-weight: normal;
        }
        @font-face {
            font-family: Cambria;
            src: url("{{ asset('fonts/cambriab.ttf') }}");
            font-weight: bold;
        }
        @page {
        	font-size: 12px;
            margin: 20px 30px;
        }

        header {
            left: 0px;
            right: 0px;

            /** Extra personal styles **/
           /* background-color: #03a9f4;
            color: white;*/
            text-align: center;
            line-height: 12px;
        }

        main {
            left: 5px;
            right: 5px;
            line-height: 12px;
        }
    </style>
</head>
<body>
    <?php 
    	LoadHelper('format'); $i = 1;
    ?>
    <header>
        <table width="100%" style="font-family: Cambria;font-size: 12px;font-weight: bold;line-height: 10px;">
			<tr>
				<td style="text-align: center;">LAPORAN DATA ANGSURAN</td>
			</tr>
			<tr>
				<td style="text-align: center;">{{strtoupper($data->project)}}</td>
			</tr>
		</table>
    </header>
    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
   		<table border="1" style="border-collapse: collapse;margin-top: 10px;font-family: Cambria;vertical-align: middle;" width="100%">
   			<thead>	
				<tr>
					<th style="width: 30px;text-align: center;">NO</th>
					<th style="width: 100px;text-align: center;">NO KAVLING</th>
					<th>NAMA</th>
					<th style="width: 80px;text-align: center;">TGL JATUH TEMPO</th>
					<th style="width: 80px;text-align: center;">HARGA TANAH</th>
					<th style="width: 80px;text-align: center;">DP</th>
					<th style="width: 80px;text-align: center;">ANGSURAN</th>
					<th style="width: 80px;text-align: center;">TOTAL ANGSURAN</th>
					<th style="width: 80px;text-align: center;">TOTAL UANG MASUK</th>
					<th style="width: 80px;text-align: center;">SISA PIUTANG</th>
					<th style="text-align: center;">TUNGGAKAN</th>
				</tr>
   			</thead>
			<tbody>
				@foreach($data->lap_penjualan as $lp)
				<tr>
					<td style="text-align: center;">{{$i++}}</td>
					<td>
						@foreach($lp->penjualan_detail as $pd)
							@if($loop->first)
								{{$pd->no_kavling}}
							@else
								| {{$pd->no_kavling}}
							@endif
						@endforeach
					</td>
					<td>{{$lp->nama}}</td>
					<td style="text-align: center;">{{toDateDisplay($lp->tgl_bayar)}}</td>
					<td style="text-align: center;">{{toMOney($lp->total_harga-$lp->potongan)}}</td>
					<td style="text-align: center;">{{toMoney($lp->uang_muka)}}</td>
					<td style="text-align: center;">{{toMoney($lp->angsuran)}}</td>
					<td style="text-align: center;">{{toMoney($lp->total_angsuran)}}</td>
					<td style="text-align: center;">{{toMoney($lp->total_angsuran+$lp->uang_muka)}}</td>
					<td style="text-align: center;">{{toMoney(($lp->total_harga-$lp->potongan)-($lp->total_angsuran+$lp->uang_muka))}}</td>
					<td style="text-align: center;padding-top: 10px;">
						<div class="col-xs-12">
							@foreach($lp->lap_list_angsuran as $lla)
							<div class="badge badge-danger col-xs-2 mb-1">{{bulan_tahun($lla->tgl_bayar)}}</div>
							@endforeach
						</div>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
    </main>
</body>
</html>
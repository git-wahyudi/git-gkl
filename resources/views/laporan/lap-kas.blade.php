<!DOCTYPE html>
<html lang="en" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kas</title>
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
    	LoadHelper('format'); $i = 1; $total=0;
    	foreach ($header as $hd) {
    		$td = 'td'.$hd->project->id;
    		$$td = 0;
    	}
    ?>
    <header>
        <table width="100%" style="font-family: Cambria;font-size: 12px;font-weight: bold;line-height: 10px;">
			<tr>
				<td style="text-align: center;">LAPORAN KAS SEDERHANA</td>
			</tr>
			<tr>
				<td style="text-align: center;">GRIYA KENCANA LESTARI</td>
			</tr>
			<tr>
				<td style="text-align: center;">{{$periode}}</td>
			</tr>
		</table>
    </header>
    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
   		<table border="1" style="border-collapse: collapse;margin-top: 10px;font-family: Cambria;vertical-align: middle;" width="100%">
   			<thead>	
				<tr>
					<th style="width: 30px;text-align: center;">NO</th>
					<th style="width: 80px;text-align: center;">TANGGAL</th>
					<th>RINCIAN</th>
					@foreach($header as $h)
					<th style="width: 80px;">PEMASUKAN ({{strtoupper($h->project->project)}})</th>
					@endforeach
					<th style="width: 80px;text-align: right;">PENGELUARAN</th>
				</tr>
   			</thead>
			<tbody>
				@foreach($data as $dt)
				<tr>
					<td style="text-align: center;">{{$i++}}</td>
					<td style="text-align: center;">{{toDateDisplay($dt->tgl)}}</td>
					<td>{{$dt->keterangan}}</td>
					@foreach($header as $h)
					<td style="text-align: right;">
						@if($dt->code == 'In' && $dt->project_id == $h->project->id)
						{{str_replace('-','',toMoney($dt->jumlah))}}
						@php
							$i = $h->project->id; 
							${"td$i"} += $dt->jumlah; 
						@endphp
						@endif
					</td>
					@endforeach
					<td style="text-align: right;">
						@if($dt->code == 'Out')
							{{str_replace('-','',toMoney($dt->jumlah))}}
							@php $total = $total+$dt->jumlah; @endphp
						@endif
					</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="3" style="text-align: right;">Total</td>
					@foreach($header as $h)
					<td style="text-align: right;">
						@php $show = 'td'.$h->project->id; @endphp
						{{toMoney($$show)}}
					</td>
					@endforeach
					<td style="text-align: right;">{{toMoney($total)}}</td>
				</tr>
			</tbody>

			
		</table>
    </main>
</body>
</html>
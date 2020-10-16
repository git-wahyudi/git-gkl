<!DOCTYPE html>
<html lang="en" >
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Print Kwitansi</title>
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
        	font-size: 14px;
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
    <?php LoadHelper('format'); $i = 1; $total=0;?>
    <header>
        <table width="100%">
			<tr>
				<td style="width: 90px;">
					<img src="img/gkl.png" style="width: 80px;">
				</td>
				<td style="line-height: 12px;vertical-align: bottom;font-family: Cambria;font-weight: bold;">
					Jl. Lingkar Selatan II RT. 023 Paal Merah<br>
					Jambi Selatan, Kota Jambi<br>
					Telp. 0812 7829 8080
				</td>
				<td style="vertical-align: bottom;width: 100px;">					
					<h1 style="margin-bottom: -5px;">KWITANSI</h1><br>
					No: {{$la->no_kwitansi}}
				</td>
			</tr>
		</table>
    </header>
    <!-- Wrap the content of your PDF inside a main tag -->
    <main>
   		<table width="100%">
			<tr>
				<td colspan="3" style="border:1px solid;padding: 15px;line-height: 20px;">
					<table width="100%">
						<tr>
							<td style="width: 150px; vertical-align: top;">Terima Dari</td>
							<td style="width: 10px; vertical-align: top;">:</td>
							<td style="vertical-align: top;">{{strtoupper($la->penjualan->nama)}}</td>
						</tr>
						<tr>
							<td style="vertical-align: top">Uang Sejumlah</td>
							<td style="vertical-align: top">:</td>
							<td style="vertical-align: top">{{ucwords(terbilang($la->jml_bayar))}} Rupiah</td>
						</tr>
						<tr>
							<td style="vertical-align: top">Untuk Pembayaran</td>
							<td style="vertical-align: top">:</td>
							<td style="vertical-align: top">{{$la->ket}} Kav. {{$la->penjualan->project}} No. {{$kavling}}</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td style="border-top: 1px solid;border-bottom: 1px solid;padding-left: 0px;padding-right: 0px;">
					<table width="100%" style="border-collapse: collapse;font-size: 18px;margin-left: 0px;margin-right: 0px;margin-top: 1px;margin-bottom: 1px;">
						<tr>
							<td style="width: 50px;padding-left: 0px;padding-top: 8px;padding-bottom: 5px;">Rp.</td>
							<td style="padding-top: 8px;padding-bottom: 5px;border-top: 1px solid;border-bottom: 1px solid;background:  #f5f4f4;text-align: center;">{{toMoney($la->jml_bayar)}}</td>
						</tr>
					</table>
				</td>
				<td></td>
				<td style="text-align: center;">Jambi, {{tgl_indo($la->tgl_bayar)}}</td>
			</tr>
			<tr>
				<td colspan="3"></td>
			</tr>
			<tr>
				<td style="border:1px solid;line-height: 18px;padding:5px;">
					Catatan:
					<span style="color: red;">Validasi pembayaran dianggap sah bila disertai tanda tangan dan stempel.</span>
				</td>
				<td style="width: 300px;text-align: center;vertical-align: bottom;line-height: 16px;">
					<u>{{ucwords($la->penjualan->nama)}}</u><br>Customer
				</td>
				<td style="width: 200px;text-align: center;vertical-align: bottom;line-height: 16px;">
					<u>{{ucwords($la->created_by)}}</u><br>Admin
				</td>
			</tr>

			
		</table>
    </main>
</body>
</html>
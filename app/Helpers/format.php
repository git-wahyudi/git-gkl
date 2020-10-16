<?php
function toDateSystem($date){
	$date = explode("-", $date);
	return $date[2]."-".$date[1]."-".$date[0];
}

function toZero($digit, $data){
	$data = sprintf("%0".$digit."d", $data);
	return $data; 
}

function toDateDisplay($date){
	$date = explode("-", $date);
	return $date[2]."-".$date[1]."-".$date[0];
}

function toDecimal($rupiah){
	$rupiah = str_replace("Rp", "", $rupiah);
	$rupiah = trim($rupiah);
	$rupiah = str_replace(",", "", $rupiah);
	if((int)$rupiah==0){
		return '0.00';
	}
	return $rupiah;
}

function toMoney($data){
	return  number_format($data,0,",",".");
}

function toMoneyInput($data){
	return  str_replace('.', '', $data);
}

function selisih_hari($tgl_awal, $tgl_akhir){
	$tanggal1 = new DateTime($tgl_awal);
	$tanggal2 = new DateTime($tgl_akhir);
	 
	$selisih = $tanggal2->diff($tanggal1)->format("%a");
	return $selisih;
}

function tgl_indo($data){
	$tgl = explode("-",$data);
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	if(count($tgl)==3){
		return $tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0];
	}else{
		return "";
	}
}

function datetime($data){
	$time = substr($data, 11, 8);
	$data = substr($data, 0,10);
	$tgl = explode("-",$data);
	if(count($tgl)==3){
		return $tgl[2]."-".$tgl[1]."-".$tgl[0]." ".$time;
	}else{
		return "";
	}
}

function datetime_indo($data){
	$time = substr($data, 11, 8);
	$data = substr($data, 0,10);
	$tgl = explode("-",$data);
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	if(count($tgl)==3){
		return $tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0]." ".$time;
	}else{
		return "";
	}
}

function bulan_tahun($data){
	$data = substr($data, 0,10);
	$tgl = explode("-",$data);
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	if(count($tgl)==3){
		return $bulan[(int)$tgl[1]]." ".$tgl[0];
	}else{
		return "";
	}
}

function datetime_id($data){
	$time = substr($data, 11, 8);
	$data = substr($data, 0,10);
	$tgl = explode("-",$data);
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	if(count($tgl)==3){
		return $tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0];
	}else{
		return "";
	}
}

function tgl_bulan($data){
	$tgl = explode("-",$data);
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	if(count($tgl)==3){
		return $tgl[2]." ".$bulan[(int)$tgl[1]];
	}else{
		return "";
	}
}

function bulan($data){
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', "4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	
	return $bulan[(int)$data];
}

function tgl_indo_singkat($data){
	$tgl = explode("-",$data);
	$bulan = array("0"=>"","1"=>'Jan', "2"=>'Feb', "3"=>'Mar', "4"=>"Apr", "5"=>'Mei', "6"=>'Jun', "7"=>'Jul', "8"=>
		'Ags', "9"=>'Sep', "10"=>'Okt', "11"=>'Nov',"12"=>'Des');
	if(count($tgl)==3){
		return $tgl[2]." ".$bulan[(int)$tgl[1]]." ".$tgl[0];
	}else{
		return "";
	}
}

function rentang_tanggal($tanggal_awal, $tanggal_akhir){
	$bulan = array("0"=>"","1"=>'Januari', "2"=>'Februari', "3"=>'Maret', 
		"4"=>"April", "5"=>'Mei', "6"=>'Juni', "7"=>'Juli', "8"=>
		'Agustus', "9"=>'September', "10"=>'Oktober', "11"=>'November',"12"=>'Desember');
	$tanggal = "";
	$tgl1 = explode("-",$tanggal_awal);
	$tgl2 = explode("-",$tanggal_akhir);
    if(count($tgl1)==3){
    	$tanggal = (int)$tgl1[2]." ".$bulan[(int)$tgl1[1]] ;
    	if($tgl1[0]!=$tgl2[0]){
    		$tanggal .= " ". $tgl1[0];
    	}
    	$tanggal .= " - ";
    }

    if(count($tgl2)==3){
    	$tanggal .= (int)$tgl2[2]." ".$bulan[(int)$tgl2[1]] ." ". $tgl2[0]; 	
    }

    return $tanggal;
}

function hitungumurtahunbulan($birthday){
	
	// Convert Ke Date Time
	
	$biday = new DateTime($birthday);
	$today = new DateTime();
	
	$diff = $today->diff($biday);
	return $diff->y." Thn ".$diff->m." Bln";

}
function hitungumurdalambulan($birthday){
	
	// Convert Ke Date Time
	
	$biday = new DateTime($birthday);
	$today = new DateTime('Y-m-d');
	
	$diff = $today->diff($biday);
	return ($diff->y * 12 ) + $diff->m;

}

function hitungumurdalamhari($birthday){
	$tgl_ppdb = DB::table('setting_umum')->where('tingkat','sd')->first();
	$CheckIn  = $birthday;
	$CheckOut  = $tgl_ppdb->tanggal_patokan_umur;
	$CheckInX = explode("-", $CheckIn);
	$CheckOutX =  explode("-", $CheckOut);
	$date1 =  mktime(0, 0, 0, $CheckInX[1],$CheckInX[2],$CheckInX[0]);
	$date2 =  mktime(0, 0, 0, $CheckOutX[1],$CheckOutX[2],$CheckOutX[0]);
	$interval =($date2 - $date1)/(3600*24);
	return $interval;
}

function getInstansiName(){
	if(Auth::user()->jenis_instansi == 'all'){
		$instansi = 'Administrator';
	}else if(Auth::user()->jenis_instansi == 'kel'){
		$name = DB::table('instansis')->where('id',Auth::user()->instansi_id)->first();
		$instansi = 'Kelurahan '.$name->nama;
	}else if(Auth::user()->jenis_instansi == 'kec'){
		$name = DB::table('instansis')->where('id',Auth::user()->instansi_id)->first();
		$instansi = 'Kecamatan '.$name->nama;
	}
	return $instansi;
}

function getRole(){
	$role = DB::table('user_roles as a')->select('b.nama_role')
			->leftJoin('roles as b', 'a.role_id', 'b.id')
			->where('user_id',Auth::user()->id)
			->first();
	return ucfirst($role->nama_role);
}

function terbilang($x) {
  $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

  if ($x < 12)
    return " " . $angka[$x];
  elseif ($x < 20)
    return terbilang($x - 10) . " belas";
  elseif ($x < 100)
    return terbilang($x / 10) . " puluh" . terbilang($x % 10);
  elseif ($x < 200)
    return "seratus" . terbilang($x - 100);
  elseif ($x < 1000)
    return terbilang($x / 100) . " ratus" . terbilang($x % 100);
  elseif ($x < 2000)
    return "seribu" . terbilang($x - 1000);
  elseif ($x < 1000000)
    return terbilang($x / 1000) . " ribu" . terbilang($x % 1000);
  elseif ($x < 1000000000)
    return terbilang($x / 1000000) . " juta" . terbilang($x % 1000000);
}



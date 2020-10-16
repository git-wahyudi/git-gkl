<?php

use Illuminate\Support\Facades\Route;

//clear cache
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    return "Cache is cleared";
});

Route::get('/', function (){ 
	if(Auth::user()){ return redirect('/admin'); }
	return view('welcome'); 
})->name('login');
Route::post('/submit-login', 'LoginController@submit_login');
Route::get('/logout', 'LoginController@logout');

Route::middleware(['auth.login','auth.menu'])->group(function(){
	Route::group(['prefix'=>'admin'], function(){
		Route::get('/', 'AdminController@index');
		Route::get('/get-record/{id}', 'AdminController@get_record');
		Route::post('/submit-password', 'AdminController@submit_password');		
		Route::post('/edit-profile', 'AdminController@edit_profile');		

		Route::group(['prefix'=>'pengaturan-menu'], function(){
			Route::get('/', 'MenuController@index');
			Route::get('/datatable', 'MenuController@datatable');
			Route::get('/get-menu', 'MenuController@getSelectValue');	
			Route::get('/get-record/{id}', 'MenuController@get_record');	
			Route::get('/get-record-data/{id}', 'MenuController@get_record_with_data');	
			Route::get('/get-induk', 'MenuController@get_induk');	
			Route::post('/add', 'MenuController@submit_data');
			Route::post('/update', 'MenuController@update_data');
			Route::post('/delete', 'MenuController@delete_data');
		});

		Route::group(['prefix'=>'pengaturan-roles'], function(){
			Route::get('/', 'RolesController@index');
			Route::get('/datatable', 'RolesController@datatable');
			Route::get('/get-record/{uuid}', 'RolesController@get_record');
			Route::post('/add', 'RolesController@submit_data');
			Route::post('/update', 'RolesController@update_data');
			Route::post('/delete', 'RolesController@delete_data');

			Route::get('/{uuid}', 'RolesController@index_role_menu');
			Route::get('/{uuid}/deep-datatable', 'RolesController@deep_datatable');
			Route::post('/{uuid}/add', 'RolesController@insert_data');
			Route::post('/{uuid}/update', 'RolesController@update_role_menu');
			Route::post('/{uuid}/delete', 'RolesController@delete_role_menu');
			Route::get('/get-data/{uuid}', 'RolesController@get_data');
		});

		Route::group(['prefix'=>'pengaturan-users'], function(){
			Route::get('/', 'UsersController@index');
			Route::get('/datatable', 'UsersController@datatable');
			Route::get('/get-record/{uuid}', 'UsersController@get_record');
			Route::post('/add', 'UsersController@submit_data' );
			Route::post('/update', 'UsersController@update_data');
			Route::post('/delete', 'UsersController@delete_data');
			Route::post('/reset-password', 'UsersController@reset_password');
		});

		Route::group(['prefix'=>'pengaturan-data-pengguna'], function(){
			Route::get('/', 'PenggunaController@index');
			Route::get('/datatable', 'PenggunaController@datatable');
			Route::get('/get-record/{uuid}', 'PenggunaController@get_record');
			Route::post('/add', 'PenggunaController@submit_data' );
			Route::post('/update', 'PenggunaController@update_data');
			Route::post('/delete', 'PenggunaController@delete_data');
			Route::post('/reset-password', 'PenggunaController@reset_password');
		});

		Route::group(['prefix'=>'pengaturan-users-role'], function(){
			Route::get('/', 'UsersRoleController@index');
			Route::get('/datatable', 'UsersRoleController@datatable');
			Route::get('/get-select', 'UsersRoleController@get_select');
			Route::get('/get-record/{uuid}', 'UsersRoleController@get_record');
			Route::post('/add', 'UsersRoleController@submit_data');
			Route::post('/update', 'UsersRoleController@update_data');
			Route::post('/delete', 'UsersRoleController@delete_data');
		});

		Route::group(['prefix'=>'sales'], function(){
			Route::get('/datatable/{id}', 'AdminController@datatable');
			Route::get('/get-kavling/{uuid}', 'AdminController@get_kavling');
		});

		Route::group(['prefix'=>'master-data-owner'], function(){
			Route::get('/', 'OwnerController@index');
			Route::get('/datatable', 'OwnerController@datatable');
			Route::get('/get-record/{uuid}', 'OwnerController@get_record');
			Route::post('/add', 'OwnerController@submit_data' );
			Route::post('/update', 'OwnerController@update_data');
			Route::post('/delete', 'OwnerController@delete_data');
		});
		
		Route::group(['prefix'=>'master-data-project'], function(){
			Route::get('/', 'ProjectController@index');
			Route::get('/datatable', 'ProjectController@datatable');
			Route::get('/get-record/{uuid}', 'ProjectController@get_record');
			Route::post('/add', 'ProjectController@submit_data' );
			Route::post('/update', 'ProjectController@update_data');
			Route::post('/delete', 'ProjectController@delete_data');

			Route::group(['prefix'=>'{uuid}'], function(){
				Route::get('/', 'ProjectItemController@index');
				Route::get('/datatable', 'ProjectItemController@datatable');
				Route::get('/get-record/{detail}', 'ProjectItemController@get_record');
				Route::post('/add', 'ProjectItemController@submit_data' );
				Route::post('/update', 'ProjectItemController@update_data');
				Route::post('/delete', 'ProjectItemController@delete_data');
			});
		});

		Route::group(['prefix'=>'master-data-customer'], function(){
			Route::get('/', 'CustomerController@index');
			Route::get('/datatable', 'CustomerController@datatable');
			Route::get('/get-record/{uuid}', 'CustomerController@get_record');
			Route::post('/add', 'CustomerController@submit_data' );
			Route::post('/update', 'CustomerController@update_data');
			Route::post('/delete', 'CustomerController@delete_data');
		});

		Route::group(['prefix'=>'penjualan'], function(){
			Route::get('/', 'PenjualanController@index');
			Route::get('/datatable', 'PenjualanController@datatable');
			Route::get('/get-record/{uuid}', 'PenjualanController@get_record');
			Route::post('/add', 'PenjualanController@submit_data' );
			Route::post('/update', 'PenjualanController@update_data');
			Route::post('/delete', 'PenjualanController@delete_data');

			Route::group(['prefix'=>'kredit/{uuid}'], function(){
				Route::get('/', 'PenjualanKreditController@index');
				Route::get('/datatable', 'PenjualanKreditController@datatable');
				Route::get('/get-detail', 'PenjualanKreditController@get_detail');
				Route::get('/get-record/{detail}', 'PenjualanKreditController@get_record');
				Route::post('/add', 'PenjualanKreditController@submit_data' );
				Route::post('/update', 'PenjualanKreditController@update_data');
				Route::post('/delete', 'PenjualanKreditController@delete_data');
				Route::post('/posting', 'PenjualanKreditController@posting_data');
				Route::get('/spjb', 'PenjualanKreditController@spjb');
				Route::get('/kwitansi', 'PenjualanKreditController@kwitansi');
			});

			Route::group(['prefix'=>'cash/{uuid}'], function(){
				Route::get('/', 'PenjualanCashController@index');
				Route::get('/datatable', 'PenjualanCashController@datatable');
				Route::get('/get-detail', 'PenjualanCashController@get_detail');
				Route::get('/get-record/{detail}', 'PenjualanCashController@get_record');
				Route::post('/add', 'PenjualanCashController@submit_data' );
				Route::post('/update', 'PenjualanCashController@update_data');
				Route::post('/delete', 'PenjualanCashController@delete_data');
				Route::post('/posting', 'PenjualanCashController@posting_data');
				Route::get('/spjb', 'PenjualanCashController@spjb');
				Route::get('/kwitansi', 'PenjualanCashController@kwitansi');
			});

			Route::group(['prefix'=>'cash-tempo/{uuid}'], function(){
				Route::get('/', 'PenjualanCashTempoController@index');
				Route::get('/datatable', 'PenjualanCashTempoController@datatable');
				Route::get('/get-detail', 'PenjualanCashTempoController@get_detail');
				Route::get('/get-rencana-bayar/{rb}', 'PenjualanCashTempoController@get_rencana_bayar');
				Route::get('/get-record/{detail}', 'PenjualanCashTempoController@get_record');
				Route::post('/add', 'PenjualanCashTempoController@submit_data' );
				Route::post('/update', 'PenjualanCashTempoController@update_data');
				Route::post('/delete', 'PenjualanCashTempoController@delete_data');
				Route::post('/hapus', 'PenjualanCashTempoController@hapus_data');
				Route::post('/rencana-bayar', 'PenjualanCashTempoController@rencana_bayar');
				Route::post('/posting', 'PenjualanCashTempoController@posting_data');
				Route::get('/spjb', 'PenjualanCashTempoController@spjb');
				Route::get('/kwitansi', 'PenjualanCashTempoController@kwitansi');
			});
		});

		Route::group(['prefix'=>'angsuran'], function(){
			Route::get('/', 'AngsuranController@index');
			Route::get('/datatable', 'AngsuranController@datatable');

			Route::group(['prefix'=>'kredit/{uuid}'], function(){
				Route::get('/', 'AngsuranKreditController@index');
				Route::get('/datatable', 'AngsuranKreditController@datatable');
				Route::get('/get-detail', 'AngsuranKreditController@get_detail');
				Route::post('/add', 'AngsuranKreditController@submit_data' );
				Route::get('/{ang_uuid}', 'AngsuranKreditController@print' );
				Route::post('/pelunasan', 'AngsuranKreditController@pelunasan' );
			});

			Route::group(['prefix'=>'cash-tempo/{uuid}'], function(){
				Route::get('/', 'AngsuranCashTempoController@index');
				Route::get('/datatable', 'AngsuranCashTempoController@datatable');
				Route::get('/get-data', 'AngsuranCashTempoController@get_data');
				Route::get('/get-detail/{ud}', 'AngsuranCashTempoController@get_detail');
				Route::post('/add', 'AngsuranCashTempoController@submit_data' );
				Route::get('/{ang_uuid}', 'AngsuranCashTempoController@print' );
				Route::post('/pelunasan', 'AngsuranCashTempoController@pelunasan' );
			});
		});

		Route::group(['prefix'=>'history-kredit'], function(){
			Route::get('/', 'LunasController@index');
			Route::get('/datatable', 'LunasController@datatable');

			Route::group(['prefix'=>'{uuid}'], function(){
				Route::get('/', 'LunasDetailController@index');
				Route::get('/datatable', 'LunasDetailController@datatable');
				Route::get('/{ang_uuid}', 'LunasDetailController@print' );
			});
		});

		Route::group(['prefix'=>'cash-flow'], function(){
			Route::get('/', 'CashFlowController@index');
			Route::get('/datatable', 'CashFlowController@datatable');
			Route::get('/get-detail/{uuid}', 'CashFlowController@get_detail');
			Route::post('/add', 'CashFlowController@submit_data' );
		});

		Route::group(['prefix'=>'laporan-kas'], function(){
			Route::get('/', 'LaporanKasController@index');
			Route::get('/{bulan}/{tahun}/{ukuran}', 'LaporanKasController@get_laporan');
		});

		Route::group(['prefix'=>'laporan-angsuran'], function(){
			Route::get('/', 'LaporanAngsuranController@index');
			Route::get('/{project}', 'LaporanAngsuranController@get_laporan');
		});
	});
});
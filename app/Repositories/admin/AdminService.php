<?php
namespace App\Repositories\admin;

use App\Repositories\admin\AdminRepository;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidatePassword;
use App\Http\Requests\ValidateProfile;
use Auth;
use Hash;
use DB;
use File;

class AdminService extends Controller
{
	protected $repo;

    public function __construct(AdminRepository $repo)
    {
        $this->repo = $repo;
    }

    function submit_password(ValidatePassword $r){
    	$id_user = Auth::user()->id;
        $current = trim($r->lama);
        $pass1 = trim($r->baru1);
        $pass2 = trim($r->baru2);

        $user = $this->repo->find($id_user);
        if (Hash::check($current, $user->password)){
            if($pass1==$pass2 && strlen($pass1)>=6){
                $password_baru = Hash::make($pass1);
                DB::beginTransaction();
                try{
                    $this->repo->update(['password' => $password_baru],$id_user);
                    DB::commit();
                    return response()->json(['success' => 'Password berhasil diubah!']);
                }catch (\Exception $e){
                    DB::rollback();
                    return response()->json(['error' => 'Terjadi kesalahan!']);
                }
            }else{
                return response()->json(['error' => 'Kombinasi password tidak sesuai!']);
            }
        }else{
            return response()->json(['error' => 'Password lama tidak sesuai!']);
        }
    }

    function edit_profile(ValidateProfile $r){
    	$id = decrypt($r->id);
        $path = 'photo';
        DB::beginTransaction();
        try{
            $this->repo->update_profile(['nama'=>trim($r->nama), 'telp'=>trim($r->telp)],$id);
            if($r->file('photo') != null){
                $photo = $r->file('photo');
                $photo_nama = 'profile-'.$this->Uuid().'.'.$photo->getClientOriginalExtension();
                $photo->move($path, $photo_nama);
                file::delete('photo/'.$r->old_photo);
                $this->repo->update_photo(['photo'=>$photo_nama],$id);
            }
            DB::commit();
            return response()->json(['success'=>'Profile berhasil diupdate!']);
        }catch(\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan!']);
        }
    }
}
<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\ModelUser;
use App\ModelSepeda;
use App\ModelBooking as booking;
use Validator;
use Response;
use Auth;
use DB;

class ApiController extends Controller
{ 
  public function customerupdate(Request $request){
    $id = $request->id;
    $email = $request->email;
    $password = $request->password;
    $nama = $request->nama;
    $nohp = $request->nohp;
    $alamat = $request->alamat;
    $noktp = $request->noktp;
    DB::table('TBUser')->where('id',$id)->update([
      'email' => $email,
      'password' => $password,
      'nama' => $nama,
      'nohp' => $nohp,
      'alamat' => $alamat,
      'noktp' => $noktp
    ]);
    $arr = array("status" => 200,"message" => "SUCCES", "data" => "SUCCES");
    return response()->json($arr);
  }
  public function deleteitem(Request $req){
    $id = $req->id;
    $data = ModelSepeda::where('id',$id)->first();
    $path = storage_path('images/'.$data->gambar);
    File::delete($path);
    $data->delete();
    $arr = array("status" => 200, "message" => "SUCCES");
    return response()->json($arr);
  }
  public function getimage($filename){
    $path = storage_path('images/'.$filename);
    if(!File::exists($path)) abort(404);
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file,200);
    $response->header("Content-Type",$type);
    return $response;
  }
  public function postsepeda(Request $request){
    $getkodesepeda = $request->kodesepeda;
    $getmerk = $request->merk;
    $getwarna = $request->warna;
    $gethargasewa = $request->hargasewa;

    if ($request->hasFile('image')) {
      $image = $request->file('image');
      $name = time().'.'.$image->getClientOriginalExtension();
      $destinationPath = storage_path('/images');
      $image->move($destinationPath, $name);
    
    }else{
      $arr = array("status" => 201, "message" => "failed");
      return response()->json($arr);
    }
    $url = "http://192.168.6.233:8000/api/image/".$name;
    $newUser = ModelSepeda::insert([
      'kodesepeda' => $getkodesepeda,
      'merk' => $getmerk,
      'warna' => $getwarna,
      'gambar' => $name,
      'url_image' => $url,
      'hargasewa' => $gethargasewa
    ]);

    $arr = array("status" => 200,"message" => "SUCCES", "data" => $newUser);
    return response()->json($arr);
  }
  public function deletecustomer(Request $req){
    $id = $req->id;
    $data = ModelUser::where('id',$id)->first();
    $data->delete();
    $arr = array("status" => 200, "message" => "SUCCES");
    return response()->json($arr);
  }
  public function bookingupdate(Request $req){
    $id = $req->id;
    $activated = $activated;
    $getdate = date("Y-m-d");
    DB::table('TBbooking')->where('id',$id)->update([
      'email' => $email,
    ]);
  } 
  public function bookingitem(Request $req){
    $i_user = $req->I_USER;
    $i_item = $req->I_ITEM;
    $getdate = date("Y-m-d");
    $booking = booking::insert([
      'user_id' => $i_user,
      'bicycle_id' => $i_item,
      'date_booking' => $getdate,
      'status' => "ACTIVATED" //END
    ]);
    if(!isset($booking)){
      $arr = array("status" => 201, "message" => "Failed");
      return response()->json($arr);
    }
    $arr = array("status" => 200,"message" => "SUCCES");
    return response()->json($arr,201);
  }

  public function getcustomer(){
    $data = ModelUser::get();
    if(!isset($data)){
      $arr = array("status" => 201, "message" => "FAILED","data" => $data);
      return response()->json($arr);
    }
    $arr = array("status" => 200, "message" => "SUCCES","data" => $data);
    return response()->json($arr);
  }
  public function postRegister(Request $request){
    $email = $request->email;
    $password = $request->password;
    $nama = $request->nama;
    $nohp = $request->nohp;
    $alamat = $request->alamat;
    $noktp = $request->noktp;
    if(!isset($email)){
      $arr = array(
        "status" => 422,
        "message" => "email tidak boleh kosong.."
      );
      return $arr;
    }if(!isset($password)){
      $arr = array(
        "status" => 422,
        "message" => "password tidak boleh kosong.."
      );
      return $arr;
    }if(!isset($nama)){
      $arr = array(
        "status" => 422,
        "message" => "nama tidak boleh kosong.."
      );
      return $arr;
    }if(!isset($nohp)){
      $arr = array(
      "status" => 422,"message" 
      => "nohp tidak boleh kosong.."
    );
    return $arr;
    }if(!isset($noktp)){
      $arr = array(
        "status" => 422,
        "message" => "noktp tidak boleh kosong.."
      );
      return $arr;
    }if(!isset($alamat)){
      $arr = array(
        "status" => 422,
        "message" => "alamat tidak boleh kosong.."
      );
      return $arr;
    }
    $there_is = ModelUser::where('email',$email)->first();
    if(isset($there_is))
    {$arr=array("message"=>"email sudah terpakai");
     return $arr;
    }
    $there_isUsernama = ModelUser::where('nama',$nama)->first();
    if(isset($there_is))
    {$arr=array("message"=>"nama sudah terpakai");
     return $arr;
    }
    $newUser = ModelUser::insert([
      'email' => $email,
      'password' => $password,
      'nama' => $nama,
      'nohp' => $nohp,
      'alamat' => $alamat,
      'noktp' => $noktp,
      'role_user' => '1',
    ]);
    if(!isset($newUser)){
      $arr = array("status" => 201, "message" => "Failed");
      return response()->json($arr);
    }
    $arr = array("status" => 200,"message" => "SUCCES");
    return response()->json($arr,201);
  }
  public function postLogin(Request $request){
    $email = $request->email;
    $password = $request->password;
    $akun = DB::table('TBUser')->where('email', $request->email)->first();
    if(!isset($akun)){
      $arr = array("status" => 201, "message" => "email incorect");
      return response()->json($arr);
    }
    $valPass = $akun->password;
    if($valPass == $password){
       if($akun->role_user =='1'){
          $getdata = DB::table('TBUser')->where('id', $akun->id)->first();
          $arr = array("status" => 200,"role" => "1","message" => "SUCCES", "data" => $getdata);
          return response()->json($arr);
       }elseif($akun->role_user =='2'){
        $getdata = DB::table('TBUser')->where('id', $akun->id)->first();
          $arr = array("status" => 200,"role" => "2","message" => "SUCCES", "data" => $getdata);
          return response()->json($arr);
       }
    }else{  
      $arr = array("status" => 201, "message" => "password incorect");
      return response()->json($arr);
    } 
  }
  public function getitem(){
    $data = ModelSepeda::get();
    $arr = array("status" => 200,"role"=> "2","message" => "Succes", "data" => $data);
    return response()->json($arr);
  }
  
}
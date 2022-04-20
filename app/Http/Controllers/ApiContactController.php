<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contact;
use App\Token;
class ApiContactController extends Controller
{
    //
    public function index(){
        
        $contacts=Contact::with('user')->where('record_deleted',0)->get();
        if($contacts==null)
        {
            return response()->json(['status'=>false,'message'=>'no contact found']);
        }
        return response()->json(['status'=>true,'message'=>'success','contacts'=>$contacts]);
    }
    public function store(Request $request){
       
        $data=$request->all();
        $validator=\Validator::make($request->all(),
        [
            'message'=>'required|max:191|min:3',
            'subject'=>'required|max:1000|min:5',
        ]);
        if ($validator->fails()) {
          return response()->json(['status'=>false,'message'=>'wrong data error','errors'=>$validator->errors()]);
        }
        
        if(isset($_GET['access_token']))
        {
         $access_token=$_GET['access_token'];
        }
         else
          return response()->json(['status'=>false,'message'=>'you dont have prevliouse ']);
           $token=Token::where('access_token',$access_token)->first();
           $user=$token->user;
           if($user==null)
           return response()->json(['status'=>false,'message'=>'you dont have prevliouseee']);
           $data['user_id']=$user->id;
         
          $contact=Contact::create($data);

         return response()->json(['status'=>true,'message'=>'post stored succssed','contac'=>$contact]);
    
    }

    public function delete($id){

        if(isset($_GET['access_token']))
        {
        $access_token=$_GET['access_token'];
        }
        else
        return response()->json(['status'=>false,'message'=>'you dont have prevliouse ']);

        $contact=contact::find($id);
        $token=Token::where('access_token',$access_token)->first();
        $user=$token->user;
       

        if($contact==null)
        return response()->json(['status'=>false,'message'=>'contact not found']);

        if($user->id!=$contact->user_id)
        return response()->json(['status'=>false,'message'=>'you dont have previouse']);

        $contact->delete();
        return response()->json(['status'=>true,'message'=>'YOur offer deleted successfully']);


    }
}

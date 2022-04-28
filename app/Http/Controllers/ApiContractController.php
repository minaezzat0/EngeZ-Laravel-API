<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Job;
use App\Offer;
use App\Token;
use App\Contact;
use App\contract;
class ApiContractController extends Controller
{
    //
    public function index(){
        $contracts=Contract::with('job','user','freelancer')->get();
        return response()->json(['status'=>true,'message'=>'contracted successfuly','contracts'=>$contracts]);
    }
    public function store(Request $request){
        $data=$request->all();
        $validator=\Validator::make($request->all(),
        [
            'title'=>'required|max:30|min:3',
            'desc'=>'required|max:1000|min:5',
             'price'=>'required',
            'job_id'=>'required|exists:jobs,id',
            'freelancer_id'=>'required|exists:users,id',
            

        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'mesage'=>'wrong data error','errors'=>$validator->errors()]);
        }
      $token=Token::where('access_token',$request->access_token)->first();
      $user=User::where('id',$token->user_id)->first();
      if($user==null)
      return response()->json(['status'=>false,'message'=>'you dont have previouse']);
 
      $data['user_id']=$user->id;
      $job=Job::find($request->job_id);
      $job->status=1;
      $job->save();
      $contract=Contract::create($data);
      return response()->json(['status'=>true,'message'=>'contracted successfuly','contract'=>$contract]);


        
    }
    public function show($id){

        $contract=contract::with('freelancer','job','user')->find($id);
        if($contract==null)
        return response()->json(['status'=>false,'message'=>'no contract found']);

        return response()->json($contract);
    }
    
    public function contractforuser($id)
    {

        $user = User::find($id);
        if ($user == null)
            return response()->json(['status' => false, 'message' => 'no user found']);

        if ($user->role == 'freelancer') {
            $contracts = contract::with('freelancer', 'job', 'user')->where('freelancer_id', $id)->get();
            if (count($contracts) == 0)
                return response()->json(['status' => false, 'message' => 'no contract found']);

            return response()->json($contracts);
        }
        $contracts = contract::with('freelancer', 'job', 'user')->where('user_id', $id)->get();
        if (count($contracts) == 0)
            return response()->json(['status' => false, 'message' => 'no contract found']);

        return response()->json($contracts);
    }
    public function mycontracts()
    {
        if (isset($_GET['access_token'])) {
            $access_token = $_GET['access_token'];
        } else
            return response()->json(['status' => false, 'message' => 'you dont have prevliouse ']);
        $token = Token::where('access_token', $access_token)->first();
        $user = $token->user;
        if ($user == null)
            return response()->json(['status' => false, 'message' => 'you dont have prevliouseee']);
        if ($user->role == 'users') {
            $contracts = Contract::with('job', 'freelancer','user')->where('user_id', $user->id)->get();
            return response()->json($contracts);
        }
        if ($user->role == 'freelancer') {
            $contracts = Contract::with('freelancer','job', 'user')->where('freelancer_id', $user->id)->get();
            return response()->json($contracts);
        }
    }
}

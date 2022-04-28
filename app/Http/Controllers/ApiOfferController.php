<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Offer;
use App\Job;
use App\User;
use App\Token;
use App\Mail\welcomeMail;
class ApiOfferController extends Controller
{
    
    public function index()
    {
        $offers=Offer::with('job')->get();
        return response()->json(['status'=>true,'message'=>'done','offers'=>$offers]);
        
    }
    public function store(Request $request)
    {
        $offer;
        $data=$request->all();
        $validator=\Validator::make($request->all(),
        [
            'content'=>'required|max:191|min:3',
            'job_id'=>'required|exists:jobs,id',
            'offer_amount'=>'required',
             
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'mesage'=>'wrong data error','errors'=>$validator->errors()]);
        }

         if(isset($_GET['access_token']))
         {
          $access_token=$_GET['access_token'];
         }
          else
           return response()->json(['message'=>'you dont have prevliouse ']);
 
           $token=Token::where('access_token',$access_token)->first();
           $user=$token->user;
           $user=User::where('id',$token->user_id)->first();
            $job=Job::where('id',$request->job_id)->first();

            if($user==null |$job==null)
            return response()->json(['message'=>'you dont have prevliouseee']);
            $data['user_id']=$user->id;
            $offer=Offer::create( $data);

             $details=['content'=>$offer->content,'offer_amount'=>$offer->offer_amount,'job'=>$job->title,'freelancer_name'=>$user->name,'freelancer_number'=>$user->mobilenumber];
             \Mail::to($job->user->email)->send(new welcomeMail($details));


            return response()->json(['status'=>true,'message'=>'offer send and store successfuly ','offer'=>$offer]);

    }
    public function delete($id)
    { 

            if(isset($_GET['access_token']))
            {
            $access_token=$_GET['access_token'];
            }
            else
            return response()->json(['message'=>'you dont have prevliouse ']);

            $offer=Offer::find($id);
            $token=Token::where('access_token',$access_token)->first();
            $user=$token->user;
           

            if($offer==null)
            return response()->json(['status'=>false,'message'=>'offer not found']);

            if($user->id!=$offer->user_id)
            return response()->json(['status'=>false,'message'=>'you dont have previouse']);

            $offer->delete();
            return response()->json(['status'=>true,'message'=>'YOur offer deleted successfully']);

    }
    function mail()
    {
            $details=['content'=>'healillo'];
            \Mail::to('mohamedelsdodey1996@gmail.com')->send(new welcomeMail($details));
            return response()->json(['success']);
    
        }
    public function offersByJobID($id)
    {
        $job = Job::find($id);
        // $job = $job->id;
        $offers = Offer::with('job')->where('job_id', $job->id)->get();
        return response()->json($offers);
    }
    public function verifyoffer($id){

        $offer=Offer::find($id);
        $job=Job::find($offer->job_id);

        $offers=$job->offers;
        foreach($offers as $offer)
        {
            if($offer->id==$id)
            {
                $offer->status='accepted';
                $offer->save();
            continue;
            }
            $offer->status='rejected';
            $offer->save();
        }
        $offer=Offer::find($id);
        $job=Job::find($offer->job_id);
        $job->status=1;
        $job->save();
        $details = "Your offer has been Accepted";
        \Mail::to($job->user->email)->send(new offerAccept($details));
        return response()->json(['status'=>true,'mesaage'=>'successed']);


    }
     public function show($id){
        $offer=Offer::with('job','user')->find($id);
        if($offer==null)
        return response()->json(['status'=>false,'message'=>'offer not found']);
        return response()->json($offer);

    }
    public function myoffers()
    {
        if (isset($_GET['access_token'])) {
            $access_token = $_GET['access_token'];
        } else
            return response()->json(['status' => false, 'message' => 'you dont have prevliouse ']);

        $token = Token::where('access_token', $access_token)->first();
        $user = $token->user;
        if ($user == null)
            return response()->json(['status' => false, 'message' => 'user not found']);


        $offers = Offer::with('job')->where('user_id', $user->id)->get();
        return response()->json( $offers);
    }
    public function offersByJobID($id)
    {
        $job = Job::find($id);
        // $job = $job->id;
        $offers = Offer::with('job','user')->where('job_id', $job->id)->get();
        return response()->json($offers);
    }

}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Job;
use App\User;
use App\Offer;
use App\Token;
class ApiJobController extends Controller
{
    function index()
    {

        $jobs=Job::with('user','category')->where('record_deleted',0)->get();
        if($jobs==null)
        return response()->json(['status'=>false ,'message'=>'no user yet']);

        return response()->json($jobs);
   
    }
    function show($id)
  {
    
        
 

        $job=Job::find($id);
         
        if($job==null||$job->record_deleted==1)
        return response()->json(['status'=>false,'message'=>'job not found']);    

        $count=(int) $job->viewcount;
        $job->viewcount=$count+1;
        $job->save();
        $job=Job::with('offers')->find($id);
    
        return response()->json($job);    
  }

  function store(Request $request)
  {
       
     $data=$request->all();
        $validator=\Validator::make($request->all(),
        [
            'title'=>'required|max:191|min:3',
            'desc'=>'required|max:1000|min:5',
            'img'=>'nullable',
             'duration'=>'required',
             'balance'=>'required',
             'adress'=>'required',
            'category_id'=>'required|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json(['mesage'=>'wrong data error','errors'=>$validator->errors()]);
        }
        if($request->file('img'))
        {
            $image=$request->file('img');
            $imagename=time().$image->getclientOriginalName();
            $img=\Image::make($image->getRealPath());
            $img->resize(350,350);
            $img->save(public_path('asset/images/jobs/'.$imagename));
           
            $data['img']=$imagename;
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

           $job=Job::create($data);

          return response()->json($job);
          

  }
  
  function edite($id,Request $request)
  {
            $data=$request->all();

            $validator=\Validator::make($request->all(),
            [
                'title'=>'nullable|max:191|min:3',
                'desc'=>'nullable|max:1000|min:5',
                'img'=>'nullable',
                'duration'=>'nullable',
                'balance'=>'nullable',
                'adress'=>'nullable',
                'category_id'=>'nullable|exists:categories,id'

            ]);
            if ($validator->fails()) {
                return response()->json(['status'=>false,'mesage'=>'wrong data error','errors'=>$validator->errors()]);
            }

            if($request->file('img'))
            {
                $image=$request->file('img');
                $imagename=time().$image->getclientOriginalName();
                $img=\Image::make($image->getRealPath());
                $img->resize(350,350);
                $img->save(public_path('asset/images/jobs/'.$imagename));
               
                $data['img']=$imagename;
            }
    
             if(isset($_GET['access_token']))
             {
              $access_token=$_GET['access_token'];
             }
              else
               return response()->json(['status'=>false,'message'=>'you dont have prevliouse ']);
                $token=Token::where('access_token',$access_token)->first();
                $user=$token->user;
                $job=Job::find($id);

                if($user==null||($user->id!=$job->user_id))
                return response()->json(['status'=>false,'message'=>'you dont have prevliouseee']);
              
                $data['user_id']=$user->id;

                
                $job->update($data);

                return response()->json($job);
                   
            
             }
             function delete($id)
            {
  
                if(isset($_GET['access_token']))
                {
                 $access_token=$_GET['access_token'];
                }
                 else
                  return response()->json(['status'=>false,'message'=>'you dont have prevliouse ']);
                   $token=Token::where('access_token',$access_token)->first();
                   $user=$token->user;
                   $job=Job::find($id);
                   
                   if($job==null)
                   return response()->json(['status'=>false,'message'=>'job not found ']);

            
                   if($user==null||($user->id!=$job->user_id))
                   return response()->json(['status'=>false,'message'=>'you dont have prevliouseee']);
                   $offers=Offer::where('job_id',$id)->get();
                   foreach($offers as $offer)
                   {
                   $offer->delete();
                   }
                    $job->delete();
                   return response()->json(['status'=>true,'message'=>'deleted successful']);


          }


          public function myjobs(){
              
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
              
        $jobs=Job::with('offers')->where('user_id',$user->id)->get();
        if($jobs==null)
        return response()->json(['status'=>false ,'message'=>'no jobs for you']);

        return response()->json($jobs);

          }
   
          public function latestfivejobs()
          {
            $jobs=Job::with('offers')->orderBy('id','Desc')->take(5)->get();
            if($jobs==null)
            return response()->json(['status'=>false ,'message'=>'no user yet']);
            return response()->json($jobs);
          }
        
          public function getjobsforuser($id)
          {
            $user = User::find($id);
            $jobs = Job::with('offers')->where('user_id', $user->id)->get();
            return response()->json($jobs);
          }


}

<?php

namespace App\Http\Controllers;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Http\Request;
use App\User;
use App\Category;
use Auth;
use App\Job;
use App\Offer;
use App\Token;
use App\Contact;
use App\Contract;

class ApiUserController extends Controller
{
    //
    function register(Request $request)
    {

        $validator=\Validator::make($request->all(),
        [
            'name'=>'required|max:191|min:3',
            'email'=>'required|unique:users|max:1000|min:5',
            'password'=>'required',
            'img'=>'required|image|max:10240|mimes:jpg,jpeg,png',
            'category_id'=>'required|exists:categories,id',
             'mobilenumber'=>'required',
             'id_card'=>'required',
             'adress'=>'required',
             'role'=>'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message'=>'you have errors','errors'=>$validator->errors()]);
        
         }


        $image=$request->file('img');
        $imagename=time().$image->getclientOriginalName();
        $img=\Image::make($image->getRealPath());
        $img->resize(350,350);
        $img->save(public_path('asset/images/users/'.$imagename));
       
        $user= User::create(['name'=>$request->name,'email'=>$request->email,
        'password'=>\Hash::make($request->password),
        'img'=>$imagename,
        'mobilenumber'=>$request->mobilenumber,
        'category_id'=>$request->category_id,
        'id_card'=>$request->id_card,
        'adress'=>$request->adress,
        'role'=>$request->role
         ]);
         $token=Token::create(['access_token'=> \Str::random(64),'user_id'=>$user->id]);
         

        return response()->json(['status'=>true,'message'=>'you are Register Successful','user'=>$user,"access_token"=>$token->access_token]);
    
    }


    function login(Request $request)
    {

       $validator=\Validator::make($request->all(),
       [
           'email'=>'required|email|max:1000|min:5',
           'password'=>'required',
           
       ]);
        
          
   
   
       if ($validator->fails()) {
           return response()->json(['status'=>false,'mesage'=>'wrong data','errors'=>$validator->errors()]);
       }
   
   
      $cred=['email'=>$request->email,'password'=>$request->password];
      if(Auth::attempt($cred))
      {
          $user=User::find(Auth::user()->id);
          $token=Token::create(['access_token'=> \Str::random(64),'user_id'=>$user->id]);
      
          return response()->json(['status'=>true,'message'=>'login successful','user'=>$user,'access_token'=>$token->access_token]);
      }
   
     else
     return  response()->json([ 'status'=>false,'message'=>'wrong email or password']);
     
    }
    function logout(Request $request)
    {

      $token=Token::where('access_token','=',$request->access_token)->first();
        if($token==null)
        return response()->json(['status'=>false,'message'=>'user not found']);

       $token->delete();
      return response()->json(['status'=>true,'message'=>'log out successfuly']);
    }

    public function index()
    {
        $users=User::with('category')->get();
        return response()->json(['status'=>true,'message'=>'successed','users'=>$users]);
   
    }

    public function show($id)
    {
        $user=User::with('category')->find($id);
        if($user==null)
        return response()->json(['status'=>false,'message'=>'user not found']);

        return response()->json(['status'=>true,'message'=>'successed','user'=>$user]);

    }

    public function edite($id,Request $request)
    {

        $validator=\Validator::make($request->all(),
        [
            'name'=>'nullable|max:191|min:3',
            'email'=>'nullable|max:1000|min:5',
            'password'=>'nullable',
            'img'=>'nullable|image|max:10240|mimes:jpg,jpeg,png',
            'category_id'=>'nullable|exists:categories,id',
            'mobilenumber'=>'nullable',
            'id_card'=>'nullable',
            'adress'=>'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>false,'message'=>'you have errors','errors'=>$validator->errors()]);
        
         }
       
        $user=User::find($id);
        if($user==null)
        {
            return response()->json(['status'=>false,'message'=>'user not find']);
  
        }
        $data=$request->all();
        if($request->file('img'))
        {
            $image=$request->file('img');
            $imagename=time().$image->getclientOriginalName();
            $img=\Image::make($image->getRealPath());
            $img->resize(350,350);
            $img->save(public_path('asset/images/users/'.$imagename));
           
            $data['img']=$imagename;
            unlink(public_path('asset/images/users/'.$user->img));
        }
        $user->update($data);
        return response()->json(['status'=>true,'message'=>'user updated successfuly','user'=>$user]);
        
    }
      public function delete($id)
      {

        $user=User::find($id);

        if($user==null)
            return response()->json(['status'=>false,'message'=>'user not find']);
            $offers=Offer::where('user_id',$id)->get();
            foreach($offers as $offer)
            {
            $offer->delete();
            }
          
            $jobs=Job::where('user_id',$id)->get();
            foreach($jobs as $job)
            {
            $job->delete();
            }
            $tokens=Token::where('user_id',$id)->get();
            foreach($tokens as $token)
            {
            $token->delete();
            }
            $contacts=Contact::where('user_id',$id)->get();
            foreach($contacts as $contact)
            {
            $contact->delete();
            }
            $user->delete();
            return response()->json(['status'=>true ,'message'=>'user deleted successfuly']);

  
      }
      public function dashboard()
      {

          $alluser=User::get()->count();
          $admin_count=User::where('role','admin')->get()->count();
          $freelancer_count=User::where('role','freelancer')->get()->count();
          $user_count=User::where('role','user')->get()->count();
          $categories_count=Category::get()->count();
          $contact_count=Contact::get()->count();
          $jobs_count=Job::get()->count();
          $offers_count=Offer::get()->count();
          $contracts=Contract::get();
          $contracts_value=0;
          foreach($contracts as $contract)
          {
              $contracts_value=$contracts_value+floatval($contract->price);
          }
          $users=User::where('role','freelancer')->get();
          $large_user='no user yet';
          $large_value=0;
          $users_contracts=[];

          if(count($users)>0)
          {
              foreach($users as $user)
              {
                  $contracts=Contract::where('freelancer_id',$user->id)->get();
                  $value=0;
                  foreach($contracts as $contract)
                  {
                      $value=$value+floatval($contract->price);
                  }
                  $users_contracts[$user->id]=$value;
              }
              $value = max($users_contracts);
              $id = array_search($value, $users_contracts);
               $large_user=User::find($id);
               $large_value=$value;
          }
          $maxuser=['user'=>$large_user,'contracts_value'=>$large_value];

          return response()->json(['status'=>true,'message'=>'success','allusers'=>$alluser,'admin_count'=>$admin_count,'freelancer_count'=>$freelancer_count,'user_count'=>$user_count,'categories'=>$categories_count,'job_count'=>$jobs_count,'offers'=>$offers_count,'contact_count'=>$contact_count,'contracts_value'=>$contracts_value,'maxuser'=>$maxuser]);
 
      }


 
      public function google($google)
      {
          return Socialite::driver($google)->redirect();
      }
   
      /**
       * Obtain the user information from GitHub.
       *
       * @return \Illuminate\Http\Response
       */
      public function googleCallback($google)
      {
          $user = Socialite::driver($google)->user();
        //  dd($user);
        
          $dbuser=User::where('email',$user->email)->first();
          $newuser=new User();
    
                if($dbuser==null)
                {
                    $categories=Category::get();
                   $flag=true;
                    foreach($categories as $category)
                       {
                           if($category->name=='none')
                           {
                               $newuser->category_id=$category->id;
                               $flag=false;
                               break;
                           }
                       }
                       if($flag)
                       {
                           $category=new Category();
                           $category->name='none';
                           $category->save();
                           $newuser->category_id=$category->id;

                       }
                      $newuser->name=$user->name;
                      $newuser->email=$user->email;
                      $newuser->img=$user->avatar;
                      $newuser->save();
                    $logeduser=$newuser;
                    $token=Token::create(['access_token'=> \Str::random(64),'user_id'=>$newuser->id]);

                    return response()->json(['status'=>true,'message'=>'you are register successfully','user'=>$newuser,'access_token'=>$token->access_token]);
                }
            else
            {

            
            $token=Token::where('user_id',$dbuser->id);
            return response()->json(['status'=>true,'message'=>'you are login successfuly ','user'=>$dbuser]);
            }
           
          // $user->token;
      }

    public function admins()
    {
        $users=User::with('category')->where('role','admin')->get();
        if($users==null)
        return response()->json(['status'=>false,'message'=>'there is no admin users']);

        return response()->json(['status'=>true,'message'=>'successed','users'=>$users]);
   

    }
    public function freelancers()
    {
        $users=User::with('category')->where('role','freelancer')->get();
        if($users==null)
        return response()->json(['status'=>false,'message'=>'there is no freelancer users']);

        return response()->json(['status'=>true,'message'=>'successed','users'=>$users]);
   
        
    }
    public function ordinaryusers()
    {
        $users=User::with('category')->where('role','users')->get();
        if($users==null)
        return response()->json(['status'=>false,'message'=>'there is no admin users']);
        return response()->json(['status'=>true,'message'=>'successed','users'=>$users]);
   
        
    }

}

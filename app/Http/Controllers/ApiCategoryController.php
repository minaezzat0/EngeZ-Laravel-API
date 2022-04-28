<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Job;
use App\User;
use App\Offer;
use App\Token;
use App\Category;
class ApiCategoryController extends Controller
{
    //
    public function index()
    {
        $categories=Category::get();
       
        return response()->json($categories);

    }
    public function store(Request $request)
    {

        $validator=\Validator::make($request->all(),
        [
           'name'=>'required|unique:categories|string|min:3|max:15',
           
        ]);
         if ($validator->fails()) {
            return response()->json(['status'=>false, 'message'=>'you have errors','errors'=>$validator->errors()]);
        
         }
        


         $category=Category::create(['name'=>$request->name]);
         return response()->json(['status'=>true,'message'=>'category saved successfuly','category'=>$category]);
     
    }
    public function edit($id,Request $request)
    {
        $validator=\Validator::make($request->all(),
        [
           'name'=>'required|string|min:3|max:15',
           
        ]);
         if ($validator->fails()) {
            return response()->json(['status'=>false, 'message'=>'you have errors','errors'=>$validator->errors()]);
        
         }
         $category=Category::find($id);
         if($category==null)
         return response()->json(['status'=>false, 'message'=>'category not found']);
      $category->name=$request->name;
      $category->save();
      return response()->json(['status'=>true,'message'=>'category updated successfuly','category'=>$category]);

      
    }
    public function delete($id)
    {
        $category=Category::find($id);
        if($category==null)
        return response()->json(['status'=>false, 'message'=>'category not found']);
       $users=User::where('id',$category->user_id)->get();
       foreach($users as $user)
       {
         
         $offers=Offer::where('user_id',$user->id)->get();
         foreach($offers as $offer)
         {
         $offer->delete();
         }
       
         $jobs=Job::where('user_id',$user->id)->get();
         foreach($jobs as $job)
         {
         $job->delete();
         }
         $tokens=Token::where('user_id',$user->id)->get();
         foreach($tokens as $token)
         {
         $token->delete();
         }
         $contacts=Contact::where('user_id',$user->id)->get();
         foreach($contacts as $contact)
         {
         $contact->delete();
         }


          $user->delete();
       }
        $category->delete();
        return response()->json(['status'=>true,'message'=>'category deleted successfuly',]);
  

    }

}

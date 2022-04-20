<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Token;
class is_api_user
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      
        $user=null;

        if( $request->access_token!=null)
        {


            $token=Token::where('access_token',$request->access_token)->first();
             if($token==null)
             return response()->json(['status'=>false,'message'=>'you dont have periviouse']);  

            $user=$token->user;

        }
        else
            {
               return response()->json(['status'=>false,'access token is requierd']);  
            }
        if($user!=null)
        return $next($request);
        else 
        {
            return response()->json(['status'=>false,'message'=>'you cant loginn']);  
        }
    }
}

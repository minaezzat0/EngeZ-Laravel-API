<?php

namespace App\Http\Middleware;

use Closure;
use App\Token;
class is_api_freelancer
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
        
        $flag=false;
        
          if($request->access_token!=null)
          {
            $token=Token::where('access_token',$request->access_token)->first();
            $user=$token->user;
            if($user!=null && $user->role=='freelancer')
            {
                $flag=true;
            }
             
         
        }
          else
            {
                return response()->json(['message'=>'access_token is required']);  
            }
        if($flag)
        return $next($request);
        else 
        {

            return response()->json(['message'=>'you dont have previouse']);  

        }
    }
}

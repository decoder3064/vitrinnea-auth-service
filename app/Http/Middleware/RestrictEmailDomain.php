<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictEmailDomain
{
    public function handle(Request $request, Closure $next)
    {
        $email = $request->input('email');
        
        // Check if email ends with @vitrinnea.com
        if ($email && !str_ends_with($email, '@vitrinnea.com')) {
            return response()->json([
                'success' => false,
                'message' => 'Only Vitrinnea employees can access this service.'
            ], 403);
        }
        
        return $next($request);
    }
} 

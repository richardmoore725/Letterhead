<?php

namespace App\Http\Middleware;

use App\Models\Message;
use Illuminate\Http\Request;
use Closure;

class ValidatePromotionMessageDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        $validator = app('validator')
            ->make($request->input(), [
                'message' => 'required|string',
                'promotionId' => 'required|integer',
            ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $message = $request->input('message');
        $promotionId = $request->input('promotionId');

        $request->setRouteResolver(function () use ($message, $promotionId, $route) {
            $route[2]['message'] = $message;
            $route[2]['promotionId'] = $promotionId;

            return $route;
        });

        return $next($request);
    }
}

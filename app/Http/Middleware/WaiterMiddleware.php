<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class WaiterMiddleware
{
    public function __construct(private \stdClass $output)
    {
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->role === User::USER_TYPE_WAITER)
            return $next($request);

        $this->output->Error = [__('errors.unauthorized')];
        return response()->json($this->output, Response::HTTP_UNAUTHORIZED);

    }
}

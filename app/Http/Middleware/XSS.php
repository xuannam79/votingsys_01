<?php

namespace App\Http\Middleware;

use Closure;

class XSS
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
        $input = $request->all();

        $callback = function (&$value) use ($input) {
            if (!array_key_exists('optionDescription', $input)) {
                $value = htmlentities($value);
            }
        };

        array_walk_recursive($input, $callback);
        $request->merge($input);

        return $next($request);
    }
}

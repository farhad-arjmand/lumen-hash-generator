<?php namespace FarhadArjmand\LumenHashGenerator\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;

/**
 * Class     JwtMiddleware
 *
 * @package  Themesfa\LumenHashGenerator\Middleware
 * @author   Themesfa <info@themesfa.net>
 */
class JwtMiddleware
{
	public function handle(Request $request, Closure $next)
	{
		$token = $request->get('token');

		if(!$token) {
			// Unauthorized response if token not there
			return response()->json([
				'error' => 'Token not provided.'
			], 401);
		}

		try {
			$credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
		} catch(ExpiredException $e) {
			return response()->json([
				'error' => 'Provided token is expired.'
			], 400);
		} catch(Exception $e) {
			return response()->json([
				'error' => 'An error while decoding token.'
			], 400);
		}

		// Now let's put the user in the request class so that you can grab it from there
		$user = User::find($credentials->sub);
		$request->auth = $user;

		return $next($request);
	}
}
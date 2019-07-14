<?php namespace FarhadArjmand\LumenHashGenerator\Controllers;

use App\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Validation\ValidationException;

/**
 * Class     AuthController
 *
 * @package  Themesfa\LumenHashGenerator\Controllers
 * @author   Themesfa <info@themesfa.net>
 */
class AuthController extends Controller
{

	/**
	 * Register a user and return the token.
	 *
	 * @param Request $request
	 * @return mixed
	 * @throws ValidationException
	 */
	public function register(Request $request) {

		# Validate Data
		$this->validate($request, [
			'name'      => 'required',
			'email'     => 'required|email',
			'country'   => 'required',
			'password'  => 'required'
		]);

		# Check User exist ot not.
		if (User::where('email', '=', Input::get('email'))->exists()) {
			return response()->json([
				'error' => 'User already exist!, Please login.',
			], 400);
		}

		# Register User
		$user           = new User();
		$user->name     = Input::get( 'name' );
		$user->email    = Input::get( 'email' );
		$user->country  = Input::get( 'country' );
		$user->password = Hash::make( Input::get( 'password' ) );
		$user->save();

		# Generate the token
		return response()->json([
			'token' => $this->jwt($user),
		], 200);
	}

	/**
	 * Authenticate a user and return the token if the provided credentials are correct.
	 *
	 * @param Request $request
	 * @return mixed
	 * @throws ValidationException
	 */
	public function login(Request $request) {

		# Validate Data
		$this->validate($request, [
			'email'     => 'required|email',
			'password'  => 'required'
		]);

		# Find the user by email
		$user = User::where('email', Input::get('email'))->first();

		if (!$user) {
			return response()->json([
				'error' => 'Email does not exist.',
			], 400);
		}

		# Verify the password and generate the token
		if (Hash::check(Input::get('password'), $user->password)) {
			return response()->json([
				'token' => $this->jwt($user),
			], 200);
		}

		// Bad Request response
		return response()->json([
			'error' => 'Email or password is wrong.'
		], 400);
	}

	/**
	 * Create a new token.
	 *
	 * @param User $user
	 * @return string
	 */
	protected function jwt(User $user) {
		$leeway  = config( 'hash.jwt-leeway', 60 );
		$payload = [
			'iss' => "lumen-hash-generator-jwt", // Issuer of the token
			'sub' => $user->id, // Subject of the token
			'iat' => time(), // Time when JWT was issued.
			'exp' => time() + $leeway // Expiration time
		];

		# Expiration time
		JWT::$leeway = $leeway;

		// As you can see we are passing `JWT_SECRET` as the second parameter that will
		// be used to decode the token in the future.
		return JWT::encode($payload, env('JWT_SECRET'));
	}
}
<?php namespace FarhadArjmand\LumenHashGenerator\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class     HashController
 *
 * @package  Themesfa\LumenHashGenerator\Controllers
 * @author   Themesfa <info@themesfa.net>
 */
class HashController extends Controller {

	/**
	 * Generate random hash.
	 *
	 * @param Request $request
	 * @return string
	 * @throws ValidationException
	 */
	public function generator(Request $request)
	{
		# Validate Data
		$this->validate($request, [
			'str'     => 'required',
		]);

		# generate the hash
		$algo = config( 'hash.algo', 'sha1' );
		$data = config( 'hash.salt' ) . $request->input( 'str' );
		$raw  = config( 'hash.raw', 'hex' ) === 'binary' ? true : false;
		$hash = hash( $algo, $data, $raw );

		# Respond
		return response()->json([
			'hash' => $hash,
		], 200);
	}

}
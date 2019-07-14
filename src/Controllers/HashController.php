<?php namespace FarhadArjmand\LumenHashGenerator\Controllers;

use App\Http\Controllers\Controller;

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
	 * @return string
	 */
	public function generator()
	{
		# generate the hash
		$algo = config( 'hash.algo', 'sha1' );
		$data = config( 'hash.salt' ) . time(); // to create random hash we use time() function.
		$raw  = config( 'hash.raw', 'hex' ) === 'binary' ? true : false;
		$hash = hash( $algo, $data, $raw );

		# Respond
		return response()->json([
			'hash' => $hash,
		], 200);
	}

}
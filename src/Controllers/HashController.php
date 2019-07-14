<?php namespace FarhadArjmand\LumenHashGenerator\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
	public function generator(Request $request)
	{
		# generate the hash
		$algo = config( 'hash.algo', 'sha1' );
		$salt = config( 'hash.salt' );
		$data = $salt . time(); // to create random hash we use time() function.
		$raw  = config( 'hash.raw', 'hex' ) === 'binary' ? true : false;
		$hash = hash( $algo, $data, $raw );

		# Monolog
		$log['user_id'] = $request->auth->id;
		$log['algo']    = $algo;

		if( !empty($salt) ){
			$log['salt'] = $salt;
		}

		$log['hash'] = $hash;

		monolog()->info('New Hash Generated.', $log);

		# Respond
		return response()->json([
			'hash' => $hash,
		], 200);
	}

}
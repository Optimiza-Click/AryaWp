<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/09/18
 * Time: 9:57
 */

namespace Blogging;

use Auth\JwtAuth;

class AryaWp
{
    public function __construct()
    {
        add_action('rest_api_init', [AryaApi::class, 'registerEndpoints']);
    }

    public static function isAllowToCreatePost($request) {
        try {
            JwtAuth::isValidToken($request->key, 'arya_wp');
        }catch (\Exception $e){
            return ['request' => $e->getMessage()];
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/09/18
 * Time: 9:57
 */

namespace Blogging;

use Firebase\JWT\JWT;

class AryaWp extends AryaApi
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'registerEndpoints']);
    }

    public static function isAllowToCreatePost($request) {
        try {
            self::isValidToken($request->key, $request->auth);
        }catch (\Exception $e){
            return ['request' => $e->getMessage()];
        }
    }

    public static function isValidToken($jwt, $stringValidation)
    {
        if(empty($jwt)){
            throw new \Exception ("Missing token");
        }

        $key = file_get_contents('key.txt', FILE_USE_INCLUDE_PATH);

        $decoded = JWT::decode($jwt, $key, ['RS256']);

        if($decoded !== $stringValidation){
            throw new \Exception ("Token not valid");
        }
    }

    public static function createImageFromRemoteUrl($imageUrl) {
        $body = wp_remote_retrieve_body(wp_remote_get( $imageUrl, [ 'timeout' => 8  ] ));

        $filename = wp_upload_dir()['path'] . '/' . basename($imageUrl);

        file_put_contents($filename, $body);

        return (object) wp_upload_bits(basename($imageUrl), null, file_get_contents($filename));
    }

    public static function createCustomFieldsByPostId(array $metas, int $id): void {
        foreach($metas as $key => $meta) {
            update_post_meta($id, $key, $meta);
        }
    }
}
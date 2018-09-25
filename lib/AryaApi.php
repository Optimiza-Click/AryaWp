<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/09/18
 * Time: 9:54
 */

namespace Blogging;

use WP_REST_Request;

class AryaApi
{
    public static function registerEndpoints()
    {
        register_rest_route('arya/v1', '/posts/', [
            'methods' => 'POST',
            'callback' => [self::class, 'createPost'],
        ]);

        register_rest_route('arya/v1',  '/posts/image', [
            'methods' => 'POST',
            'callback' => [self::class, 'createImageByUrl'],
        ]);
    }

    public static function createImageByUrl(WP_REST_Request $request) {
        $image_url = $request->get_param('image_url');
        $fileType = wp_check_filetype( basename( $image_url ), null );

        $attachment = [
            'guid'           => $image_url,
            'post_mime_type' => $fileType['type'],
            'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $image_url ) )
        ];

        return ['result' => wp_insert_attachment( $attachment, $image_url )];
    }

    public static function createPost(WP_REST_Request $request) {

        $post = (object) $request->get_body_params();

        if(empty((array) $post)) {
            return ['result' => false, 'error' => 'Complete fields first'];
        }

        if(!AryaWp::isAllowToCreatePost($post->auth)) {
            return ['result' => false, 'error' => 'The JWT auth code isn\'t correct'];
        }

        $id = wp_insert_post([
            'post_title' => $post->title,
            'post_excerpt' => $post->excerpt,
            'post_status' => $post->status,
            'post_content' => $post->content,
            'post_type' => 'post'
        ]);

        if(!$id) {
            return ['result' => false, 'error' => $id];
        }

        $imageId = set_post_thumbnail( $id,  $post->image_id);

        if(!$imageId) {
            return ['result' => false, 'error' => $imageId];
        }

        return ['result' => true, 'id' => $id];
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 24/09/18
 * Time: 9:54
 */

namespace Blogging;

class AryaApi
{
    public static function registerEndpoints() {
        register_rest_route('arya/v1', '/posts/', [
            'methods' => 'POST',
            'callback' => [self::class, 'createPost'],
        ]);
    }

    protected static function createPost($request) {
        $post = (object) $request->get_params();

        if(!AryaWp::isAllowToCreatePost($post)) {
            return ['error' => 'The JWT auth code isn\'t correct'];
        }

        $id = wp_insert_post([
            'post_title' => $post->title,
            'post_excerpt' => $post->excerpt,
            'post_status' => $post->status,
            'post_content' => $post->content,
            'post_type' => 'post'
        ]);

        if(!$id) {
            return ['error' => $id];
        }

        $imageId = set_post_thumbnail( $id,  $post->image_id);

        if(!$imageId) {
            return ['error' => $imageId];
        }

        return ['result' => true];
    }
}
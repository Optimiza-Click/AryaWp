# ARYA
Plugin to manage the wordpress posts via API with JWT authentication.

### How use it?
* If we want set a thumbnail in a post, we must upload the image first into a gallery; if you can know the parameters visit "https://v2.wp-api.org/reference/media/". It'll return the id of image that we'll use later.
```
POST http://example.com/wp-json/wp/v2/media/
```
* To upload the post we use this endpoint.
```
{
    title: <string>'example title',
    excerpt: <string>'example_excerpt',
    status: <string>'example_status',
    content: <string>'example_status'
    image_id: <number>2
}

POST http://example.com/wp-json/arya/v1/posts
```
# ARYA
Plugin to manage the wordpress posts via API with JWT authentication.

### How use it?
 If we want set a thumbnail in a post, we must upload the image first into a gallery.

####  Check Arya status
```
GET http://example.com/wp-json/arya/v1/ping
```

###### SUCCESS 200: EXAMPLE
```
{
  result: true
}
```

#### To upload the image:
```
POST http://example.com/wp-json/arya/v1/posts/image
```

| Title  | Type  |  Description |
|---|---|---|
| image_url  | string  | the url of the image |

###### SUCCESS 200: EXAMPLE
```
{
  result: true,
  id: 18
}
```

####  To upload the post we use this endpoint.
```
POST http://example.com/wp-json/arya/v1/posts
```

| Title  | Type  |  Description |
|---|---|---|
| title  | string  | title of post |
| excerpt  | string  | a little description of content |
| status  | string  | will be publish or draft |
| content  | string  | the content of post |
| image_id  | number  | the id of the image uploaded previusly |

###### SUCCESS 200: EXAMPLE
```
{
  result: true,
  id: 15,
  url: 'http://example.com/post_example'
}
```
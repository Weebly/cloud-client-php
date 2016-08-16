<?php
/**
 * Blog class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a Weebly Cloud blog.
 */
class Blog extends Utils\CloudResource
{
    /**
     * ID of the user the blog belongs to.
     *
     * @var string $user_id
     */
    private $user_id;

    /**
     * ID of the site the blog belongs to.
     *
     * @var string $site_id
     */
    private $site_id;

    /**
     * Unique ID of the blog.
     *
     * @var string $blog_id
     */
    private $blog_id;

    /**
     * Creates a new Blog object.
     *
     * @param string $user_id ID of the user the blog belongs to.
     * @param string $site_id ID of the site the blog belongs to.
     * @param string $blog_id Unique ID of the blog.
     * @param bool $initialize Whether or not to retrieve this blog's properties
     *          from the server upon instantiation. The properties can later
     *          be retrieved by calling get().
     * @param object $existing Object to use as the Blog's properties if initialize
     *          is false.
     * @return Blog
     */
    public function __construct($user_id, $site_id, $blog_id, $initialize = true, $existing = null) {
        $this->user_id = $user_id;
        $this->site_id = $site_id;
        $this->blog_id = $blog_id;
        $this->url = "user/$user_id/site/$site_id/blog/$blog_id";
        if ($initialize) {
            $this->get();
        } else {
            $this->properties = $existing;
        }
    }

    /**
     * Returns a CloudList of BlogPosts on this Blog.
     *
     * @return CloudList
     */
    public function listBlogPosts() {
        $client = Utils\CloudClient::getClient();
        $res =  $client->getList($this->url . "/post");
        return new Utils\CloudList(
            $res,
            "\WeeblyCloud\BlogPost",
            array("user_id"=>$this->user_id, "site_id"=>$this->site_id, "blog_id"=>$this->blog_id)
        );
    }

    /**
     * Returns the BlogPost with the given id.
     *
     * @param string $post_id ID of the BlogPost to return.
     * @return BlogPost
     */
    public function getBlogPost($post_id) {
        return new BlogPost($this->user_id, $this->site_id, $this->blog_id, $post_id);
    }

    /**
     * Creates a new BlogPost on the blog.
     *
     * @param string $post_body The body of the BlogPost.
     * @param array $data Optional data about the BlogPost.
     * @return BlogPost
     */
    public function createBlogPost($post_body, $data = []) {
        $data["post_body"] = $post_body;
        $client = Utils\CloudClient::getClient();
        $post = json_decode($client->post($this->url . "/post", $data)->body);
        return new BlogPost($this->user_id, $this->site_id, $this->blog_id, $post->post_id, false, $post);
    }

    /**
     * Converts a JSON response into an array of
     * Blog objects. Because the formatting of responses
     * and the IDS needed for instantiation are
     * inconsistent across endpoints, this is handled
     * on a class-by-class basis.
     *
     * @param array $ids The IDs necessary to construct the Blogs
     *              (user_id and site_id).
     * @param string $json JSON of a list of blogs.
     * @return CloudList
     */
    public static function arrayFromJSON($ids, $json) {
        $user_id = $ids["user_id"];
        $site_id = $ids["site_id"];
        $blogs = array();
        $arr = json_decode($json);
        foreach ($arr as $blog) {
            $blogs[] = new Blog($user_id, $site_id, $blog->blog_id, false, $blog);
        }
        return $blogs;
    }
}

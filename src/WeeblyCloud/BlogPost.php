<?php
/**
 * BlogPost class file
 *
 * @package WeeblyCloud
 * @author Caitlin Scarberry <caitlin.scarberry@weebly.com>
 */

namespace WeeblyCloud;

/**
 * Represents a mutable and deletable Weebly Cloud blog post.
 */
class BlogPost extends Utils\CloudResource
{
	use Traits\DeletableTrait, Traits\MutableTrait;
	/**
	 * ID of the user the blog post belongs to.
	 *
	 * @var string $user_id
	 */
	private $user_id;

	/**
	 * ID of the site the blog post belongs to.
	 *
	 * @var string $site_id
	 */
	private $site_id;

	/**
	 * ID of the blog the post belongs to.
	 *
	 * @var string $blog_id
	 */
	private $blog_id;

	/**
	* Unique ID of the blog post.
	*
	* @var string $post_id
	*/
	private $post_id;

	/**
	 * Creates a new BlogPost object.
	 *
	 * @param string $user_id ID of the user the post belongs to.
	 * @param string $site_id ID of the site the post belongs to.
	 * @param string $blog_id ID of the blog the post belongs to.
	 * @param string $post_id Unique ID of the blog post.
	 * @param bool $initialize Whether or not to retrieve this blog post's properties
	 * 			from the server upon instantiation. The properties can later
	 * 			be retrieved by calling get().
	 * @param object $existing Object to use as the BlogPost's properties if initialize
	 * 			is false.
	 * @return BlogPost
	 */
	public function __construct($user_id, $site_id, $blog_id, $post_id, $initialize = true, $existing = null){
		$this->user_id = $user_id;
		$this->site_id = $site_id;
		$this->blog_id = $blog_id;
		$this->post_id = $post_id;
		$this->url = "user/$user_id/site/$site_id/blog/$blog_id/post/$post_id";
		if ($initialize) {
			$this->get();
		} else {
			$this->properties = $existing;
		}
	}

	/**
	 * Converts a JSON response into an array of
	 * BlogPost objects. Because the formatting of responses
	 * and the IDS needed for instantiation are
	 * inconsistent across endpoints, this is handled
	 * on a class-by-class basis.
	 *
	 * @param array $ids The IDs necessary to construct the BlogPosts
	 *				(user_id, site_id, and blog_id).
	 * @param string $json JSON of a list of BlogPosts.
	 * @return CloudList
	 */
	public static function arrayFromJSON($ids, $json) {
		$user_id = $ids["user_id"];
		$site_id = $ids["site_id"];
		$blog_id = $ids["blog_id"];
		$posts = array();
		$arr = json_decode($json);
		foreach ($arr as $post) {
			$posts[] = new BlogPost($user_id, $site_id, $blog_id, $post->post_id, false, $post);
		}
		return $posts;
	}

}

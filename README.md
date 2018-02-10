# Weebly Cloud API Library: PHP

## Installation

###Composer

	composer require weebly/cloud-client

###Other
Download the latest release and include init.php in your code.

	require_once("path/to/cloud-client-php/src/init.php");

## Setup and Authentication
API calls are authenticated through a public key and a hash of your request and secret key. You can create and manage your API keys in the settings section of the Weebly Cloud Admin.

	WeeblyCloud\CloudClient::setKeys(YOUR_API_KEY, YOUR_API_SECRET);

You must set your public and secret key **before** making any calls to the API.

## Examples

#### Typical use case: create a user and site, get a login link

```php
// Get the admin account
try {
	$account = new WeeblyCloud\Account();
} catch (WeeblyCloud\Utils\CloudException $e) {
	print("Unable to get account.\n");
	print("Error code {$e->getCode()}: {$e->getMessage()}.\n");
	exit();
}

//Create a new user
try {
	$user = $account->createUser("test@domain.com");
} catch (WeeblyCloud\Utils\CloudException $e) {
	print("Unable to create user.\n");
	print("Error code {$e->getCode()}: {$e->getMessage()}.\n");
	exit();
}

//Store the user's ID
$user_id = $user->getProperty("user_id");

//Create a site
try {
	$site = $user->createSite("domain.com", ["site_title"=>"My Website"]);
} catch (WeeblyCloud\Utils\CloudException $e) {
	print("Unable to create site.\n");
	print("Error code {$e->getCode()}: {$e->getMessage()}.\n");
	exit();
}

//Store the site's ID
$site_id = $site->getProperty("site_id");

//Get and print a login link
try {
	print($site->loginLink());
} catch (WeeblyCloud\Utils\CloudException $e) {
	print("Unable to generate login link.\n");
	print("Error code {$e->getCode()}: {$e->getMessage()}.\n");
	exit();
}
```

#### Printing the name of all pages in a site matching the query "help"

```php
$pages = $site->listPages(["query"=>"help"]);
while ($page = $pages->next()) {
	print($page->getProperty("title")."\n");
}
```
or

```php
foreach ($site->listPages(["query"=>"help"]) as $page) {
	print($page->getProperty("title")."\n");
}

```

## Errors
If a request fails, the CloudClient throws a CloudException. A list of error codes can be found in the [API documentation](https://cloud-developer.weebly.com/about-the-rest-apis.html). The exception can be caught as folllows:

```php
# Create client
$client = WeeblyCloud\Utils\CloudClient::getClient();

try {
	$account = new WeeblyCloud\Account();
} catch (WeeblyCloud\Utils\CloudException $e) {
	print($e);
}
```

## Resources
The library provides classes that represent API resources. These resources can be **mutable**, meaning their properties can be changed, and **deletable**.

Common methods:

- The method **`$resource->getProperty($property)`** will return a given property of the resource. If the property does not exist, it will return **null**.
- The method **`$resource->setProperty($property, $value)`** will set the value of a given property of the resource. Changes will not be saved in the database until **`$resource->save()`** is called. If the resource is not mutable, calling this method will throw an exception. Not every property of a mutable resource can be changed; for more information, reference the [Cloud API Documentation](https://cloud-developer.weebly.com/about-the-rest-apis.html) for the resource in question's `PUT` method.
- The method **`$resource->save()`** saves the properties changed by setProperty() to the database. If the resource is not mutable, calling this method will throw an exception.
- The method **`$resource->delete()`** deletes the resource from the database. If the resource is not deletable, calling this method will throw an exception.

### Instantiating Resources
For example, to create an object representing a site with id `$site_id` and owned by `$user_id`:

	$site = new WeeblyCloud\Site($user_id, $site_id);

All resource constructors have two optional parameters, `initialize` (true by default) and `existing`. If `initialize` is set to false, the properties of the object are not retrieved from the database when the object is instantiated. Instead, `existing` is used to set the object's properties. This can be used to reduce unecessary API calls when chaining calls.

#### Examples

Retrieve all the sites of a user without getting that user's information:

	$sites = (new WeeblyCloud\User($user_id, false))->listSites();

Retrieve information about a site's store:

	$store = (new WeeblyCloud\Site($user_id, $site_id))->getStore();

Retrieve a list of products in the site's store:

	$products = $store->listProducts();
	while ($product = $products->next()) {
		print($product->getProperty("name")."\n");
	}


### Iterable Results
Methods beginning with `list` return a `CloudList`. Use the `next` function or a foreach loop to iterate through the list. For example:

```php
$sites = (new WeeblyCloud\User($user_id))->listSites();

while ($site = $sites->next()) {
	print($site->getProperty("site_title")."\n");
}
```
This would list the titles of all sites belonging to a given user.

##Resource Types

In addition to this readme, each resource class has phpdoc documentation. To view that documentation, install phpDocumentor and run these commands in the cloud-client-php directory:

	phpdoc -d ./src/WeeblyCloud
	open output/namespaces/WeeblyCloud.html

### Account
[API Documentation](https://cloud-developer.weebly.com/account.html)

A **mutable** representation of your Cloud Admin account, specified by your API keys. To construct:

	$account = new WeeblyCloud\Account();

- **`createUser($email, $data = [])`** Creates a new user in the database. Requires the user's **email**, and optionally takes an associative array, **data**, of additional properties. Returns the newly created User.
- **`listPlans()`** Gets a `CloudList` of available plans.
- **`getPlan($plan_id)`** Get a single plan by ID.

### Blog
[API Documentation](https://cloud-developer.weebly.com/blog.html)

A respresentation of a blog. To construct:

	$blog = new WeeblyCloud\Blog($user_id, $site_id, $blog_id);

- **`listBlogPosts()`** Returns a CloudList of BlogPosts on this Blog.
- **`getBlogPost($post_id)`** Returns the `BlogPost` with the given id.
- **`makeBlogPost($post_body, data = [] )`** Creates a new BlogPost on the blog. Requires the post's **post_body** and an optional associative array of additional parameters. Returns a `BlogPost` resource.

### BlogPost
[API Documentation](https://cloud-developer.weebly.com/blog-post.html)

A **mutable** and **deletable** respresentation of a blog post. To construct:

	$blog_post = new WeeblyCloud\BlogPost($user_id, $site_id, $blog_id, $post_id);

> There are no `BlogPost` specific methods.

### Form
[API Documentation](https://cloud-developer.weebly.com/form.html)

A respresentation of a form. To construct:

	$form = new WeeblyCloud\Form($user_id, $site_id, $form_id);

- **`listFormEntries($search_params = [])`** Returns a `CloudList` of `FormEntry` resources for a given form subject to the optional search parameters.
- **`getFormEntry($entry_id)`** Return the `FormEntry` with the given id.


### FormEntry
[API Documentation](https://cloud-developer.weebly.com/form-entry.html)

A respresentation of a form entry. To construct:

	$form_entry = new WeeblyCloud\FormEntry($user_id, $site_id, $form_id, $entry_id);

> There are no `FormEntry` specific methods.

### Group
[API Documentation](https://cloud-developer.weebly.com/group.html)

A **mutable** and **deletable** respresentation of a group. To construct:

	$group = new WeeblyCloud\Group($user_id, $site_id, $group_id);

### Page
[API Documentation](https://cloud-developer.weebly.com/page.html)

A **mutable** respresentation of a page. To construct:

	$page = new WeeblyCloud\Page($user_id, $site_id, $page_id);

> There are no `Page` specific methods.

### Plan
[API Documentation](https://cloud-developer.weebly.com/plan.html)

A respresentation of a plan. To construct:

	$plan = new WeeblyCloud\Plan($plan_id);

> There are no `Plan` specific methods.

### Member
[API Documentation](https://cloud-developer.weebly.com/member.html)

A **mutable** and **deletable** respresentation of a member. To construct:

	$member = new WeeblyCloud\Member($user_id, $site_id, $member_id);

### Product 
[API Documentation](https://cloud-developer.weebly.com/product.html)

A **mutable** and **deletable** respresentation of a site. To construct:

To construct:

	$product = new WeeblyCloud\Product($user_id, $site_id, $product_id);

- **`publish()`** Publishes the site.
- **`unpublish()`** Unpublishes the site.

### Site
[API Documentation](https://cloud-developer.weebly.com/site.html)

A **mutable** and **deletable** respresentation of a site. To construct:

To construct:

	$site = new WeeblyCloud\Site($user_id, $site_id);

- **`publish()`** Publishes the site.
- **`unpublish()`** Unpublishes the site.
- **`loginLink()`** Generates a one-time log-in link that redirects users to the editor for this Site. Will throw an exception if the user whose ID was used to instantiate the Site object is disabled.
- **`setPublishCredentials($data)`** Sets publish credentials for a given site. If a user's site will not be hosted by Weebly, publish credentials can be provided. Required properties of `$data` are publish\_host, publish\_username, publish\_password, and publish\_path.
- **`restore($domain)`** When a site is restored the owner of the site is granted access to it in the exact state it was when it was deleted, including the Weebly plan assigned. Restoring a site does not issue an automatic publish
- **`disable()`** Disables a site, preventing the user from accessing it through the editor.
- **`enable()`** Enables a site, allowing it to be edited. Sites are enabled by default when created.
- **`listPages($search_params = [])`** Returns a `CloudList` of `Pages` on this `Site`, subject to the search parameters.
- **`listMembers($search_params = [])`** Returns a `CloudList` of `Members` on this `Site`, subject to the search parameters.
- **`listGroups($search_params = [])`** Returns a `CloudList` of `Members` on this `Site`, subject to the search parameters.
- **`listForms()`** Returns a `CloudList` of `Forms` on this `Site`.
- **`listBlogs()`** Returns a `CloudList` of `Blogs` on this `Site`.
- **`getPage($page_id)`** Return the `Page` with the given id.
- **`getMember($member_id)`** Return the `Member` with the given id.
- **`getGroup($group_id)`** Return the `Group` with the given id.
- **`getForm($form_id)`** Return the `Form` with the given id.
- **`getBlog($blog_id)`** Return the `Blog` with the given id.
- **`getStore()`** Return the `Store` resource for the site.
- **`getPlan()`** Returns the `Plan` resource for the site.
- **`setPlan($plan_id, $term = 1)`** Assign a plan to the site with an optional term length.
- **`setTheme($theme_id, $is_custom)`** Assign a theme to the site by ID. Requires a parameter **is_custom**, distinguishing whether the theme is a Weebly theme or a custom theme.
- **`createMember($data)`** Creates a new `Member` of the site in the database. Returns the newly created `Member`.
- **`createGroup($name)`** Creates a new `Group` of members of the site in the database. Returns the newly created `Group`.


### Store
[API Documentation](https://cloud-developer.weebly.com/store.html)

A **mutable** respresentation of a site. To construct:

To construct:

	$store = new WeeblyCloud\Store($user_id, $site_id);

- **`updateStore($values)`** Update the store with provided values.
- **`listProducts($search_params)`** Retrieves a list of products for the store, subject to search parameters.
- **`getProductCount()`** Returns the number of products in the store.
- **`createProduct($product_name, $product_skus, $data = [])`** Creates a new `Product` in the `Store` in the database. Returns the newly created `Product`.
- **`getProduct($product_id)`** Retrieves a specific `Product` from the `Store` by ID.

### User
[API Documentation](https://cloud-developer.weebly.com/user.html)

A **mutable** respresentation of a WeeblyCloud user. To construct:

	$user = new WeeblyCloud\User($user_id);

- **`enable()`** Enables a user account after an account has been disabled. Enabling a user account will allow users to log in and edit their sites. When a user is created, their account is automatically enabled. **`disable()`** Disables a user account, preventing them from logging in or editing their sites.
- **`loginLink()`** Generates a one-time login link. Will return an error if the user has been disabled.
- **`getAvailableThemes($search_params = [])`** Returns an array of themes available to this user, subject to optional search parameters. The themes in the array are NOT resource objects, but are constructed directly from the response JSON. For valid search parameters, see the API documentation.
- **`listSites($search_params = [])`** Returns a CloudList of sites belonging to this user, subject to optional search parameters. **`getSite($site_id)`** Returns the `Site` with the given ID.
- **`createCustomTheme($name, $zip_url)`** Adds a custom theme to a user. Requires the name of the theme and the url of a publicly accessible .zip file.
- **`createSite($domain, $data = [])`** Creates a new `Site` belonging to this user in the database. Requires the site's **domain** and an optional associative array of properties, **data**. Returns the newly created `Site`.

## Making Raw API Calls
Not every resource has a cooresponding resource class. It is possible to make a raw API call using a `CloudClient` object.

```php
$client = WeeblyCloud\Utils\CloudClient::getClient();
```
Using that client, call `get`, `post`, `put`, `patch`, or `delete`. All client request methods take a url as their first argument. `post`, `patch`, and `put` take an optional hash map of data that will be sent in the request body. `get` takes an optional hash map whose values will be used in the query string of the request.

The url **must not** include a leading slash.

#### Request examples

##### Get cloud admin account account
```php
# Get client
$client = WeeblyCloud\Utils\CloudClient::getClient();

# Request the /account endpoint
$client->get("account");
```

##### Update a page title
```php
# Get client
$client = WeeblyCloud\Utils\CloudClient::getClient();

# Build endpoint with IDs
$endpoint = "user/{$user_id}/site/{$site_id}/page/{$page_id}";

# Make the request
$client->patch($endpoint, ["title"=>"New Title"]);
```

##### Get all sites for a user (with search parameters)
```php
# Get client
$client = WeeblyCloud\Utils\CloudClient::getClient();

# Build endpoint with IDs
$endpoint = "user/{$user_id}/site";

# Make the request (get all sites this user owns)
$client->get($endpoint, ["role"=>"owner"]);
```

### Handling Responses
All requests return a `CloudResponse` object or throw a CloudException. The JSON returned by the request can be accessed through the response's `body` property.

```php
# Make a request
$response = $client->get("account");

# Print JSON body of response
print($response->body);
```


###Pagination example
If the endpoint supports pagination, the next and previous pages of results can be retrieved with the `getPreviousPage()` and `getNextPage()` methods. If there is no next or previous page, those methods return null.

```php
# Create client
$client = WeeblyCloud\Utils\CloudClient::getClient();
$response = $client->get("user/{$user_id}/site",["limit"=>10]);

while($response){
	print($response->body . "\n");
	$response =  $response->getNextPage();
}
```
Get all of a user's sites, 10 sites per page.

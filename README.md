#simple instagram api

This is a simple set of classes to make requests to the Instagram API.

## what it is
This class was created due to my needs to display photos from a specific account from Instagram.

First, I used [NOEinteractive/instagramphp](https://github.com/NOEinteractive/instagramphp), but I needed a few more options (like handling pagination), so I decided to create this one, based on the former.

## what it is not
It was not my intention to build an app that users can authorize it in Instagram, for instance. If you need something like this, feel free to clone this repository and do whatever you want with it - what about making a pull request afterwards? :)

## usage
To use this class, first you need to register an application at [Instagram Developer Dashboard](https://instagram.com/developer/) and grab its access token.

Sample code:

```php
require 'class/Instagram/Base.php';
$instagram = new Instagram_Base($accessToken);
$result = $instagram->getResource('Photo')->getPhotos($username);
if ((!empty($result)) && ($result->getCount())) {
    foreach ($result->getData() as $row) {
        var_dump($row);
    }
}
```

## what is a resource?
[API endpoints](https://instagram.com/developer/endpoints/) are here represented as resources.
I only needed two: one to get an user ID based on its name, and another to actually fetch the photos of an account.
If you want to add more resources, all you have to do is create a file in the `Resource` folder with the name you want *(using [CamelCase](http://en.wikipedia.org/wiki/CamelCase) rules)* with a class that extends `Instagram_Resource_Abstract`.

Example: to add a new `Foo` resource, create a file named `Foo.php` with a class like this:

```php
class Instagram_Resource_Foo extends Instagram_Resource_Abstract
{

    /* 
     * My methods here
     */

}
```

To make a request to a URL, use `$this->request($url)`. It returns `false` if an error occurs or an `Instagram_ResultSet` object otherwise.

## structure

```
|-- Base.php            Base class, responsible to manage all the resource classes
|-- Factory.php         Factory of objects (currently only used to build Resources)
|-- Resource            Folder with all the resources
|   |-- Abstract.php    Abstract class of resources
|   |-- Photo.php       Simple resource class to fetch photos from a user
|   `-- User.php        Simple resource class to get an user ID by its name
`-- ResultSet.php       Class that holds a query response from a resource
```

## code documentation
I tried my best commenting the code using [phpDoc syntax](http://www.phpdoc.org/docs/latest/index.html), and I used [phpDocumentor 2](http://www.phpdoc.org/) to generate some nice documentation files. They are located in `docs` folder.

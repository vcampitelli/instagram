<?php
/**
 * Simple set of classes to make requests to the Instagram API
 * 
 * First, I used noe-interactive.com's class (see link below),
 * but I needed a few more options (like handling pagination),
 * so I decided to create this one, based on the former.
 * 
 * @author      Vinícius Campitelli <eu@viniciuscampitelli.com>
 * @link        https://github.com/NOEinteractive/instagramphp  Base class
 * @version     1.0.0
 * 
 * @license     http://opensource.org/licenses/gpl-license.php GNU Public License
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
 
/**
 * cURL extension is required to communicate with https
 * 
 * @link        http://br2.php.net/manual/en/book.curl.php
 */ 
if (!function_exists('curl_version')) {
    throw new Exception('This class requires cURL PHP extension to use Instagram API. ' .
        'Please visit http://br2.php.net/manual/en/book.curl.php to read more.');
}

/**
 * @see         Instagram\Resource\Instagram_Resource_Abstract
 */
if (!class_exists('Instagram_Resource_Abstract')) {
    require __DIR__ . '/Resource/Abstract.php';
}

/**
 * @see         Instagram\Instagram_Factory
 */
if (!class_exists('Instagram_Factory')) {
    require __DIR__ . '/Factory.php';
}

/**
 * Base class to interact with other resources
 * 
 * @package     Instagram\Instagram_Base
 * @see         Instagram\Resource\Instagram_Resource_Abstract  Resource abstract class
 * @see         Instagram\Instagram_Factory                     Factory class
 */
class Instagram_Base
{
    
    /**
     * Access Token generated from Instagram's Developer Panel
     * @link    https://instagram.com/developer/    URL to register clients and obtain access tokens
     * @var     string
     */
    private $__accessToken = '';
    
    /**
     * cURL configuration to be used
     * @var array
     */
    private $__arrCurlConfig = array(
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_TIMEOUT         => 5,
        CURLOPT_HEADER          => false
    );
    
    /**
     * Class constructor
     *
     * @param   string      $accessToken    Access Token (optional)
     * @return  void
     */
    public function __construct($accessToken = null)
    {
        if (!empty($accessToken)) {
            $this->setAccessToken($accessToken);
        }
    }
    
    /**
     * Builds a resource that extends Instagram_Resource_Abstract
     *
     * @throws  Exception       If class doesn't extend Instagram_Resource_Abstract
     * @param   string  $name   Resource class name
     * @return  object
     */
    public function getResource($name)
    {
        $name = "Resource_{$name}";
        $hasPool = Instagram_Factory::has($name);
        $obj = Instagram_Factory::build($name, array($this) /* $args */);
        
        // If is a new built class, check if it extends Instagram_Resource_Abstract
        if ((!$hasPool) && (!is_subclass_of($obj, 'Instagram_Resource_Abstract'))) {
            throw new Exception("{$class} must be a subclass of Instagram_Resource_Abstract.");
        }
        
        return $obj;
    }   

    /**
     * Sets the current Access Token to be used
     *
     * @param   string  $accessToken    Access Token
     * @return  this
     */
    public function setAccessToken($accessToken)
    {
        $this->__accessToken = trim($accessToken);
        return $this;
    }
    
    /**
     * Gets the current Access Token
     *
     * @return  string
     */
    public function getAccessToken()
    {
        return $this->__accessToken;
    }
    
    /**
     * Sets current user
     *
     * @param   string|int  $user   Username or user ID
     * @return  this
     */
    public function getUserId($user)
    {
        return (int) $this->getResource('User')->getIdByUser($user);
    }
    
    /**
     * Sets cURL configuration
     *
     * @param   array   $arrConfig  Configuration to be used
     * @return  this
     */
    public function setCurlConfig(array $arrConfig)
    {
        $this->__arrCurlConfig = array(
            CURLOPT_RETURNTRANSFER => true
        ) + $arrConfig;
        return $this;
    }
    
    /**
     * Gets cURL current configuration
     *
     * @return  array
     */
    public function getCurlConfig()
    {
        return $this->__arrCurlConfig;
    }
    
}

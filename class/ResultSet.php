<?php
/**
 * Simple set of classes to make requests to the Instagram API
 * 
 * @author      Vinícius Campitelli <eu@viniciuscampitelli.com>
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
 * Class that handles data pagination
 *
 * @package     Instagram\Instagram_ResultSet
 * @see         Instagram\Resource\Instagram_Resource_Abstract  Resource abstract class
 */
class Instagram_ResultSet implements Iterator
{
    
    /**
     * Response data
     * @var     object
     */
    private $__response = array();
    
    /**
     * Object of Instagram_Resource_Abstract
     * @var     object
     */
    private $__resource = null;
    
    /**
     * Number of elements in response data
     * @var     int
     */
    private $__count = 0;
    
    /**
     * Current position of the iterator
     * @var int
     */
    private $__pos = 0;
    
    /**
     * Class constructor
     *
     * @throws  Exception           If either $response or $resource is not valid
     * @param   object  $response   An object containing the response of a request
     * @param   object  $resource   An Instagram_Resource_* object that performed the request
     * @return  void
     */
    public function __construct($response, $resource)
    {
        if (!is_subclass_of($resource, 'Instagram_Resource_Abstract')) {
            throw new Exception('Resource ' . get_class($resource) . ' must be a subclass of Instagram_Resource_Abstract.');
        }
        
        if (!is_object($response)) {
            throw new Exception('Instagram_ResultSet::fromResponse expects its first parameter to be a valid request response.');
        }
        
        $this->__resource = $resource;
        $this->__response = $response;
        
        if (!empty($response->data)) {
            $this->__count = count($response->data);
        }
    }

    /**
     * Gets the URL of next page of results if exists
     *
     * @return  boolean|string  False if there is no next page, or its URL otherwise
     */
    public function getNextPageUrl()
    {
        $response = $this->getResponse();
        if (empty($response->pagination->next_url)) {
            return false;
        }
        
        $arrUrl = parse_url($response->pagination->next_url); 
        if (empty($arrUrl)) {
            return false;
        }
        
        if (empty($arrUrl['query'])) {
            $arrQuery = array();
        } else {
            parse_str($arrUrl['query'], $arrQuery);
        }
        
        unset($arrQuery['access_token']);
        $arrUrl['query'] = http_build_query($arrQuery);
        
        return $this->getResource()->buildUrl($arrUrl);
    }
    
    /**
     * Gets the content of next page of results if exists
     *
     * @param   int $quantity               Quantity of results to fetch
     * @return  boolean|Instagram_ResultSet False if there is no next page, or a new Instagram_ResultSet otherwise
     */
    public function getNextPage($quantity = null)
    {
        $url = $this->getNextPageUrl();
        if (empty($url)) {
            return false;
        }
        
        return $this->getResource()->request($url, (int) $quantity);
    }

    /**
     * Resets data array position (used by Iterator)
     *
     * @return  this
     */
    public function rewind()
    {
        $this->__pos = 0;
        return $this;
    }

    /**
     * Returns the current element in data array(used by Iterator)
     *
     * @return  mixed
     */
    public function current()
    {
        return $this->getResponse()->data[$this->__pos];
    }

    /**
     * Returns current data array position (used by Iterator)
     *
     * @return  int
     */
    public function key()
    {
        return $this->__pos;
    }

    /**
     * Advances data array position (used by Iterator)
     *
     * @return  this
     */
    public function next()
    {
        ++$this->__pos;
        return $this;
    }

    /**
     * Checks of current data array position is valid (used by Iterator)
     *
     * @return  boolean
     */
    public function valid()
    {
        return isset($this->getResponse()->data[$this->__pos]);
    }
    
    /**
     * Returns entire response
     *
     * @return  object
     */
    public function getResponse()
    {
        return $this->__response;
    }
    
    /**
     * Returns the resource that performed the request
     *
     * @return  object  One resource that extends Instagram_Resource_Abstract
     */
    public function getResource()
    {
        return $this->__resource;
    }
    
    /**
     * Returns only the data part in current response
     *
     * @return  array
     */
    public function getData()
    {
        return $this->getResponse()->data;
    }
    
    /**
     * Returns the number of elements in response data
     *
     * @return  int
     */
    public function getCount()
    {
        return $this->__count;
    }

}

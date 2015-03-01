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
 * Resource class to query photos
 *
 * @package     Instagram\Resource\Instagram_Resource_User
 * @see         Instagram\Resource\Instagram_Resource_Abstract  Resource abstract class
 */
class Instagram_Resource_User extends Instagram_Resource_Abstract
{

    /**
     * If $user is a integer, returns its.
     * Otherwise, gets its ID.
     *
     * @param   string|int  $user   Username or user ID
     * @return  int
     */
    public function getIdByUser($user)
    {
        $userId = (int) $user;
        if (!$userId) {
            $userId = $this->_getIdFromUsername($user);
        }
        return $userId;
    }
    
    /**
     * Gets corresponding user ID from a username
     *
     * @param   string  $username   Desired username
     * @return  int|boolean         False if any error occurs, or an integer representing the user ID
     */
    protected function _getIdFromUsername($username)
    {
        $username = urlencode($username);
        
        // Makes the request
        $result = $this->request("https://api.instagram.com/v1/users/search?q={$username}");
        if (empty($result)) {
            return false;
        }
        
        if (!$result->getCount()) {
            return false;
        }
        
        return (int) $result->getData()[0]->id;
    }
    
}

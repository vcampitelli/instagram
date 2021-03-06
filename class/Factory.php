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
 * Factory class to build other objects
 * 
 * @package     Instagram\Instagram_Factory
 */
class Instagram_Factory
{    

    /**
     * Pool of resources
     * @static
     * @var array
     */
    private static $__arrPool = array();
    
    /**
     * Builds an object
     *
     * @throw   Exception       Whenever an error occurs
     * @static
     * @param   string  $name   Class name
     * @param   array   $args   Class constructor arguments (optional)
     * @return  object
     */
    public static function build($name, array $args = array())
    {
        // Normalize names
        $name = self::__normalize($name);
        $class = 'Instagram_' . strtr($name, ' ', '_');
        
        // Check if object exists in pool
        if (!self::has($name)) {
            // Including file
            if (!class_exists($class)) {
                $file = __DIR__ . '/' . str_replace(' ', '/', $name) . '.php';
                if (is_file($file)) {
                    require $file;
                    if (!class_exists($class)) {
                        throw new Exception("Class {$class} doesn't exist.");
                    }
                } else {
                    throw new Exception("File {$file} doesn't exist.");
                }
            }
            
            // Check if class extends Instagram_Resource_Abstract
            if ((!empty($mainClass)) && (!is_subclass_of($class, $mainClass))) {
                throw new Exception("{$class} must be a subclass of {$mainClass}.");
            }

            // Instantiate object
            if (empty($args)) {
                $instance = new $class();
            } else {
                $obj = new ReflectionClass($class);
                $instance = $obj->newInstanceArgs($args);
            }
            self::$__arrPool[$name] = $instance;
        }
        
        return self::$__arrPool[$name];
    }
    
    /**
     * Checks if exists an instance of specified class in pool
     *
     * @static
     * @param   string  $class  Class name
     * @return  boolean
     */
    public static function has($class)
    {
        return isset(self::$__arrPool[self::__normalize($class)]);
    }
    
    /**
     * Normalizes a class name
     *
     * @static
     * @param   string  $class  Class name
     * @return  string
     */
    private static function __normalize($class)
    {
        return ucwords(strtr(strtolower($class), '_', ' '));
    }
}

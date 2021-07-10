<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Emblaze\Container;

use Closure;
use ReflectionClass;

class Container
{
    /**
     * This will hold the list of App containers
     *
     * @var array $containers
     */
    protected $containers = [];

    /**
     * This will hold the list of Instances of Class/Object
     *
     * @var array
     */
    protected $instances = [];
   
    /**
     * Add CLASS|OBJECT to Container
     *
     * @param mixed $name
     * @param mixed $value
     * @param bool $singleton
     * @return void
     */
    public function add($name,$value,$singleton)
    {
        $this->containers[$name] = [
            'value' => $value,
            'singleton' => $singleton
        ];
    }

    /**
     * Get the data from containers
     *
     * @param mixed $name
     * @return mixed
     */
    public function get($name) {
        return $this->containers[$name] ?? null;
    }

    /**
     * Bind a singleton CLASS|OBJECT
     *
     * @param mixed $class
     * @param mixed $value
     * @return void
     */
    public function singleton($class,$value = null)
    {
        $this->bind($class,$value,true);
    }
    
    /**
     * Bind CLASS|OBJECT to our App
     *
     * @param mixed $class
     * @param mixed $value
     * @param boolean $singleton
     * @return void
     */
    public function bind($class, $value = null, $singleton = false)
    {
        if($value === null) {
            $value = $class;
        }
        
        // check if class is already exists
        if($this->get($class)) {
            throw new \Exception('Can\'t add again. This class '.$class.' is already exists in containers');
        }
        
        $this->add($class, $value, $singleton);
    }

    /**
     * Check if Singleton
     *
     * @param mixed $class
     * @return boolean
     * @return mixed
     */
    protected function isSingleton($class) {
        $class = $this->get($class);

        if($class == null) {
            return false;
        }

        return $class['singleton'];
    }

    /**
     * Check if the singleton resolved
     *
     * @param mixed $class
     * @return mixed
     */
    public function singletonResolved($class)
    {
        return array_key_exists($class, $this->instances);
    }

    /**
     * Get singleton Instance
     *
     * @param mixed $class
     * @return mixed
     */
    public function getSingletonInstance($class)
    {
        return $this->singletonResolved($class) ? $this->instances[$class] : null;
    }

    /**
     * Resolve the CLASS|OBJECT
     *
     * @param mixed $class
     * @param array $args
     * @return mixed
     */
    public function resolve($class, $args = [])
    {
        if(!$this->get($class)) {
            throw new \Exception('This '.$class.' is not yet bind, please bind it.');
        }
         
        if($this->isSingleton($class) && $this->singletonResolved($class)) {
            return $this->getSingletonInstance($class);
        }

        // get the value into containers
        $value = $this->containers[$class]['value'];

        
        // if the value is Closure/Function then call it.
        if($value instanceof Closure) {
            return $this->prepareObject($class, call_user_func($value, $args));
        }
        
        // Check that the class exists before trying to use it
        if (!class_exists($value)) {
            throw new \Exception('This '.$class.' is not exists.');
        }

        // Create a reflection of the class
        $reflector = new ReflectionClass($class);

        if(!$reflector->isInstantiable()) {
            throw new \Exception('Class '.$class.' is not instantiable.');
        }

        // get the __construct 
        $constructor = $reflector->getConstructor();

        // if __construct is null
        if (is_null($constructor)) {
            return $reflector->newInstanceArgs($args);
        }
        
        // __construct(parameters)
        $parameters = $constructor->getParameters();

        $dependencies = $this->buildDependencies($parameters) ?? [];

        $instance = $reflector->newInstanceArgs($dependencies);
        
        return $this->prepareObject($class,$instance);
    }


    /**
     * Prepare new instance of CLASS|OBJECT
     *
     * @param mixed $class
     * @param mixed $instance
     * @return mixed
     */
    protected function prepareObject($class, $instance = null)
    {
        if($this->isSingleton($class)) {
            $this->instances[$class] = $instance;
        }

        return $instance;
    }

    /**
     * Build the Dependencies
     *
     * @param array $parameters
     * @return mixed
     */
    protected function buildDependencies($parameters)
    {

        $dependencies = [];
        foreach($parameters as $dependency) {
            $type = $dependency->getType();

            $class = $type->getName();
            
            // if the dependency is not a CLASS
            if(!class_exists($class)) {
                if ($dependency->isDefaultValueAvailable()) {
                    $dependencies[] = $dependency->getDefaultValue();
                    continue;
                } else {
                    throw new \Exception("Can not be resolve class dependency {$dependency->name}");
                }
            }
            
            // if possible we need to resolve the class dependency

            // Check if the $class is not yet bound.
            if(!$this->get($class)) {

                // throw new \Exception('This '.$class.' is not yet bind, please bind it.');

                // lets try it to create a new instance. OR lets bind it soon in the `future features`.???
                // $dependencies[] = new $class;

                // check first if the class dependency has a __construct and required parameters.
                // Create a reflection of the class
                $reflector = new ReflectionClass($class);
                
                if(!$reflector->isInstantiable()) {
                    throw new \Exception('Can not be resolve class dependency because the dependency class '.$class.' is not instantiable.');
                }
                
                // get the __construct 
                $constructor = $reflector->getConstructor();

                // if __construct is null
                if (is_null($constructor)) {
                    $dependencies[] = $reflector->newInstance();
                    continue;
                }

                $parameters = $constructor->getParameters();

                // check the count of class dependency $parameters count
                if(count($parameters) > 0) {
                    throw new \Exception("Can't be resolve dependency class {$class} because it has required parameters.");
                } else {
                    
                    $dependencies[] = $reflector->newInstance();
                    continue;
                }
                
            }
        
            // if the class is already added to containers (bind/singleton),
            // then we can resolved that BUT right now only if the value is Closure/Function
            $classBound = $this->get($class);
            if(($classBound['value'] ?? null) instanceof Closure) {
                $dependencies[] = call_user_func($classBound['value']);
            } else {
                throw new \Exception("Complicated!!! - Can't be resolve dependency class {$class}.");
            }

        }
        
        return $dependencies;
    }
}
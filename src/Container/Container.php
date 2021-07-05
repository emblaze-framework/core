<?php

namespace Emblaze\Container;

use ReflectionClass;

/**
 * Notes: The main purpose of Container is to resolved the OBJECT,
 * in other words to Instantiate a CLASS
 * 
 * The way the container works is we are going to allowed to bind objects into our container
 */
class Container
{   
    public static $instance;
     /**
     * Bindings Object
     *
     * @var array
     */
    protected $bindings = [];

    /**
     * OBJECT/CLASS instance holder.
     *
     * @var array
     */
    protected $instances = [];
    
    public function __construct() {}

     /**
     * Get the instance of the class ($this)Container
     */
    public static function getInstance()
    {
        if(!self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Set/Bind Object
     *
     * @param string $key
     * @param Class|Object $value
     * @param boolean $singleton
     * @return void
     */
    public function bind($key, $value, $singleton = false)
    {
        // set the binding $key to its value, and if singleton or not.
        $this->bindings[$key] = array(
            'value' => $value, 
            'singleton' => $singleton
        );
        
    }

    /**
     * This is like Set/Bind Object, BUT it's singleton OBJECT/CLASS
     *
     * @param string $key
     * @param Class|Object $value
     * @return mixed
     */
    public function singleton($key, $value)
    {
        return $this->bind($key,$value,true);
    }

    /**
     * Get Object
     *
     * @param string $key
     * @return void
     */
    public function getBinding($key)
    {
       
        // if the $key are not exists in our bindings array:
        if(!array_key_exists($key,$this->bindings)){
            return null;
        }
      
        // return the object with that $key
        return $this->bindings[$key];
    }


    /**
     * Check if the class/object is singleton
     *
     * @param string $key
     * @return boolean
     */
    public function isSingleton($key)
    {
        $binding = $this->getBinding($key);

        if($binding == null) {
            return false;
        }

        return $binding['singleton'];
    }


    /**
     * Check if the Object/Class is already instantiated/Resolved.
     *
     * @param string $key
     * @return bool
     */
    public function singletonResolved($key)
    {
        return array_key_exists($key,$this->instances);
    }

    /**
     * Get Class/Object Singleton Instance from $instances using $key
     *
     * @param string $key
     * @return mixed
     */
    public function getSingletonInstance($key)
    {
        return $this->singletonResolved($key) ? $this->instances[$key] : null;
    }


    /**
     * Instatntiate or Resolved the OBJECT/CLASS
     *
     * @param string $key
     * @param array $args
     * @return void
     */
    public function resolve($key, array $args = [])
    {
        $class = $this->getBinding($key);
        
        // if the class is null then set the class to the value of $key
        if($class === null) {
            $class = $key;
        }

        
        if($this->isSingleton($key) && $this->singletonResolved($key)) {
            return $this->getSingletonInstance($key);
        }

        
        $object = $this->buildObject($class, $args);

        return $this->prepareObject($key,$object);
    }

    /**
     * prepare the object if singleton or not.
     *
     * @param string $key
     * @param Class|Object $object
     * @return void
     */
    protected function prepareObject($key, $object = null)
    {
        if($this->isSingleton($key)) {
            $this->instances[$key] = $object;
        }

        return $object;
    }


    // Build the class/create a reflection of the class and,
    // check whether the class has a __constructor(args/dependencies)
    // if there is a __constructor in the class the we're going to go through all of 
    // the __constructor args/dependencies and resolved each one of them recurvesively
    /**
     *  Build the class/create a reflection of the class
     *
     * @param string $className
     * @param array $args
     * @return void
     */
    protected function buildObject($className, array $args = array())
    {
        $className = $className['value'];

        // https://www.php.net/manual/en/class.reflectionclass.php
        $reflectionClass = new ReflectionClass($className);

        // dump($reflectionClass);

        // Check if the $className is not instantiable
        if(!$reflectionClass->isInstantiable()) {
            throw new \Exception("Class [$className] is not a resolvable dependency");
        }


        // if the $clasName has a __constructor()
        if($reflectionClass->getConstructor() !== null) {

            $constructor = $reflectionClass->getConstructor(); //-> $className __constructor(args/dependencies)

            $dependencies = $constructor->getParameters(); //-> args/dependencies
    
            $args = $this->buildDependencies($args, $dependencies);
            
        }

       
        // dump($args);
        
        // dump($reflectionClass);

        $object = $reflectionClass->newInstanceArgs($args);

        
        return $object;
    }

    protected function buildDependencies($args, $dependencies = [])
    {
        // Loop through OBJECT/CLASS dependencies
        foreach($dependencies as $dependency) {

            $reflectionParameterType = $dependency->getType();

            // dependency type is array
            if($reflectionParameterType->getName() === 'array') {
                array_push($args,$dependency->getDefaultValue());
                continue;
            }

            // dependency type is string
            if($reflectionParameterType->getName() === 'string') {
                array_push($args,$dependency->getDefaultValue());
                continue;
            }

             // Check if the dependency is optional then continue;
            if($dependency->isOptional()) continue;
           
            
            // if the dependency is not CLASS
            if(!class_exists($reflectionParameterType->getName())) {
                continue;
            }

        

            //  if the dependency is CLASS
            if(class_exists($reflectionParameterType->getName())) {
                
                // $class = $reflectionParameterType->getName();
                
                // array_push($args, new $class);
                // $class = $dependency->getClass();

                // vd($class->name);
                // vd($reflectionParameterType->getName());
                // die();
                $class = $reflectionParameterType->getName();

                array_push($args, new $class);
                continue;

            }
            
            // array_push($args, $this->resolve($dependency->name));
           
            // if(get_class($this) === $reflectionParameterType->getName()) {
            //     // array_unshift($args,$this);
            //     array_unshift($args,$this);
            //     continue;
            // }

            // array_unshift($args,$this->resolve($dependency->name));
            array_push($args,$this->resolve(ucfirst($dependency->name)));
            
        } //end foreach

        return $args;
    }
}
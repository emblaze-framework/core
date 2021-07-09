<?php
/*
 * This file is part of the EmblazeCore library.
 *
 * (c) Rey Mark Divino <contact@reymarkdivino.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
// namespace Emblaze\Container;

// use Closure;
// use ReflectionClass;

// class Container
// {
//     protected $bindings = [];
//     protected $instances = [];

//     public function bind($abstract, $concrete = null, $singleton = false)
//     {
//         $this->bindings[$abstract] = array(
//             'concrete' => $concrete === null ? null : $abstract, 
//             'singleton' => $singleton
//         );
//     }

   

//     public function get($abstract, $parameters = array())
//     {
//         if (!isset($this->bindings[$abstract])) {
//             return null;
//         }

//         if($this->bindings[$abstract]['singleton']) {
//             return $this->bindings[$abstract]['concrete'];
//         }


//         return $this->build($this->bindings[$abstract]['concrete'], $parameters);
//     }
    
//     public function singleton($abstract, $concrete)
//     {
//         return $this->bind($abstract,$concrete,true);
//     }

    

//     public function build($concrete, $parameters)
//     {
//         // if ($concrete instanceof Closure) {
//         //     return $concrete($this, $parameters);
//         // }
//         if ($concrete instanceof Closure) {
//             return $concrete($this, $parameters);
//         }

//         $reflector = new ReflectionClass($concrete);

//         if (!$reflector->isInstantiable()) {
//             throw new \Exception("Class {$concrete} is not instantiable");
//         }

//         $constructor = $reflector->getConstructor();

//         if (is_null($constructor)) {
//             return $reflector->newInstance();
//         }

//         $parameters = $constructor->getParameters();
        
//         $dependencies = $this->getDependencies($parameters);

//         dump($dependencies);

//         $instance = $reflector->newInstanceArgs($dependencies);
        
//         if($concrete['singleton']) {
//             $this->instances[$concrete] = $instance;    
//         }

//         return $instance;
//     }

//     public function getDependencies($parameters)
//     {
//         $dependencies = array();
//         foreach ($parameters as $parameter) {
            
//             $dependencyType = $parameter->getType();
            
//             // $dependency = $parameter->getClass();
            
//             // $dependency = $dependencyType->getName();
           

//             // if ($dependency === null) {
//             if ($dependency = $dependencyType->getName()) {
//                 if ($parameter->isDefaultValueAvailable()) {
//                     $dependencies[] = $parameter->getDefaultValue();
//                 } else {
//                     throw new \Exception("Can not be resolve class dependency {$parameter->name}");
//                 }
//             } else {
                
//                 // $dependencies[] = $this->get($dependency->name);
//                 $dependencies[] = $this->get($dependency);

//             }
//         }

//         return $dependencies;
//     }
// }
<?php
/**
 * This file is part of the Injector component.
 * (c) 2016 Catalyst Collective, All rights reserved.
 *
 * The applied license is stored at the root directory of this package.
 */

namespace Catalyst\Injector;

use Catalyst\Injector\Shared\MethodIncubatorTrait;
use Catalyst\Injector\Shared\PropertyIncubatorTrait;
use LogicException;
use ReflectionClass;

/**
 * Class InjectorFactory
 *
 * @package catalyst.injector
 * @author Matthias Kaschubowski <nihylum@gmail.com>
 * @version 1.0
 */
class InjectorFactory
{
    use PropertyIncubatorTrait;
    use MethodIncubatorTrait;

    /**
     * Object-based cache for class-based properties
     *
     * @var array[]
     */
    protected $propertyCache = [];

    /**
     * Object-based cache for class-based methods
     *
     * @var array[]
     */
    protected $methodCache = [];

    /**
     * factorization method.
     *
     * @param object $target
     * @param bool $allowUndeclaredProperties
     * @throws LogicException if the target is not an object or the target is an closure object
     * @return Injector
     */
    public function factorize($target, $allowUndeclaredProperties = true)
    {
        if ( ! is_object($target) ) {
            throw new LogicException(
                'target parameter must be an object'
            );
        }

        if ( $target instanceof \Closure ) {
            throw new LogicException(
                'target parameter can not be an closure object'
            );
        }

        $allowUndeclaredProperties = (bool) $allowUndeclaredProperties;
        $properties = new ScopeCollection($this->marshalProperties($target));
        $methods = new ScopeCollection($this->marshalMethods($target));

        return new Injector($target, $methods, $properties, $allowUndeclaredProperties);
    }

    /**
     * Factory method implementation.
     *
     * @param object $object
     * @return mixed
     */
    public static function createFrom($object)
    {
        return (new static)->factorize($object);
    }

    /**
     * marshals properties from and into the cache for the given object.
     *
     * @param object $target
     * @return string[]
     */
    protected function marshalProperties($target)
    {
        $className = get_class($target);

        if ( ! array_key_exists($className, $this->propertyCache) ) {
            $classReflection = new ReflectionClass($target);
            $this->propertyCache[$className] = $this->fetchObjectProperties($classReflection);
        }

        return $this->propertyCache[$className];
    }

    /**
     * marshals methods from and into the cache for the given object.
     *
     * @param $target
     * @return string[]
     */
    protected function marshalMethods($target)
    {
        $className = get_class($target);

        if ( ! array_key_exists($className, $this->methodCache) ) {
            $classReflection = new ReflectionClass($target);
            $this->methodCache[$className] = $this->fetchObjectMethods($classReflection);
        }

        return $this->methodCache[$className];
    }
}
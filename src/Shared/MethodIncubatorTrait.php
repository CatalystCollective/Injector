<?php
/**
 * This file is part of the Injector component.
 * (c) 2016 Catalyst Collective, All rights reserved.
 *
 * The applied license is stored at the root directory of this package.
 */

namespace Catalyst\Injector\Shared;

use ReflectionClass;
use ReflectionMethod;

/**
 * Trait MethodIncubatorTrait
 *
 * @package catalyst.injector
 * @author Matthias Kaschubowski <nihylum@gmail.com>
 * @version 1.0
 */
trait MethodIncubatorTrait
{
    /**
     * fetches the methods of a class reflection
     *
     * @param ReflectionClass $reflection
     * @return string[]
     */
    protected function fetchObjectMethods(ReflectionClass $reflection)
    {
        $methods = [];
        $filter = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE;

        foreach ( $reflection->getMethods($filter) as $name => $method ) {
            /** @var ReflectionMethod $property */
            $methods[$name] = $method->isPrivate()
                ? $method->getDeclaringClass()
                : $reflection->getName()
            ;
        }

        if ( $parent = $reflection->getParentClass() ) {
            $parentMethods = $this->fetchObjectMethods($parent);

            if ( ! empty($parentMethods) ) {
                foreach ( $parentMethods as $name => $className ) {
                    if ( ! array_key_exists($name, $methods) ) {
                        $methods[$name] = $className;
                    }
                }
            }
        }

        return $methods;
    }
}
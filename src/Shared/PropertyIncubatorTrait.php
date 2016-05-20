<?php
/**
 * This file is part of the Injector component.
 * (c) 2016 Catalyst Collective, All rights reserved.
 *
 * The applied license is stored at the root directory of this package.
 */

namespace Catalyst\Injector\Shared;


use ReflectionClass;
use ReflectionProperty;

/**
 * Trait PropertyIncubatorTrait
 *
 * @package catalyst.injector
 * @author Matthias Kaschubowski <nihylum@gmail.com>
 * @version 1.0
 */
trait PropertyIncubatorTrait
{
    /**
     * fetches the properties of a class reflection
     *
     * @param ReflectionClass $reflection
     * @return string[]
     */
    protected function fetchObjectProperties(ReflectionClass $reflection)
    {
        $properties = [];
        $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE;

        foreach ( $reflection->getProperties($filter) as $name => $property ) {
            /** @var ReflectionProperty $property */
            $properties[$name] = $property->isPrivate()
                ? $property->getDeclaringClass()
                : $reflection->getName()
            ;
        }

        if ( $parent = $reflection->getParentClass() ) {
            $parentProperties = array_merge($properties, $this->fetchObjectProperties($parent));

            if ( ! empty($parentProperties) ) {
                foreach ( $parentProperties as $name => $className ) {
                    if ( ! array_key_exists($name, $properties) ) {
                        $properties[$name] = $className;
                    }
                }
            }
        }

        return $properties;
    }
}
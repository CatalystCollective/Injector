<?php
/**
 * This file is part of the Injector component.
 * (c) 2016 Catalyst Collective, All rights reserved.
 *
 * The applied license is stored at the root directory of this package.
 */

namespace Catalyst\Injector;


use IteratorAggregate;
use Closure;
use LogicException;
use Catalyst\Injector\ScopeCollection as Scopes;
use Traversable;

/**
 * Class Injector
 *
 * @package catalyst.injector
 * @author Matthias Kaschubowski <nihylum@gmail.com>
 * @version 1.0
 */
class Injector implements IteratorAggregate
{
    /**
     * Target object to work on
     *
     * @var object
     */
    protected $target;

    /**
     * Collection of Scopes to use for methods
     *
     * @var ScopeCollection|null
     */
    protected $methodScopes;

    /**
     * Collection of Scopes to use for properties
     *
     * @var ScopeCollection
     */
    protected $propertyScopes;

    /**
     * decides whether a undeclared parameter can be setted or not.
     *
     * @var bool
     */
    protected $allowUndeclared;

    /**
     * Injector constructor.
     *
     * @api 1.0
     *
     * @param $target
     * @param Scopes|null $methods
     * @param Scopes|null $properties
     * @param bool $allowUndeclared
     * @throws LogicException if target parameter was not an object or is an instance of \Closure
     */
    public function __construct($target, Scopes $methods = null, Scopes $properties = null, $allowUndeclared = true)
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

        $this->target = $target;
        $this->methodScopes = $methods;
        $this->propertyScopes = $properties;
        $this->allowUndeclared = (bool) $allowUndeclared;
    }

    /**
     * magic getter for properties
     *
     * @api 1.0
     *
     * @param $property
     * @throws LogicException if the property does not exists
     * @return mixed
     */
    public function __get($property)
    {
        $accessor = Closure::bind(
            function($property) {
                if ( ! property_exists($this, $property) ) {
                    throw new LogicException(
                        sprintf(
                            'unknown property: %s',
                            $property
                        )
                    );
                }

                return $this->{$property};
            },
            $this->target,
            $this->propertyScopes instanceof Scopes
                ? $this->propertyScopes->get($property, $this->target)
                : $this->target
        );

        try {
            return $accessor($property);
        }
        catch ( LogicException $exception ) {
            throw $exception;
        }
    }

    /**
     * magic setter for properties
     *
     * @api 1.0
     *
     * @param $property
     * @param $newValue
     * @throws LogicException if the property does not exists
     */
    public function __set($property, $newValue)
    {
        $undeclared = $this->allowUndeclared;

        $accessor = Closure::bind(
            function($property, $newValue) use ($undeclared) {
                if ( ! property_exists($this, $property) && ! $undeclared ) {
                    throw new LogicException(
                        sprintf(
                            'unknown property: %s',
                            $property
                        )
                    );
                }

                $this->{$property} = $newValue;
            },
            $this->target,
            $this->propertyScopes instanceof Scopes
                ? $this->propertyScopes->get($property, $this->target)
                : $this->target
        );

        try {
            $accessor($property, $newValue);
        }
        catch ( LogicException $exception ) {
            throw $exception;
        }
    }

    /**
     * detects whether a property exists or not.
     *
     * @api 1.0
     *
     * @param $property
     * @return mixed
     */
    public function __isset($property)
    {
        $accessor = Closure::bind(
            function($property) {
                return property_exists($this, $property);
            },
            $this->target,
            $this->propertyScopes instanceof Scopes
                ? $this->propertyScopes->get($property, $this->target)
                : $this->target
        );

        return $accessor($property);
    }

    /**
     * magically unsets a property
     *
     * @api 1.0
     *
     * @param $property
     */
    public function __unset($property)
    {
        $accessor = Closure::bind(
            function($property) {
                unset($this->{$property});
            },
            $this->target,
            $this->propertyScopes instanceof Scopes
                ? $this->propertyScopes->get($property, $this->target)
                : $this->target
        );

        $accessor($property);
    }

    /**
     * magic caller for a method
     *
     * @api 1.0
     *
     * @param $method
     * @param array $args
     * @throws LogicException if the method does not exists
     * @return mixed
     */
    public function __call($method, array $args)
    {
        $accessor = Closure::bind(
            function($method, array $args) {
                if ( ! method_exists($this, $method) ) {
                    throw new LogicException(
                        sprintf(
                            'unknown method: %s',
                            $method
                        )
                    );
                }

                return call_user_func_array([$this, $method], $args);
            },
            $this->target,
            $this->methodScopes instanceof Scopes
                ? $this->methodScopes->get($method, $this->target)
                : $this->target
        );

        try {
            return $accessor($method, $args);
        }
        catch ( LogicException $exception ) {
            throw $exception;
        }
    }

    /**
     * returns the injector target
     *
     * @return object
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new PropertyIterator($this);
    }
}
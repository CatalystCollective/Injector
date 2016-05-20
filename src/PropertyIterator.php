<?php
/**
 * This file is part of the Injector component.
 * (c) 2016 Catalyst Collective, All rights reserved.
 *
 * The applied license is stored at the root directory of this package.
 */

namespace Catalyst\Injector;


use Catalyst\Injector\Shared\PropertyIncubatorTrait;
use Iterator;
use ReflectionClass;

/**
 * Class PropertyIterator
 *
 * @package catalyst.iterator
 * @author Matthias Kaschubowski <nihylum@gmail.com>
 * @version 1.0
 */
class PropertyIterator implements \Iterator
{
    use PropertyIncubatorTrait;

    /**
     * Injector to iterate over
     *
     * @var Injector
     */
    protected $injector;

    /**
     * properties
     *
     * @var array
     */
    protected $keys;

    /**
     * current position
     *
     * @var int
     */
    protected $position = 0;

    /**
     * PropertyIterator constructor.
     *
     * @api 1.0
     *
     * @param Injector $injector
     */
    public function __construct(Injector $injector)
    {
        $this->injector = $injector;
        $this->keys = array_keys($this->fetchObjectProperties(new ReflectionClass($injector->getTarget())));
    }

    /**
     * Return the current element
     *
     * @api 1.0
     *
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->injector->{$this->keys[$this->position]};
    }

    /**
     * Move forward to next element
     *
     * @api 1.0
     *
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * Return the key of the current element
     *
     * @api 1.0
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->keys[$this->position];
    }

    /**
     * Checks if current position is valid
     *
     * @api 1.0
     *
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return array_key_exists($this->position, $this->keys);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @api 1.0
     *
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->position = 0;
    }
}
<?php
/**
 * This file is part of the Injector component.
 * (c) 2016 Catalyst Collective, All rights reserved.
 *
 * The applied license is stored at the root directory of this package.
 */

namespace Catalyst\Injector;


/**
 * Class ScopeCollection
 *
 * @package catalyst.injector
 * @author Matthias Kaschubowski <nihylum@gmail.com>
 * @version 1.0
 */
class ScopeCollection
{
    /**
     * Collection of items associated with their scope class names.
     *
     * @var string[]
     */
    protected $items = [];

    /**
     * ScopeCollection constructor.
     *
     * @param array $itemsAndScopes
     */
    public function __construct(array $itemsAndScopes)
    {
        foreach ( $itemsAndScopes as $item => $scope ) {
            $this->set($item, $scope);
        }
    }

    /**
     * sets a item's class name.
     *
     * @api 1.0
     *
     * @param string $item
     * @param string $className
     */
    public function set($item, $className)
    {
        $this->items[(string) $item] = (string) $className;
    }

    /**
     * gets a item's class name. If the item is not known $default will be returned.
     *
     * @api 1.0
     *
     * @param string $item
     * @param object|string|null $default
     * @return string|object|null
     */
    public function get($item, $default = null)
    {
        if ( ! array_key_exists((string) $item, $this->items) ) {
            return $default;
        }

        return $this->items[$item];
    }
}
<?php
/**
 * This file is part of the vcard package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Event;

/**
 * Class ResponseEvent
 * @package Vardius\Bundle\CrudBundle\Event
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ResponseEvent
{
    /** @var  array */
    protected $params;

    /**
     * ResponseEvent constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return ResponseEvent
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

}
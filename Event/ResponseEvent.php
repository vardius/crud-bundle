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
     * @param mixed $params
     */
    public function __construct($params = [])
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param $params
     * @return ResponseEvent
     */
    public function setParams($params):self
    {
        $this->params = $params;
        return $this;
    }
}

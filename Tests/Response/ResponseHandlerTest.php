<?php
/**
 * This file is part of the vardius/crud-bundle package.
 *
 * (c) Rafał Lorenz <vardius@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Vardius\Bundle\CrudBundle\Tests\Response;

use Vardius\Bundle\CrudBundle\Response\ResponseHandler;

/**
 * Class ResponseHandlerTest
 * @package Vardius\Bundle\CrudBundle\Tests\Response
 * @author Rafał Lorenz <vardius@gmail.com>
 */
class ResponseHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $templating;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $serializer;

    /**
     * @inheritDoc
     */
    protected function setUp()
    {
        $this->templating = $this
            ->getMockBuilder('Symfony\Bridge\Twig\TwigEngine')
            ->disableOriginalConstructor()
            ->getMock();

        $this->serializer = $this
            ->getMockBuilder('JMS\Serializer\Serializer')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetHtmlCaseOne()
    {
        $this->templating
            ->expects($this->once())
            ->method('exists')
            ->with('show')
            ->willReturn(true);

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->with('show')
            ->willReturn(true);

        $handler = new ResponseHandler($this->templating, $this->serializer);

        $html = $handler->getHtml('view', 'show', []);

        $this->assertEquals(true, $html);
    }

    public function testGetHtmlCaseTwo()
    {
        $this->templating->expects($this->at(0))
            ->method('exists')
            ->with($this->equalTo('show'))
            ->will($this->returnValue(false));

        $this->templating->expects($this->at(1))
            ->method('exists')
            ->with($this->equalTo('show.html.twig'))
            ->will($this->returnValue(true));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->will($this->returnCallback(function ($template, $params) {
                \PHPUnit_Framework_Assert::assertEquals('show.html.twig', $template);
            }));

        $handler = new ResponseHandler($this->templating, $this->serializer);
        $handler->getHtml('view', 'show', []);
    }

    public function testGetHtmlCaseThree()
    {
        $this->templating->expects($this->at(0))
            ->method('exists')
            ->with($this->equalTo('show'))
            ->will($this->returnValue(false));

        $this->templating->expects($this->at(1))
            ->method('exists')
            ->with($this->equalTo('show.html.twig'))
            ->will($this->returnValue(false));

        $this->templating->expects($this->at(2))
            ->method('exists')
            ->with($this->equalTo('viewshow.html.twig'))
            ->will($this->returnValue(true));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->will($this->returnCallback(function ($template, $params) {
                \PHPUnit_Framework_Assert::assertEquals('viewshow.html.twig', $template);
            }));

        $handler = new ResponseHandler($this->templating, $this->serializer);
        $handler->getHtml('view', 'show', []);
    }

    public function testGetHtmlCaseFour()
    {
        $this->templating->expects($this->at(0))
            ->method('exists')
            ->with($this->equalTo('show'))
            ->will($this->returnValue(false));

        $this->templating->expects($this->at(1))
            ->method('exists')
            ->with($this->equalTo('show.html.twig'))
            ->will($this->returnValue(false));

        $this->templating->expects($this->at(2))
            ->method('exists')
            ->with($this->equalTo('viewshow.html.twig'))
            ->will($this->returnValue(false));

        $this->templating->expects($this->at(3))
            ->method('exists')
            ->with($this->equalTo('VardiusCrudBundle:Actions:show.html.twig'))
            ->will($this->returnValue(true));

        $this->templating
            ->expects($this->once())
            ->method('render')
            ->will($this->returnCallback(function ($template, $params) {
                \PHPUnit_Framework_Assert::assertEquals('VardiusCrudBundle:Actions:show.html.twig', $template);
            }));

        $handler = new ResponseHandler($this->templating, $this->serializer);
        $handler->getHtml('view', 'show', []);
    }

    public function testGetHtmlException()
    {
        $this->templating
            ->expects($this->exactly(4))
            ->method('exists')
            ->willReturn(false);

        $handler = new ResponseHandler($this->templating, $this->serializer);

        $this->setExpectedException('Symfony\Component\Routing\Exception\ResourceNotFoundException');

        $handler->getHtml('view', 'show', []);
    }
}

<?php
namespace Dittto\CustomRequestBundle\Request;

use Symfony\Component\HttpFoundation\Request;

class AbstractRequestTypeTest extends \PHPUnit_Framework_TestCase
{
    private $testRequestType;

    public function setUp()
    {
        $this->testRequestType = new class extends AbstractRequestType {
            public function validate():bool {
                return false;
            }
        };
    }

    public function testSettingAndRetrievingRequest()
    {
        $testRequest = new Request();
        $newRequest = $this->testRequestType->setOriginalRequest($testRequest)->getOriginalRequest();
        $this->assertSame($testRequest, $newRequest);
    }

    public function testUsingChainingForSettingReturnsSame()
    {
        $testRequest = new Request();
        $chainedRequestType = $this->testRequestType->setOriginalRequest($testRequest);
        $this->assertEquals(get_class($this->testRequestType), get_class($chainedRequestType));
        $this->assertSame($this->testRequestType, $chainedRequestType);
    }
}

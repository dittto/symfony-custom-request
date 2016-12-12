<?php
namespace Dittto\CustomRequestBundle\Request\Filter;

use Dittto\CustomRequestBundle\Request\RequestTypeInterface;
use Symfony\Component\HttpFoundation\Request;

class ExceptionOnFailedGetRequestTest extends \PHPUnit_Framework_TestCase
{
    private $requestType;

    public function setUp()
    {
        $this->requestType = new class implements RequestTypeInterface  {
            private $request;
            private $validateResponse = false;

            public function setOriginalRequest(Request $request):RequestTypeInterface {
                $this->request = $request;
                return $this;
            }

            public function getOriginalRequest():?Request {
                return $this->request;
            }

            public function validate():bool {
                return $this->validateResponse;
            }

            public function setValidateResponse(bool $validateResponse):void {
                $this->validateResponse = $validateResponse;
            }
        };
    }

    public function testExceptionDoesntTriggerOnValid()
    {
        $request = new Request();
        $request->setMethod('GET');
        $this->requestType->setOriginalRequest($request);
        $this->requestType->setValidateResponse(true);

        $filter = new ExceptionOnFailedGetRequest();
        $this->assertTrue($filter->filterRequest($this->requestType, true));
    }

    public function testExceptionDoesntTriggerWhenNotGetRequest()
    {
        $request = new Request();
        $request->setMethod('POST');
        $this->requestType->setOriginalRequest($request);
        $this->requestType->setValidateResponse(true);

        $filter = new ExceptionOnFailedGetRequest();
        $this->assertTrue($filter->filterRequest($this->requestType, true));
        $this->assertFalse($filter->filterRequest($this->requestType, false));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionTriggersOnInvalidGet()
    {
        $request = new Request();
        $request->setMethod('GET');
        $this->requestType->setOriginalRequest($request);
        $this->requestType->setValidateResponse(true);

        $filter = new ExceptionOnFailedGetRequest();
        $filter->filterRequest($this->requestType, false);
    }
}

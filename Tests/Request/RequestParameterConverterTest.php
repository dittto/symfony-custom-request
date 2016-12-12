<?php
namespace Dittto\CustomRequestBundle\Request;

use Dittto\CustomRequestBundle\Request\Filter\RequestFilterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class RequestParameterConverterTest extends \PHPUnit_Framework_TestCase
{
    private $requestType;
    private $request;
    private $paramConverter;

    public function setUp()
    {
        $this->requestType = new class implements RequestTypeInterface {
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

        $this->request = new Request();
        $this->paramConverter = new ParamConverter(['name' => 'Bill', 'class' => get_class($this->requestType)]);
    }

    public function testApplyingParametersWhenValidationFails()
    {
        $this->requestType->setValidateResponse(false);

        $requestParamConverter = new RequestParameterConverter();
        $requestParamConverter->addSupportedClass($this->requestType);

        $this->assertFalse($requestParamConverter->apply($this->request, $this->paramConverter));
    }

    public function testApplyingParametersWhenValidationPasses()
    {
        $this->requestType->setValidateResponse(true);

        $requestParamConverter = new RequestParameterConverter();
        $requestParamConverter->addSupportedClass($this->requestType);

        $this->assertTrue($requestParamConverter->apply($this->request, $this->paramConverter));
    }

    public function testSupportedRequestTypes()
    {
        $requestParamConverter = new RequestParameterConverter();
        $requestParamConverter->addSupportedClass($this->requestType);
        $this->assertTrue($requestParamConverter->supports($this->paramConverter));
    }

    public function testUnsupportedRequestTypes()
    {
        $requestParamConverter = new RequestParameterConverter();
        $this->assertFalse($requestParamConverter->supports($this->paramConverter));
    }

    public function testSupportParamConverterMissingAClass()
    {
        $paramConverter = new ParamConverter(['name' => 'Bill']);
        $requestParamConverter = new RequestParameterConverter();
        $this->assertFalse($requestParamConverter->supports($paramConverter));
    }

    public function testAddingASlottedFilter()
    {
        $testFilter = new class implements RequestFilterInterface {
            public function filterRequest(RequestTypeInterface $request, bool $isValid):bool {
                return $isValid;
            }
        };

        $this->requestType->setValidateResponse(true);

        $requestParamConverter = new RequestParameterConverter();
        $requestParamConverter->addSupportedClass($this->requestType);
        $requestParamConverter->addFilter($testFilter, 10);
        $this->assertTrue($requestParamConverter->apply($this->request, $this->paramConverter));
    }

    public function testAddingAnUnslottedFilter()
    {
        $testFilter = new class implements RequestFilterInterface {
            public function filterRequest(RequestTypeInterface $request, bool $isValid):bool {
                return false;
            }
        };

        $this->requestType->setValidateResponse(true);

        $requestParamConverter = new RequestParameterConverter();
        $requestParamConverter->addSupportedClass($this->requestType);
        $requestParamConverter->addFilter($testFilter);
        $this->assertFalse($requestParamConverter->apply($this->request, $this->paramConverter));
    }

    public function testAddingMultipleFilters()
    {
        $passFilter = new class implements RequestFilterInterface {
            public function filterRequest(RequestTypeInterface $request, bool $isValid):bool {
                return true;
            }
        };
        $failFilter = new class implements RequestFilterInterface {
            public function filterRequest(RequestTypeInterface $request, bool $isValid):bool {
                return false;
            }
        };

        $this->requestType->setValidateResponse(true);

        $requestParamConverter = new RequestParameterConverter();
        $requestParamConverter->addSupportedClass($this->requestType);
        $requestParamConverter->addFilter($passFilter, 1);
        $requestParamConverter->addFilter($failFilter, 2);
        $this->assertFalse($requestParamConverter->apply($this->request, $this->paramConverter));
    }
}

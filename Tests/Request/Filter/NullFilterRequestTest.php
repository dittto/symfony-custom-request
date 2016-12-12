<?php
namespace Dittto\CustomRequestBundle\Request\Filter;

use Dittto\CustomRequestBundle\Request\RequestTypeInterface;
use Symfony\Component\HttpFoundation\Request;

class NullFilterRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testNullFilterJustPassesValidityThrough()
    {
        $requestType = new class implements RequestTypeInterface  {
            private $request;

            public function setOriginalRequest(Request $request):RequestTypeInterface {
                $this->request = $request;
                return $this;
            }

            public function getOriginalRequest():?Request {
                return $this->request;
            }

            public function validate():bool {
                return true;
            }
        };

        $filter = new NullFilterRequest();
        $this->assertTrue($filter->filterRequest($requestType, true));
        $this->assertFalse($filter->filterRequest($requestType, false));
    }
}

<?php
namespace Dittto\CustomRequestBundle\Request;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRequestType implements RequestTypeInterface
{
    private $request;

    public function setOriginalRequest(Request $request):RequestTypeInterface
    {
        $this->request = $request;

        return $this;
    }

    public function getOriginalRequest():?Request
    {
        return $this->request;
    }
}

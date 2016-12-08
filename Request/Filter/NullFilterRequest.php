<?php
namespace Dittto\CustomRequestBundle\Request\Filter;

use Dittto\CustomRequestBundle\Request\RequestTypeInterface;

class NullFilterRequest implements RequestFilterInterface
{
    public function filterRequest(RequestTypeInterface $request, bool $isValid):bool
    {
        return $isValid;
    }
}

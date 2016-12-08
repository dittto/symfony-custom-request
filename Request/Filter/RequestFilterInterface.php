<?php
namespace Dittto\CustomRequestBundle\Request\Filter;

use Dittto\CustomRequestBundle\Request\RequestTypeInterface;

interface RequestFilterInterface
{
    public function filterRequest(RequestTypeInterface $request, bool $isValid):bool;
}

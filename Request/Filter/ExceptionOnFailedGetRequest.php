<?php
namespace Dittto\CustomRequestBundle\Request\Filter;

use Dittto\CustomRequestBundle\Request\RequestTypeInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionOnFailedGetRequest implements RequestFilterInterface
{
    public function filterRequest(RequestTypeInterface $request, bool $isValid):bool
    {
        if ($request->getOriginalRequest()->isMethod('GET') && !$isValid) {
            throw new HttpException(400, 'Failed validation');
        }

        return $isValid;
    }
}

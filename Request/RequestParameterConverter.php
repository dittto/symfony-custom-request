<?php
namespace Dittto\CustomRequestBundle\Request;

use Dittto\CustomRequestBundle\Request\Filter\RequestFilterInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestParameterConverter implements ParamConverterInterface
{
    private $supportedTypes = [];

    /**
     * @var RequestFilterInterface[]
     */
    private $requestFilters = [];

    public function apply(Request $request, ParamConverter $configuration)
    {
        // get the request type
        $requestType = $this->getRequestType($configuration->getClass())
            ->setOriginalRequest($request);

        // re-store the request type against the request
        $request->attributes->set($configuration->getName(), $requestType);

        // validate the request type
        $isValid = $requestType->validate();

        return $this->filterRequests($requestType, $isValid);
    }

    public function supports(ParamConverter $configuration):bool
    {
        if ($configuration->getClass() === null) {
            return false;
        }

        return $this->hasRequestType($configuration->getClass());
    }

    public function addSupportedClass(RequestTypeInterface $classReference):void
    {
        $this->supportedTypes[get_class($classReference)] = $classReference;
    }

    public function addFilter(RequestFilterInterface $filter, int $slot = null):void
    {
        // if no slot set, push onto stack
        if ($slot === null) {
            array_push($this->requestFilters, $filter);
        }
        // if slot is set, don't reuse it
        if (!isset($this->requestFilters[$slot])) {
            $this->requestFilters[$slot] = $filter;
        }
    }

    private function getRequestType(string $className):RequestTypeInterface
    {
        return $this->supportedTypes[$className];
    }

    private function hasRequestType(string $className):bool
    {
        return isset($this->supportedTypes[$className]);
    }

    private function filterRequests(RequestTypeInterface $request, bool $isValid):bool
    {
        foreach ($this->requestFilters as $filter) {
            $isValid = $filter->filterRequest($request, $isValid);
        }

        return $isValid;
    }
}

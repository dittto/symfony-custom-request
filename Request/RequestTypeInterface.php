<?php
namespace Dittto\CustomRequestBundle\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestTypeInterface
{
    public function setOriginalRequest(Request $request):RequestTypeInterface;

    public function getOriginalRequest():?Request;

    public function validate():bool;
}

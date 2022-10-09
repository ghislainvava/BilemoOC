<?php

namespace App\Services;

use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PageService 
{   public function getPagination(request $request):Response
    {
        
        

        return  $request->get('limit', 3);
    }

}
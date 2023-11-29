<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;


class BeforeLogoutListener 
{


    public function onkernelRequest(RequestEvent $requestEvent)
    {
        //Get data from Database and validate this action

        if ($requestEvent->getRequest()->getPathInfo() === '/logout') {
            //here the code to execute before logout happens

        }
    }
}

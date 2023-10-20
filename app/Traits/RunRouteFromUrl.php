<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait RunRouteFromUrl {

    public function getApiRouteData(string $url, array $params = []) {
        
        $urlParts = parse_url($url);
        $requestUrl = $urlParts['scheme'] .'://' . $urlParts['host'] . $urlParts['path'];

        //get url parts and assign to new request, ignoring any blacklisted
        $requestParams = [];
        $paramKeys = array_keys($params);
        $urlParts = parse_url($url);
        parse_str($urlParts['query'], $urlParams);
        
        //set parameters on base request
        foreach($urlParams as $param => $value) {
            if(!in_array($param, $paramKeys)) {
                // request()->request->set($param , $value);
                $requestParams[$param] = $value;
            }
        }

        foreach($params as $param => $value) {
            // request()->request->set($param , $value);
            $requestParams[$param] = $value;
        }

        $requestParams['job_user'] = request()->job_user;
        $requestParams['log_type'] = request()->log_type;
        $requestParams['log_target'] = request()->log_target;
        $requestParams['log_uuid'] = request()->log_uuid;

        //currently hijacks current request, as the resolved request is based off this.
        request()->replace($requestParams);

        $route = app('router')->getRoutes()->match(Request::create($requestUrl, 'GET', $requestParams));
        // $route->withoutMiddleware(['auth:api']); //breaks in octane since all users will get the middleware removed for this route
       

        //currently looks like router marries up request+route, and can call both together

        // $route->bind($request);

        // dd($route->getContainer());

        // dd($request->getMethod(), $request->getRequestUri(), request()->getRequestUri());


        // app('\Illuminate\Routing\Router')->dispatchToRoute($request);
        // dd('huh');
        // dd(app('\Illuminate\Routing\Router')->runRoute($request, $route));

        return $route->run();
    }
}
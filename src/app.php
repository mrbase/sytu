<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Sytu\Spotify\Provider\Service\SpotifyServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app['asset_path'] = 'http://sytu.un:9990';

$app->register(new RoutingServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new HttpFragmentServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new Sorien\Provider\PimpleDumpProvider());

$app->register(new SpotifyServiceProvider());

// twig setup
$app['twig'] = $app->extend('twig', function ($twig, $app) {
    // add custom globals, filters, tags, ...

    $twig->addFunction(new \Twig_SimpleFunction('asset', function ($asset) use ($app) {
        $path = '';
//        if (!empty($app['asset_path'])) {
//            $path = $app['asset_path'];
//        }

        return $path.$app['request_stack']->getMasterRequest()->getBasepath().'/'.ltrim($asset, '/');
    }));

    return $twig;
});


// Ensure login
$app->before(function (Request $request, Application $app) {
    // We need to disable redirects on the login url's.
    if (in_array($request->getPathInfo(), ['/spotify/auth', '/spotify/auth/callback'])) {
        return null;
    }

    if (false === $token = $app['spotify']->getAuthendicationToken()) {
        return $app->redirect('/spotify/auth');
    }

    $app['spotify']->reAuthendicate($token);
});


// Body to json on Content-Type: application/json
$app->before(function (Request $request) {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

return $app;

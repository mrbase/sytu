<?php
/**
 * This file is part of the sytu.dk package.
 *
 * (c) ${COPYRIGHT}
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sytu\Spotify\Provider\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SpotiftControllerProvider
 *
 * @package Sytu\Spotify\Provider\Controller
 * @author  Ulrik NIelsen <me@ulrik.co>
 */
class SpotifyControllerProvider implements ControllerProviderInterface
{
    /**
     * @param Application $app
     *
     * @return mixed
     */
    public function connect(Application $app)
    {
        /** @var \Silex\ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/auth', function (Application $app) {
            $authorizeUrl = $app['spotify']->getAuthorizeUrl(['scope' => [
                'playlist-read-private',
                'user-read-private',
            ]]);

            return $app->redirect($authorizeUrl);
        });

        $controllers->get('/auth/callback', function (Application $app, Request $request) {
            if (false === $app['spotify']->getAuthendicationToken()) {
                $app['spotify']->authendicate($request->query->get('code'));
            }

            return $app->redirect('/');
        });


        $controllers->get('/playlists', function (Application $app) {
            $playlists = $app['spotify']->getUserPlaylists($app['spotify']->me()->id, ['limit' => 50]);

            return new JsonResponse($playlists);
        });

        $controllers->get('/playlist/{owner}/{playlistId}', function (Application $app, $owner, $playlistId) {
            $playlist = $app['spotify']->getUserPlaylist($owner, $playlistId);

            return new JsonResponse($playlist);
        });

        return $controllers;
    }
}

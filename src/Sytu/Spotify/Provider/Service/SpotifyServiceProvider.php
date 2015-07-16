<?php

namespace Sytu\Spotify\Provider\Service;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;

/**
 * Class SpotifyServiceProvider
 *
 * @package Sytu\Spotify\Provider\Controller
 * @author  Ulrik NIelsen <me@ulrik.co>
 */
class SpotifyServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     */
    public function register(Container $container)
    {
        $container['spotify.options'] = [
        	'callback'  => '',
        	'client_id' => '',
        	'secret'    => '',
        ];

        $container['spotify'] = function () use ($container) {
            return new SpotifyService(
                new SpotifyWebAPI(),
                new Session(
                    $container['spotify.options']['client_id'],
                    $container['spotify.options']['secret'],
                    $container['spotify.options']['callback']
                ),
                $container['session']
            );
        };
    }
}

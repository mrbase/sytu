<?php
/**
 * This file is part of the sytu.dk package.
 *
 * (c) ${COPYRIGHT}
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sytu\Spotify\Provider\Service;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;
use Symfony\Component\HttpFoundation\Session\Session as HttpSession;

/**
 * Class SpotifyService
 *
 * @package Sytu\Spotify\Provider\Service
 * @author  Ulrik NIelsen <me@ulrik.co>
 *
 * @method bool addMyTracks($tracks) Add tracks to the current user's Spotify library.
 * @method bool addUserPlaylistTracks($userId, $playlistId, $tracks, $options = []) Add tracks to a user's playlist.
 * @method array|object createUserPlaylist($userId, $options) Create a new playlist for a user.
 * @method array currentUserFollows($type, $ids) Check to see if the current user is following one or more artists or other Spotify users.
 * @method bool deleteMyTracks($tracks) Delete tracks from current user's Spotify library.
 * @method string|bool deleteUserPlaylistTracks($userId, $playlistId, $tracks, $snapshotId = '') Delete tracks from a playlist and retrieve a new snapshot ID.
 * @method bool followArtistsOrUsers($type, $ids) Add the current user as a follower of one or more artists or other Spotify users.
 * @method bool followPlaylist($userId, $playlistId, $options = []) Add the current user as a follower of a playlist.
 * @method array|object getAlbum($albumId) Get a album.
 * @method array|object getAlbums($albumIds) Get multiple albums.
 * @method array|object getAlbumTracks($albumId, $options = []) Get a album's tracks.
 * @method array|object getArtist($artistId) Get an artist.
 * @method array|object getArtists($artistIds) Get multiple artists.
 * @method array|object getArtistRelatedArtists($artistId) Get an artist's related artists.
 * @method array|object getArtistAlbums($artistId, $options = []) Get an artist's albums.
 * @method array|object getArtistTopTracks($artistId, $country) Get an artist's top tracks in a country.
 * @method array|object getFeaturedPlaylists($options = []) Get Spotify featured playlists.
 * @method array|object getCategoriesList($options = []) Get a list of categories used to tag items in Spotify (on, for example, the Spotify player’s "Browse" tab).
 * @method array|object getCategory($categoryId, $options = []) Get a single category used to tag items in Spotify (on, for example, the Spotify player’s "Browse" tab).
 * @method array|object getCategoryPlaylists($categoryId, $options = []) Get a list of Spotify playlists tagged with a particular category.
 * @method mixed getLastResponse() Get the latest full response from the Spotify API.
 * @method array|object getNewReleases($options = []) Get new releases.
 * @method array|object getMySavedTracks($options = []) Get the current user’s saved tracks.
 * @method bool getReturnAssoc() Get the return type for the Request body element.
 * @method array|object getTrack($trackId) Get a track.
 * @method array|object getTracks($trackIds) Get multiple tracks.
 * @method array|object getUser($userId) Get a user.
 * @method array|object getUserPlaylists($userId, $options = []) Get a user's playlists.
 * @method array|object getUserPlaylist($userId, $playlistId, $options = []) Get a user's specific playlist.
 * @method array|object getUserPlaylistTracks($userId, $playlistId, $options = []) Get the tracks in a user's playlist.
 * @method array|object me() Get the currently authenticated user.
 * @method array myTracksContains($tracks) Check if tracks is saved in the current user's Spotify library.
 * @method string|bool reorderUserPlaylistTracks($userId, $playlistId, $options) Reorder the tracks in a user's playlist.
 * @method bool replaceUserPlaylistTracks($userId, $playlistId, $tracks) Replace all tracks in a user's playlist with new ones.
 * @method array|object search($query, $type, $options = []) Search for an item.
 * @method void setAccessToken($accessToken) Set the access token to use.
 * @method void setReturnAssoc($returnAssoc) Set the return type for the Request body element.
 * @method bool unfollowArtistsOrUsers($type, $ids) Remove the current user as a follower of one or more artists or other Spotify users.
 * @method bool unfollowPlaylist($userId, $playlistId) Remove the current user as a follower of a playlist.
 * @method bool updateUserPlaylist($userId, $playlistId, $options) Update the details of a user's playlist.
 * @method array userFollowsPlaylist($ownerId, $playlistId, $options) Check if a user is following a playlist.
 *
 * @method string getAuthorizeUrl($options = []) Get the authorization URL.
 * @method string getAccessToken() Get the access token.
 * @method string getClientId() Get the client ID.
 * @method string getClientSecret() Get the client secret.
 * @method int getExpires() Get the number of seconds for which the access token is valid.
 * @method string getRedirectUri() Get the client's redirect URI.
 * @method string getRefreshToken() Get the refresh token.
 * @method bool refreshAccessToken() Refresh an access token.
 * @method bool requestCredentialsToken($scope = []) Request an access token using the Client Credentials Flow.
 * @method bool requestAccessToken($authorizationCode) Request an access token given an authorization code.
 * @method void setClientId($clientId) Set the client ID.
 * @method void setClientSecret($clientSecret) Set the client secret.
 * @method void setRedirectUri($redirectUri) Set the client's redirect URI.
 * @method void setRefreshToken($refreshToken) Set the refresh token.
 */
class SpotifyService
{
    /**
     * @var SpotifyWebAPI
     */
    private $api;

    /**
     * @var SpotifySession
     */
    private $spotifySession;

    /**
     * @var HttpSession
     */
    private $httpSession;

    /**
     * @param SpotifyWebAPI  $api
     * @param SpotifySession $spotifySession
     * @param HttpSession    $httpSession
     */
    public function __construct(SpotifyWebAPI $api, SpotifySession $spotifySession, HttpSession $httpSession)
    {
        $this->api            = $api;
        $this->spotifySession = $spotifySession;
        $this->httpSession    = $httpSession;
    }

    /**
     * @return mixed
     */
    public function getAuthendicationToken()
    {
        return $this->httpSession->get('_spotify_access_token', false);
    }

    /**
     * @param string $accessToken
     */
    public function authendicate($accessToken)
    {
        $this->spotifySession->requestAccessToken($accessToken);
        $this->api->setAccessToken($this->spotifySession->getAccessToken());
        $this->httpSession->set('_spotify_access_token', $this->spotifySession->getRefreshToken());
    }

    /**
     * @param string $accessToken
     */
    public function reAuthendicate($accessToken)
    {
        $this->spotifySession->setRefreshToken($accessToken);
        $this->spotifySession->refreshAccessToken();
        $this->api->setAccessToken($this->spotifySession->getAccessToken());
    }

    /**
     * @param string $method
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($method, array $arguments = [])
    {
        $class = 'api';
        if (!method_exists($this->{$class}, $method)) {
            $class = 'spotifySession';
            if (!method_exists($this->{$class}, $method)) {
                throw new \BadMethodCallException('The method: '.$method.' does not exist.');
            }
        }

        return call_user_func_array([$this->{$class}, $method], $arguments);
    }
}

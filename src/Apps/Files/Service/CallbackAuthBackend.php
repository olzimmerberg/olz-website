<?php

declare(strict_types=1);

namespace Olz\Apps\Files\Service;

use Sabre\HTTP\RequestInterface;
use Sabre\HTTP\ResponseInterface;

/**
 * Callback auth backend.
 *
 * This backend works by calling a function to determine the authenticated user.
 */
class CallbackAuthBackend implements \Sabre\DAV\Auth\Backend\BackendInterface {
    /**
     * This is the prefix that will be used to generate principal urls.
     *
     * @var string
     */
    protected $principalPrefix = 'principals/';

    /**
     * Callback.
     *
     * @var callable
     */
    protected $callBack;

    /**
     * Creates the backend.
     *
     * A callback must be provided to handle authentication.
     */
    public function __construct(callable $callBack) {
        $this->callBack = $callBack;
    }

    /**
     * When this method is called, the backend must check if authentication was
     * successful.
     *
     * The returned value must be one of the following
     *
     * [true, "principals/username"]
     * [false, "reason for failure"]
     *
     * If authentication was successful, it's expected that the authentication
     * backend returns a so-called principal url.
     *
     * Examples of a principal url:
     *
     * principals/admin
     * principals/user1
     * principals/users/joe
     * principals/uid/123457
     *
     * If you don't use WebDAV ACL (RFC3744) we recommend that you simply
     * return a string such as:
     *
     * principals/users/[username]
     *
     * @return array
     */
    public function check(RequestInterface $request, ResponseInterface $response) {
        $cb = $this->callBack;
        $result = $cb();

        $was_successful = $result[0];
        if ($was_successful) {
            $username = $result[1];
            return [true, $this->principalPrefix.$username];
        }
        $failure_reason = $result[1];
        return [false, $failure_reason];
    }

    /**
     * This method is called when a user could not be authenticated, and
     * authentication was required for the current request.
     *
     * This gives you the opportunity to set authentication headers. The 401
     * status code will already be set.
     *
     * In this case of Basic Auth, this would for example mean that the
     * following header needs to be set:
     *
     * $response->addHeader('WWW-Authenticate', 'Basic realm=SabreDAV');
     *
     * Keep in mind that in the case of multiple authentication backends, other
     * WWW-Authenticate headers may already have been set, and you'll want to
     * append your own WWW-Authenticate header instead of overwriting the
     * existing one.
     */
    public function challenge(RequestInterface $request, ResponseInterface $response) {
    }
}

<?php

namespace xelioplus\loginapi\controller;

use Symfony\Component\HttpFoundation\RedirectResponse;

class api
{
    /* @var \phpbb\auth\auth */
    protected $auth;

    /* @var \phpbb\config\config */
    protected $config;

    /* @var \phpbb\request\request */
    protected $request;

    /* @var \phpbb\user */
    protected $user;

    /* @var \phpbb\controller\helper */
    protected $helper;

    /**
     * Constructor
     *
     * @param \phpbb\auth\auth         $auth    Auth object
     * @param \phpbb\config\config     $config  Config object
     * @param \phpbb\request\request   $request Request object
     * @param \phpbb\user              $user    User object
     * @param \phpbb\controller\helper $helper  Controller helper object
     */
    public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\request\request $request, \phpbb\user $user, \phpbb\controller\helper $helper)
    {
        $this->auth = $auth;
        $this->config = $config;
        $this->request = $request;
        $this->user = $user;
        $this->helper = $helper;
    }

    /**
     * Handle a request to the API endpoint
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle()
    {
        $request = $this->parse_request();
        if ($request === null) {
            return $this->create_invalid_request_response();
        }

        if ($request['action'] == 'login') {
            if ($this->user->data['is_bot']) {
                return $this->helper->error($this->user->lang('LOGINAPI_BOT'), 403);
            } elseif ($this->user->data['is_registered']) {
                $groups = array('user');
                if ($this->auth->acl_get('a_external')) {
                    $groups[] = 'admin';
                }
                $response = array(
                    'action' => 'login',
                    'success' => true,
                    'time' => time(),
                    'user' => array(
                        //'id' => $this->user->data['user_id'],
                        'id' => $this->user->data['username_clean'],
                        'username' => $this->user->data['username_clean'],
                        'groups' => $groups,
                    ),
                );

                return $this->build_response($response, $request['return']);
            } else {
                if (isset($request['site'])) {
                    $message = $this->user->lang('LOGINAPI_EXPLAIN_SITE', $request['site']);
                } else {
                    $message = $this->user->lang('LOGINAPI_EXPLAIN_GENERAL');
                }

                return login_box('', $message);
            }
        }

        return $this->create_invalid_request_response();
    }

    /**
     * Parse and validate a request
     *
     * @return array|null Validated request content or null
     */
    protected function parse_request()
    {
        $raw = $this->request->variable('r', '', false, \phpbb\request\request_interface::GET);
        $signature = $this->request->variable('s', '', false, \phpbb\request\request_interface::GET);

        if (!$raw || !$signature) {
            return;
        }

        if ($signature !== $this->sign($raw)) {
            return;
        }

        $data = json_decode($this->base64url_decode($raw), true);
        if (!$data || !isset($data['time']) || !isset($data['action'])) {
            return;
        }

        $time = intval($data['time']);
        if (abs($time - time()) > 600) {
            return;
        }

        return $data;
    }

    /**
     * Build a redirect response back to the frontend service
     *
     * @param array  $data   Data passed back to frontend
     * @param string $return Callback URL of the frontend
     */
    protected function build_response($data, $return)
    {
        $encoded = $this->base64url_encode(json_encode($data));
        $signature = $this->sign($encoded);

        $url = $return;
        if (strpos($url, '?') > 0) {
            $url .= '&';
        } else {
            $url .= '?';
        }
        $url .= sprintf('r=%s&s=%s', $encoded, $signature);

        return new RedirectResponse($url);
    }

    /**
     * Sign a given input
     *
     * @param  string $input
     * @return string Signature of the input data.
     */
    protected function sign($input)
    {
        return hash('sha256', $input.$this->config['xelioplus_loginapi_token']);
    }

    /**
     * Create a response to indicate invalid requests
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function create_invalid_request_response()
    {
        return $this->helper->error($this->user->lang('LOGINAPI_INVALID_REQUEST'), 400);
    }

    /**
     * Base64-encode a given input (URL-safe)
     *
     * @param  string $data
     * @return string
     */
    protected function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Decode a base64-encoded input (URL-safe)
     *
     * @param  string $data
     * @return string
     */
    protected function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

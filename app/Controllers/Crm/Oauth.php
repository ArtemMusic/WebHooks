<?php

namespace App\Controllers\Crm;
use Classes\Client;

class Oauth extends \Core\Controllers\Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->middleware('AllowOrigin');
    }

    public function redirectIntegration(Client $client)
    {
        $l = logger('crm/Oauth/oauth_integration.log');
        $l->log('Oauth redirect', $this->request->input());

        $validator = validator($this->request->input(), [
            'code' => 'default:|string',
            'referer' => 'default:|string',
            'state' => 'default:|string',
            'from_widget' => 'optional',
            'error' => 'optional'
        ]);
        if (!$validator->isValid()) {
            $l->log('Error', $validator->errors());
            ajaxError($validator->data(), 400);
        }
        $data = $validator->data();

        if ($data->error) {
            ajaxError($data->error, 200);
        }
        $data = $client->crmIntegration()->fetchAccessToken($data->code);
        $l->log('AccessToken', $data);
        ajaxSuccess();
    }
}
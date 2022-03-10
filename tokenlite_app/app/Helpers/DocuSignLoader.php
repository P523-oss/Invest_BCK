<?php

namespace App\Helpers;
use DocuSign\Rest\Client;
use Exception;

require '../../vendor/autoload.php';

class DocuSignLoader
{

	protected $username = "20bce872-6aa5-4527-ba7b-bd650731174d";
    protected $password = "Obmayd.o93ay1.";
    protected $integrator_key = "a90a4b27-f49e-4dcc-984e-98d41dd1bd5c";


	// change to production before going live
    protected $host = "https://demo.docusign.net/restapi";

    protected $client;
    protected $clientParams;

	public function __construct($clientParams)
	{
		// Once instantiated, authentication is handled automatically
        $this->client = new Client([
			'username'       => $this->username,
			'password'       => $this->password,
			'integrator_key' => $this->integrator_key,
			'host'           => $this->host
        ]);
        $this->clientParams = $clientParams;
	}

	/**
     * @return DocuSign\eSign\Model\EnvelopeSummary
     * signatureRequestFromTemplate
     */
	public function startSignage()
	{
        // Mode = email / embedded
        $modeEmailOnly = true;

        $tplRoleParams = [
        'email' 	=> $this->clientParams['email'],
        'name'  	=> $this->clientParams['name'],
        'role_name' => 'Investor'];
        if (!$modeEmailOnly)
            $tplRoleParams ['client_user_id'] = $this->clientParams['clientId'];

    	$envelopeSummary = $this->client->envelopes->createEnvelope($this->client->envelopeDefinition([
	    		'status'         => 'sent',
	    		'email_subject'  => 'Tokenizer - Resolute Token Agreement - e-signage',
	    		//'template_id'    => '6b75b192-1745-47cf-a0f0-d83d98b445f1',
                'template_id'    => '3ed442de-5355-4db7-9f21-6068ab7b89cb',
                'template_roles' => [
                    $this->client->templateRole($tplRoleParams)
	    		]
	    	])
        );
        if (!$modeEmailOnly){
            $envelopeApi = $this->client->envelopes;
            $recipient_view_request = new \DocuSign\eSign\Model\RecipientViewRequest();
            $recipient_view_request->setReturnUrl('https://www.example.net/callback/docusign');
            $recipient_view_request->setClientUserId($this->clientParams['clientId']);
            $recipient_view_request->setUserId($this->clientParams['clientId']);
            $recipient_view_request->setUserName($this->clientParams['name']);
            $recipient_view_request->setEmail($this->clientParams['email']);
            $recipient_view_request->setAuthenticationMethod("Email");
            $recipient_view_request->setRecipientId(1);
            //dump ($recipient_view_request);

            $signingView = $envelopeApi->createRecipientView($this->client->getAccountId(), $envelopeSummary->getEnvelopeId(), $recipient_view_request);
            return $signingView;
        }

        return $envelopeSummary;
    }
}

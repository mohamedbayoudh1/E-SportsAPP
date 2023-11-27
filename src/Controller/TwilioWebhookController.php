<?php
// src/Controller/TwilioWebhookController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

/**
 * @Route("/webhooks/twilio")
 */
class TwilioWebhookController extends AbstractController
{
    /**
     * @Route("/sms/incoming", name="webhook.twilio.sms_incoming")
     * @param Request $request
     * @param Client $twilioClient
     * @return Response
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function handleIncomingSmsMessage(Request $request, Client $twilioClient)
    {
        $from = $request->request->get('From');
        $now = new \DateTime();
        $body = $now->format('Y-m-d H:i:s');

        $twilioClient->messages->create($from, [
            "body" => $now->format('Y-m-d H:i:s'),
            "from" => $this->getParameter('12028001691')
        ]);

        return new Response();
    }
}

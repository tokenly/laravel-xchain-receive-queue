<?php

namespace Tokenly\XchainReceiveQueue;


use Exception;
use Illuminate\Http\Exception\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tokenly\LaravelEventLog\Facade\EventLog;
use Tokenly\XChainClient\WebHookReceiver;

class XchainReceiveHandler {

    function __construct(WebHookReceiver $webhook_receiver)
    {
        $this->webhook_receiver = $webhook_receiver;
    }

    public function handleXchainNotification(Request $request) {
        try {
            // get the payload
            $data = $this->webhook_receiver->validateAndParseWebhookNotificationFromRequest($request);
            $payload = $data['payload'];

            // queue the job
            $this->handleXChainPayload($payload);

            // return an immediate ok
            return response('ok', 200);

        } catch (Exception $e) {
            EventLog::logError('webhook.error', $e);
            if ($e instanceof HttpResponseException) { throw $e; }
            throw new HttpResponseException(new Response("An error occurred", 500), 500);
        }
    }

    public function handleXChainPayload($payload) {
        // determine the job class
        $job_class = config('xchainqueue.jobClass');
        if (!class_exists($job_class)) { throw new Exception("Job class ".json_encode($job_class)." not found", 1); }

        // queue the payload
        $job = new $job_class($payload);
        dispatch($job);
    }

}

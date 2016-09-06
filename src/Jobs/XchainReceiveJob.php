<?php

namespace Tokenly\XchainReceiveQueue\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Exception;

class XchainReceiveJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $payload = null;

    function __construct($payload)
    {
        $this->payload = $payload;
    }

    public function handle() {
        // \Illuminate\Support\Facades\Log::debug("\$this->payload=".json_encode($this->payload, 192));
        $event = $this->payload['event'];
        if (!$event) { throw new Exception("undefined event for payload ".json_encode($this->payload, 192), 1); }

        // handle defined event
        $method = "handleEvent_".$event;
        if (method_exists($this, $method)) {
            return call_user_func([$this, $method], $this->payload);
        }

        // handle all events
        $this->handleAnyEvent($this->payload);

        // no defined event handler
        return;
    }

    protected function handleAnyEvent($payload) {
        // abstract
    }

    /*
    protected function handleEvent_receive($payload) {

    }

    protected function handleEvent_send($payload) {

    }

    protected function handleEvent_credit($payload) {

    }

    protected function handleEvent_debit($payload) {

    }

    protected function handleEvent_issuance($payload) {

    }
    */
}

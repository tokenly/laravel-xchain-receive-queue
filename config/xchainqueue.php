<?php

return [

    // the URL path that receives the xchain notificatons
    'receivePath' => env('XCHAIN_CALLBACK_URL', '_xchain/notificaton'),

    // define this to be your own xchain payload handler
    'jobClass'    => 'Tokenly\XchainReceiveQueue\Jobs\XchainReceiveJob',

];

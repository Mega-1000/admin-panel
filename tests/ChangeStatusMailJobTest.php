<?php

include dirname(__FILE__).'./../vendor/autoload.php';

\App\Jobs\OrderStatusChangedNotificationJob::dispatch(1);
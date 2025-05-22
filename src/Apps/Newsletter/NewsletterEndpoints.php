<?php

namespace Olz\Apps\Newsletter;

use Olz\Apps\BaseAppEndpoints;
use Olz\Apps\Newsletter\Endpoints\UpdateNotificationSubscriptionsEndpoint;
use PhpTypeScriptApi\Api;

class NewsletterEndpoints extends BaseAppEndpoints {
    public function __construct(
        protected UpdateNotificationSubscriptionsEndpoint $updateNotificationSubscriptionsEndpoint,
    ) {
    }

    public function register(Api $api): void {
        $api->registerEndpoint('updateNotificationSubscriptions', $this->updateNotificationSubscriptionsEndpoint);
    }
}

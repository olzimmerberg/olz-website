<?php

namespace Olz\Utils;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait MessageBusTrait {
    protected function messageBus(): MessageBusInterface {
        $util = WithUtilsCache::get('messageBus');
        assert($util);
        return $util;
    }

    #[Required]
    public function setMessageBus(MessageBusInterface $new): void {
        WithUtilsCache::set('messageBus', $new);
    }
}

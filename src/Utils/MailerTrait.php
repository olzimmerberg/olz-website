<?php

namespace Olz\Utils;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait MailerTrait {
    protected function mailer(): MailerInterface {
        $util = WithUtilsCache::get('mailer');
        assert($util);
        return $util;
    }

    #[Required]
    public function setMailer(MailerInterface $new): void {
        WithUtilsCache::set('mailer', $new);
    }
}

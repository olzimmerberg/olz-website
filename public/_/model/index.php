<?php

require_once __DIR__.'/common.php';

require_once __DIR__.'/deprecated.php';

require_once __DIR__.'/AccessToken.php';
require_once __DIR__.'/AccessTokenRepository.php';
require_once __DIR__.'/AuthRequest.php';
require_once __DIR__.'/AuthRequestRepository.php';
require_once __DIR__.'/BildDerWoche.php';
require_once __DIR__.'/Blog.php';
require_once __DIR__.'/Counter.php';
require_once __DIR__.'/CounterRepository.php';
require_once __DIR__.'/Download.php';
require_once __DIR__.'/FacebookLink.php';
require_once __DIR__.'/FacebookLinkRepository.php';
require_once __DIR__.'/Forum.php';
require_once __DIR__.'/Galerie.php';
require_once __DIR__.'/GalerieRepository.php';
require_once __DIR__.'/GoogleLink.php';
require_once __DIR__.'/GoogleLinkRepository.php';
require_once __DIR__.'/Karte.php';
require_once __DIR__.'/Link.php';
require_once __DIR__.'/NotificationSubscription.php';
require_once __DIR__.'/NotificationSubscriptionRepository.php';
require_once __DIR__.'/OlzText.php';
require_once __DIR__.'/Role.php';
require_once __DIR__.'/RoleRepository.php';
require_once __DIR__.'/SolvEvent.php';
require_once __DIR__.'/SolvEventRepository.php';
require_once __DIR__.'/SolvPerson.php';
require_once __DIR__.'/SolvPersonRepository.php';
require_once __DIR__.'/SolvResult.php';
require_once __DIR__.'/SolvResultRepository.php';
require_once __DIR__.'/StravaLink.php';
require_once __DIR__.'/StravaLinkRepository.php';
require_once __DIR__.'/TelegramLink.php';
require_once __DIR__.'/TelegramLinkRepository.php';
require_once __DIR__.'/Throttling.php';
require_once __DIR__.'/ThrottlingRepository.php';
require_once __DIR__.'/User.php';
require_once __DIR__.'/UserRepository.php';

// Don't forget to change `public/_/config/doctrine.php`
require_once __DIR__.'/../anmelden/model/index.php';
require_once __DIR__.'/../news/model/index.php';
require_once __DIR__.'/../quiz/model/index.php';
require_once __DIR__.'/../termine/model/index.php';

<?php

namespace Olz\EventListener;

use Olz\Components\Error\OlzErrorPage\OlzErrorPage;
use Olz\Utils\LogsUtils;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
final class ExceptionListener {
    private static $is_handling_exception = false;

    public function onKernelException(ExceptionEvent $event) {
        if (self::$is_handling_exception) {
            return;
        }
        self::$is_handling_exception = true;
        $exception = $event->getThrowable();

        $logger = LogsUtils::fromEnv()->getLogger('KERNEL');
        $response = new Response();

        if ($exception instanceof HttpExceptionInterface) {
            $http_status_code = $exception->getStatusCode();
            $response->setStatusCode($http_status_code);
            $response->headers->replace($exception->getHeaders());
            $response->setContent(OlzErrorPage::render([
                'http_status_code' => $http_status_code,
            ]));
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent(OlzErrorPage::render([]));
            $logger->warning("Non-HttpExceptionInterface exception: {$exception}");
        }

        $event->setResponse($response);
    }
}

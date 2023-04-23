<?php

namespace Olz\EventListener;

use Olz\Components\Error\OlzErrorPage\OlzErrorPage;
use Olz\Utils\WithUtilsTrait;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

#[AsEventListener(event: 'kernel.exception', method: 'onKernelException')]
class KernelExceptionListener {
    use WithUtilsTrait;

    protected $is_handling_exception;

    public function onKernelException(ExceptionEvent $event) {
        $exception = $event->getThrowable();
        $previous_exception = $this->is_handling_exception;
        $this->is_handling_exception = $exception;

        if ($previous_exception !== null) {
            $this->log()->warning("Was already handling exception {$previous_exception->getMessage()}, but now also {$exception->getMessage()}.", [$previous_exception, $exception]);
            return;
        }

        $response = new Response();

        if ($exception instanceof HttpExceptionInterface) {
            $this->log()->notice("Handling HttpExceptionInterface {$exception->getMessage()}...", [$exception]);
            $http_status_code = $exception->getStatusCode();
            $response->setStatusCode($http_status_code);
            $response->headers->replace($exception->getHeaders());
            $response->setContent(OlzErrorPage::render([
                'http_status_code' => $http_status_code,
            ], $this));
        } else {
            $this->log()->warning("Non-HttpExceptionInterface exception: {$exception->getMessage()}", [$exception]);
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setContent(OlzErrorPage::render([], $this));
        }

        $event->setResponse($response);
    }
}

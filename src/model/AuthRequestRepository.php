<?php

use Doctrine\ORM\EntityRepository;

require_once __DIR__.'/AuthRequest.php';
require_once __DIR__.'/../config/doctrine.php';

class AuthRequestRepository extends EntityRepository {
    public const NUM_TRIES = 32;
    public const TRIES_RESET_INTERVAL = '+8 hour'; // Reset after 8h

    public function addAuthRequest($ip_address, $action, $username, $timestamp = null) {
        if ($timestamp === null) {
            $timestamp = new DateTime();
        }
        $auth_request = new AuthRequest();
        $auth_request->setIpAddress($ip_address);
        $auth_request->setAction($action);
        $auth_request->setTimestamp($timestamp);
        $auth_request->setUsername($username);
        $this->getEntityManager()->persist($auth_request);
        $this->getEntityManager()->flush();
    }

    public function canAuthenticate($ip_address, $timestamp = null) {
        $tries_reset_interval = DateInterval::createFromDateString(self::TRIES_RESET_INTERVAL);
        if ($timestamp === null) {
            $timestamp = new DateTime();
        }
        $sanitized_ip_address = DBEsc($ip_address);
        $min_timestamp = $timestamp->sub($tries_reset_interval);
        $sanitized_min_timestamp = $min_timestamp->format('Y-m-d H:i:s');
        $dql = "
            SELECT ar 
            FROM AuthRequest ar 
            WHERE 
                ar.ip_address='{$sanitized_ip_address}' 
                AND ar.timestamp>'{$sanitized_min_timestamp}'
                AND ar.action IN ('AUTHENTICATED', 'BLOCKED', 'INVALID_CREDENTIALS')
            ORDER BY ar.timestamp DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults(self::NUM_TRIES);
        $auth_requests = $query->getResult();
        $num_unsuccessful_auth_requests = 0;
        foreach ($auth_requests as $auth_request) {
            $action = $auth_request->getAction();
            if ($action === 'AUTHENTICATED') {
                break;
            }
            $num_unsuccessful_auth_requests++;
        }
        return $num_unsuccessful_auth_requests < self::NUM_TRIES;
    }

    public function canValidateAccessToken($ip_address, $timestamp = null) {
        $tries_reset_interval = DateInterval::createFromDateString(self::TRIES_RESET_INTERVAL);
        if ($timestamp === null) {
            $timestamp = new DateTime();
        }
        $sanitized_ip_address = DBEsc($ip_address);
        $min_timestamp = $timestamp->sub($tries_reset_interval);
        $sanitized_min_timestamp = $min_timestamp->format('Y-m-d H:i:s');
        $dql = "
            SELECT ar 
            FROM AuthRequest ar 
            WHERE 
                ar.ip_address='{$sanitized_ip_address}' 
                AND ar.timestamp>'{$sanitized_min_timestamp}'
                AND ar.action IN ('TOKEN_VALIDATED', 'TOKEN_BLOCKED', 'INVALID_TOKEN', 'EXPIRED_TOKEN')
            ORDER BY ar.timestamp DESC";
        $query = $this->getEntityManager()->createQuery($dql);
        $query->setMaxResults(self::NUM_TRIES);
        $auth_requests = $query->getResult();
        $num_unsuccessful_auth_requests = 0;
        foreach ($auth_requests as $auth_request) {
            $action = $auth_request->getAction();
            if ($action === 'TOKEN_VALIDATED') {
                break;
            }
            $num_unsuccessful_auth_requests++;
        }
        return $num_unsuccessful_auth_requests < self::NUM_TRIES;
    }
}

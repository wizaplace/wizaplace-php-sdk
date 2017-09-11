<?php
/**
 * @copyright Copyright (c) Wizacha
 * @license Proprietary
 */
declare(strict_types = 1);

use VCR\Request;
use VCR\VCR;

require_once(__DIR__.'/../vendor/autoload.php');

error_reporting(-1);

// Configure VCR
ini_set('opcache.enable', '0');
VCR::configure()->setMode(VCR::MODE_ONCE);
VCR::configure()->enableLibraryHooks(['stream_wrapper', 'curl'])
    ->addRequestMatcher('headers_custom_matcher', static function (Request $first, Request $second) : bool {
        $headersBags = [$first->getHeaders(), $second->getHeaders()];

        $boundaryHeaderStart = 'multipart/form-data; boundary=';
        foreach ($headersBags as &$headers) {
            // Replace the random multipart boundary,
            // so requests with different boundaries in headers still match each other
            if (strpos($headers['Content-Type'] ?? '', $boundaryHeaderStart) === 0) {
                $headers['Content-Type'] = $boundaryHeaderStart.'fakeBoundary';
            }
            // Remove flaky headers that we don't care about
            unset($headers['User-Agent']);
        }

        return $headersBags[0] == $headersBags[1];
    })
    ->addRequestMatcher('body_custom_matcher', static function (Request $first, Request $second) : bool {
        $data = array_map(static function (Request $req) : array {
            return [
                'body' => $req->getBody(),
                'Content-Type' => $req->getHeaders()['Content-Type'] ?? '',
            ];
        }, [$first, $second]);

        $boundaryHeaderStart = 'multipart/form-data; boundary=';
        foreach ($data as &$requestData) {
            // Replace the random multipart boundary,
            // so requests with different boundaries in body still match each other
            if (strpos($requestData['Content-Type'], $boundaryHeaderStart) === 0) {
                $boundary = substr($requestData['Content-Type'], strlen($boundaryHeaderStart));
                $requestData['body'] = str_replace('--'.$boundary, '--fakeBoundary', $requestData['body']);
            }
        }

        return $data[0]['body'] === $data[1]['body'];
    })
    ->enableRequestMatchers(array('method', 'url', 'query_string', 'body_custom_matcher', 'post_fields', 'headers_custom_matcher'));
VCR::turnOn();
VCR::turnOff();

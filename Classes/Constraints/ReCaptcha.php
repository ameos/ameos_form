<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Constraints;

use Ameos\AmeosForm\Exception\MissingDependencyException;
use ReCaptcha\ReCaptcha as GoogleReCaptcha;

/**
 * Configuration: privateKey. Requires google/recaptcha
 */
class ReCaptcha extends ConstraintAbstract
{
    public function isValid(mixed $value): bool
    {
        $request = $this->form->getCurrentRequest();
        $body = $request->getParsedBody();
        $response = is_array($body) ? $this->stringify($body['g-recaptcha-response'] ?? '') : '';
        if ($response === '') {
            return false;
        }
        if (!class_exists(GoogleReCaptcha::class)) {
            throw new MissingDependencyException('install recaptcha library (composer require google/recaptcha)', 1731340813);
        }
        $remoteIp = $request->getServerParams()['REMOTE_ADDR'] ?? null;
        $recaptcha = new GoogleReCaptcha($this->stringify($this->configuration['privateKey'] ?? ''));
        $result = $recaptcha->verify($response, is_string($remoteIp) ? $remoteIp : null);
        return is_object($result) && is_callable([$result, 'isSuccess']) && $result->isSuccess() === true;
    }
}

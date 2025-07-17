<?php

namespace Arrtxp\Core\Validators;

use Arrtxp\Core\Validator;
use Laminas\ReCaptcha\ReCaptcha as LaminasReCaptcha;

class ReCaptcha extends Validator
{
    public const string INVALID = 'invalid';

    private string $siteKey;
    private string $secretKey;

    protected array $messages = [
        self::INVALID => 'Jesteś robotem?',
    ];

    public function __construct(array $config)
    {
        $this->siteKey = $config['google']['reCaptcha']['siteKey'];
        $this->secretKey = $config['google']['reCaptcha']['secretKey'];
    }

    public function isValid($value): bool
    {
        if (empty($value)) {
            return $this->error(self::INVALID);
        }

        $reCaptcha = new LaminasReCaptcha($this->siteKey, $this->secretKey);
        $result = $reCaptcha->verify($value);

        if (!$result->isValid()) {
            return $this->error(self::INVALID);
        }

        return true;
    }
}
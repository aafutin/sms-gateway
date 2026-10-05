<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class SendSmsRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Regex(pattern: '/^\+7\d{10}$/', message: 'Телефон должен быть в формате +7XXXXXXXXXX.')]
        public readonly string $phone,

        #[Assert\NotBlank]
        #[Assert\Length(max: 1000)]
        public readonly string $text,
    ) {
    }
}

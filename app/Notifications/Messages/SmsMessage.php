<?php

namespace App\Notifications\Messages;

class SmsMessage
{
    public function __construct(
        public string $content,
        public ?string $to = null,
        public array $meta = [],
    ) {
    }

    public static function make(string $content): self
    {
        return new self($content);
    }

    public function to(string $to): self
    {
        $this->to = $to;
        return $this;
    }

    public function meta(array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }
}

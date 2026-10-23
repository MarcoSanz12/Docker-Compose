<?php

namespace App\Model;

class Language
{

// Id, name, country, phone, card_number, kills
    private int $id;

    private string $name;

    public const TABLE = 'language';

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
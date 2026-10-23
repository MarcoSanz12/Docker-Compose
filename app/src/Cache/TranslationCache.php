<?php

namespace App\Cache;

use App\Repository\TranslationRepository;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class TranslationCache
{
    private TranslationRepository $repository;
    private AbstractAdapter $cache;

    public function __construct(TranslationRepository $repository, AbstractAdapter $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }
    public function findForLanguage(mixed $languageId,mixed $phrase): ?string
    {
        $key = sprintf("translation;%d;%s", $languageId, $phrase);

        return $this->cache->get(
            $key,
            function (ItemInterface $item) use ($languageId, $phrase) {
                echo "Adding $phrase to cache...";

                return $this->repository->findForLanguage(
                    languageId: $languageId,
                    phrase: $phrase
                );
            }
        );
    }
}

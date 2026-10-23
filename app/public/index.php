<?php

phpinfo(flags: INFO_ALL);

use App\Cache\TranslationCache;
use App\Database\Connection;
use App\Repository\LanguageRepository;
use App\Repository\TranslationRepository;
use Symfony\Component\Cache\Adapter\RedisAdapter;

require dirname(__DIR__) . '/vendor/autoload.php';

$client = RedisAdapter::createConnection(
    "redis://{$_ENV['REDIS_HOST']}:{$_ENV['REDIS_PORT']}"
);

$cacheAdapter = new RedisAdapter($client);

$databaseConnected = false;
$languages = [];
$translation = null;
$errorMessage = null;
$phrase = '';
$selectedLanguage = '';

try {
    Connection::getInstance()->getPdo()->query('SELECT 1');
    $databaseConnected = true;

    $languages = (new LanguageRepository())->findAll();



    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $phrase = trim((string) ($_POST['phrase'] ?? ''));
        $languageId = filter_var($_POST['language'] ?? null, FILTER_VALIDATE_INT);
        $languageIds = array_map(
            static fn ($language): int => $language->getId(),
            $languages
        );

        if ($phrase === '' || $languageId === false || !in_array($languageId, $languageIds, true)) {
            $errorMessage = 'Enter a phrase and select a language.';
        } else {
            $selectedLanguage = (string) $languageId;
            $translationCache = new TranslationCache(repository: new TranslationRepository(), cache: $cacheAdapter);
            $translation = $translationCache->findForLanguage(languageId: $languageId, phrase: $phrase);

            if ($translation === null) {
                $errorMessage = 'No translation was found for that phrase and language.';
            }
        }
    }
} catch (\PDOException $exception) {
    error_log('Database connection failed: ' . $exception->getMessage());
    $databaseConnected = false;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Translate This</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        @media (min-width: 992px) {
            .rounded-lg-3 {
                border-radius: .3rem;
            }
        }
    </style>

</head>
<body>
<div class="bg-dark text-secondary px-4 py-5 text-center" style="height: 100vh">
    <div class="py-5">
        <h1 class="display-5 fw-bold text-white">Translate This</h1>
        <div class="col-lg-6 mx-auto">
            <?php if ($databaseConnected): ?>
                <p class="alert alert-success">Database connection established.</p>
            <?php else: ?>
                <p class="alert alert-danger">Could not connect to the database. Check the application logs.</p>
            <?php endif; ?>

            <p class="fs-5 mb-4">Select a language and enter your word(s).</p>

            <form method="post">
                <div class="row g-3">
                    <div class="col">
                        <select name="language" class="form-select" aria-label="Select a language" required>
                            <option value="">Select a language</option>
                            <?php foreach ($languages as $language): ?>
                                <option value="<?= $language->getId() ?>"
                                    <?= $selectedLanguage === (string) $language->getId() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($language->getName(), ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col">
                        <input name="phrase" type="text" class="form-control" placeholder="Phrase"
                               aria-label="Phrase" value="<?= htmlspecialchars($phrase, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-info" type="submit">Translate</button>
                    </div>
                </div>
            </form>

            <?php if ($translation !== null): ?>
                <p class="alert alert-info mt-4">
                    <?= htmlspecialchars($phrase, ENT_QUOTES, 'UTF-8') ?>:
                    <?= htmlspecialchars($translation, ENT_QUOTES, 'UTF-8') ?>
                </p>
            <?php elseif ($errorMessage !== null): ?>
                <p class="alert alert-warning mt-4"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>

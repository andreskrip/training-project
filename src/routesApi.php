<?php
return [
    '~^articles/(\d+)$~' => [\MyProject\Controllers\Api\ArticlesApiController::class, 'view'],
    '~^articles/create$~' => [\MyProject\Controllers\Api\ArticlesApiController::class, 'create'],
];

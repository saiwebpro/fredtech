<?php

use spark\middlewares\CsrfGuard\CsrfGuard;

// Add CSRF Guard
$app->add(new CsrfGuard);

<?php

use Siberian\Assets;
use Siberian\Hook;
use Siberian\Translation;

/**
 * @param $payload
 * @return mixed
 * @throws Zend_Exception
 */
function claimsDashboardNav ($payload) {
    return Claims_Model_Claims::dashboardNav($payload);
}

$init = static function ($bootstrap) {
    Assets::registerScss([
        '/app/local/modules/Claims/features/claims/scss/claims.scss'
    ]);
    Translation::registerExtractor(
        'claims',
        'Claims',
        '/app/local/modules/Claims/resources/translations/default/claims.po');
    Hook::listen('editor.left.menu.ready', 'claims_nav', 'claimsDashboardNav');
};


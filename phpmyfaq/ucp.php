<?php

/**
 * User Control Panel.
 *
 * PHP Version 5.6
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @category  phpMyFAQ
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2012-2018 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      https://www.phpmyfaq.de
 * @since     2012-01-12
 */

use phpMyFAQ\User\CurrentUser;
use phpMyFAQ\Services\Gravatar;

if (!defined('IS_VALID_PHPMYFAQ')) {
    $protocol = 'http';
    if (isset($_SERVER['HTTPS']) && strtoupper($_SERVER['HTTPS']) === 'ON') {
        $protocol = 'https';
    }
    header('Location: '.$protocol.'://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']));
    exit();
}

if ($user instanceof CurrentUser) {
    try {
        $faqsession->userTracking('user_control_panel', $user->getUserData('display_name'));
    } catch (Exception $e) {
        // @todo handle the exception
    }

    if ($faqConfig->get('main.enableGravatarSupport')) {
        $gravatar = new Gravatar($faqConfig);
        $gravatarImg = sprintf(
            '<a target="_blank" href="http://www.gravatar.com">%s</a>',
            $gravatar->getImage($user->getUserData('email'), ['class' => 'img-circle', 'size' => 125])
        );
    } else {
        $gravatarImg = '';
    }

    $tpl->parse(
        'writeContent',
        array(
            'headerUserControlPanel' => $PMF_LANG['headerUserControlPanel'],
            'ucpGravatarImage' => $gravatarImg,
            'userid' => $user->getUserId(),
            'csrf' => $user->getCsrfTokenFromSession(),
            'msgRealName' => $PMF_LANG['ad_user_name'],
            'realname' => $user->getUserData('display_name'),
            'msgEmail' => $PMF_LANG['msgNewContentMail'],
            'email' => $user->getUserData('email'),
            'msgPassword' => $PMF_LANG['ad_auth_passwd'],
            'msgConfirm' => $PMF_LANG['ad_user_confirm'],
            'msgSave' => $PMF_LANG['msgSave'],
            'msgCancel' => $PMF_LANG['msgCancel'],
        )
    );

    $tpl->parseBlock(
        'index',
        'breadcrumb',
        [
            'breadcrumbHeadline' => $PMF_LANG['headerUserControlPanel']
        ]
    );

} else {
    // Redirect to login
    header('Location: '.$faqConfig->getDefaultUrl());
    exit();
}

<?php
/**
 * Routes Configuration
 *
 * This files stores all the routes for the core WHSuite system.
 *
 * @package  WHSuite-Configs
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2013, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */

/**
 * Admin Routes
 */
App::get('router')->attach('/admin', array(
    'name_prefix' => 'admin-',
    'values' => array(
        'sub-folder' => 'admin',
        'addon' => 'plesk'
    ),
    'params' => array(
        'id' => '(\d+)'
    ),

    'routes' => array(
        'service-plesk-manage' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/plesk/hosting/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'manageHosting'
            )
        ),
        'service-plesk-create' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/plesk/hosting/create/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'createAccount'
            )
        ),
        'service-plesk-suspend' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/plesk/hosting/suspend/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'suspendAccount'
            )
        ),
        'service-plesk-unsuspend' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/plesk/hosting/unsuspend/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'unsuspendAccount'
            )
        ),
        'service-plesk-terminate' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/plesk/hosting/terminate/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'terminateAccount'
            )
        ),
        'server-plesk-manage' => array(
            'params' => array(
                'server_id' => '(\d+)',
            ),
            'path' => '/servers/group/{:id}/server/{:server_id}/plesk/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'manageServer'
            )
        ),
        'server-plesk-reboot' => array(
            'params' => array(
                'server_id' => '(\d+)',
            ),
            'path' => '/servers/group/{:id}/server/{:server_id}/plesk/reboot/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'rebootServer'
            )
        ),
        'server-plesk-restart-service' => array(
            'params' => array(
                'server_id' => '(\d+)',
                'service' => '(\w+)'
            ),
            'path' => '/servers/group/{:id}/server/{:server_id}/plesk/restart/{:service}/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'restartService'
            )
        ),
    )
));


/**
 * Client Routes
 */

App::get('router')->attach('', array(
    'name_prefix' => 'client-',
    'values' => array(
        'sub-folder' => 'client',
        'addon' => 'plesk'
    ),
    'params' => array(
        'id' => '(\d+)'
    ),

    'routes' => array(
        'service-plesk-manage' => array(
            'path' => '/plesk/manage/{:id}/',
            'values' => array(
                'controller' => 'pleskController',
                'action' => 'manageHosting'
            )
        ),
    ),
));

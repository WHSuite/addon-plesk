<?php

App::get('hooks')->startListening('client-load-service-plesk', 'cpanel-update-client-service', function($purchase_id) {
  App::factory('\Addon\Plesk\Libraries\Plesk')->updateRemote($purchase_id);
});

App::get('hooks')->startListening('admin-load-service-plesk', 'admin-update-client-service', function($purchase_id) {
  App::factory('\Addon\Plesk\Libraries\Plesk')->updateRemote($purchase_id);
});

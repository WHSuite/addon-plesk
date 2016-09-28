<div class="row">
    <div class="col-md-2">
        <img src="<?php echo $assets->image('Plesk::logo.png'); ?>" width="100%">
    </div>
    <div class="col-md-1 text-right">
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                <?php echo $lang->get('options'); ?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="<?php echo $router->generate('admin-server-plesk-restart-service', array('id' => $group->id, 'server_id' => $server_id, 'service' => 'web')); ?>"><?php echo $lang->get('restart_httpd'); ?></a></li>
                <li class="divider"></li>
                <li><a href="<?php echo $router->generate('admin-server-plesk-reboot', array('id' => $group->id, 'server_id' => $server_id)); ?>"><?php echo $lang->get('reboot_server'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-9 text-right">

        <h2 class="nomargin"><?php echo $server['hostname']; ?></h2>
        <b><?php echo $lang->get('version'); ?>:</b> <?php echo $server['version']; ?>
        <b><?php echo $lang->get('load_averages'); ?>:</b> <?php echo $server['loadavg']['one'].', '.$server['loadavg']['five'].', '.$server['loadavg']['fifteen']; ?>
    </div>

</div>
<hr>
<div class="row">
    <div class="col-sm-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><?php echo $lang->get('domain'); ?></th>
                    <th><?php echo $lang->get('ip_address'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($server['accounts'] as $account): ?>
                <tr>
                    <td><?php echo $account['domain']; ?></td>
                    <td><?php echo $account['ip_address']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

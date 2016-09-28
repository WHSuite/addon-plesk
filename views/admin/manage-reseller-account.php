<div class="row">
    <div class="col-md-12">
        <table class="table table-striped">
            <tbody>
                <tr>
                    <td><strong><?php echo $lang->get('clients'); ?></strong></td>
                    <td><?php echo $account->data->stat->{'active-clients'}; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('domains'); ?></strong></td>
                    <td><?php echo $account->data->stat->{'active-domains'}; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo $lang->get('ip_addresses'); ?></strong></td>
                    <td>
                    <?php $ip_addresses = array(); ?>
                    <?php foreach($account->data->ippool->ip as $ip): ?>
                        <?php $ip_addresses[] = $ip->{'ip-address'}; ?>
                    <?php endforeach; ?>
                    <?php echo implode(", ", $ip_addresses); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="text-center">
            <a href="<?php echo $router->generate('admin-service-plesk-create', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-secondary"><?php echo $lang->get('create_account'); ?></a>
            <a href="<?php echo $router->generate('admin-service-plesk-suspend', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-warning">Suspend Account</a>
            <a href="<?php echo $router->generate('admin-service-plesk-unsuspend', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-warning">Unsuspend Account</a>
            <a href="<?php echo $router->generate('admin-service-plesk-terminate', array('id' => $client->id, 'service_id' => $service->id)); ?>" class="btn btn-danger" onclick="return confirm('<?php echo $lang->get('confirm_delete'); ?>')">Terminate Account</a>
        </div>
    </div>
</div>

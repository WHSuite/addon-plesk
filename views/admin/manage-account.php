<div class="row">
    <div class="col-md-8">
        <h3 class="nomargin"><?php echo $account->data->gen_info->name; ?></h3>
    </div>
    <div class="col-md-4">
        <b><?php echo $lang->get('ip_address'); ?>: </b> <?php echo $account->data->gen_info->dns_ip_address; ?>
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

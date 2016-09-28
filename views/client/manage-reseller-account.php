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
        <div class="well text-center">
            <a href="http://<?php echo $ip_addresses[0]; ?>:8443" class="btn btn-primary" target="_blank"><?php echo $lang->get('access_control_panel'); ?></a>
        </div>
    </div>
</div>

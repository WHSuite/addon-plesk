<?php
namespace Addon\Plesk\Migrations;

use \App\Libraries\BaseMigration;

class Migration2014_07_16_114700_version1 extends BaseMigration
{
    public function up($addon_id)
    {
        // Server Module
        $module = new \ServerModule();
        $module->name = 'Plesk';
        $module->slug = 'plesk';
        $module->addon_id = $addon_id;
        $module->save();

        // Purchase data custom field group.
        $group = new \DataGroup();
        $group->slug = 'plesk_purchase_data';
        $group->name = 'Plesk Purchase Data';
        $group->addon_id = $addon_id;
        $group->is_editable = 0;
        $group->is_active = 0;
        $group->save();
    }

    public function down($addon_id)
    {
        \ServerModule::where('addon_id', '=', $addon_id)->delete();

        $data_group = \DataGroup::where('slug', '=', 'plesk_purchase_data')->first();
        $fields = $data_group->DataField()->get();

        foreach ($fields as $field) {
            $field->DataFieldValue()->delete();

            $field->delete();
        }

        $data_group->delete();
    }
}

<?php defined('BASEPATH') or exit('No direct script access allowed');

$config['module']['master_amount_leave']['visible']     = TRUE;
$config['module']['master_amount_leave']['main_warehouse']   = FALSE;
$config['module']['master_amount_leave']['parent']      = 'master_data_hrd';
$config['module']['master_amount_leave']['label']       = 'Amount Leave';
$config['module']['master_amount_leave']['name']        = 'amount_leave';
$config['module']['master_amount_leave']['route']       = 'amount_leave';
$config['module']['master_amount_leave']['view']        = config_item('module_path') .'amount_leave/';
$config['module']['master_amount_leave']['language']    = 'item_group_lang';
$config['module']['master_amount_leave']['table']       = 'tb_amount_leave_items';
$config['module']['master_amount_leave']['model']       = 'Amount_Leave_Model';
$config['module']['master_amount_leave']['permission']  = array(
    'index'   => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'import'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'edit'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'info'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'save'    => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_create'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_edit'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
    'contract_delete'  => 'ADMIN,SUPER ADMIN,HR MANAGER,HR STAFF',
);

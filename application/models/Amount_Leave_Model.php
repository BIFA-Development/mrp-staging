<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Amount_Leave_Model extends MY_Model
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = config_item('module')['master_amount_leave'];
    }

    public function getSelectedColumns()
    {
        return array(
            'No',
            'Leave Name',
            'Group Leave',
            'Amount',
        );
    }

    public function getSearchableColumns()
    {
        return array(
            'leave_name',
            'position',
            'amount_leave',
        );
    }

    public function getOrderableColumns()
    {
        return array(
            null,
            'leave_name',
            'position',
            'amount_leave',
        );
    }

    private function searchIndex()
    {

        if (!empty($_POST['columns'][1]['search']['value'])){
            $status = $_POST['columns'][1]['search']['value'];

            // $this->db->where('tb_amount_leave_items.status', $status);
        }else{
            // $this->db->where('tb_amount_leave_items.status', 'AVAILABLE');
        }

        $i = 0;

        foreach ($this->getSearchableColumns() as $item){
            if ($_POST['search']['value']){
                if ($i === 0){
                $this->db->group_start();
                $this->db->like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                } else {
                $this->db->or_like('UPPER('.$item.')', strtoupper($_POST['search']['value']));
                }

                if (count($this->getSearchableColumns()) - 1 == $i)
                $this->db->group_end();
            }

            $i++;
        }
    }

    function getIndex($return = 'array')
    {
        $selected = array(
            'tb_amount_leave_items.*',
            'tb_leave_type.name_leave AS leave_name',
            'tb_group_leave.name_group AS name_group',
        );
        $this->db->select($selected);
        $this->db->from('tb_amount_leave_items');
        $this->db->join('tb_leave_type', 'tb_amount_leave_items.leave_id = tb_leave_type.id', 'left');
        $this->db->join('tb_group_leave', 'tb_amount_leave_items.position = tb_group_leave.id', 'left');

        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])){
            foreach ($_POST['order'] as $key => $order){
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            $this->db->order_by('id', 'desc');
        }

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();

        if ($return === 'object'){
            return $query->result();
        } elseif ($return === 'json'){
            return json_encode($query->result());
        } else {
            return $query->result_array();
        }
    }

    function countIndexFiltered()
    {
        $this->db->from('tb_amount_leave_items');
        // $this->db->join('tb_master_coa','tb_master_coa.coa=tb_master_expense_reimbursement.account_code','left');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function countIndex()
    {
        $this->db->from('tb_amount_leave_items');
        // $this->db->join('tb_master_coa','tb_master_coa.coa=tb_master_expense_reimbursement.account_code','left');

        $query = $this->db->get();

        return $query->num_rows();
    }

    public function findById($id)
    {
        $this->db->from('tb_amount_leave_items');
        $this->db->where('id',$id);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function findOneBy($criteria)
    {
        $this->db->from('tb_amount_leave_items');
        $this->db->where($criteria);

        $query = $this->db->get();

        return $query->unbuffered_row('array');
    }

    public function insert(array $user_data)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->insert('tb_amount_leave_items');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function update(array $user_data, $id)
    {
        $this->db->trans_begin();

        $this->db->set($user_data);
        $this->db->where('id',$id);
        $this->db->update('tb_amount_leave_items');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function delete()
    {
        $this->db->trans_begin();

        $id = $this->input->post('id');
        $this->db->where('id', $id);
        $this->db->update('tb_amount_leave_items');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function import(array $user_data)
    {
        $this->db->trans_begin();

        foreach ($user_data as $key => $data){
            $this->db->set('expense_name', strtoupper($data['expense_name']));
            $this->db->set('account_code', $data['account_code']);
            $this->db->set('created_by', config_item('auth_person_name'));
            $this->db->set('updated_by', config_item('auth_person_name'));
            $this->db->insert('tb_master_expense_reimbursement');
        }

        if ($this->db->trans_status() === FALSE)
        return FALSE;

        $this->db->trans_commit();

        return TRUE;
    }
}

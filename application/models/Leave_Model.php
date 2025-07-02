<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_Model extends MY_Model
{
    protected $module;
    protected $budget_year;
    protected $connection;
    protected $budget_month;

    public function __construct()
    {
        parent::__construct();
        
        $this->budget_year  = find_budget_setting('Active Year');
        $this->module = config_item('module')['leave'];
        $this->connection   = $this->load->database('budgetcontrol', TRUE);
        $this->budget_year  = find_budget_setting('Active Year');
        $this->budget_month = find_budget_setting('Active Month');
    }

    public function getSelectedColumns()
    {
        $return = array(

            'No',
            'Request Date',
            'Document Number',
            'Person Name',
            'Leave Type Name',
            'Status',
            'Is Reserved',
            'Notes Approval',

        );
        return $return;
    }

    public function getSearchableColumns()
    {
        return array(
            'document_number',
            'request_date',
        );
    }

    function countIndexFiltered()
    {
        $this->db->from('tb_leave_requests');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }


    public function getOrderableColumns()
    {
        return array(
            null,
            'request_date',
            'document_number',
        );
    }

    public function countIndex()
    {
        $this->db->from('tb_leave_requests');

        $query = $this->db->get();

        return $query->num_rows();
    }


    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])){
            $search_required_date = $_POST['columns'][1]['search']['value'];
            $range_date  = explode(' ', $search_required_date);

            $this->db->where('tb_leave_requests.request_date >= ', $range_date[0]);
            $this->db->where('tb_leave_requests.request_date <= ', $range_date[1]);
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
        
        $selected_person = getEmployeeById(config_item('auth_user_id'));
        $person_number   = $selected_person['employee_number'];

        $selected = array(
            'tb_leave_requests.*',
        );

        $this->db->select($selected);
        $this->db->from('tb_leave_requests');

        $this->db->group_start();
        $this->db->where('tb_leave_requests.employee_number', $person_number);
        $this->db->or_where('tb_leave_requests.head_dept', $person_number);
        $this->db->group_end();

    
        
        $this->searchIndex();

        $column_order = $this->getOrderableColumns();

        if (isset($_POST['order'])){
            foreach ($_POST['order'] as $key => $order){
                $this->db->order_by($column_order[$_POST['order'][$key]['column']], $_POST['order'][$key]['dir']);
            }
        } else {
            // $this->db->order_by('id', 'desc');
            // $this->db->order_by('request_date', 'desc');
            // $this->db->order_by('document_number', 'desc');

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


    public function getEmployeeHasAnnualLeave($employee_number, $type_leave) {
        if (isEmployeeContractActiveExist($employee_number)) {
            $kontrak_active = findContractActive($employee_number);
    
            $this->db->select('tb_employee_has_leave.*');
            $this->db->where('tb_employee_has_leave.employee_number', $employee_number);
            $this->db->where('tb_employee_has_leave.employee_contract_id', $kontrak_active['id']);
            $this->db->from('tb_employee_has_leave');
            $queryemployee_has_annual_leave = $this->db->get();
    
            if ($queryemployee_has_annual_leave->num_rows() > 0) {
                $row = $queryemployee_has_annual_leave->unbuffered_row('array');
    
                $return['status'] = 'success';
                $return['amount_leave'] = $row['amount_leave'];
                $return['used_leave'] = $row['used_leave'];
                $return['left_leave'] = $row['left_leave'];
                $return['employee_has_leave_id'] = $row['id'];
            } else {
                $return['status'] = 'warning';
                $return['amount_leave'] = 0;
                $return['used_leave'] = 0;
                $return['employee_has_leave_id'] = null;
                $return['kontrak'] = $kontrak_active['id'];
                $return['message'] = 'Karyawan ini tidak memiliki cuti yang tersisa pada kontrak aktif terakhir';
            }
    
            return $return;
        } else {
            $return['status'] = 'warning';
            $return['amount_leave'] = 0;
            $return['used_leave'] = 0;
            $return['employee_has_leave_id'] = null;
            $return['message'] = 'Karyawan ini tidak memiliki cuti yang tersisa pada kontrak aktif terakhir';
            return $return;
        }
    }

    public function save()
    {
        $this->db->trans_begin();
        // CHANGE OLD DATA
        if (isset($_SESSION['leave']['id'])) {

            $id             = $_SESSION['leave']['id'];
            $dataOld        = $this->findById($id);
            $get_leave      = getLeaveCodeById($dataOld['leave_type']);
            $get_leave_code = $get_leave['leave_code'];

            $this->db->set('status','REVISED');
            $this->db->where('id', $id);
            $this->db->update('tb_leave_requests');
            

            if($dataOld['status'] != 'REJECT'){
                if($get_leave_code == 'L01'){
                    $this->db->set('used_leave', 'used_leave - ' . $dataOld['total_leave_days'], FALSE);
                    $this->db->set('left_leave', 'left_leave + ' . $dataOld['total_leave_days'], FALSE);
                    $this->db->where('tb_employee_has_leave.id',  $_SESSION['leave']['employee_has_leave_id']);
                    $this->db->update('tb_employee_has_leave');
                }
            }
        }




        // CREATE NEW DOCUMENT
        $document_number            = sprintf('%06s', $_SESSION['leave']['document_number']) . $_SESSION['leave']['format_number'];
        $leave_start_date           = $_SESSION['leave']['leave_start_date'];
        $leave_end_date             = $_SESSION['leave']['leave_end_date'];
        $total_leave_days           = $_SESSION['leave']['total_leave_days'];
        $reason                     = $_SESSION['leave']['reason'];
        $leave_type                 = $_SESSION['leave']['leave_type'];
        $employee_number            = $_SESSION['leave']['employee_number'];
        $selected_person            = getEmployeeByEmployeeNumber($employee_number);
        $person_name                = $selected_person['name'];
        $warehouse                  = $_SESSION['leave']['warehouse'];
        $is_reserved                = $_SESSION['leave']['is_reserved'];
        $head_data                  = getEmployeeById($_SESSION['leave']['head_dept']);
        $head_dept                  = $head_data['employee_number'];
        $employee_has_leave_id      = $_SESSION['leave']['employee_has_leave_id'];
        $get_leave                  = getLeaveCodeById($leave_type);
        $get_leave_code             = $get_leave['leave_code'];
        $leave_type_name            = $get_leave['name_leave'];

        $status = "WAITING APPROVAL BY HEAD DEPT";

        if($is_reserved == 'yes'){
            $status = "DRAFT";
        } else {
            if($get_leave_code == "L01" || $get_leave_code == "L02" || $get_leave_code == "L03" || $get_leave_code == "L04" || $get_leave_code == "L05"){
                $status = "WAITING APPROVAL BY HEAD DEPT";
            } else if($get_leave_code == "L07"){
                $getLevel = getUserById($selected_person['user_id']);
                if($getLevel['auth_level'] == '3' || $getLevel['auth_level'] == '10'){
                    $status = "WAITING APPROVAL BY BOD";
                }
                $status = $warehouse == 'JAKARTA' ? "WAITING APPROVAL BY VP" : "WAITING APPROVAL BY HOS";
    
            } else if($get_leave_code == "L02"){
    
            } else if($get_leave_code == "L02"){
    
            } else if($get_leave_code == "L02"){
    
            } else if($get_leave_code == "L02"){
    
            }
        }





        $this->db->set('request_date', date('Y-m-d H:i:s'));
        $this->db->set('leave_start_date', $leave_start_date);
        $this->db->set('leave_end_date', $leave_end_date);
        $this->db->set('total_leave_days', $total_leave_days);
        $this->db->set('reason', $reason);
        $this->db->set('leave_type', $leave_type);
        $this->db->set('leave_type_name', $leave_type_name);
        $this->db->set('employee_number', $employee_number);
        $this->db->set('person_name', $person_name);
        $this->db->set('warehouse', $warehouse);
        $this->db->set('status', $status);
        $this->db->set('head_dept', $head_dept);
        $this->db->set('is_reserved', $is_reserved);
        $this->db->set('document_number', $document_number);
        $this->db->set('employee_has_leave_id', $employee_has_leave_id);

        
        $this->db->set('request_by', config_item('auth_person_name'));
        $this->db->insert('tb_leave_requests');
        $document_id = $this->db->insert_id();

        if(!empty($_SESSION['leave']['attachment'])){
            foreach ($_SESSION['leave']['attachment'] as $key) {
                $this->db->set('id_poe', $document_id);
                $this->db->set('id_po', $document_id);
                $this->db->set('file', $key);
                $this->db->set('tipe', 'LEAVE');
                $this->db->set('tipe_att', 'other');
                $this->db->insert('tb_attachment_poe');
            }

                    
            log_message('debug', 'Query Attachment: ' . $this->db->last_query());
            log_message('debug', 'Affected rows attachment: ' . $this->db->affected_rows());
        }

        $total = array();

        if($get_leave_code == 'L01'){
            $this->db->set('used_leave', 'used_leave + ' . $total_leave_days, FALSE);
            $this->db->set('left_leave', 'left_leave - ' . $total_leave_days, FALSE);
            $this->db->where('tb_employee_has_leave.id', $employee_has_leave_id);
            $this->db->update('tb_employee_has_leave');
        }

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        // $this->send_mail($document_id,'head_dept','request');

        $this->db->trans_commit();
        return TRUE;
    }

    public function findById($id)
    {
        $this->db->select('tb_leave_requests.*');
        $this->db->where('tb_leave_requests.id', $id);
        $this->db->from('tb_leave_requests');
        
        $query      = $this->db->get();
        $row        = $query->unbuffered_row('array');

        return $row;
    }

    public function listAttachment($id)
    {
        $this->db->where('id_poe', $id);
        $this->db->where('tipe', 'LEAVE');
        $this->db->where(array('deleted_at' => NULL));
        return $this->db->get('tb_attachment_poe')->result_array();
    }

    public function checkAttachment($id)
    {
        $this->db->where('id_poe', $id);
        $this->db->where('tipe', 'LEAVE');
        $this->db->where(array('deleted_at' => NULL));
		$this->db->from('tb_attachment_poe');
        $num_rows = $this->db->count_all_results();

		return $num_rows;
    }

    function add_attachment_to_db($id_poe, $url,$tipe_att='other')
    {
        $this->db->trans_begin();

        $this->db->set('id_poe', $id_poe);
        $this->db->set('id_po', $id_poe);
        $this->db->set('file', $url);
        $this->db->set('tipe', 'LEAVE');
        $this->db->set('tipe_att', $tipe_att);
        $this->db->insert('tb_attachment_poe');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    function delete_attachment_in_db($id_att)
    {
        $this->db->trans_begin();

        $this->db->set('deleted_at',date('Y-m-d'));
        $this->db->set('deleted_by', config_item('auth_person_name'));
        $this->db->where('id', $id_att);
        $this->db->update('tb_attachment_poe');

        if ($this->db->trans_status() === FALSE)
            return FALSE;

        $this->db->trans_commit();
        return TRUE;
    }

    public function sendLeavePlan($id)
    {
        $this->db->trans_begin();

        $selected = array(
            'tb_leave_requests.*',
        );
        $this->db->select($selected);
        $this->db->where('tb_leave_requests.id', $id);
    
        $query      = $this->db->get('tb_leave_requests')->row_array();

        $selected_person            = getEmployeeByEmployeeNumber($query['employee_number']);

        $get_leave                  = getLeaveCodeById($query['leave_type']);
        $get_leave_code             = $get_leave['leave_code'];
        $leave_type_name            = $get_leave['name_leave'];

        $status = "WAITING APPROVAL BY HEAD DEPT";

        if($get_leave_code == "L01" || $get_leave_code == "L02" || $get_leave_code == "L03" || $get_leave_code == "L04" || $get_leave_code == "L05"){
            $status = "WAITING APPROVAL BY HEAD DEPT";
        } else if($get_leave_code == "L07"){
            $getLevel = getUserById($selected_person['user_id']);
            if($getLevel['auth_level'] == '3' || $getLevel['auth_level'] == '10'){
                $status = "WAITING APPROVAL BY BOD";
            }
            $status = $warehouse == 'JAKARTA' ? "WAITING APPROVAL BY VP" : "WAITING APPROVAL BY HOS";

        } else if($get_leave_code == "L02"){

        } else if($get_leave_code == "L02"){

        } else if($get_leave_code == "L02"){

        } else if($get_leave_code == "L02"){

        }

        $this->db->set('status',$status);
        $this->db->set('is_reserved','no');
        $this->db->where('id', $id);
        $this->db->update('tb_leave_requests');

        // if ($this->db->trans_status() === FALSE)
        // return FALSE;

        // // $this->send_mail($document_id,'head_dept','request');

        // $this->db->trans_commit();
        // return TRUE;

        if ($this->db->trans_status() === FALSE)
        return ['status'=>FALSE,'document_number'=>$query['document_number']];

        $this->db->trans_commit();
        return ['status'=>TRUE,'document_number'=>$query['document_number']];

    }


    public function approve($document_id, $approval_notes)
    {
        $this->db->trans_begin();

        $total      = 0;
        $success    = 0;
        $failed     = sizeof($document_id);
        $x          = 0;
        $send_email_to = NULL;

        foreach ($document_id as $id) {
            $selected = array(
                'tb_leave_requests.*',
            );
            $this->db->select($selected);
            $this->db->where('tb_leave_requests.id', $id);
        
            $query      = $this->db->get('tb_leave_requests');
            $leave        = $query->unbuffered_row('array');

            $findDataPosition = findPositionByEmployeeNumber($leave['employee_number']);

            $this->db->set('status','APPROVED');
            $this->db->set('notes_approval', $approval_notes[$x]);
            $this->db->set('head_dept_approved_by',config_item('auth_person_name'));
            $this->db->where('id', $id);
            $this->db->update('tb_leave_requests');

            $this->db->set('document_type','LEAVE');
            $this->db->set('document_number',$leave['document_number']);
            $this->db->set('document_id', $id);
            $this->db->set('action','validated by');
            $this->db->set('date', date('Y-m-d'));
            $this->db->set('username', config_item('auth_username'));
            $this->db->set('person_name', config_item('auth_person_name'));
            $this->db->set('roles', config_item('auth_role'));
            $this->db->set('notes', $approval_notes[$x]);
            $this->db->set('sign', get_ttd(config_item('auth_person_name')));
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('tb_signers');
            $send_email_to = NULL;

            $approved_ids[] = $id;  // Add ID to the approved list

            $total++;
            $success++;
            $failed--;
            $x++;
            
        }

        

        if ($this->db->trans_status() === FALSE)
            return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];

        // if($send_email_to!=NULL){
        //     $this->send_mail($document_id, $send_email_to);
        // }

        

        $this->db->trans_commit();
        return $return = ['status'=> TRUE,'total'=>$total,'success'=>$success,'failed'=>$failed, 'approved_ids' => $approved_ids];
        
        

        
    }


    public function reject($document_id,$approval_notes)
    {
        $this->db->trans_begin();

        $total      = 0;
        $success    = 0;
        $failed     = sizeof($document_id);
        $x          = 0;
        $send_email_to = NULL;

        foreach ($document_id as $id) {
            $selected = array(
                'tb_leave_requests.*',
                
            );
            $this->db->select($selected);
            $this->db->where('tb_leave_requests.id', $id);
            
            $query      = $this->db->get('tb_leave_requests');
            $leave        = $query->unbuffered_row('array');

            $findDataPosition = findPositionByEmployeeNumber($leave['employee_number']);
            $get_leave                  = getLeaveCodeById($leave['leave_type']);
            $get_leave_code             = $get_leave['leave_code'];
            

            $this->db->set('status','REJECT');
            $this->db->set('rejected_by',config_item('auth_person_name'));
            $this->db->set('notes_approval', $approval_notes[$x]);
            $this->db->where('id', $id);
            $this->db->update('tb_leave_requests');

            $this->db->set('document_type','LEAVE');
            $this->db->set('document_number',$leave['document_number']);
            $this->db->set('document_id', $id);
            $this->db->set('action','validated by');
            $this->db->set('date', date('Y-m-d'));
            $this->db->set('username', config_item('auth_username'));
            $this->db->set('person_name', config_item('auth_person_name'));
            $this->db->set('roles', config_item('auth_role'));
            $this->db->set('notes', $approval_notes[$x]);
            $this->db->set('sign', get_ttd(config_item('auth_person_name')));
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('tb_signers');


            if($get_leave_code == 'L01'){
                $this->db->set('used_leave', 'used_leave - ' . $leave['total_leave_days'], FALSE);
                $this->db->set('left_leave', 'left_leave + ' . $leave['total_leave_days'], FALSE);
                $this->db->where('tb_employee_has_leave.id',  $leave['employee_has_leave_id']);
                $this->db->update('tb_employee_has_leave');
            }
            // $send_email_to = 'hr_manager';

            $total++;
            $success++;
            $failed--;
            $x++;
        }

        

        if ($this->db->trans_status() === FALSE)
            return $return = ['status'=> FALSE,'total'=>$total,'success'=>$success,'failed'=>$failed];

        // if($send_email_to!=NULL){
        //     $this->send_mail($document_id, $send_email_to);
        // }
        

        $this->db->trans_commit();
        return $return = ['status'=> TRUE,'total'=>$total,'success'=>$success,'failed'=>$failed];
    }

    


}
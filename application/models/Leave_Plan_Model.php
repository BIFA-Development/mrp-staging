<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_Plan_Model extends MY_Model
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
            'Name',
            'Employee Number',
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );
        return $return;
    }

    public function getSearchableColumns()
    {
        return array(
            'nama',
            'employee_number',
        );
    }

    function countIndexFiltered()
    {
        $this->db->from('tb_master_employees');

        $this->searchIndex();

        $query = $this->db->get();

        return $query->num_rows();
    }


    public function getOrderableColumns()
    {
        return array(
            null,
            'nama',
            'employee_number',
        );
    }

    public function countIndex()
    {
        $this->db->from('tb_master_employees');

        $query = $this->db->get();

        return $query->num_rows();
    }


    private function searchIndex()
    {
        if (!empty($_POST['columns'][1]['search']['value'])){
            $search_required_date = $_POST['columns'][1]['search']['value'];
            $range_date  = explode(' ', $search_required_date);

            $this->db->where('tb_leave_plan.request_date >= ', $range_date[0]);
            $this->db->where('tb_leave_plan.request_date <= ', $range_date[1]);
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

    public function findEmployeeBy($conditions)
    {

        $this->db->select(array(
            'tb_master_levels.level AS level_name',
            'tb_master_levels.id AS level_id',
            'tb_master_employees.*'
        ));
        $this->db->from('tb_master_employees');
        $this->db->join('tb_master_levels', 'tb_master_employees.level_id = tb_master_levels.id', 'left'); // Use LEFT JOIN
        $this->db->where($conditions); // Dynamic conditions

        $query = $this->db->get();
        $row = $query->unbuffered_row('array');

        if ($row) {
            $department = getDepartmentById($row['department_id']);
            $row['department_name'] = $department['department_name'];
        }

        return $row;
    }


    function getIndex($return = 'array')
    {
        
        $selected_person = getEmployeeById(config_item('auth_user_id'));
        $person_number   = $selected_person['employee_number'];
        $department_id   = $selected_person['department_id'];
        $dataEmployee    = getListEmployeeByDepartment($department_id);

        $this->db->select("
            tb_master_employees.name AS nama,
            tb_master_employees.employee_number AS employee_number,
            tb_master_employees.employee_id AS employee_id,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 1 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Januari,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 2 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Februari,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 3 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Maret,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 4 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS April,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 5 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Mei,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 6 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Juni,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 7 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Juli,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 8 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Agustus,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 9 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS September,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 10 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Oktober,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 11 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS November,
            STRING_AGG(CASE WHEN EXTRACT(MONTH FROM lp.leave_start_date) = 12 THEN TO_CHAR(lp.leave_start_date, 'DD') END, ', ') AS Desember
        ", false);

        $this->db->from('tb_master_employees'); // alias 'me' harus di sini
        $this->db->join(
            'tb_leave_plan AS lp',
            'lp.employee_number = tb_master_employees.employee_number AND EXTRACT(YEAR FROM lp.leave_start_date) = 2025',
            'left'
        );

        $this->db->group_by('tb_master_employees.name, tb_master_employees.employee_number');

        if(config_item('auth_role') == 'HR STAFF' || config_item('auth_role') == 'HR MANAGER') {
        //ALL

        } elseif(config_item('as_head_department')=='yes'){
            $this->db->where_in('tb_master_employees.employee_number', $dataEmployee);

        } else {
            $this->db->where('tb_master_employees.employee_number', $person_number);
        }


    
        
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
            $this->db->where('tb_employee_has_leave.leave_type', $type_leave);
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

    public function getEmployeeHasLongLeave($employee_number, $type_leave) {
        if (isEmployeeContractActiveExist($employee_number)) {
            $kontrak_active = findContractActive($employee_number);
    
            $this->db->select('tb_employee_has_leave.*');
            $this->db->where('tb_employee_has_leave.employee_number', $employee_number);
            $this->db->where('tb_employee_has_leave.employee_contract_id', $kontrak_active['id']);
            $this->db->where('tb_employee_has_leave.leave_type', $type_leave);
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
        if (isset($_SESSION['leave_plan']['id'])) {

            $id             = $_SESSION['leave_plan']['id'];
            $dataOld        = $this->findById($id);
            $get_leave      = getLeaveCodeById($dataOld['leave_type']);
            $get_leave_code = $get_leave['leave_code'];

            $this->db->set('status','REVISED');
            $this->db->where('id', $id);
            $this->db->update('tb_leave_plan');
        }




        // CREATE NEW DOCUMENT
        $document_number            = sprintf('%06s', $_SESSION['leave_plan']['document_number']) . $_SESSION['leave_plan']['format_number'];
        $leave_start_date           = $_SESSION['leave_plan']['leave_start_date'];
        $leave_end_date             = $_SESSION['leave_plan']['leave_end_date'];
        $total_leave_days           = $_SESSION['leave_plan']['total_leave_days'];
        $reason                     = $_SESSION['leave_plan']['reason'];
        $leave_type                 = $_SESSION['leave_plan']['leave_type'];
        $employee_number            = $_SESSION['leave_plan']['employee_number'];
        $selected_person            = getEmployeeByEmployeeNumber($employee_number);
        $person_name                = $selected_person['name'];
        $warehouse                  = $_SESSION['leave_plan']['warehouse'];
        $head_data                  = getEmployeeById($_SESSION['leave_plan']['head_dept']);
        $head_dept                  = $head_data['employee_number'];
        $employee_has_leave_id      = $_SESSION['leave_plan']['employee_has_leave_id'];
        $get_leave                  = getLeaveCodeById($leave_type);
        $get_leave_code             = $get_leave['leave_code'];
        $leave_type_name            = $get_leave['name_leave'];

        $status = "PLAN";






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
        $this->db->set('document_number', $document_number);
        $this->db->set('employee_has_leave_id', $employee_has_leave_id);

        
        $this->db->set('request_by', config_item('auth_person_name'));
        $this->db->insert('tb_leave_plan');
        $document_id = $this->db->insert_id();

        if(!empty($_SESSION['leave_plan']['attachment'])){
            foreach ($_SESSION['leave_plan']['attachment'] as $key) {
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


        if ($this->db->trans_status() === FALSE)
            return FALSE;

        // $this->send_mail($document_id,'head_dept','request');

        $this->db->trans_commit();
        return TRUE;
    }

    public function findById($id)
    {
        $this->db->select('tb_leave_plan.*');
        $this->db->where('tb_leave_plan.id', $id);
        $this->db->from('tb_leave_plan');
        
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

}
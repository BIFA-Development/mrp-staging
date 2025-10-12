<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leave_Plan extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['leave_plan'];
        $this->load->model($this->module['model'], 'model');
        $this->load->helper($this->module['helper']);
        $this->load->library('upload');
        $this->load->library('email');
        $this->data['module'] = $this->module;
    }
    public function index()
    {
        $this->authorized($this->module, 'index');

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = $this->model->getSelectedColumns();
        $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source');
        $this->data['grid']['fixed_columns']    = 1;
        $this->data['grid']['summary_columns']  = array(2);
        $this->data['grid']['order_columns']    = array();

        // $this->data['grid']['order_columns']    = array(

        //     0   => array( 0 => 0,  1 => '' ),
        //     1   => array( 0 => 1,  1 => '' ),
        //     2   => array( 0 => 2,  1 => '' ),
        //     3   => array( 0 => 3,  1 => '' ),
        //     4   => array( 0 => 4,  1 => '' ),
        //     5   => array( 0 => 5,  1 => '' ),
        //     6   => array( 0 => 6,  1 => 'desc' ),
        // );

        $this->render_view($this->module['view'] .'/index');
    }

    public function index_data_source()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'index') === FALSE){
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {


            $entities = $this->model->getIndex();
            $data     = array();
            $no       = $_POST['start'];

            foreach ($entities as $row){
                $no++;
                $col = array();
                
                
                $col[] = print_number($no);  
                $col[] = print_string($row['nama']);
                $col[] = print_string($row['employee_number']);
                $col[] = print_string($row['januari']);
                $col[] = print_string($row['februari']);
                $col[] = print_string($row['maret']);
                $col[] = print_string($row['april']);
                $col[] = print_string($row['mei']);
                $col[] = print_string($row['juni']);
                $col[] = print_string($row['juli']);
                $col[] = print_string($row['agustus']);
                $col[] = print_string($row['september']);
                $col[] = print_string($row['oktober']);
                $col[] = print_string($row['november']);
                $col[] = print_string($row['desember']);
                
                
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];
                
                if ($this->has_role($this->module, 'info')){
                    $col['DT_RowAttr']['onClick']     = '';
                    $col['DT_RowAttr']['data-id']     = $row['id'];
                    $col['DT_RowAttr']['onclick'] = "window.location.href='".site_url('profile/plan/'.$row['employee_id'])."'";
                    $col['DT_RowAttr']['style'] = 'cursor: pointer;';
                }

                $data[] = $col;
            }

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data"            => $data,
                "total"           => array()
            );
        }

        echo json_encode($result);
    }

   

    public function set_doc_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (empty($_GET['data']))
            $number = leave_last_number();
        else
            $number = $_GET['data'];

        $_SESSION['leave_plan']['document_number'] = $number;
    }

    public function set_request_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        
        $_SESSION['leave_plan']['request_date'] = date('Y-m-d', strtotime($_GET['data']));


    }

    public function set_leave_type()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave_plan']['leave_type'] = $_GET['data'];

        $get_leave                  = getLeaveCodeById($_GET['data']);
        $get_leave_code             = $get_leave['leave_code'];

        $_SESSION['leave_plan']['leave_code'] = $get_leave_code;

    }

    public function set_leave_start_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        
        $_SESSION['leave_plan']['leave_start_date'] = date('Y-m-d', strtotime($_GET['data']));

    }

    public function set_leave_end_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave_plan']['leave_end_date'] = date('Y-m-d', strtotime($_GET['data']));

    }

    public function set_total_leave_days()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave_plan']['total_leave_days'] = $_GET['data'];

    }


    public function set_head_dept()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['leave_plan']['head_dept'] = $_GET['data'];
    }


    public function set_is_reserved()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave_plan']['is_reserved'] = $_GET['data'];

    }

    public function set_reason()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave_plan']['reason'] = $_GET['data'];

    }

    public function set_employee_has_leave_id()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave_plan']['employee_has_leave_id'] = $_GET['data'];

    }

    public function set_warehouse()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['leave_plan']['warehouse'] = $_GET['data'];

    }

    public function set_employee_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['leave']['employee_number'] = $_GET['data'];
        $entityEmployee = $this->model->findEmployeeBy(array('tb_master_employees.employee_number' => $_GET['data']));
        $_SESSION['leave']['gender'] = $entityEmployee['gender'];
    }

    public function get_leave_type_list()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');
        
        $gender = $_GET['gender'];
        $id_leave_plan = $_GET['id_leave_plan'];


        $list = getLeaveType($gender,$id_leave_plan, FALSE);
        
        echo json_encode($list);
    }


    public function create($idnya = NULL)
    {
        $this->authorized($this->module, 'create');

        $_SESSION['leave_plan']['codenya'] = $idnya;
        
    
        if ($idnya !== NULL) {
            $_SESSION['leave_plan'] = array();


            
            $employee_id  = config_item('auth_user_id');
            $employee = findEmployeeByUserId($employee_id);
            $kontrak_active = findContractActive($employee['employee_number']);
            $department = getDepartmentById($employee['department_id']);
            $holidays = getHolidays();

            $_SESSION['leave_plan']['document_number']           = leave_plan_last_number();
            $_SESSION['leave_plan']['format_number']             = leave_plan_format_number();
            $_SESSION['leave_plan']['employee_number']           = $employee['employee_number'];
            $_SESSION['leave_plan']['contract_number']           = $kontrak_active['contract_number'];
            $_SESSION['leave_plan']['start_contract']            = print_date($kontrak_active['start_date'], 'd M Y');
            $_SESSION['leave_plan']['end_contract']              = print_date($kontrak_active['end_date'], 'd M Y');
            $_SESSION['leave_plan']['department_name']           = $department['department_name'];
            $_SESSION['leave_plan']['department_id']             = $employee['department_id'];
            $_SESSION['leave_plan']['holidays']                  = $holidays;
    
            $_SESSION['leave_plan']['request_date']              = NULL;
            $_SESSION['leave_plan']['leave_start_date']          = NULL;
            $_SESSION['leave_plan']['leave_end_date']            = NULL;
            $_SESSION['leave_plan']['total_leave_days']          = NULL;
            $_SESSION['leave_plan']['reason']                    = NULL;
            $_SESSION['leave_plan']['leave_type']                = NULL;
            $_SESSION['leave_plan']['type_leave']                = NULL;
            $_SESSION['leave_plan']['leave_type_name']           = NULL;
            $_SESSION['leave_plan']['warehouse']                 = NULL;
            $_SESSION['leave_plan']['employee_has_leave_id']     = NULL;
            $_SESSION['leave_plan']['is_reserved']               = NULL;
            $_SESSION['leave_plan']['head_dept']                 = NULL;
            $_SESSION['leave_plan']['attachment']                    = array();
        }
    
        if (!isset($_SESSION['leave_plan'])) {
            redirect($this->module['route']);
        }
    
        $this->data['page']['content']   = $this->module['view'] . '/create';
        $this->data['page']['offcanvas'] = $this->module['view'] . '/create_offcanvas_add_item';
    
        $this->render_view($this->module['view'] . '/create');
    }


    public function edit($id)
    {
        $this->authorized($this->module, 'create');

        $entity = $this->model->findById($id);

        $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
        $format_number      = substr($entity['document_number'], 6, 25);
        $holidays = getHolidays();

        $employee = findDepartmentByEmployeeNumber($entity['employee_number']);
        $department = getDepartmentById($employee['department_id']);

        $kontrak_active = findContractActive($entity['employee_number']);


        if (preg_match('/-R(\d+)/', $entity['document_number'], $matches)) {
            $current_revision = intval($matches[1]); // Ambil angka revisi terakhir
            $revisi = $current_revision + 1; // Tambah revisi berikutnya
            $document_number = str_replace('-R' . $current_revision, '', $document_number); // Hapus revisi sebelumnya
        } else {
            $revisi = 1; // Jika belum ada revisi, mulai dari 1
        }
    
        $new_document_number = $document_number . '-R' . $revisi;

        $_SESSION['leave_plan']                              = $entity;
        $_SESSION['leave_plan']['id']                        = $id;
        $_SESSION['leave_plan']['edit']                      = $entity['document_number'];
        $_SESSION['leave_plan']['document_number']           = $new_document_number;
        $_SESSION['leave_plan']['format_number']             = $format_number;
        $_SESSION['leave_plan']['holidays']                  = $holidays;
        $_SESSION['leave_plan']['employee_number']           = $entity['employee_number'];
        $_SESSION['leave_plan']['department_id']             = $employee['department_id'];
        $_SESSION['leave_plan']['department_name']           = $department['department_name'];
        $_SESSION['leave_plan']['leave_start_date']           = print_date($entity['leave_start_date'], 'd-m-Y');
        $_SESSION['leave_plan']['leave_end_date']           = print_date($entity['leave_end_date'], 'd-m-Y');
        $_SESSION['leave_plan']['start_contract']            = print_date($kontrak_active['start_date'], 'd M Y');
        $_SESSION['leave_plan']['end_contract']              = print_date($kontrak_active['end_date'], 'd M Y');


        redirect($this->module['route'] .'/create');
    }

    public function info($id)
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'info') === FALSE){
            $return['type'] = 'denied';
            $return['info'] = "You don't have permission to access this data. You may need to login again.";
        } else {
            $entity = $this->model->findById($id);

            $this->data['entity'] = $entity;

            $return['type'] = 'success';
            $return['info'] = $this->load->view($this->module['view'] .'/info', $this->data, TRUE);
        }

        echo json_encode($return);
    }


    public function get_annual_leave()
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //     redirect($this->modules['secure']['route'] .'/denied');
        

        $employee_number = $_GET['employee_number'];
        $type = $_GET['type'];

        $employee_has_leave = $this->model->getEmployeeHasAnnualLeave($employee_number,$type);
        
        echo json_encode($employee_has_leave);
    }

    public function get_long_leave()
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //     redirect($this->modules['secure']['route'] .'/denied');
        

        $employee_number = $_GET['employee_number'];
        $type = $_GET['type'];

        $employee_has_leave = $this->model->getEmployeeHasLongLeave($employee_number,$type);
        
        echo json_encode($employee_has_leave);
    }

    public function get_contract_period()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $employee_number = $_GET['employee_number'];
        $leave_type = $_GET['leave_type'];

        $return = array();

        // Check if it's annual leave (L01)
        $get_leave = getLeaveCodeById($leave_type);
        $get_leave_code = $get_leave['leave_code'];

        if ($get_leave_code == 'L01') {
            if (isEmployeeContractActiveExist($employee_number)) {
                $kontrak_active = findContractActive($employee_number);
                
                // Get annual leave usage data
                $annual_leave_data = $this->model->getAnnualLeaveUsage($employee_number, $kontrak_active['start_date'], $kontrak_active['end_date']);
                
                // Get annual leave quota if available
                $annual_leave_quota = $this->model->getEmployeeHasAnnualLeave($employee_number, 1); // 1 is typically annual leave type
                
                $return['status'] = 'success';
                $return['contract_number'] = $kontrak_active['contract_number'];
                $return['start_date'] = print_date($kontrak_active['start_date'], 'd M Y');
                $return['end_date'] = print_date($kontrak_active['end_date'], 'd M Y');
                $return['contract_period'] = print_date($kontrak_active['start_date'], 'd M Y') . ' s/d ' . print_date($kontrak_active['end_date'], 'd M Y');
                $return['total_annual_leave_used'] = $annual_leave_data['total_used'];
                $return['annual_leave_details'] = $annual_leave_data['details'];
                
                // Add quota information if available and extended period info
                $return['extended_period'] = $annual_leave_data['extended_period'];
                
                if ($annual_leave_quota['status'] == 'success') {
                    $return['annual_leave_quota'] = $annual_leave_quota['amount_leave'];
                    // Calculate real-time remaining leave: quota - actual usage from leave plans
                    $return['annual_leave_remaining'] = $annual_leave_quota['amount_leave'] - $annual_leave_data['total_used'];
                    // Ensure it doesn't go below zero
                    if ($return['annual_leave_remaining'] < 0) {
                        $return['annual_leave_remaining'] = 0;
                    }
                    $return['message'] = 'Contract period: ' . $return['contract_period'];
                } else {
                    $return['annual_leave_quota'] = 0;
                    $return['annual_leave_remaining'] = 0;
                    $return['message'] = 'Contract period: ' . $return['contract_period'];
                }
            } else {
                $return['status'] = 'error';
                $return['contract_number'] = '';
                $return['start_date'] = '';
                $return['end_date'] = '';
                $return['contract_period'] = '';
                $return['total_annual_leave_used'] = 0;
                $return['annual_leave_details'] = array();
                $return['annual_leave_quota'] = 0;
                $return['annual_leave_remaining'] = 0;
                $return['message'] = 'Karyawan ini tidak memiliki kontrak aktif. Silakan hubungi HR untuk mengaktifkan kontrak terlebih dahulu sebelum membuat rencana cuti tahunan.';
            }
        } else {
            $return['status'] = 'info';
            $return['contract_number'] = '';
            $return['start_date'] = '';
            $return['end_date'] = '';
            $return['contract_period'] = '';
            $return['total_annual_leave_used'] = 0;
            $return['annual_leave_details'] = array();
            $return['message'] = 'Contract period validation only applies to annual leave';
        }

        echo json_encode($return);
    }

    public function sendLeavePlan()
    {
        if ($this->input->is_ajax_request() === FALSE)
        redirect($this->modules['secure']['route'] .'/denied');

        $id = $this->input->post('id');


        if (is_granted($this->module, 'create') == FALSE){
            $alert['success'] = FALSE;
            $alert['message'] = 'You are not allowed to save this Document!';
        } else {

            $entity = $this->model->findById($id);
            $errors = array();
            $kontrak_active = findContractActive($entity['employee_number']);

            $get_leave                  = getLeaveCodeById($entity['leave_type']);
            $get_leave_code             = $get_leave['leave_code'];

            // Check date range conflict before sending leave plan
            $date_conflict = $this->checkDateRangeConflict($entity['employee_number'], $entity['leave_start_date'], $entity['leave_end_date'], $id);
            
            if (!empty($date_conflict)) {
                $errors[] = 'Range tanggal cuti sudah pernah diminta pada periode yang sama. Silakan pilih tanggal yang berbeda.';
            }

            if($get_leave_code == 'L01'){
                $start_contract = new DateTime($kontrak_active['start_date']);
                $end_contract = new DateTime($kontrak_active['end_date']);
                $leave_start_date = new DateTime($entity['leave_start_date']);
                $leave_end_date = new DateTime($entity['leave_end_date']);
            
                // Cek apakah tanggal cuti di luar rentang kontrak
                if ($leave_start_date < $start_contract || $leave_end_date > $end_contract) {
                    if($entity['is_reserved'] != 'yes'){
                        $errors[] = "ID Kontrak".$id;
                        $errors[] = "Start Kontrak {$start_contract->format('Y-m-d')}";
                        $errors[] = "Tanggal permintaan cuti tahunan harus berada dalam masa kontrak: {$start_contract->format('Y-m-d')} s.d. {$end_contract->format('Y-m-d')}.";
                    }
                }
            }

            if (!empty($errors)){
                $alert['type'] = 'danger';
                $alert['info'] = implode('<br />', $errors);
            } else {
                $leave = $this->model->sendLeavePlan($id);
                if ($leave['status']){
                    $alert['type'] = 'success';
                    $alert['info'] = 'Leave has been create #'.$leave['document_number'];
                    $alert['link'] = site_url($this->module['route']);
                } else {
                    $alert['type'] = 'danger';
                    $alert['info'] = 'There are error while creating data. Please try again later.';
                }
    
            }
            
           
            echo json_encode($alert);
        }
    }

    public function save()
    {
        
        if ($this->input->is_ajax_request() == FALSE)
            redirect($this->modules['secure']['route'] . '/denied');


        if (is_granted($this->module, 'create') == FALSE){
            $data['success'] = FALSE;
            $data['message'] = 'You are not allowed to save this Document!';
        } else {
            $errors = array();

            $document_number = $_SESSION['leave_plan']['document_number'] . $_SESSION['leave_plan']['format_number'];

            

            if ($_SESSION['leave_plan']['reason']==NULL || $_SESSION['leave_plan']['reason']=='') {
                $errors[] = 'Attention!! Please Fill Notes !!';
            }


            
            $get_leave                  = getLeaveCodeById($_SESSION['leave_plan']['leave_type']);
            $get_leave_code             = $get_leave['leave_code'];

          

            if ($_SESSION['leave_plan']['warehouse']==NULL || $_SESSION['leave_plan']['warehouse']=='') {
                $errors[] = 'Attention!! Please Fill Warehouse!!';
            }


            if ($_SESSION['leave_plan']['leave_type']==NULL || $_SESSION['leave_plan']['leave_type']=='') {
                $errors[] = 'Attention!! Please Fill Leave Type!!'. $_SESSION['leave_plan']['leave_type'].'-'.$_SESSION['leave_plan']['type_leave'];
            }

            // Validate date range conflict for all leave types (except if it's an edit and same date range)
            $employee_number = $_SESSION['leave_plan']['employee_number'];
            $leave_start_date = $_SESSION['leave_plan']['leave_start_date'];
            $leave_end_date = $_SESSION['leave_plan']['leave_end_date'];
            
            if ($leave_start_date && $leave_end_date && $employee_number) {
                $current_id = isset($_SESSION['leave_plan']['id']) ? $_SESSION['leave_plan']['id'] : null;
                $date_conflict = $this->checkDateRangeConflict($employee_number, $leave_start_date, $leave_end_date, $current_id);
                
                if (!empty($date_conflict)) {
                    $errors[] = 'Range tanggal cuti sudah pernah diminta pada periode yang sama. Silakan pilih tanggal yang berbeda atau hubungi HR jika ada keperluan khusus.';
                }
            }

            // Additional validation for annual leave (L01)
            if ($get_leave_code == 'L01') {
                
                // Check if employee has active contract
                if (isEmployeeContractActiveExist($employee_number)) {
                    $kontrak_active = findContractActive($employee_number);
                    
                    // Validate date range is within contract period
                    $contract_start = new DateTime($kontrak_active['start_date']);
                    $contract_end = new DateTime($kontrak_active['end_date']);
                    $start_date = new DateTime($leave_start_date);
                    $end_date = new DateTime($leave_end_date);
                    
                    if ($start_date < $contract_start || $end_date > $contract_end) {
                        $errors[] = 'Tanggal cuti harus berada dalam periode kontrak aktif: ' . 
                                   print_date($kontrak_active['start_date'], 'd M Y') . ' s/d ' . 
                                   print_date($kontrak_active['end_date'], 'd M Y');
                    }
                    
                    // Validate total leave days doesn't exceed remaining quota
                    $employee_has_leave = $this->model->getEmployeeHasAnnualLeave($employee_number, $_SESSION['leave_plan']['leave_type']);
                    if ($employee_has_leave['status'] == 'success') {
                        $total_leave_days = intval($_SESSION['leave_plan']['total_leave_days']);
                        
                        if ($total_leave_days > $employee_has_leave['left_leave']) {
                            $errors[] = 'Total hari cuti (' . $total_leave_days . ' hari) melebihi sisa kuota cuti tahunan (' . 
                                       $employee_has_leave['left_leave'] . ' hari). Silakan kurangi jumlah hari cuti.';
                        }
                    }
                } else {
                    $errors[] = 'Karyawan tidak memiliki kontrak aktif. Silakan hubungi HR untuk mengaktifkan kontrak terlebih dahulu.';
                }
            }

            

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['leave_plan']);
        
                    $data['success'] = TRUE;
                    $data['message'] = 'Document '. $document_number .' has been saved. You will redirected now.';
                } else {
                    $data['success'] = FALSE;
                    $data['message'] = 'Error while saving this document. Please ask Technical Support.';
                }
            }
        }

        echo json_encode($data);
    }

    /**
     * Check if the given date range conflicts with existing leave plans (excluding rejected ones)
     * 
     * @param string $employee_number Employee number
     * @param string $start_date Leave start date (Y-m-d format)
     * @param string $end_date Leave end date (Y-m-d format)
     * @param int|null $exclude_id ID to exclude from check (for edit operations)
     * @return array Array of conflicting leave plans
     */
    private function checkDateRangeConflict($employee_number, $start_date, $end_date, $exclude_id = null)
    {
        $this->db->select('lp.id, lp.document_number, lp.leave_start_date, lp.leave_end_date, lp.status, lt.name_leave as leave_type_name');
        $this->db->from('tb_leave_plan lp');
        $this->db->join('tb_leave_type lt', 'lt.id = lp.leave_type', 'left');
        $this->db->where('lp.employee_number', $employee_number);
        $this->db->where('lp.status !=', 'reject'); // Exclude rejected requests
        
        // Date range overlap check: (start1 <= end2) AND (start2 <= end1)
        $this->db->group_start();
            $this->db->where('lp.leave_start_date <=', $end_date);
            $this->db->where('lp.leave_end_date >=', $start_date);
        $this->db->group_end();
        
        // Exclude current record if editing
        if ($exclude_id) {
            $this->db->where('lp.id !=', $exclude_id);
        }
        
        $this->db->order_by('lp.leave_start_date', 'ASC');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return array();
    }

    public function attachment()
    {
        $this->authorized($this->module, 'create');

        $this->render_view($this->module['view'] . '/attachment');
    }

    public function add_attachment()
    {
        $result["status"] = 0;
        $date = new DateTime();
        $config['upload_path'] = 'attachment/leaves/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
            $error = array('error' => $this->upload->display_errors());
        } else {

            $data = array('upload_data' => $this->upload->data());
            $url = $config['upload_path'] . $data['upload_data']['file_name'];
            array_push($_SESSION["leave"]["attachment"], $url);
            $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function manage_attachment($id)
    {
        $this->authorized($this->module, 'info');

        $this->data['manage_attachment'] = $this->model->listAttachment($id);
        $this->data['id'] = $id;
        $this->render_view($this->module['view'] . '/manage_attachment');
    }

    public function add_attachment_to_db($id)
    {
        $result["status"] = 0;
        $date = new DateTime();
        $config['upload_path'] = 'attachment/leaves/';
        $config['allowed_types'] = 'jpg|png|jpeg|doc|docx|xls|xlsx|pdf';
        $config['max_size']  = 2000;

        $this->upload->initialize($config);

        if (!$this->upload->do_upload('attachment')) {
            $error = array('error' => $this->upload->display_errors());
            $result["status"] = $error;
        } else {
            $data = array('upload_data' => $this->upload->data());
            $url = $config['upload_path'] . $data['upload_data']['file_name'];
            // array_push($_SESSION["poe"]["attachment"], $url);
            $this->model->add_attachment_to_db($id, $url);
            $result["status"] = 1;
        }
        echo json_encode($result);
    }

    public function delete_attachment_in_db($id_att, $id_poe)
    {
        $this->model->delete_attachment_in_db($id_att);

        redirect($this->module['route'] . "/manage_attachment/" . $id_poe, 'refresh');
        // echo json_encode($result);
    }

    public function multi_approve()
    {
        $document_id = $this->input->post('document_id');
        $document_id = str_replace("|", "", $document_id);
        $document_id = substr($document_id, 0, -1);
        $document_id = explode(",", $document_id);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($document_id);
        $x = 0;

        
        $save_approval = $this->model->approve($document_id, $notes);
        if ($save_approval['status']) {
                if(!empty($save_approval['approved_ids'])){
                    $this->session->set_flashdata('alert', array(
                        'type' => 'success',
                        'info' => $save_approval['success'] . " leave has been approve!"
                    ));
                } else {
                    $this->session->set_flashdata('alert', array(
                        'type' => 'success',
                        // 'info' => $save_approval['success'] . " data has been update!"
                        'info' => "Data has been update!"

                    ));
                }
        }else{
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $save_approval['failed'] . " rejected"
            ));
        }
        
        if ($save_approval['status']) {
            $result['status'] = 'success';
        } else {
            $result['status'] = 'failed';
        }
        echo json_encode($result);
    }

    public function multi_reject()
    {
        $document_id = $this->input->post('document_id');
        $document_id = str_replace("|", "", $document_id);
        $document_id = substr($document_id, 0, -1);
        $document_id = explode(",", $document_id);

        $str_notes = $this->input->post('notes');
        $notes = str_replace("|", "", $str_notes);
        $notes = substr($notes, 0, -3);
        $notes = explode("##,", $notes);

        $total = 0;
        $success = 0;
        $failed = sizeof($document_id);
        $x = 0;

        $save_approval = $this->model->reject($document_id, $notes);
        if ($save_approval['status']) {
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => "Data has been update!"
            ));
        }else{
            $this->session->set_flashdata('alert', array(
                'type' => 'danger',
                'info' => "There are " . $save_approval['failed'] . " rejected"
            ));
        }
        

        if ($success > 0) {
            // $this->model->send_mail_approval($id_expense_request, 'approve', config_item('auth_person_name'),$notes);
            $this->session->set_flashdata('alert', array(
                'type' => 'success',
                'info' => $success . " data has been update!"
            ));
        }
        if ($failed > 0) {
            $this->session->set_flashdata('alert', array(
                // 'type' => 'danger',
                'type' => 'success',
                'info' => $success . " data has been update!"
                // 'info' => "There are " . $failed . " errors"
            ));
        }
        
        if ($save_approval['total'] == 0) {
            $result['status'] = 'failed';
        } else {
            $result['status'] = 'success';
        }
        echo json_encode($result);
    }

    
}

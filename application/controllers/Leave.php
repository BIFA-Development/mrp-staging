<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Leave extends MY_Controller
{
    protected $module;

    public function __construct()
    {
        parent::__construct();

        $this->module = $this->modules['leave'];
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

        $this->data['grid']['order_columns']    = array(

            0   => array( 0 => 0,  1 => '' ),
            1   => array( 0 => 1,  1 => '' ),
            2   => array( 0 => 2,  1 => '' ),
            3   => array( 0 => 3,  1 => '' ),
            4   => array( 0 => 4,  1 => '' ),
            5   => array( 0 => 5,  1 => '' ),
            6   => array( 0 => 6,  1 => 'desc' ),
        );

        $this->render_view($this->module['view'] .'/index');
    }

    public function reporting()
    {
        $this->authorized($this->module, 'index_reporting');

        $this->data['page']['title']            = $this->module['label'];
        $this->data['grid']['column']           = $this->model->getSelectedColumns();
        $this->data['grid']['data_source']      = site_url($this->module['route'] .'/index_data_source_reporting');
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

        $this->render_view($this->module['view'] .'/reporting/index');
    }

     public function index_data_source_reporting()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        if (is_granted($this->module, 'index') === FALSE){
            $return['type'] = 'danger';
            $return['info'] = "You don't have permission to access this page!";
        } else {


            $entities = $this->model->getIndexReporting();
            $data     = array();
            $no       = $_POST['start'];

            foreach ($entities as $row){
                $no++;
                $col = array();
                
                
                if (is_granted($this->module, 'approval')){
                    if($row['status']=='WAITING APPROVAL BY HEAD DEPT' && $row['head_dept']== getEmployeeById(config_item('auth_user_id'))['employee_number'] || config_item('auth_role') == 'HEAD OF SCHOOL'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY HOS' && config_item('auth_role') == 'HEAD OF SCHOOL'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY VP' && config_item('auth_role') == 'VP FINANCE'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY HR' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY BOD' && config_item('auth_role') == 'CHIEF OF FINANCE'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY BOD' && config_item('auth_role') == 'CHIEF OPERATION OFFICER'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='REVISED'){
                        $col[] = print_number($no);
                    } else {
                        $col[] = print_number($no);
                    }
                }else{
                    $col[] = print_number($no);
                } 
                $col[] = print_date($row['request_date'], 'd F Y');    
                $col[] = print_string($row['document_number']);
                $col[] = print_string($row['person_name']);
                $col[] = print_string($row['leave_type_name']);
                $col[] = print_string($row['status']);
                if($row['ref_leave_plan_number']!=''){
                    $col[] = print_string($row['ref_leave_plan_number']);
                } else {
                    $col[] = print_string('- ');
                }
                if($row['status']=='approved' || $row['status']=='closed'){
                    $col[] = print_string($row['notes_approval']);
                }else{
                    if($row['notes_approval'] != ''){
                        if (is_granted($this->module, 'approval') === TRUE) {
                            $col[] = '<input type="text" id="note_' . $row['id'] . '" value="' . $row['notes_approval'] . '" autocomplete="off"/>';
                        } else {
                            $col[] = print_string($row['notes_approval']);
                        }
                    } else {
                        if (is_granted($this->module, 'approval') === TRUE) {
                            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                        } else {
                            $col[] = print_string($row['notes_approval']);
                        }
                    }
                    
                }
                // if($row['is_reserved'] == TRUE){
                //     $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary">'.$row['id'].'</a>';
                // } else {
                //     $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary">'.$row['id'].'</a>';
                //     // $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary" disabled>Edit</a>';
                // }
                

                
                
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];
                
                if ($this->has_role($this->module, 'info')){
                    $col['DT_RowAttr']['onClick']     = '';
                    $col['DT_RowAttr']['data-id']     = $row['id'];
                    $col['DT_RowAttr']['data-target'] = '#data-modal';
                    $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
                }

                $data[] = $col;
            }

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data"            => $data,
                "total"           => array(
                )
            );
        }

        echo json_encode($result);
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
                
                
                if (is_granted($this->module, 'approval')){
                    if($row['status']=='WAITING APPROVAL BY HEAD DEPT' && $row['head_dept']== getEmployeeById(config_item('auth_user_id'))['employee_number']){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY HEAD DEPT' && config_item('auth_role') == 'HEAD OF SCHOOL'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY HOS' && config_item('auth_role') == 'HEAD OF SCHOOL'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY VP' && config_item('auth_role') == 'VP FINANCE'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY HR' && in_array(config_item('auth_username'),list_username_in_head_department(11))){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY BOD' && config_item('auth_role') == 'CHIEF OF FINANCE'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='WAITING APPROVAL BY BOD' && config_item('auth_role') == 'CHIEF OPERATION OFFICER'){
                        $col[] = '<input type="checkbox" id="cb_' . $row['id'] . '"  data-id="' . $row['id'] . '" name="" style="display: inline;">';
                    } else if($row['status']=='REVISED'){
                        $col[] = print_number($no);
                    } else {
                        $col[] = print_number($no);
                    }
                }else{
                    $col[] = print_number($no);
                } 
                $col[] = print_date($row['request_date'], 'd F Y');    
                $col[] = print_string($row['document_number']);
                $col[] = print_string($row['person_name']);
                $col[] = print_string($row['leave_type_name']);
                $col[] = print_string($row['status']);
                if($row['ref_leave_plan_number']!=''){
                    $col[] = print_string($row['ref_leave_plan_number']);
                } else {
                    $col[] = print_string('- ');
                }
                if($row['status']=='approved' || $row['status']=='closed'){
                    $col[] = print_string($row['notes_approval']);
                }else{
                    if($row['notes_approval'] != ''){
                        if (is_granted($this->module, 'approval') === TRUE) {
                            $col[] = '<input type="text" id="note_' . $row['id'] . '" value="' . $row['notes_approval'] . '" autocomplete="off"/>';
                        } else {
                            $col[] = print_string($row['notes_approval']);
                        }
                    } else {
                        if (is_granted($this->module, 'approval') === TRUE) {
                            $col[] = '<input type="text" id="note_' . $row['id'] . '" autocomplete="off"/>';
                        } else {
                            $col[] = print_string($row['notes_approval']);
                        }
                    }
                    
                }
                // if($row['is_reserved'] == TRUE){
                //     $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary">'.$row['id'].'</a>';
                // } else {
                //     $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary">'.$row['id'].'</a>';
                //     // $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary" disabled>Edit</a>';
                // }
                

                
                
                $col['DT_RowId'] = 'row_'. $row['id'];
                $col['DT_RowData']['pkey']  = $row['id'];
                
                if ($this->has_role($this->module, 'info')){
                    $col['DT_RowAttr']['onClick']     = '';
                    $col['DT_RowAttr']['data-id']     = $row['id'];
                    $col['DT_RowAttr']['data-target'] = '#data-modal';
                    $col['DT_RowAttr']['data-source'] = site_url($this->module['route'] .'/info/'. $row['id']);
                }

                $data[] = $col;
            }

            $result = array(
                "draw"            => $_POST['draw'],
                "recordsTotal"    => $this->model->countIndex(),
                "recordsFiltered" => $this->model->countIndexFiltered(),
                "data"            => $data,
                "total"           => array(
                )
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

        $_SESSION['leave']['document_number'] = $number;
    }

    public function set_request_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        
        $_SESSION['leave']['request_date'] = date('Y-m-d', strtotime($_GET['data']));


    }

    public function set_leave_type()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave']['leave_type'] = $_GET['data'];

        $get_leave                  = getLeaveCodeById($_GET['data']);
        $get_leave_code             = $get_leave['leave_code'];

        $_SESSION['leave']['leave_code'] = $get_leave_code;

    }

    public function set_leave_start_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        
        $_SESSION['leave']['leave_start_date'] = date('Y-m-d', strtotime($_GET['data']));

    }

    public function set_leave_end_date()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave']['leave_end_date'] = date('Y-m-d', strtotime($_GET['data']));

    }

    public function set_total_leave_days()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave']['total_leave_days'] = $_GET['data'];

    }


    public function set_head_dept()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['leave']['head_dept'] = $_GET['data'];
    }

    public function set_id_leave_plan()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['leave']['id_leave_plan'] = $_GET['data'];
    }


    public function set_is_reserved()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave']['is_reserved'] = $_GET['data'];

    }

    public function set_reason()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave']['reason'] = $_GET['data'];

    }

    public function set_employee_has_leave_id()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');


        $_SESSION['leave']['employee_has_leave_id'] = $_GET['data'];

    }

    public function set_warehouse()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['leave']['warehouse'] = $_GET['data'];

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
        $employee_number = $_GET['employee_number'];


        $list = getLeaveType($gender,$id_leave_plan, FALSE, $employee_number);

        echo json_encode($list);
    }


    public function create($idnya = NULL, $planId = NULL)
    {
        $this->authorized($this->module, 'create');

        $_SESSION['leave']['codenya'] = $idnya;
        
    
        if ($idnya !== NULL) {
            $_SESSION['leave'] = array();


            
            $employee_id  = config_item('auth_user_id');
            $employee = findEmployeeByUserId($employee_id);
            $kontrak_active = findContractActive($employee['employee_number']);
            $department = getDepartmentById($employee['department_id']);
            $holidays = getHolidays();

            $_SESSION['leave']['document_number']           = leave_last_number();
            $_SESSION['leave']['format_number']             = leave_format_number();
            $_SESSION['leave']['employee_number']           = $employee['employee_number'];
            $_SESSION['leave']['contract_number']           = $kontrak_active['contract_number'];
            $_SESSION['leave']['employee_contract_id']      = $kontrak_active['id'];
            $_SESSION['leave']['start_contract']            = print_date($kontrak_active['start_date'], 'd M Y');
            $_SESSION['leave']['end_contract']              = print_date($kontrak_active['end_date'], 'd M Y');
            $_SESSION['leave']['department_name']           = $department['department_name'];
            $_SESSION['leave']['department_id']             = $employee['department_id'];
            $_SESSION['leave']['holidays']                  = $holidays;
    
            $_SESSION['leave']['request_date']              = NULL;
            $_SESSION['leave']['leave_start_date']          = NULL;
            $_SESSION['leave']['leave_end_date']            = NULL;
            $_SESSION['leave']['total_leave_days']          = NULL;
            $_SESSION['leave']['reason']                    = NULL;
            $_SESSION['leave']['leave_type']                = NULL;
            $_SESSION['leave']['type_leave']                = NULL;
            $_SESSION['leave']['leave_type_name']           = NULL;
            $_SESSION['leave']['warehouse']                 = NULL;
            $_SESSION['leave']['employee_has_leave_id']     = NULL;
            $_SESSION['leave']['head_dept']                 = NULL;
            $_SESSION['leave']['attachment']                    = array();
        }

        if($idnya !== NULL && $planId !== NULL){
            $entity = $this->model->findLeavePlanById($planId);
            $employee_id  = config_item('auth_user_id');
            $employee = findEmployeeByUserId($employee_id);
            $kontrak_active = findContractActive($employee['employee_number']);
            $department = getDepartmentById($employee['department_id']);
            $holidays = getHolidays();
           


            $_SESSION['leave'] = array();
            $_SESSION['leave']['document_number']           = leave_last_number();
            $_SESSION['leave']['format_number']             = leave_format_number();
            $_SESSION['leave']['employee_number']           = $entity['employee_number'];
            $_SESSION['leave']['employee_contract_id']      = $kontrak_active['id'];
            $_SESSION['leave']['contract_number']           = $kontrak_active['contract_number'];
            $_SESSION['leave']['start_contract']            = print_date($kontrak_active['start_date'], 'd M Y');
            $_SESSION['leave']['end_contract']              = print_date($kontrak_active['end_date'], 'd M Y');
            $_SESSION['leave']['department_name']           = $department['department_name'];
            $_SESSION['leave']['department_id']             = $employee['department_id'];
            $_SESSION['leave']['holidays']                  = $holidays;
    
            $_SESSION['leave']['request_date']              = $entity['request_date'];
            $_SESSION['leave']['leave_start_date']          = $entity['leave_start_date'];
            $_SESSION['leave']['leave_end_date']            = $entity['leave_end_date'];
            $_SESSION['leave']['total_leave_days']          = $entity['total_leave_days'];
            $_SESSION['leave']['reason']                    = $entity['reason'];
            $_SESSION['leave']['leave_type']                = $entity['leave_type'];
            $_SESSION['leave']['type_leave']                = NULL;
            $_SESSION['leave']['leave_type_name']           = NULL;
            $_SESSION['leave']['warehouse']                 = $entity['warehouse'];
            $_SESSION['leave']['employee_has_leave_id']     = $entity['employee_has_leave_id'];
            $_SESSION['leave']['head_dept']                 = $entity['head_dept'];
            $_SESSION['leave']['id_leave_plan']             = $entity['id'];
            $_SESSION['leave']['attachment']                = array();
        }
    
        if (!isset($_SESSION['leave'])) {
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

        $_SESSION['leave']                              = $entity;
        $_SESSION['leave']['id']                        = $id;
        $_SESSION['leave']['edit']                      = $entity['document_number'];
        $_SESSION['leave']['document_number']           = $new_document_number;
        $_SESSION['leave']['format_number']             = $format_number;
        $_SESSION['leave']['holidays']                  = $holidays;
        $_SESSION['leave']['employee_number']           = $entity['employee_number'];
        $_SESSION['leave']['department_id']             = $employee['department_id'];
        $_SESSION['leave']['department_name']           = $department['department_name'];
        $_SESSION['leave']['leave_start_date']          = print_date($entity['leave_start_date'], 'd-m-Y');
        $_SESSION['leave']['employee_contract_id']      = $kontrak_active['id'];
        $_SESSION['leave']['leave_end_date']            = print_date($entity['leave_end_date'], 'd-m-Y');
        $_SESSION['leave']['start_contract']            = print_date($kontrak_active['start_date'], 'd M Y');
        $_SESSION['leave']['end_contract']              = print_date($kontrak_active['end_date'], 'd M Y');


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


    public function get_maternity_leave()
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //     redirect($this->modules['secure']['route'] .'/denied');
        

        $employee_number = $_GET['employee_number'];
        $type = $_GET['type'];

        $employee_has_leave = $this->model->getEmployeeMaternityLeave($employee_number,$type);
        
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

            if($get_leave_code == 'L01'){
                $start_contract = new DateTime($kontrak_active['start_date']);
                $end_contract = new DateTime($kontrak_active['end_date']);
                $leave_start_date = new DateTime($entity['leave_start_date']);
                $leave_end_date = new DateTime($entity['leave_end_date']);
            
                // Cek apakah tanggal cuti di luar rentang kontrak
                if ($leave_start_date < $start_contract || $leave_end_date > $end_contract) {
                    
                    $errors[] = "ID Kontrak".$id;
                    $errors[] = "Start Kontrak {$start_contract->format('Y-m-d')}";
                    $errors[] = "Tanggal permintaan cuti tahunan harus berada dalam masa kontrak: {$start_contract->format('Y-m-d')} s.d. {$end_contract->format('Y-m-d')}.";
                
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

            $document_number = $_SESSION['leave']['document_number'] . $_SESSION['leave']['format_number'];

            if ($_SESSION['leave']['employee_has_leave_id']==NULL || $_SESSION['leave']['employee_has_leave_id']=='') {
                if($_SESSION['leave']['leave_code'] == "L01"){
                    $errors[] = 'Attention!! Please Fill Employee Has!!';
                }
            }

            if($_SESSION['leave']['leave_code'] == "L02" || $_SESSION['leave']['leave_code'] == "L03"){
                if ($_SESSION['leave']['attachment'] == array()){
                    $errors[] = 'Attention!! Please Add Attachment!!';
                }
            }


            if ($_SESSION['leave']['head_dept']==NULL || $_SESSION['leave']['head_dept']=='') {
                $errors[] = 'Attention!! Please Fill Head / Atasan !!';
            }

            if ($_SESSION['leave']['reason']==NULL || $_SESSION['leave']['reason']=='') {
                $errors[] = 'Attention!! Please Fill Notes !!';
            }


            
            $get_leave                  = getLeaveCodeById($_SESSION['leave']['leave_type']);
            $get_leave_code             = $get_leave['leave_code'];

            if($get_leave_code == 'L01'){
                $start_contract = new DateTime($_SESSION['leave']['start_contract']);
                $end_contract = new DateTime($_SESSION['leave']['end_contract']);
                $leave_start_date = new DateTime($_SESSION['leave']['leave_start_date']);
                $leave_end_date = new DateTime($_SESSION['leave']['leave_end_date']);
            
                // Cek apakah tanggal cuti di luar rentang kontrak
                if ($leave_start_date < $start_contract || $leave_end_date > $end_contract) {
                    
                    $errors[] = "Tanggal permintaan cuti tahunan harus berada dalam masa kontrak: {$start_contract->format('Y-m-d')} s.d. {$end_contract->format('Y-m-d')}.";
                    
                }
            }
            

            if ($_SESSION['leave']['warehouse']==NULL || $_SESSION['leave']['warehouse']=='') {
                $errors[] = 'Attention!! Please Fill Warehouse!!';
            }


            if ($_SESSION['leave']['leave_type']==NULL || $_SESSION['leave']['leave_type']=='') {
                $errors[] = 'Attention!! Please Fill Leave Type!!'. $_SESSION['leave']['leave_type'].'-'.$_SESSION['leave']['type_leave'];
            }

            // $errors[] = 'Attention!! Please Fill Employee Has ID!!'. $_SESSION['leave']['employee_has_leave_id'].'-';

            

            if (!empty($errors)){
                $data['success'] = FALSE;
                $data['message'] = implode('<br />', $errors);
            } else {
                if ($this->model->save()){
                    unset($_SESSION['leave']);
        
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

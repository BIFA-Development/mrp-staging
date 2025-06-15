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
                $col[] = print_date($row['request_date'], 'd F Y');    
                $col[] = print_string($row['document_number']);
                $col[] = print_string($row['person_name']);
                $col[] = print_string($row['leave_type_name']);
                $col[] = print_string($row['status']);
                if($row['is_reserved'] == TRUE){
                    $col[] = print_string('Rencana Cuti');
                } else {
                    $col[] = print_string('-');
                }
                if($row['is_reserved'] == TRUE){
                    $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary">'.$row['id'].'</a>';
                } else {
                    $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary">'.$row['id'].'</a>';
                    // $col[] = '<a href="' . site_url($this->module['route'] . '/edit/' . $row['id']) . '" class="btn btn-sm btn-primary" disabled>Edit</a>';
                }
                

                
                
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


        $_SESSION['leave']['set_warehouse'] = $_GET['data'];

    }

    public function set_employee_number()
    {
        if ($this->input->is_ajax_request() === FALSE)
            redirect($this->modules['secure']['route'] .'/denied');

        $_SESSION['leave']['employee_number'] = $_GET['data'];
    }


    public function create($idnya = NULL)
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
            $_SESSION['leave']['is_reserved']               = NULL;
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
        $_SESSION['leave']['leave_start_date']           = print_date($entity['leave_start_date'], 'd-m-Y');
        $_SESSION['leave']['leave_end_date']           = print_date($entity['leave_end_date'], 'd-m-Y');


        redirect($this->module['route'] .'/create');
    }


    // public function edit($id)
    // {
    //     $this->authorized($this->module, 'create');
    
    //     $entity = $this->model->findById($id);

    //     $document_number    = sprintf('%06s', substr($entity['document_number'], 0, 6));
    //     $format_number      = substr($entity['document_number'], 9, 25);

    //     if (preg_match('/-R(\d+)/', $entity['document_number'], $matches)) {
    //         $current_revision = intval($matches[1]); // Ambil angka revisi terakhir
    //         $revisi = $current_revision + 1; // Tambah revisi berikutnya
    //         $document_number = str_replace('-R' . $current_revision, '', $document_number); // Hapus revisi sebelumnya
    //     } else {
    //         $revisi = 1; // Jika belum ada revisi, mulai dari 1
    //     }
    
    //     // Pastikan tidak ada revisi duplikat
    //     $new_document_number = $document_number . '-R' . $revisi;
    
    //     if ($entity) {
    //         $_SESSION['leave'] = array();
    //         $_SESSION['leave']['id']                   = $id;
    //         $_SESSION['leave']['document_number']        = $new_document_number;
    //         $_SESSION['leave']['format_number']        = $format_number;
    //         $_SESSION['leave']['employee_number']      = $entity['employee_number'];
    //         $_SESSION['leave']['contract_number']      = $entity['contract_number'];
    //         $_SESSION['leave']['start_contract']       = print_date($entity['start_date'], 'd M Y');
    //         $_SESSION['leave']['end_contract']         = print_date($entity['end_date'], 'd M Y');
    //         $_SESSION['leave']['department_name']      = $entity['department_name'];
    //         $_SESSION['leave']['department_id']        = $entity['department_id'];
    //         $_SESSION['leave']['holidays']             = getHolidays();
    
    //         $_SESSION['leave']['request_date']         = isset($entity['request_date']) ? $entity['request_date'] : NULL;
    //         $_SESSION['leave']['leave_start_date']     = isset($entity['leave_start_date']) ? $entity['leave_start_date'] : NULL;
    //         $_SESSION['leave']['leave_end_date']       = isset($entity['leave_end_date']) ? $entity['leave_end_date'] : NULL;
    //         $_SESSION['leave']['total_leave_days']     = isset($entity['total_leave_days']) ? $entity['total_leave_days'] : NULL;
    //         $_SESSION['leave']['reason']               = isset($entity['reason']) ? $entity['reason'] : NULL;
    //         $_SESSION['leave']['leave_type']           = isset($entity['leave_type']) ? $entity['leave_type'] : NULL;
    //         $_SESSION['leave']['type_leave']           = isset($entity['type_leave']) ? $entity['type_leave'] : NULL;
    //         $_SESSION['leave']['leave_type_name']      = isset($entity['leave_type_name']) ? $entity['leave_type_name'] : NULL;
    //         $_SESSION['leave']['warehouse']            = isset($entity['warehouse']) ? $entity['warehouse'] : NULL;
    //         $_SESSION['leave']['employee_has_leave_id']= isset($entity['employee_has_leave_id']) ? $entity['employee_has_leave_id'] : NULL;
    //         $_SESSION['leave']['is_reserved']          = isset($entity['is_reserved']) ? $entity['is_reserved'] : NULL;
    //     }
    
    //     redirect($this->module['route'] . '/create');
    // }
    

    public function get_annual_leave()
    {
        // if ($this->input->is_ajax_request() === FALSE)
        //     redirect($this->modules['secure']['route'] .'/denied');
        

        $employee_number = $_GET['employee_number'];
        $type = $_GET['type'];

        $employee_has_leave = $this->model->getEmployeeHasAnnualLeave($employee_number,$type);
        
        echo json_encode($employee_has_leave);
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
                $errors[] = 'Attention!! Please Fill Employee Has!!';
            }


            if ($_SESSION['leave']['reason']==NULL || $_SESSION['leave']['reason']=='') {
                $errors[] = 'Attention!! Please Fill Notes!!';
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
    
}

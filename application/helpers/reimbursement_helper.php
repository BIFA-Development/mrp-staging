<?php defined('BASEPATH') or exit('No direct script access allowed');

if (! function_exists('reimbursement_format_number')) {
  function reimbursement_format_number()
  {
    $div  = config_item('document_format_divider');
    $base = (config_item('include_base_on_document') === TRUE) ? $div . config_item('auth_warehouse') : NULL;
    $mod  = config_item('module');
    $year = date('Y');
    $month = date('m');

    $return = $div . 'RF' . $div . 'HRD-BIFA' . $div . $month . $div . 'BALI' . $div . $year;

    return $return;
  }
}

if (! function_exists('reimbursement_last_number')) {
  function reimbursement_last_number()
  {
    $CI = &get_instance();

    $format = reimbursement_format_number();

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_reimbursements');
    $CI->db->like('document_number', $format, 'both');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    $last   = $row->last_number;
    $number = substr($last, 0, 6);
    $next   = $number + 1;
    $return = sprintf('%06s', $next);

    return $return;
  }
}

if (! function_exists('getNotifRecipientHrManager')) {
  function getNotifRecipientHrManager()
  {
    $CI = &get_instance();

    $head_dept = array();

    foreach (list_user_in_head_department(11) as $head) {
      $head_dept[] = $head['username'];
    }

    $CI->db->select('email');
    $CI->db->from('tb_auth_users');
    $CI->db->where_in('username', $head_dept);
    $query  = $CI->db->get();
    $result = $query->result_array();
    return $result;
  }
}

if (! function_exists('getNotifRecipientByRole')) {
    /**
     * Mengambil email penerima langsung dari tabel master karyawan
     * @param string $position_name Nama jabatan (VP FINANCE, HEAD OF SCHOOL, dll)
     */
    function getNotifRecipientByRole($position_name)
    {
        $CI = &get_instance();

        // Kita langsung ambil dari tb_master_employees
        $CI->db->select('email, name as person_name');
        $CI->db->from('tb_master_employees');
        $CI->db->where('position', $position_name);
        
        // Opsional: Jika ada kolom status aktif di tb_master_employees, tambahkan di sini
        // $CI->db->where('status', 'ACTIVE'); 

        $query = $CI->db->get();
        return $query->result_array();
    }
}

if (! function_exists('getNotifRecipient_byLevel')) {
    /**
     * Mengambil email penerima berdasarkan auth_level
     * @param int $level Angka level jabatan (3, 10, 11, 16, dll)
     */
    function getNotifRecipient_byLevel($level)
    {
        $CI = &get_instance();

        $CI->db->select('email, person_name');
        $CI->db->from('tb_auth_users');
        $CI->db->where('auth_level', $level);
        $CI->db->where('status', 1); // Pastikan hanya mengambil user yang aktif
        
        $query = $CI->db->get();
        return $query->result_array();
    }
}

if (! function_exists('get_reimbursement_last_number')) {
  function get_reimbursement_last_number()
  {
    $CI = &get_instance();

    $format = travel_on_duty_format_number();

    $CI->db->select_max('document_number', 'last_number');
    $CI->db->from('tb_reimbursements');
    $CI->db->like('document_number', $format, 'both');

    $query  = $CI->db->get();
    $row    = $query->unbuffered_row();
    // $last   = $row->last_number;
    // $number = substr($last, 0, 6);
    // $next   = $number + 1;
    // $return = sprintf('%06s', $next);

    return $row;
  }
}

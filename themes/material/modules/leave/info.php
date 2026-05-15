<div class="card card-underline style-default-bright">
  <div class="card-head style-primary-dark">
    <header><?=strtoupper($module['label']);?></header>

    <div class="tools">
      <div class="btn-group">
        <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
          <i class="md md-close"></i>
        </a>
      </div>
    </div>
  </div>

  <div class="card-body">
    <div class="row" id="document_master">
      <div class="col-sm-12 col-md-4 col-md-push-8">
        <div class="well">
          <div class="clearfix">
            <div class="pull-left">DOCUMENT NO.: </div>
            <div class="pull-right"><?=print_string($entity['document_number']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">REQUEST DATE: </div>
            <div class="pull-right"><?=formatDateIndonesian($entity['request_date']);?></div>
          </div>
          <div class="clearfix">
            <div class="pull-left">BASE: </div>
            <div class="pull-right"><?=print_string($entity['warehouse']);?></div>
          </div>
        </div>
      </div>

      <div class="col-sm-12 col-md-8 col-md-pull-4">
        <dl class="dl-inline">
            <dt>Status</dt>
            <dd><?=$entity['status'];?></dd>

            <?php if($entity['status']=='REJECT'):?>
              <dt>Rejected By</dt>
              <dd><?=($entity['rejected_by']==null)? 'N/A':print_string($entity['rejected_by']);?></dd>
            <?php else:?>
              <dt>Validated By</dt>
              <dd><?=($entity['validated_by']==null)? 'N/A':print_string($entity['validated_by']);?></dd>

              <dt>HR Approved By</dt>
              <dd><?=($entity['hr_approved_by']==null)? 'N/A':print_string($entity['hr_approved_by']);?></dd>
            <?php endif;?>
           

            <dt>Employee Number</dt>
            <dd><?=($entity['employee_number']==null)? 'N/A':print_string($entity['employee_number']);?></dd>

            <dt>Name/Nama</dt>
            <dd><?=($entity['person_name']==null)? 'N/A':print_string($entity['person_name']);?></dd>

          

            <dt>Occupation/Jabatan</dt> 
            <dd><?= print_string(getEmployeeByEmployeeNumber($entity['employee_number'])['position']);?></dd>
            
            <dt>Leave Start Date</dt>
            <dd><?php 
                if($entity['leave_start_date']==null) {
                    echo 'N/A';
                } else {
                    echo formatDateIndonesian($entity['leave_start_date']) . ' (' . getDayNameIndonesian($entity['leave_start_date']) . ')';
                }
            ?></dd>

            <dt>Leave End Date</dt>
            <dd><?php 
                if($entity['leave_end_date']==null) {
                    echo 'N/A';
                } else {
                    echo formatDateIndonesian($entity['leave_end_date']) . ' (' . getDayNameIndonesian($entity['leave_end_date']) . ')';
                }
            ?></dd>
            
            <dt>Total Days</dt>
            <dd><?=($entity['total_leave_days']==null)? 'N/A':print_string($entity['total_leave_days']);?> hari</dd>

            <dt>Requested By</dt>
            <dd><?=($entity['reason']==null)? 'N/A':strtoupper($entity['request_by']);?></dd>

            <dt>Leave Type</dt>
            <dd><?=($entity['leave_type']==null)? 'N/A':print_string(getLeaveCodeById($entity['leave_type'])['name_leave']);?></dd>

            <?php 
            // Check if leave type is annual leave (L01) and show remaining leave balance
            if($entity['leave_type'] != null) {
                $leave_info = getLeaveCodeById($entity['leave_type']);
                if($leave_info['leave_code'] == 'L01') {
                    $annual_leave_data = getAnnualLeaveEmployee($entity['employee_number'], $entity['leave_type']);
                    if($annual_leave_data && isset($annual_leave_data['left_leave'])) {
                        echo '<dt>Sisa Cuti Tahunan</dt>';
                        echo '<dd>' . $annual_leave_data['left_leave'] . ' hari</dd>';
                    }
                }
            }
            ?>

            <dt>Reason</dt>
            <dd><?=($entity['reason']==null)? 'N/A':print_string($entity['reason']);?></dd>

            
        </dl>
      </div>
    </div>

   
  </div>

    <div class="card-foot">
        <?php
            $today    = date('Y-m-d');
            $date     = strtotime('-2 day',strtotime($today));
            $data     = date('Y-m-d',$date);
        ?>
        
        <?php if (is_granted($module, 'delete') && $entity['date'] >= $data):?>
        <?=form_open(current_url(), array(
            'class' => 'form-xhr pull-left',
        ));?>
        <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">

        <!-- <a href="<?=site_url($module['route'] .'/delete_ajax/');?>" class="btn btn-floating-action btn-danger btn-xhr-delete btn-tooltip ink-reaction" id="modal-delete-data-button">
            <i class="md md-delete"></i>
            <small class="top left">delete</small>
        </a> -->
        <?=form_close();?>
        <?php endif;?>
        <a href="<?= site_url($module['route'] . '/manage_attachment/' . $entity['id']); ?>" onClick="return popup(this, 'attachment')" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction">
            <i class="md md-attach-file"></i>
            <small class="top right">Manage Attachment</small>
        </a>

        <div class="pull-right">
            <?php if (is_granted($module, 'create')  && $entity['status'] != 'REVISED' && $entity['status'] != 'APPROVED'): ?>
            <?php if (getEmployeeById(config_item('auth_user_id'))['employee_number'] != $entity['head_dept']): ?>
              <a href="<?=site_url($module['route'] .'/edit/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" id="modal-edit-data-button">
                <i class="md md-edit"></i>
                <small class="top right">edit</small>
            </a>
            <?php endif;?>
            <?php endif;?>

            <?php if (is_granted($module, 'print')):?>
            <a href="<?=site_url($module['route'] .'/print_pdf/'. $entity['id']);?>" class="btn btn-floating-action btn-primary btn-tooltip ink-reaction" target="_blank" id="modal-print-data-button">
                <i class="md md-print"></i>
                <small class="top right">print</small>
            </a>
            <?php endif;?>
            <?=form_open(current_url(), array(
                'class' => 'form-xhr-create-expense pull-left',
            ));?>
            <input type="hidden" name="id" id="id" value="<?=$entity['id'];?>">
            

            <?=form_close();?>
        </div>
    </div>
</div>
<script type="text/javascript">
  function popup(mylink, windowname) {
    var height = window.innerHeight;
    var widht;
    var href;

    if (screen.availWidth > 768) {
      width = 769;
    } else {
      width = screen.availWidth;
    }

    var left = (screen.availWidth / 2) - (width / 2);
    var top = 0;
    // var top = (screen.availHeight / 2) - (height / 2);

    if (typeof(mylink) == 'string') href = mylink;
    else href = mylink.href;

    window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);

    if (!window.focus) return true;
    else return false;
  }
</script>

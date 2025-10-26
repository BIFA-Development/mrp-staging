<style>
  @media print {
    .new-page {
      page-break-before: always;
    }
  }

  .table-reimbursement th,
  .table-reimbursement td {
    padding: 5px 5px;
    font-size: 12px;
  }
</style>
<table class="table-reimbursement" width="100%">
  <tr>
    <th width="30%"> Document No </th>
    <td width="70%">: <?= print_string($entity['document_number'] == '' ? '-' : $entity['document_number']); ?></td>
  </tr>
  <tr>
    <th width="30%"> Leave Type </th>
    <td width="70%">: <?= ($entity['leave_type']==null) ? 'N/A' : print_string(getLeaveCodeById($entity['leave_type'])['name_leave']); ?></td>
  </tr>
  <tr>
    <th width="30%"> Request Date </th>
    <td width="70%">: <?= formatDateIndonesian($entity['request_date']); ?></td>
  </tr>
  <tr>
    <th width="30%"> Base </th>
    <td width="70%">: <?= print_string($entity['warehouse']); ?></td>
  </tr>
  <tr>
    <th width="30%"> Status </th>
    <td width="70%">: <?= $entity['status']; ?></td>
  </tr>
  <tr>
    <th> Employee Number </th>
    <td>: <?= ($entity['employee_number']==null) ? 'N/A' : print_string($entity['employee_number']); ?></td>
  </tr>
  <tr>
    <th> Name/Nama </th>
    <td>: <?= ($entity['person_name']==null) ? 'N/A' : print_string($entity['person_name']); ?></td>
  </tr>
  <tr>
    <th> Occupation/Jabatan </th>
    <td>: <?= print_string(getEmployeeByEmployeeNumber($entity['employee_number'])['position']); ?></td>
  </tr>
  <tr>
    <th> Leave Start Date </th>
    <td>: <?php 
        if($entity['leave_start_date']==null) {
            echo 'N/A';
        } else {
            echo formatDateIndonesian($entity['leave_start_date']) . ' (' . getDayNameIndonesian($entity['leave_start_date']) . ')';
        }
    ?></td>
  </tr>
  <tr>
    <th> Leave End Date </th>
    <td>: <?php 
        if($entity['leave_end_date']==null) {
            echo 'N/A';
        } else {
            echo formatDateIndonesian($entity['leave_end_date']) . ' (' . getDayNameIndonesian($entity['leave_end_date']) . ')';
        }
    ?></td>
  </tr>
  <tr>
    <th> Total Days </th>
    <td>: <?= ($entity['total_leave_days']==null) ? 'N/A' : print_string($entity['total_leave_days']); ?> hari</td>
  </tr>
  <?php 
  // Check if leave type is annual leave (L01) and show remaining leave balance
  if($entity['leave_type'] != null) {
      $leave_info = getLeaveCodeById($entity['leave_type']);
      if($leave_info['leave_code'] == 'L01') {
          $annual_leave_data = getAnnualLeaveEmployee($entity['employee_number'], $entity['leave_type']);
          if($annual_leave_data && isset($annual_leave_data['left_leave'])) {
              echo '<tr>';
              echo '<th> Sisa Cuti Tahunan </th>';
              echo '<td>: ' . $annual_leave_data['left_leave'] . ' hari</td>';
              echo '</tr>';
          }
      }
  }
  ?>
  <tr>
    <th> Requested By </th>
    <td>: <?= ($entity['request_by']==null) ? 'N/A' : strtoupper($entity['request_by']); ?></td>
  </tr>
  <tr>
    <th> Reason </th>
    <td>: <?= ($entity['reason']==null) ? 'N/A' : print_string($entity['reason']); ?></td>
  </tr>
  <?php if($entity['status']=='REJECT'): ?>
  <tr>
    <th> Rejected By </th>
    <td>: <?= ($entity['rejected_by']==null) ? 'N/A' : print_string($entity['rejected_by']); ?></td>
  </tr>
  <?php else: ?>
  <tr>
    <th> Validated By </th>
    <td>: <?= ($entity['validated_by']==null) ? 'N/A' : print_string($entity['validated_by']); ?></td>
  </tr>
  <tr>
    <th> HR Approved By </th>
    <td>: <?= ($entity['hr_approved_by']==null) ? 'N/A' : print_string($entity['hr_approved_by']); ?></td>
  </tr>
  <?php endif; ?>
</table>



<div class="clear"></div>



<?php if ($entity['signers']['rejected by']['person_name']) : ?>
Rejected by : <?=$entity['signers']['rejected by']['person_name'];?> , at : <?=print_date($entity['signers']['rejected by']['date'],'d M Y');?>
<?php endif; ?>
<div class="clear"></div>

<?php if($entity['status']!='REJECTED' && $entity['status']!='REVISED'):?>

<table class="condensed" style="margin-top: 20px;" width="100%">
  <tr>
    <td valign="top" style="text-align:center;">
      <p>
        Requested by
        <br />Employee<br />
        <?php if ($entity['signers']['requested by']['sign']) : ?>
          <?=print_date($entity['signers']['requested by']['date'],'d M Y');?>
          <br>  
          <img src="<?= base_url('ttd_user/' . $entity['signers']['requested by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['requested by']['person_name'];?>
      </p>
    </td>
    <?php if (!in_array(getLeaveCodeById($entity['leave_type'])['leave_code'], ['L04', 'L01', 'L02', 'L05'])) : ?>
      <td valign="top" style="text-align:center;">
      <p>
        HR Approved by
        <br />
        <?php if ($entity['signers']['hr approved by']['sign']) : ?>
          <?=print_date($entity['signers']['hr approved by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['hr approved by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['hr approved by']['person_name'];?>
      </p>
    </td>
    <?php endif; ?>
    <td valign="top" style="text-align:center;">
      <p>
        Validated by
        <?php if (print_string(getEmployeeByEmployeeNumber($entity['employee_number'])['position']) == "VP FINANCE" || print_string(getEmployeeByEmployeeNumber($entity['employee_number'])['position']) == "COO/CEO") : ?>
        <br />CFO<br />
        <?php elseif (print_string(getEmployeeByEmployeeNumber($entity['employee_number'])['position']) == "HEAD OF SCHOOL" || print_string(getEmployeeByEmployeeNumber($entity['employee_number'])['position']) == "CFO") : ?>
        <br />COO<br />
        <?php else:?>
          <?php if ($entity['warehouse'] == "JAKARTA") : ?>
          <br />VP FINANCE<br />
          <?php else:?>
          <br />HOS<br />
          <?php endif; ?>
        <?php endif; ?>
        <?php if ($entity['signers']['validated by']['sign']) : ?>
          <?=print_date($entity['signers']['validated by']['date'],'d M Y');?>
          <br>
          <img src="<?= base_url('ttd_user/' . $entity['signers']['validated by']['sign']); ?>" width="auto" height="50">
        <?php endif; ?>
        <br />
        <br /><?=$entity['signers']['validated by']['person_name'];?>
      </p>
    </td>
  </tr>
</table>
<?php endif; ?>
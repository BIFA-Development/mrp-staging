<?= form_open_multipart(site_url($module['route'] . '/save_leave'), array(
    'autocomplete'  => 'off',
    // 'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    // 'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Add New Benefit Leave to Employee <?= $module['label']; ?></header>

        <div class="tools">
        <div class="btn-group">
            <a class="btn btn-icon-toggle btn-close" data-dismiss="modal" aria-label="Close" title="close">
            <i class="md md-close"></i>
            </a>
        </div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-12 col-md-4 col-md-offset-4">

                        <div class="form-group">
                            <input type="text" name="employee_name" id="employee_name" class="form-control" value="<?=$entity['name'];?>" readonly>
                            <input type="hidden" name="employee_number" id="employee_number" class="form-control" value="<?=$entity['employee_number'];?>" readonly>
                            <label for="employee_name">Name</label>
                        </div>

                        <div class="form-group">
                            <input type="text" name="jabatan" id="jabatan" class="form-control" value="<?=$entity['position'];?>" readonly>
                            <label for="jabatan">Jabatan</label>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="leave_type" id="leave_type" class="form-control select2" style="width: 100%" data-placeholder="Select Benefit Leave" required>
                                <option value="">Select Benefit Leave</option>
                                <?php foreach(getLeaveTypeData($entity['gender'], TRUE, $entity['employee_number']) as $leave):?>
                                <option data-id="<?=$leave['id'];?>" data-leave-code="<?=$leave['leave_code'];?>" data-leave-name="<?=$leave['name_leave'];?>" value="<?=$leave['id'];?>"><?=$leave['name_leave'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="leave_type">Leave Name</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="amount_leave" id="amount_leave" class="form-control number" value="0" step=".01">
                            <label for="amount_leave">Amount Leave Day</label>
                        </div>

                        <div class="form-group">
                            <p style="font-size:14px;"><?= print_string($kontrak_active['contract_number']) ?> (<?= print_date($kontrak_active['start_date']) ?> sd <?= print_date($kontrak_active['end_date']) ?>)</p>
                            <input type="hidden" name="employee_contract_id" id="employee_contract_id" class="form-control" value="<?=$kontrak_active['id'];?>" readonly>
                            <label for="jabatan">Periode Kontrak</label>
                        </div>

                       

                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
        <i class="md md-save"></i>
        </button>
    </div>
</div>

<?= form_close(); ?>
<script type="text/javascript">
    $('.number').number(true, 2, '.', ',');
    $('.select2').select2();

    $('#leave_type').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var leaveTypeId = selectedOption.data('id');
        var position = $('#jabatan').val().toLowerCase();
        var leave_code = $('#leave_type option:selected').data('leave-code');

        if (leave_code == 'L01') {
            var amountLeave = 0;
            
            // Staff dan Supervisor 12 hari
            // if (position.includes('staff') || position.includes('supervisor')) {
            //     amountLeave = 12;
            // }
            // Manager, GM, dan VP Marketing 14 hari
            if (position.includes('manager') || position.includes('gm') || 
                     (position.includes('vp') && position.includes('marketing'))) {
                amountLeave = 14;
            }
            // VP Finance, Flight Instructor, HOS 18 hari
            else if ((position.includes('vp') && position.includes('finance')) || 
                     position.includes('flight instructor') || 
                     position.includes('hos')) {
                amountLeave = 18;
            }
            // BOD 20 hari
            else if (position.includes('bod') || position.includes('board of director') || 
                     position.includes('cfo') || position.includes('coo')) {
                amountLeave = 20;
            } else {
                // Reset to 0 for other positions
                amountLeave = 12;
            }
            
            $('#amount_leave').val(amountLeave);
        } else if (leave_code == 'L04') { 
            $('#amount_leave').val(90);
        } else {
            // Reset to 0 for other leave types
            $('#amount_leave').val(0);
        }
    });
</script>
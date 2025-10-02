<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="has-actions style-default">
    <div class="section-body">
        <?= form_open(current_url(), array('autocomplete' => 'off', 'class' => 'form form-validate', 'id' => 'form-create-document')); ?>
        <div class="card">
        <div class="card-head style-primary-dark">
            <header><?= PAGE_TITLE; ?></header>
        </div>
        <div class="card-body no-padding">
            <?php
            if ($this->session->flashdata('alert'))
                render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
            ?>

            <div class="document-header force-padding">
                <div class="row">
                    <div class="col-sm-6 col-lg-4">
                        
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-content">
                                    <input type="text" name="document_number" id="document_number" class="form-control" value="<?= $_SESSION['leave_plan']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" readonly>
                                    <label for="document_number">No Form</label>
                                </div>
                                <span class="input-group-addon"><?= $_SESSION['leave_plan']['format_number']; ?></span>
                            </div>
                        </div>

                        <!-- <div class="form-group" style="padding-top: 25px;">
                            <select name="type_leave" id="type_leave" class="form-control select2">
                            <option> -- Pilih Tipe Cuti --</option>
                                <?php foreach(getLeaveType($_SESSION['leave_plan']['gender'], NULL, TRUE) as $leaveType):?>
                                <option data-leave-id="<?=$leaveType['id'];?>" data-leave-code="<?=$leaveType['leave_code'];?>" data-leave-name="<?=$leaveType['name_leave'];?>" value="<?=$leaveType['id'];?>" <?= ($leaveType['id'] == $_SESSION['leave_plan']['leave_type']) ? 'selected' : ''; ?>><?=$leaveType['name_leave'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="type_leave">Tipe Cuti</label>
                        </div> -->

                        <div class="form-group">
                            <label for="type_leave">Type Cuti</label>
                            <select name="type_leave" id="type_leave" class="form-control select2" data-input-type="autoset" data-source-get-type-leave-list="<?= site_url($module['route'] . '/get_leave_type_list'); ?>" required >
                                <option value="">---Choose Leave----</option>
                            </select>
                        </div>

                        <div class="form-group" style="padding-top: 25px;">
                            <select name="employee_number" id="employee_number" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_employee_number'); ?>">
                                <option></option>
                                <?php foreach(available_employee($_SESSION['leave_plan']['department_id'], config_item('auth_role'), config_item('auth_user_id')) as $user):?>
                                <option data-get-warehouse="<?=$user['warehouse'];?>"  data-department-id="<?=$user['department_id'];?>" data-department-name="<?=$user['department_name'];?>" data-gender="<?=$user['gender'];?>" data-position="<?=$user['position'];?>" value="<?=$user['employee_number'];?>" <?= ($user['employee_number'] == $_SESSION['leave_plan']['employee_number']) ? 'selected' : ''; ?>><?=$user['name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="employee_number">Name</label>
                        </div>

                        
                        <div class="form-group">
                            <input type="text" name="department_name" id="department_name" class="form-control" value="<?= $_SESSION['leave_plan']['department_name']; ?>" readonly>
                            <label for="department_name">Department</label>
                        </div>

                        <div class="form-group">
                            <select name="head_dept" id="head_dept" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" required>
                                <option></option>
                                <?php foreach(list_user_in_head_department($_SESSION['leave_plan']['department_id']) as $head):?>
                                <option value="<?=$head['user_id'];?>" <?= ( getEmployeeById($head['user_id'])['employee_number'] == $_SESSION['leave_plan']['head_dept']) ? 'selected' : ''; ?>><?=$head['person_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="head_dept">Atasan</label>
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="leave_start_date" id="leave_start_date" data-provide="datepicker" data-date-format="dd-mm-yyyy" class="form-control" value="<?= $_SESSION['leave_plan']['leave_start_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_leave_start_date'); ?>" required>
                                    <label for="leave_start_date">Leave Start Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="leave_end_date" id="leave_end_date" data-provide="datepicker" data-date-format="dd-mm-yyyy" class="form-control" value="<?= $_SESSION['leave_plan']['leave_end_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_leave_end_date'); ?>" required>
                                    <label for="leave_end_date">Leave End Date</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="number" name="total_leave_days" id="total_leave_days" class="form-control number" value="<?= $_SESSION['leave_plan']['total_leave_days']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_total_leave_days'); ?>">
                                    <label for="total_leave_days">Jumlah Hari Cuti</label>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="form-group" id="left_leave_group">
                                    <input type="number" name="left_leave" id="left_leave" class="form-control number" value="<?= $_SESSION['leave_plan']['left_leave']; ?>" data-input-type="autoset" readonly>
                                    <label for="left_leave">Sisa Cuti Tahunan</label>
                                </div>
                            </div> -->
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="radio">
                                        <input type="checkbox" name="ignore_weekend" id="ignore_weekend" value="no">
                                        <label for="ignore_weekend">Abaikan Sabtu & Minggu</label>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-6">
                                <div class="form-group">
                                    <div class="radio" id="is_reserved_group">
                                        <input type="checkbox" name="is_reserved" id="is_reserved" value="no" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_is_reserved'); ?>">
                                        <label for="is_reserved">Rencana Cuti Tahunan</label>
                                    </div>
                                </div>
                            </div> -->
                        </div>

                        <div class="form-group">
                            <textarea name="reason" id="reason" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_reason'); ?>" required ><?= $_SESSION['leave_plan']['reason']; ?></textarea>
                            <label for="reason">Reason</label>
                        </div>


                        <div class="form-group hide">
                            <input type="text" name="leave_type" id="leave_type" class="form-control" value="<?= $_SESSION['leave_plan']['leave_type']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_leave_type'); ?>" readonly>
                            <label for="leave_type">leave type</label>
                        </div> 

                        <div class="form-group hide">
                            <input type="text" name="employee_has_leave_id" id="employee_has_leave_id" class="form-control" value="<?= $_SESSION['leave_plan']['employee_has_leave_id']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_employee_has_leave_id'); ?>" readonly>
                            <label for="employee_has_leave_id">employee_has_leave_id</label>
                        </div> 

                        <div class="form-group">
                            <input type="text" name="warehouse" id="warehouse" class="form-control" value="<?= $_SESSION['leave_plan']['warehouse']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_warehouse'); ?>" readonly>
                            <label for="warehouse">Warehouse</label>
                        </div> 
                    </div>

                    <div class="col-sm-12 col-lg-4">

                    <div class="form-group" id="holiday_list_group" style="display:none;">
                        <label>Hari Libur Nasional</label>
                        <ul id="holiday_list" class="form-control-static" style="margin-top: 10px; padding-left: 20px;"></ul>
                    </div>

                    <!-- <div class="form-group" id="contract_period_group" style="display:none;">
                        <label for="contract_period_info">Contract Period Information</label>
                        <input type="text" name="contract_period_info" id="contract_period_info" class="form-control" readonly style="background-color: #f5f5f5; color: #333;">
                        <small class="text-muted">Contract period for annual leave validation</small>
                    </div> -->

                    <div class="form-group" id="annual_leave_usage_group" style="display:none;">
                        <label for="annual_leave_usage_info">Sisa Plafond Rencana Cuti Tahunan</label>
                        <input type="text" name="annual_leave_usage_info" id="annual_leave_usage_info" class="form-control" readonly>
                       
                        <!-- Hidden inputs to store quota data for validation -->
                        <input type="hidden" id="annual_leave_remaining_hidden" value="0">
                        <input type="hidden" id="annual_leave_quota_hidden" value="0">
                        <input type="hidden" id="contract_start_hidden" value="">
                        <input type="hidden" id="contract_end_hidden" value="">
                        
                        <div id="annual_leave_details" style="display:none; margin-top: 10px;">
                            <button type="button" id="toggle_leave_details" class="btn btn-link btn-sm" style="padding: 0; text-decoration: none; font-size: 12px;">
                                <i class="fa fa-eye"></i> Show Leave Details
                            </button>
                            <div id="annual_leave_details_list" style="max-height: 200px; overflow-y: auto; background-color: #f8f9fa; padding: 10px; border-radius: 4px; display: none; margin-top: 8px;">
                                <!-- Details will be populated here -->
                            </div>
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label>Last Contract Date</label>
                        <p class="form-control-static" style="margin-top: 10px;">
                            <?= $_SESSION['leave_plan']['contract_number']; ?>
                            <?= $_SESSION['leave_plan']['start_contract']; ?>
                            -
                            <?= $_SESSION['leave_plan']['end_contract']; ?>
                        </p>
                    </div> -->

                    <!-- Modal untuk konfirmasi ignore weekend -->
                    <div class="modal fade" id="ignoreWeekendModal" tabindex="-1" role="dialog" aria-labelledby="ignoreWeekendModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ignoreWeekendModalLabel">Sabtu–Minggu otomatis tidak dihitung cuti.</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Perhitungan cuti Anda saat ini termasuk hari Sabtu dan Minggu.</p>
                                    <p>Apakah Anda ingin mengabaikan hari Sabtu dan Minggu dalam perhitungan cuti agar hari cuti lebih sedikit?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="btnBatal">Batal</button>
                                    <button type="button" class="btn btn-primary" id="btnLanjutkan">Lanjutkan</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>


                    
                </div>

                <div class="card-actionbar">
                    <div class="card-actionbar-row">
                        <div class="pull-left">
                            <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction pull-left hide">
                            Add
                            </button>

                            <!-- <a style="margin-left: 15px;" href="<?= site_url($module['route'] . '/attachment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
                                Attachment
                            </a> -->
                        </div>

                        <a href="<?= site_url($module['route'] . '/discard'); ?>" class="btn btn-flat btn-danger ink-reaction">
                            Discard
                        </a>
                    </div>
                </div>

                
            </div>

        </div>
        
        <?= form_close(); ?>
        <div class="section-action style-default-bright">
            <div class="section-floating-action-row">
                <a class="btn btn-floating-action btn-lg btn-danger btn-tooltip ink-reaction"  id="btn-submit-document" href="<?= site_url($module['route'] . '/save'); ?>" >
                    <i class="md md-save"></i>
                    <small class="top right">Save Document</small>
                </a>
            </div>
        </div>
    </div>

    

    
</section>
<?php endblock() ?>

<?php startblock('scripts') ?>
<style>
    .btn-disabled {
        opacity: 0.6 !important;
        cursor: not-allowed !important;
        pointer-events: none;
    }
    .btn-disabled:hover {
        opacity: 0.6 !important;
    }
</style>
<?= html_script('vendors/pace/pace.min.js') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-ui/jquery-ui.min.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?= html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
<?= html_script('themes/material/assets/js/libs/spin.js/spin.min.js') ?>
<?= html_script('themes/material/assets/js/libs/autosize/jquery.autosize.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/jquery.validate.min.js') ?>
<?= html_script('themes/material/assets/js/libs/jquery-validation/dist/additional-methods.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/moment.min.js') ?>
<?= html_script('vendors/bootstrap-daterangepicker/daterangepicker.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap-datepicker/bootstrap-datepicker.js') ?>
<?= html_script('themes/script/jquery.number.js') ?>
<?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
<?= html_script('vendors/select2-pmd/js/pmd-select2.js') ?>

<script>

const holidaysData = <?= json_encode($_SESSION['leave_plan']['holidays']); ?>;
const holidays = holidaysData.map(h => h.holiday_date);
console.log('Holidays:', holidays);
var selectedLeave = "<?= isset($_SESSION['leave']['leave_type']) ? $_SESSION['leave']['leave_type'] : ''; ?>";
window.onload = async function(){
        console.log('mulaiinit');
        var warehouse = $('#employee_number option:selected').data('get-warehouse');  
        console.log(warehouse);
        $('#employee_number').trigger('change');
        
        // var type = $('#type_leave option:selected').data('leave-code');  


        // if (type === 'L01') {
        //     console.log('Init L01');
        //     getAnnualLeave();
        //     $('#left_leave_group').show();
        // } else {
        //     $('#left_leave_group').hide();
        // }

        // Check contract period if both employee and leave type are already selected
        setTimeout(function() {
            var employee_number = $('#employee_number').val();
            var leave_type = $('#type_leave').val();
            if (employee_number && leave_type) {
                checkContractPeriod(leave_type);
            }
        }, 1000);

    };

    function parseDate(str) {
        const [day, month, year] = str.split("-");
        return new Date(`${year}-${month}-${day}`);
    }

    function getHolidaysInRange(startStr, endStr) {
        const startDate = parseDate(startStr);
        const endDate = parseDate(endStr);
        return holidaysData.filter(h => {
            const hDate = new Date(h.holiday_date);
            return hDate >= startDate && hDate <= endDate;
        });
    }

    function showHolidaysBetweenDates() {
        const start = $('#leave_start_date').val();
        const end = $('#leave_end_date').val();

        const $list = $('#holiday_list');
        const $listGroup = $('#holiday_list_group');

        $list.empty();

        if (start && end) {
            const filtered = getHolidaysInRange(start, end);
            if (filtered.length > 0) {
                filtered.forEach(h => {
                    $list.append(`<li style="color: red; font-weight: bold;">${h.holiday_date} - ${h.description}</li>`);
                });
                $listGroup.show();
            } else {
                $list.append('<li>Tidak ada hari libur di rentang ini.</li>');
                $listGroup.hide();
            }
        }
    }

    $('#leave_start_date, #leave_end_date').on('change', showHolidaysBetweenDates);

    function countWorkingDays(startDate, endDate, holidays = [], includeWeekend = false) {
        let count = 0;
        const current = new Date(startDate);

        while (current <= endDate) {
            const day = current.getDay(); // 0 = Sunday, 6 = Saturday
            const dateStr = current.toISOString().slice(0, 10); // Format YYYY-MM-DD
            const isHoliday = holidays.includes(dateStr);
            const isWeekend = (day === 0 || day === 6);

            // Hitung hanya jika bukan holiday
            if (!isHoliday) {
                if (includeWeekend) {
                    count++; // Hitung semua hari kecuali holiday
                } else if (!isWeekend) {
                    count++; // Hitung hanya weekdays yang bukan holiday
                }
            }

            current.setDate(current.getDate() + 1);
        }

        return count;
    }

    function updateLeaveDays() {
        const startVal = $('#leave_start_date').val();
        const endVal = $('#leave_end_date').val();

        if (!startVal || !endVal) return;

        const [ds, ms, ys] = startVal.split('-');
        const [de, me, ye] = endVal.split('-');

        const startDate = new Date(`${ys}-${ms}-${ds}`);
        const endDate = new Date(`${ye}-${me}-${de}`);

        if (startDate > endDate) {
            $('#total_leave_days').val(0).trigger('change');
            return;
        }

        const includeWeekend = $('#ignore_weekend').is(':checked');

        const workingDays = countWorkingDays(startDate, endDate, holidays, includeWeekend);

        $('#total_leave_days').val(workingDays).trigger('change');
        
        // Validate leave quota and date range after updating days (silent)
        setTimeout(function() {
            validateDateRange(false); // Silent validation
        }, 100);
    }

    function checkDateRangeHasWeekends() {
        const startVal = $('#leave_start_date').val();
        const endVal = $('#leave_end_date').val();

        if (!startVal || !endVal) return false;

        const [ds, ms, ys] = startVal.split('-');
        const [de, me, ye] = endVal.split('-');

        const startDate = new Date(`${ys}-${ms}-${ds}`);
        const endDate = new Date(`${ye}-${me}-${de}`);

        if (startDate > endDate) return false;

        // Loop through the date range to check for weekends
        const current = new Date(startDate);
        while (current <= endDate) {
            const day = current.getDay(); // 0 = Sunday, 6 = Saturday
            if (day === 0 || day === 6) {
                return true; // Found a weekend day
            }
            current.setDate(current.getDate() + 1);
        }

        return false; // No weekends found in the range
    }

    
    $(function() {
        var buttonDeleteDocumentItem = $('.btn_delete_document_item');
        var buttonEditDocumentItem = $('.btn_edit_document_item');
        toastr.options.closeButton = true;
        var buttonSubmitDocument = $('#btn-submit-document');
        var formDocument = $('#form-create-document');
        var autosetInputData = $('[data-input-type="autoset"]');
        
        // Inisialisasi nilai awal total_leave_days
        var previousTotalLeaveDays = $('#total_leave_days').val() || 0;

        var today = new Date();
        var twoWeeksLater = new Date();
        twoWeeksLater.setDate(today.getDate() + 14);

        $('#leave_start_date, #leave_end_date').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            startDate: today,
        }).on('changeDate', function () {
            updateLeaveDays();
            // Validate date range and quota after updating leave days (silent validation)
            setTimeout(function() {
                validateDateRange(false); // Silent validation
                validateLeaveQuota(false); // Silent validation
            }, 200);
        });

        // Event untuk checkbox ignore_weekend tanpa modal
        $('#ignore_weekend').on('change', function () {
            if ($(this).is(':checked')) {
                $(this).val('yes');
            } else {
                $(this).val('no');
            }
            updateLeaveDays();
            // Validate quota after updating leave days (silent check)
            setTimeout(function() {
                validateLeaveQuota(false); // Silent validation
            }, 100);
        });

        // Monitor perubahan pada total_leave_days untuk menampilkan modal
        $('#total_leave_days').on('change input keyup', function() {
            var currentValue = parseInt($(this).val()) || 0;
            var previousValue = parseInt(previousTotalLeaveDays) || 0;
            var ignoreWeekendChecked = $('#ignore_weekend').is(':checked');
            
            // Cek apakah tanggal yang dipilih mengandung hari sabtu/minggu
            var hasWeekends = checkDateRangeHasWeekends();
            
            // Cek apakah ada perubahan nilai, nilai lebih besar dari sebelumnya, ignore_weekend tidak dichecklist, dan ada weekend di range tanggal
            if (currentValue != previousValue && currentValue > previousValue && !ignoreWeekendChecked && currentValue > 0 && hasWeekends) {
                $('#ignoreWeekendModal').modal('show');
            }
            
            previousTotalLeaveDays = currentValue;
            
            // Validate leave quota and date range immediately when total days change
            validateDateRange(true); // Show toast for manual input changes
            validateLeaveQuota(true); // Show toast for manual input changes
        });

        // Handle button Lanjutkan di modal
        $('#btnLanjutkan').on('click', function() {
            $('#ignore_weekend').prop('checked', true);
            $('#ignore_weekend').val('yes');
            $('#ignoreWeekendModal').modal('hide');
            updateLeaveDays();
            // Validate quota after updating leave days (silent check)
            setTimeout(function() {
                validateLeaveQuota(false); // Silent validation
            }, 100);
        });

        // Handle button Batal di modal
        $('#btnBatal').on('click', function() {
            $('#ignoreWeekendModal').modal('hide');
        });

        // $('#is_reserved').on('change', function () {
        //     if ($(this).is(':checked')) {
        //         $(this).val('yes');
        //     } else {
        //         $(this).val('no');
        //     }
        //     var val = $(this).val();
        //     var url = $(this).data('source');

        //     $.get(url, {
        //         data: val
        //     });
        // });

        $('#type_leave').change(function () {
            const leave_typedata = $(this).val(); 
            var leave_code = $('#type_leave option:selected').data('leave-code');  
            $('#leave_type').val(leave_typedata).trigger('change');

            var leave_type_data = $('#leave_type').val(); // Default to 0 if invalid.

            console.log('Perubahan Code Leave:',leave_code);
            console.log('Perubahan Data Leave:',leave_type_data);
            var today = new Date();
            var twoWeeksLater = new Date();
            twoWeeksLater.setDate(today.getDate() + 14);
            
            // Check contract period for annual leave
            if (leave_typedata && $('#employee_number').val()) {
                checkContractPeriod(leave_typedata);
            }
        });
        


        $(buttonSubmitDocument).on('click', function (e) {
            e.preventDefault();
            var button = $(this);
            
            // Check if button is disabled due to validation failures
            if (button.hasClass('btn-disabled') || button.attr('disabled')) {
                var leave_code = $('#type_leave option:selected').data('leave-code');
                if (leave_code === 'L01') {
                    var total_leave_days = parseInt($('#total_leave_days').val()) || 0;
                    var annual_leave_remaining = parseInt($('#annual_leave_remaining_hidden').val()) || 0;
                    
                    if (total_leave_days > annual_leave_remaining) {
                        toastr.error('Tidak dapat menyimpan. Total hari cuti (' + total_leave_days + ') melebihi sisa kuota (' + annual_leave_remaining + ' hari).', 'Kuota Cuti Terlampaui', {
                            timeOut: 10000,
                            closeButton: true,
                            positionClass: 'toast-top-right'
                        });
                    } else {
                        toastr.error('Tidak dapat menyimpan rencana cuti tahunan. Silakan periksa validasi form.', 'Validasi Error', {
                            timeOut: 10000,
                            closeButton: true,
                            positionClass: 'toast-top-right'
                        });
                    }
                    return false;
                }
            }
            
            // Final validation check before proceeding
            if (!validateDateRange(true) || !validateLeaveQuota(true)) {
                return false; // Stop execution if validation fails
            }
            
            button.attr('disabled', true); 
            var url = button.attr('href');
            var type_leave = $('#type_leave').val(); // Default to 0 if invalid.
            var leave_type = $('#leave_type').val(); // Default to 0 if invalid.
            var warehouse = $('#warehouse').val(); // Default to 0 if invalid.
            var head_dept = $('#head_dept').val(); // Default to 0 if invalid.
            var reason = $('#reason').val(); // Default to 0 if invalid.
            var total_leave_days = $('#total_leave_days').val(); // Default to 0 if invalid.
            var employee_has_leave_id = $('#employee_has_leave_id').val();
            $('#employee_has_leave_id').val(employee_has_leave_id).trigger('change');
            $('#warehouse').val(warehouse).trigger('change');
            $('#leave_type').val(leave_type).trigger('change');
            $('#head_dept').val(head_dept).trigger('change');
            $('#reason').val(reason).trigger('change');

            console.log("TypeLeave:", type_leave);
            console.log("LeaveType:", leave_type);
            console.log("warehouse:", warehouse);


            console.log("Total:", total_leave_days);

            console.log("Reason:", reason);
            console.log("Url:", url);
            console.log('Data', formDocument.serialize());




            

            submitForm(url,button);
            button.attr('disabled', false); 

        });

        $(autosetInputData).on('change', function() {
            var val = $(this).val();
            var url = $(this).data('source');
            var id = $(this).attr('id');

            $.get(url, {
                data: val
            });
        });

        function submitForm(url, button) {
        $.post(url, formDocument.serialize(), function (data) {
            var obj = $.parseJSON(data);

            if (obj.success === false) {
                toastr.options.timeOut = 10000;
                toastr.options.positionClass = 'toast-top-right';
                toastr.error(obj.message);
                button.attr('disabled', false); // Re-enable the button after completion.

            } else {
                toastr.options.timeOut = 4500;
                toastr.options.closeButton = false;
                toastr.options.progressBar = true;
                toastr.options.positionClass = 'toast-top-right';
                toastr.success(obj.message);
                button.attr('disabled', true); // Re-enable the button after completion.

                setTimeout(function () {
                    window.location.href = '<?= site_url($module['route']); ?>';
                }, 5000);
            }

            
        }).fail(function () {
            alert('An error occurred while submitting the form.');
            button.attr('disabled', false); // Re-enable the button on error.
        });
        }

        $('[data-toggle="redirect"]').on('click', function(e) {
            e.preventDefault;

            var url = $(this).data('url');

            window.document.location = url;
        });

        $('[data-toggle="back"]').on('click', function(e) {
            e.preventDefault;

            history.back();
        });

        var startDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?> - 1, 1);
        var lastDate = new Date(<?= config_item('period_year'); ?>, <?= config_item('period_month'); ?>, 0);
        var last_publish = $('[name="opname_start_date"]').val();
        var today = new Date();
        today.setDate(today.getDate() - 30);
        var lastToday = new Date();
        $('[data-provide="datepicker"]').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'yyyy-mm-dd',
            startDate: today,
            endDate: lastToday
        });
        $('[data-provide="daterange"]').daterangepicker({
            autoUpdateInput: false,
            parentEl: '#offcanvas-datatable-filter',
            locale: {
                cancelLabel: 'Clear'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD-MM-YYYY') + ' s/d ' + picker.endDate.format('DD-MM-YYYY')).trigger('change');

            var start_date  = new Date(picker.startDate.format('YYYY-MM-DD'));
            var end_date    = new Date(picker.endDate.format('YYYY-MM-DD'));

            // To calculate the time difference of two dates
            var Difference_In_Time = end_date.getTime() - start_date.getTime();
            
            // To calculate the no. of days between two dates
            var Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

            console.log(start_date);
            console.log(end_date);
            console.log(Difference_In_Days+1);

            $('#duration').val(Difference_In_Days+1).trigger('change');
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD')).trigger('change');
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD')).trigger('change');
            
        }).on('cancel.daterangepicker', function(ev, picker) {
            
        });

        $(document).on('click', '.btn-xhr-submit', function(e) {
            e.preventDefault();

            var button = $(this);
            var form = $('.form-xhr');
            var action = form.attr('action');

            button.attr('disabled', true);

            if (form.valid()) {
                console.log("Masuk sini");
                $.post(action, form.serialize()).done(function(data) {
                    var obj = $.parseJSON(data);

                    if (obj.type == 'danger') {
                        toastr.options.timeOut = 10000;
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.error(obj.info);
                    } else {
                        toastr.options.positionClass = 'toast-top-right';
                        toastr.success(obj.info);

                        form.reset();

                        $('[data-dismiss="modal"]').trigger('click');

                        if (datatable) {
                            datatable.ajax.reload(null, false);
                        }
                    }
                });
            }

            button.attr('disabled', false);
        });


        $(buttonDeleteDocumentItem).on('click', function(e) {
            e.preventDefault();

            var url = $(this).attr('href');
            var tr = $(this).closest('tr');

            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    var data = JSON.parse(response);

                    // Remove the row from the table
                    $(tr).remove();

                    // Update the total in the footer
                    $('#total').val(data.total);
                    $('#table-document tfoot th:last').text(data.total.toFixed(2));

                    // Check if table is empty
                    // if ($("#table-document > tbody > tr").length == 0) {
                    //     $(buttonSubmitDocument).attr('disabled', true);
                    // }
                    window.location.reload();


                    
                },
                error: function() {
                    alert('Failed to delete the item.');
                }
            });
        });



        //END
        
        // Toggle leave details visibility
        $(document).on('click', '#toggle_leave_details', function(e) {
            e.preventDefault();
            var $detailsList = $('#annual_leave_details_list');
            var $button = $(this);
            
            if ($detailsList.is(':visible')) {
                $detailsList.slideUp();
                $button.html('<i class="fa fa-eye"></i> Show Leave Details');
            } else {
                $detailsList.slideDown();
                $button.html('<i class="fa fa-eye-slash"></i> Hide Leave Details');
            }
        });
        
    });


    $('#employee_number').change(function () {
        var sourceUrl = $('#type_leave').data('source-get-type-leave-list');
        // var type = $('#type_leave').val();
        var id_leave_plan = $('#id_leave_plan').val();

        var warehouse = $('#employee_number option:selected').data('get-warehouse');  
        var gender = $('#employee_number option:selected').data('gender');
        $('#warehouse').val(warehouse).trigger('change');
        var warehouse2 = $('#warehouse').val();
        
        var today = new Date();
        var twoWeeksLater = new Date();
        twoWeeksLater.setDate(today.getDate() + 14);
        $.ajax({
            url: sourceUrl,
            type: 'GET',
            data: { gender: gender ,id_leave_plan: id_leave_plan},
            success: function (data) {
                console.log('hasilfetch');
                console.log(data);
                var response = $.parseJSON(data);
                let $select = $('#type_leave');

                // Clear current options and append the default option
                $select.empty().append('<option value="">---Choose Leave----</option>');
                console.log('selectedValue' + selectedLeave);


                if (response.length > 0) {
                    $.each(response, function (index, leave) {
                        // $select.append(`<option data-account-ben-type="${benefit.benefit_type}" 
                        //                             data-account-id="${benefit.id}" 
                        //                             data-account-ben-code="${benefit.benefit_code}" 
                        //                             data-account-code="${benefit.kode_akun}" 
                        //                             value="${benefit.employee_benefit}">
                        //                             ${benefit.employee_benefit}
                        //                 </option>`);
                        var isSelected = (leave.id == selectedLeave) ? 'selected' : '';
                        var option = `<option value="${leave.id}" 
                                        data-account-ben-type="${leave.benefit_type}" 
                                        data-leave-id="${leave.id}" 
                                        data-leave-code="${leave.leave_code}" 
                                        data-leave-name="${leave.name_leave}"
                                        ${isSelected}>
                                        ${leave.name_leave}
                                    </option>`;
                        $select.append(option);

                        // if(isSelected == 'selected'){
                        //     var benefit_type = $('#type_reimbursement option:selected').data('account-ben-type');
                        //     $('#type_benefit').val(benefit_type).trigger('change');
                        // }
                    });
                }

                // Trigger change event if needed
                // $select.trigger('change');
                
                // Check contract period if leave type is already selected
                var current_leave_type = $('#type_leave').val();
                if (current_leave_type) {
                    checkContractPeriod(current_leave_type);
                }
                
            },
            error: function () {
                toastr.error('Failed to update benefits. Please try again.');
            }
        });
    });

    

    

    Pace.on('start', function() {
        $('.progress-overlay').show();
    });

    Pace.on('done', function() {
        $('.progress-overlay').hide();
    });

    function addRow() {
        var row_payment = $('.table-row-item tbody').html();
        var el = $(row_payment);
        $('#table-document tbody').append(el);
        $('#table-document tbody tr:last').find('input[name="amount[]"]').number(true, 2, '.', ',');

        btn_row_delete_item();
    }

    function btn_row_delete_item() {
        $('.btn-row-delete-item').click(function () {
            $(this).parents('tr').remove();
        });
    }

    $('.number').number(true, 2, '.', ',');

    $('.select2').select2({
        // theme: "bootstrap",
    });

    var selectedBenefit = "<?= isset($_SESSION['leave_plan']['type']) ? $_SESSION['leave_plan']['type'] : ''; ?>";



    function showError(inputElement, message) {
        let formGroup = inputElement.closest(".form-group");
        
        if (formGroup) {
            formGroup.classList.add("has-error"); // Highlight the field
            let errorMessage = document.createElement("span");
            errorMessage.className = "text-danger error-message";
            errorMessage.innerText = message;
            formGroup.appendChild(errorMessage);
        }
    }

    // Variable to track last toast message to prevent duplicates
    var lastToastMessage = '';
    var lastToastTime = 0;
    
    function validateLeaveQuota(showToast = true) {
        var total_leave_days = parseInt($('#total_leave_days').val()) || 0;
        var annual_leave_remaining = parseInt($('#annual_leave_remaining_hidden').val()) || 0;
        var annual_leave_quota = parseInt($('#annual_leave_quota_hidden').val()) || 0;
        var leave_type = $('#type_leave').val();
        var $saveButton = $('#btn-submit-document');
        
        // Only validate for annual leave (L01)
        var leave_code = $('#type_leave option:selected').data('leave-code');
        
        if (leave_code === 'L01') {
            // Check if there's any quota available
            if (annual_leave_quota > 0) {
                if (total_leave_days > annual_leave_remaining) {
                    $saveButton.attr('disabled', true);
                    $saveButton.addClass('btn-disabled');
                    
                    // Show toast only if requested and not duplicate
                    if (showToast) {
                        var currentMessage = 'quota_exceeded_' + total_leave_days + '_' + annual_leave_remaining;
                        var currentTime = Date.now();
                        
                        if (lastToastMessage !== currentMessage || (currentTime - lastToastTime) > 3000) {
                            toastr.error(
                                'Total hari cuti (' + total_leave_days + ') melebihi sisa kuota cuti tahunan (' + annual_leave_remaining + ' hari). Silakan kurangi jumlah hari cuti.', 
                                'Kuota Cuti Terlampaui', 
                                {timeOut: 10000, positionClass: 'toast-top-right', closeButton: true}
                            );
                            lastToastMessage = currentMessage;
                            lastToastTime = currentTime;
                        }
                    }
                    return false;
                } else if (total_leave_days > 0 && annual_leave_remaining >= total_leave_days) {
                    // Valid quota usage - clear toast tracking
                    lastToastMessage = '';
                    $saveButton.attr('disabled', false);
                    $saveButton.removeClass('btn-disabled');
                    return true;
                } else if (total_leave_days === 0) {
                    // No leave days entered yet - clear toast tracking
                    lastToastMessage = '';
                    $saveButton.attr('disabled', false);
                    $saveButton.removeClass('btn-disabled');
                    return true;
                } else {
                    // Edge case: negative remaining quota
                    $saveButton.attr('disabled', true);
                    $saveButton.addClass('btn-disabled');
                    return false;
                }
            } else {
                // No quota available at all
                if (total_leave_days > 0) {
                    $saveButton.attr('disabled', true);
                    $saveButton.addClass('btn-disabled');
                    
                    // Show toast only if requested and not duplicate
                    if (showToast) {
                        var currentMessage = 'no_quota_available';
                        var currentTime = Date.now();
                        
                        if (lastToastMessage !== currentMessage || (currentTime - lastToastTime) > 3000) {
                            toastr.error(
                                'Karyawan tidak memiliki kuota cuti tahunan yang tersedia.', 
                                'Tidak Ada Kuota Cuti', 
                                {timeOut: 10000, positionClass: 'toast-top-right', closeButton: true}
                            );
                            lastToastMessage = currentMessage;
                            lastToastTime = currentTime;
                        }
                    }
                    return false;
                } else {
                    // Clear toast tracking
                    lastToastMessage = '';
                    $saveButton.attr('disabled', false);
                    $saveButton.removeClass('btn-disabled');
                    return true;
                }
            }
        } else {
            // Not annual leave, no quota validation needed - clear toast tracking
            lastToastMessage = '';
            $saveButton.attr('disabled', false);
            $saveButton.removeClass('btn-disabled');
            return true;
        }
    }

    function validateDateRange(showToast = true) {
        var leave_code = $('#type_leave option:selected').data('leave-code');
        var startVal = $('#leave_start_date').val();
        var endVal = $('#leave_end_date').val();
        var $saveButton = $('#btn-submit-document');
        
        // Only validate for annual leave (L01)
        if (leave_code === 'L01' && startVal && endVal) {
            // Get contract period from stored data
            var contractStartStr = $('#contract_start_hidden').val();
            var contractEndStr = $('#contract_end_hidden').val();
            
            if (contractStartStr && contractEndStr) {
                // Parse dates
                var [ds, ms, ys] = startVal.split('-');
                var [de, me, ye] = endVal.split('-');
                var [dcs, mcs, ycs] = contractStartStr.split('-');
                var [dce, mce, yce] = contractEndStr.split('-');
                
                var startDate = new Date(`${ys}-${ms}-${ds}`);
                var endDate = new Date(`${ye}-${me}-${de}`);
                var contractStart = new Date(`${ycs}-${mcs}-${dcs}`);
                var contractEnd = new Date(`${yce}-${mce}-${dce}`);
                
                if (startDate < contractStart || endDate > contractEnd) {
                    $saveButton.attr('disabled', true);
                    $saveButton.addClass('btn-disabled');
                    
                    // Show toast only if requested and not duplicate
                    if (showToast) {
                        var contractStartFormatted = contractStart.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
                        var contractEndFormatted = contractEnd.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'});
                        
                        var currentMessage = 'date_range_invalid_' + startVal + '_' + endVal;
                        var currentTime = Date.now();
                        
                        if (lastToastMessage !== currentMessage || (currentTime - lastToastTime) > 3000) {
                            toastr.error(
                                'Tanggal cuti harus berada dalam periode kontrak aktif: ' + contractStartFormatted + ' s/d ' + contractEndFormatted, 
                                'Tanggal Cuti Tidak Valid', 
                                {timeOut: 10000, positionClass: 'toast-top-right', closeButton: true}
                            );
                            lastToastMessage = currentMessage;
                            lastToastTime = currentTime;
                        }
                    }
                    return false;
                } else {
                    // Date range is valid - clear toast tracking and continue with quota validation
                    if (lastToastMessage.includes('date_range_invalid_')) {
                        lastToastMessage = '';
                    }
                    return validateLeaveQuota(false);
                }
            }
        }
        
        return validateLeaveQuota(false); // Silent validation when called from validateDateRange
    }

    function checkContractPeriod(leave_type) {
        var employee_number = $('#employee_number').val();
        
        if (!employee_number || !leave_type) {
            $('#contract_period_group').hide();
            $('#annual_leave_usage_group').hide();
            $('#contract_period_info').val('');
            $('#annual_leave_usage_info').val('');
            $('#annual_leave_remaining_hidden').val('0');
            $('#annual_leave_quota_hidden').val('0');
            $('#contract_start_hidden').val('');
            $('#contract_end_hidden').val('');
            
            // Enable save button when no data
            $('#btn-submit-document').attr('disabled', false).removeClass('btn-disabled');
            
            // Re-enable form fields when no selection
            $('#leave_start_date').attr('disabled', false);
            $('#leave_end_date').attr('disabled', false);
            $('#total_leave_days').attr('disabled', false);
            $('#reason').attr('disabled', false);
            return;
        }

        $.ajax({
            url: '<?= site_url($module['route'] . '/get_contract_period'); ?>',
            type: 'GET',
            data: { 
                employee_number: employee_number,
                leave_type: leave_type
            },
            success: function (response) {
                console.log('Contract period response:', response);
                var data = JSON.parse(response);
                
                if (data.status === 'success') {
                    $('#contract_period_info').val(data.contract_period);
                    $('#contract_period_group').show();
                    
                    // Show annual leave usage information with extended period
                    var usageText = '';
                    if (data.annual_leave_quota > 0) {
                        usageText = 'Sisa kuota: ' + data.annual_leave_remaining + ' hari (dari total ' + data.annual_leave_quota + ' hari)';
                    } else {
                        usageText = data.total_annual_leave_used + ' hari digunakan dalam periode perencanaan yang diperpanjang (' + data.extended_period + ')';
                    }
                    $('#annual_leave_usage_info').val(usageText);
                    $('#annual_leave_usage_group').show();
                    
                    // Store quota data in hidden inputs for validation
                    $('#annual_leave_remaining_hidden').val(data.annual_leave_remaining || 0);
                    $('#annual_leave_quota_hidden').val(data.annual_leave_quota || 0);
                    
                    // Store contract dates for validation (convert from 'd M Y' format to 'dd-mm-yyyy')
                    if (data.start_date && data.end_date) {
                        // We need to convert from response format to dd-mm-yyyy format for validation
                        var contractStartParts = data.start_date.split(' ');
                        var contractEndParts = data.end_date.split(' ');
                        
                        var monthNames = {'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04', 
                                         'May': '05', 'Jun': '06', 'Jul': '07', 'Aug': '08', 
                                         'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'};
                        
                        if (contractStartParts.length === 3 && contractEndParts.length === 3) {
                            var startFormatted = contractStartParts[0].padStart(2, '0') + '-' + 
                                                (monthNames[contractStartParts[1]] || '01') + '-' + 
                                                contractStartParts[2];
                            var endFormatted = contractEndParts[0].padStart(2, '0') + '-' + 
                                              (monthNames[contractEndParts[1]] || '12') + '-' + 
                                              contractEndParts[2];
                                              
                            $('#contract_start_hidden').val(startFormatted);
                            $('#contract_end_hidden').val(endFormatted);
                        }
                    }
                    
                    // Populate leave details
                    var detailsHtml = '';
                    if (data.annual_leave_details && data.annual_leave_details.length > 0) {
                        data.annual_leave_details.forEach(function(detail) {
                            var statusColor = '#28a745'; // Green for APPROVED
                            if (detail.status === 'PLAN') statusColor = '#ffc107'; // Yellow for PLAN
                            if (detail.status === 'PROCESSED') statusColor = '#17a2b8'; // Blue for PROCESSED
                            
                            detailsHtml += '<div style="margin-bottom: 8px; padding: 8px; background-color: #ffffff; border-left: 4px solid ' + statusColor + '; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); font-size: 11px;">';
                            detailsHtml += '<div style="display: flex; justify-content: space-between; align-items: center;">';
                            detailsHtml += '<strong style="color: #333;">' + detail.document_number + '</strong>';
                            detailsHtml += '<span style="background-color: ' + statusColor + '; color: white; padding: 2px 6px; border-radius: 12px; font-size: 10px;">' + detail.status + '</span>';
                            detailsHtml += '</div>';
                            detailsHtml += '<div style="margin-top: 4px;">';
                            detailsHtml += '<span class="text-muted" style="font-size: 10px;">' + detail.leave_start_date + ' - ' + detail.leave_end_date + '</span><br>';
                            detailsHtml += '<span style="color: #dc3545; font-weight: bold; font-size: 11px;">' + detail.total_days + ' days</span>';
                            detailsHtml += '</div>';
                            detailsHtml += '</div>';
                        });
                        $('#annual_leave_details_list').html(detailsHtml);
                        $('#annual_leave_details').show();
                        // Reset toggle button
                        $('#toggle_leave_details').html('<i class="fa fa-eye"></i> Show Leave Details');
                        $('#annual_leave_details_list').hide();
                    } else {
                        $('#annual_leave_details_list').html('<div style="text-align: center; padding: 20px;"><i class="text-muted" style="font-size: 12px;">No annual leave records found in current contract period.</i></div>');
                        $('#annual_leave_details').show();
                        // Reset toggle button
                        $('#toggle_leave_details').html('<i class="fa fa-eye"></i> Show Leave Details');
                        $('#annual_leave_details_list').hide();
                    }
                    
                    // Show info message
                    toastr.info(data.message, 'Contract Period Info', {timeOut: 8000});
                    
                    // Re-enable form fields when contract is found
                    $('#leave_start_date').attr('disabled', false);
                    $('#leave_end_date').attr('disabled', false);
                    $('#total_leave_days').attr('disabled', false);
                    $('#reason').attr('disabled', false);
                    
                    // Validate leave quota and date range after loading contract data (silent)
                    setTimeout(function() {
                        validateDateRange(false); // Silent validation
                    }, 200);
                } else if (data.status === 'error') {
                    // No active contract found - this is a blocking error
                    $('#contract_period_group').hide();
                    $('#annual_leave_usage_group').hide();
                    $('#contract_period_info').val('');
                    $('#annual_leave_usage_info').val('');
                    $('#annual_leave_remaining_hidden').val('0');
                    $('#annual_leave_quota_hidden').val('0');
                    $('#contract_start_hidden').val('');
                    $('#contract_end_hidden').val('');
                    
                    // Disable save button when no active contract
                    $('#btn-submit-document').attr('disabled', true).addClass('btn-disabled');
                    
                    // Show error toast message
                    toastr.error(data.message, 'Kontrak Tidak Aktif', {
                        timeOut: 15000,
                        closeButton: true,
                        positionClass: 'toast-top-right'
                    });
                    
                    // Clear form fields to prevent partial submission
                    $('#leave_start_date').val('').attr('disabled', true);
                    $('#leave_end_date').val('').attr('disabled', true);
                    $('#total_leave_days').val('').attr('disabled', true);
                    $('#reason').attr('disabled', true);
                } else if (data.status === 'warning') {
                    $('#contract_period_info').val('No active contract found');
                    $('#contract_period_group').show();
                    $('#annual_leave_usage_group').hide();
                    $('#annual_leave_remaining_hidden').val('0');
                    $('#annual_leave_quota_hidden').val('0');
                    
                    // Enable save button when no contract found
                    $('#btn-submit-document').attr('disabled', false).removeClass('btn-disabled');
                    
                    // Show warning message
                    toastr.warning(data.message, 'Contract Warning');
                } else {
                    // Not annual leave, hide the fields and reset validation
                    $('#contract_period_group').hide();
                    $('#annual_leave_usage_group').hide();
                    $('#contract_period_info').val('');
                    $('#annual_leave_usage_info').val('');
                    $('#annual_leave_remaining_hidden').val('0');
                    $('#annual_leave_quota_hidden').val('0');
                    
                    // Enable save button for non-annual leave
                    $('#btn-submit-document').attr('disabled', false).removeClass('btn-disabled');
                    
                    // Re-enable form fields for non-annual leave
                    $('#leave_start_date').attr('disabled', false);
                    $('#leave_end_date').attr('disabled', false);
                    $('#total_leave_days').attr('disabled', false);
                    $('#reason').attr('disabled', false);
                }
            },
            error: function () {
                toastr.error('Failed to check contract period. Please try again.');
                $('#contract_period_group').hide();
                $('#annual_leave_usage_group').hide();
                $('#contract_period_info').val('');
                $('#annual_leave_usage_info').val('');
                $('#annual_leave_remaining_hidden').val('0');
                $('#annual_leave_quota_hidden').val('0');
                
                // Enable save button on error
                $('#btn-submit-document').attr('disabled', false).removeClass('btn-disabled');
            }
        });
    }

    function popup(mylink, windowname){
        var height = window.innerHeight;
        var widht;
        var href;

        if (screen.availWidth > 768){
            width = 769;
        } else {
            width = screen.availWidth;
        }

        var left = (screen.availWidth / 2) - (width / 2);
        var top = 0;
        // var top = (screen.availHeight / 2) - (height / 2);

        if (typeof(mylink) == 'string') href = mylink;
        else href = mylink.href;

        window.open(href, windowname, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+width+', height='+height+', top='+top+', left='+left);

        if (! window.focus) return true;
        else return false;
    }

    (function($) {
        $.fn.reset = function() {
            this.find('input:text, input[type="email"], input:password, select, textarea').val('');
            this.find('input:radio, input:checkbox').prop('checked', false);
            return this;
        }

        $.fn.redirect = function(target) {
            var url = $(this).data('href');

            if (target == '_blank') {
                window.open(url, target);
            } else {
                window.document.location = url;
            }
        }

        $.fn.popup = function() {
            var popup = $(this).data('target');
            var source = $(this).data('source');

            $.get(source, function(data) {
                var obj = $.parseJSON(data);

                if (obj.type == 'denied') {
                    toastr.options.timeOut = 10000;
                    toastr.options.positionClass = 'toast-top-right';
                    toastr.error(obj.info, 'ACCESS DENIED!');
                } else {
                    $(popup)
                        .find('.modal-body')
                        .empty()
                        .append(obj.info);

                    $(popup).modal('show');

                    $(popup).on('click', '.modal-header:not(a)', function() {
                        $(popup).modal('hide');
                    });

                    $(popup).on('click', '.modal-footer:not(a)', function() {
                        $(popup).modal('hide');
                    });
                }
            })
        }
    }(jQuery));

    function submit_post_via_hidden_form(url, params) {
        var f = $("<form target='_blank' method='POST' style='display:none;'></form>").attr('action', url).appendTo(document.body);

        $.each(params, function(key, value) {
            var hidden = $('<input type="hidden" />').attr({
                name: key,
                value: JSON.stringify(value)
            });

            hidden.appendTo(f);
        });

        f.submit();
        f.remove();
    }

    function numberFormat(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }

    $(document).on('keydown', function(event) {
        if ((event.metaKey || event.ctrlKey) && (
            String.fromCharCode(event.which).toLowerCase() === '0' ||
            String.fromCharCode(event.which).toLowerCase() === 'a' ||
            String.fromCharCode(event.which).toLowerCase() === 'd' ||
            String.fromCharCode(event.which).toLowerCase() === 'e' ||
            String.fromCharCode(event.which).toLowerCase() === 'i' ||
            String.fromCharCode(event.which).toLowerCase() === 'o' ||
            String.fromCharCode(event.which).toLowerCase() === 's' ||
            String.fromCharCode(event.which).toLowerCase() === 'x')) 
        {
            event.preventDefault();
        }
    });


</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
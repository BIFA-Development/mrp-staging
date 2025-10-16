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
                                    <input type="text" name="document_number" id="document_number" class="form-control" value="<?= $_SESSION['leave']['document_number']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_doc_number'); ?>" readonly>
                                    <label for="document_number">No Form</label>
                                </div>
                                <span class="input-group-addon"><?= $_SESSION['leave']['format_number']; ?></span>
                            </div>
                        </div>

                        <!-- <div class="form-group" style="padding-top: 25px;">
                            <select name="type_leave" id="type_leave" class="form-control select2">
                            <option> -- Pilih Tipe Cuti --</option>
                                <?php foreach(getLeaveType($_SESSION['leave']['gender'], $_SESSION['leave']['id_leave_plan']) as $leaveType):?>
                                <option data-leave-id="<?=$leaveType['id'];?>" data-leave-code="<?=$leaveType['leave_code'];?>" data-leave-name="<?=$leaveType['name_leave'];?>" value="<?=$leaveType['id'];?>" <?= ($leaveType['id'] == $_SESSION['leave']['leave_type']) ? 'selected' : ''; ?>><?=$leaveType['name_leave'];?></option>
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
                            <select name="employee_number" id="employee_number" class="form-control select2" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_employee_number'); ?>" data-source-get-maternity="<?= site_url($module['route'] . '/get_maternity_leave'); ?>" data-source-get-annual="<?= site_url($module['route'] . '/get_annual_leave'); ?>" data-source-get-longleave="<?= site_url($module['route'] . '/get_long_leave'); ?>">
                                <option></option>
                                <?php foreach(available_employee($_SESSION['leave']['department_id'], config_item('auth_role'), config_item('auth_user_id')) as $user):?>
                                <option data-get-warehouse="<?=$user['warehouse'];?>"  data-department-id="<?=$user['department_id'];?>" data-department-name="<?=$user['department_name'];?>" data-gender="<?=$user['gender'];?>" data-employee-number="<?=$user['employee_number'];?>" data-position="<?=$user['position'];?>" value="<?=$user['employee_number'];?>" <?= ($user['employee_number'] == $_SESSION['leave']['employee_number']) ? 'selected' : ''; ?>><?=$user['name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="employee_number">Name</label>
                        </div>

                        
                        <div class="form-group">
                            <input type="text" name="department_name" id="department_name" class="form-control" value="<?= $_SESSION['leave']['department_name']; ?>" readonly>
                            <label for="department_name">Department</label>
                        </div>

                        <div class="form-group">
                            <select name="head_dept" id="head_dept" class="form-control" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_head_dept'); ?>" required>
                                <option></option>
                                <?php foreach(list_user_in_head_department($_SESSION['leave']['department_id']) as $head):?>
                                <option value="<?=$head['user_id'];?>" <?= ( getEmployeeById($head['user_id'])['employee_number'] == $_SESSION['leave']['head_dept']) ? 'selected' : ''; ?>><?=$head['person_name'];?></option>
                                <?php endforeach;?>
                            </select>
                            <label for="head_dept">Atasan</label>
                        </div>
                    </div>

                    <div class="col-sm-12 col-lg-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="leave_start_date" id="leave_start_date" data-provide="datepicker" data-date-format="dd-mm-yyyy" class="form-control" value="<?= $_SESSION['leave']['leave_start_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_leave_start_date'); ?>" required>
                                    <label for="leave_start_date">Leave Start Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="leave_end_date" id="leave_end_date" data-provide="datepicker" data-date-format="dd-mm-yyyy" class="form-control" value="<?= $_SESSION['leave']['leave_end_date']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_leave_end_date'); ?>" required>
                                    <label for="leave_end_date">Leave End Date</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="number" name="total_leave_days" id="total_leave_days" class="form-control number" value="<?= $_SESSION['leave']['total_leave_days']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_total_leave_days'); ?>" readonly>
                                    <label for="total_leave_days">Jumlah Hari Cuti</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" id="left_leave_group">
                                    <input type="number" name="left_leave" id="left_leave" class="form-control number" value="<?= $_SESSION['leave']['left_leave']; ?>" data-input-type="autoset" readonly>
                                    <label for="left_leave">Sisa Cuti</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Pengaturan Hari Kerja</label>
                                    <div class="radio-list" style="margin-top: 10px;">
                                        <div class="radio">
                                            <input type="radio" name="weekend_option" id="include_all" value="include_all" checked>
                                            <label for="include_all">Hitung semua hari (termasuk Sabtu & Minggu)</label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="weekend_option" id="ignore_saturday" value="ignore_saturday">
                                            <label for="ignore_saturday">Abaikan Sabtu saja</label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="weekend_option" id="ignore_sunday" value="ignore_sunday">
                                            <label for="ignore_sunday">Abaikan Minggu saja</label>
                                        </div>
                                        <div class="radio">
                                            <input type="radio" name="weekend_option" id="ignore_both" value="ignore_both">
                                            <label for="ignore_both">Abaikan Sabtu & Minggu</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal untuk konfirmasi weekend options -->
                        <div class="modal fade" id="weekendOptionsModal" tabindex="-1" role="dialog" aria-labelledby="weekendOptionsModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="weekendOptionsModalLabel">Pengaturan Hari Kerja</h5>
                                    </div>
                                    <div class="modal-body">
                                        <p>Perhitungan cuti Anda saat ini termasuk hari Sabtu dan/atau Minggu.</p>
                                        <p>Pilih pengaturan hari kerja untuk mengurangi jumlah hari cuti:</p>
                                        <div class="radio-list">
                                            <div class="radio">
                                                <input type="radio" name="modal_weekend_option" id="modal_include_all" value="include_all">
                                                <label for="modal_include_all">Hitung semua hari (termasuk Sabtu & Minggu)</label>
                                            </div>
                                            <div class="radio">
                                                <input type="radio" name="modal_weekend_option" id="modal_ignore_saturday" value="ignore_saturday">
                                                <label for="modal_ignore_saturday">Abaikan Sabtu saja</label>
                                            </div>
                                            <div class="radio">
                                                <input type="radio" name="modal_weekend_option" id="modal_ignore_sunday" value="ignore_sunday">
                                                <label for="modal_ignore_sunday">Abaikan Minggu saja</label>
                                            </div>
                                            <div class="radio">
                                                <input type="radio" name="modal_weekend_option" id="modal_ignore_both" value="ignore_both" checked>
                                                <label for="modal_ignore_both">Abaikan Sabtu & Minggu</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="btnBatal">Batal</button>
                                        <button type="button" class="btn btn-primary" id="btnTerapkan">Terapkan</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <textarea name="reason" id="reason" class="form-control" rows="4" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_reason'); ?>" required ><?= $_SESSION['leave']['reason']; ?></textarea>
                            <label for="reason">Reason</label>
                        </div>


                        <div class="form-group hide">
                            <input type="text" name="leave_type" id="leave_type" class="form-control" value="<?= $_SESSION['leave']['leave_type']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_leave_type'); ?>" readonly>
                            <label for="leave_type">leave type</label>
                        </div> 

                        <div class="form-group hide">
                            <input type="text" name="id_leave_plan" id="id_leave_plan" class="form-control" value="<?= $_SESSION['leave']['id_leave_plan']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_id_leave_plan'); ?>" readonly>
                            <label for="id_leave_plan">id_leave_plan</label>
                        </div> 

                        <div class="form-group hide">
                            <input type="text" name="employee_has_leave_id" id="employee_has_leave_id" class="form-control" value="<?= $_SESSION['leave']['employee_has_leave_id']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_employee_has_leave_id'); ?>" readonly>
                            <label for="employee_has_leave_id">employee_has_leave_id</label>
                        </div> 

                        <div class="form-group">
                            <input type="text" name="warehouse" id="warehouse" class="form-control" value="<?= $_SESSION['leave']['warehouse']; ?>" data-input-type="autoset" data-source="<?= site_url($module['route'] . '/set_warehouse'); ?>" readonly>
                            <label for="warehouse">Warehouse</label>
                        </div> 
                    </div>

                    <div class="col-sm-12 col-lg-4">

                    <div class="form-group" id="holiday_list_group" style="display:none;">
                        <label>Hari Libur Nasional</label>
                        <ul id="holiday_list" class="form-control-static" style="margin-top: 10px; padding-left: 20px;"></ul>
                    </div>

                    <!-- <div class="form-group">
                        <label>Last Contract Date</label>
                        <p class="form-control-static" style="margin-top: 10px;">
                            <?= $_SESSION['leave']['contract_number']; ?>
                            <?= $_SESSION['leave']['start_contract']; ?>
                            -
                            <?= $_SESSION['leave']['end_contract']; ?>
                        </p>
                    </div> -->

                    </div>


                    
                </div>

                <div class="card-actionbar">
                    <div class="card-actionbar-row">
                        <div class="pull-left">
                            <button type="button" href="" onClick="addRow()" class="btn btn-primary ink-reaction pull-left hide">
                            Add
                            </button>

                            <a style="margin-left: 15px;" href="<?= site_url($module['route'] . '/attachment'); ?>" onClick="return popup(this, 'attachment')" class="btn btn-primary ink-reaction">
                                Attachment
                            </a>
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

const holidaysData = <?= json_encode($_SESSION['leave']['holidays']); ?>;
const holidays = holidaysData.map(h => h.holiday_date);
console.log('Holidays:', holidays);
var selectedLeave = "<?= isset($_SESSION['leave']['leave_type']) ? $_SESSION['leave']['leave_type'] : ''; ?>";
window.onload = async function(){
        console.log('mulaiinit');
        var warehouse = $('#employee_number option:selected').data('get-warehouse');  
        var gender = $('#employee_number option:selected').data('gender');  
        console.log(warehouse);
        console.log(gender);

        $('#employee_number').trigger('change');
        
        // var type = $('#type_leave option:selected').data('leave-code');  


        // if (type === 'L01') {
        //     console.log('Init L01');
        //     getAnnualLeave();
        //     $('#left_leave_group').show();
        // } else {
        //     $('#left_leave_group').hide();
        // }


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

    function countWorkingDays(startDate, endDate, holidays = [], weekendOption = 'include_all') {
        console.log('=== CountWorkingDays Debug Start ===');
        console.log('Input startDate:', startDate);
        console.log('Input endDate:', endDate);
        console.log('Input holidays:', holidays);
        console.log('Input weekendOption:', weekendOption);

        // Basic validation
        if (!startDate || !endDate) {
            console.error('Missing date parameters');
            return 0;
        }

        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            console.error('Invalid date objects');
            return 0;
        }

        if (startDate > endDate) {
            console.error('Start date is after end date in countWorkingDays');
            return 0;
        }

        let count = 0;
        const current = new Date(startDate.getTime()); // Create a copy to avoid modifying original
        let iterations = 0;
        const maxIterations = 500; // Safety limit

        console.log('Starting loop...');

        while (current <= endDate && iterations < maxIterations) {
            const day = current.getDay(); // 0 = Sunday, 6 = Saturday
            const dateStr = current.toISOString().slice(0, 10); // Format YYYY-MM-DD
            const isHoliday = holidays.includes(dateStr);
            const isSaturday = (day === 6);
            const isSunday = (day === 0);

            console.log(`Day ${iterations + 1}: ${current.toDateString()} - Day of week: ${day} - Holiday: ${isHoliday}`);

            // Count if not a holiday
            if (!isHoliday) {
                let shouldCount = true;

                // Apply weekend filtering
                switch (weekendOption) {
                    case 'ignore_saturday':
                        shouldCount = !isSaturday;
                        console.log('  Ignore Saturday - Should count:', shouldCount);
                        break;
                    case 'ignore_sunday':
                        shouldCount = !isSunday;
                        console.log('  Ignore Sunday - Should count:', shouldCount);
                        break;
                    case 'ignore_both':
                        shouldCount = !isSaturday && !isSunday;
                        console.log('  Ignore Both - Should count:', shouldCount);
                        break;
                    case 'include_all':
                    default:
                        shouldCount = true;
                        console.log('  Include All - Should count:', shouldCount);
                        break;
                }

                if (shouldCount) {
                    count++;
                    console.log('  ✓ Counted! Total so far:', count);
                } else {
                    console.log('  ✗ Not counted');
                }
            } else {
                console.log('  ✗ Holiday - not counted');
            }

            current.setDate(current.getDate() + 1);
            iterations++;
        }

        console.log('Final count:', count);
        console.log('=== CountWorkingDays Debug End ===');

        return count;
    }

    function updateLeaveDays() {
        const startVal = $('#leave_start_date').val();
        const endVal = $('#leave_end_date').val();

        console.log('=== UpdateLeaveDays Debug Start ===');
        console.log('Start val:', startVal);
        console.log('End val:', endVal);

        if (!startVal || !endVal) {
            console.log('Missing start or end date');
            return;
        }

        // Parse dates - detect format automatically
        const startParts = startVal.split('-');
        const endParts = endVal.split('-');

        console.log('Start parts:', startParts);
        console.log('End parts:', endParts);

        if (startParts.length !== 3 || endParts.length !== 3) {
            console.error('Invalid date format');
            return;
        }

        let startDay, startMonth, startYear, endDay, endMonth, endYear;

        // Detect format based on first part length and value
        if (startParts[0].length === 4 && parseInt(startParts[0]) > 1900) {
            // Format is yyyy-mm-dd
            console.log('Detected format: yyyy-mm-dd');
            startYear = parseInt(startParts[0], 10);
            startMonth = parseInt(startParts[1], 10);
            startDay = parseInt(startParts[2], 10);
            endYear = parseInt(endParts[0], 10);
            endMonth = parseInt(endParts[1], 10);
            endDay = parseInt(endParts[2], 10);
        } else {
            // Format is dd-mm-yyyy
            console.log('Detected format: dd-mm-yyyy');
            startDay = parseInt(startParts[0], 10);
            startMonth = parseInt(startParts[1], 10);
            startYear = parseInt(startParts[2], 10);
            endDay = parseInt(endParts[0], 10);
            endMonth = parseInt(endParts[1], 10);
            endYear = parseInt(endParts[2], 10);
        }

        console.log('Parsed start components:', { day: startDay, month: startMonth, year: startYear });
        console.log('Parsed end components:', { day: endDay, month: endMonth, year: endYear });

        // Create dates: new Date(year, month-1, day)
        const startDate = new Date(startYear, startMonth - 1, startDay);
        const endDate = new Date(endYear, endMonth - 1, endDay);

        console.log('Parsed start date:', startDate);
        console.log('Parsed end date:', endDate);

        // Basic validation
        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            console.error('Invalid date objects created');
            $('#total_leave_days').val(0).trigger('change');
            return;
        }

        // Validate year is reasonable (between 2020 and 2030)
        const currentYear = new Date().getFullYear();
        if (startYear < 2020 || startYear > 2030 || endYear < 2020 || endYear > 2030) {
            console.error('Invalid year detected. Start year:', startYear, 'End year:', endYear);
            $('#total_leave_days').val(0).trigger('change');
            return;
        }

        if (startDate > endDate) {
            console.log('Start date is after end date');
            $('#total_leave_days').val(0).trigger('change');
            return;
        }

        const weekendOption = $('input[name="weekend_option"]:checked').val() || 'include_all';
        console.log('Selected weekend option:', weekendOption);
        
        const workingDays = countWorkingDays(startDate, endDate, holidays, weekendOption);
        console.log('Final calculated working days:', workingDays);

        // Set the value
        $('#total_leave_days').val(workingDays).trigger('change');
        console.log('=== UpdateLeaveDays Debug End ===');
    }

    function checkDateRangeHasWeekends() {
        const startVal = $('#leave_start_date').val();
        const endVal = $('#leave_end_date').val();

        if (!startVal || !endVal) return { hasWeekends: false, hasSaturday: false, hasSunday: false };

        const startParts = startVal.split('-');
        const endParts = endVal.split('-');

        if (startParts.length !== 3 || endParts.length !== 3) {
            return { hasWeekends: false, hasSaturday: false, hasSunday: false };
        }

        let dayStart, monthStart, yearStart, dayEnd, monthEnd, yearEnd;

        // Detect format based on first part length and value
        if (startParts[0].length === 4 && parseInt(startParts[0]) > 1900) {
            // Format is yyyy-mm-dd
            yearStart = parseInt(startParts[0], 10);
            monthStart = parseInt(startParts[1], 10);
            dayStart = parseInt(startParts[2], 10);
            yearEnd = parseInt(endParts[0], 10);
            monthEnd = parseInt(endParts[1], 10);
            dayEnd = parseInt(endParts[2], 10);
        } else {
            // Format is dd-mm-yyyy
            dayStart = parseInt(startParts[0], 10);
            monthStart = parseInt(startParts[1], 10);
            yearStart = parseInt(startParts[2], 10);
            dayEnd = parseInt(endParts[0], 10);
            monthEnd = parseInt(endParts[1], 10);
            yearEnd = parseInt(endParts[2], 10);
        }

        // Validate components
        if (isNaN(dayStart) || isNaN(monthStart) || isNaN(yearStart) || 
            isNaN(dayEnd) || isNaN(monthEnd) || isNaN(yearEnd) ||
            dayStart < 1 || dayStart > 31 || monthStart < 1 || monthStart > 12 ||
            dayEnd < 1 || dayEnd > 31 || monthEnd < 1 || monthEnd > 12 ||
            yearStart < 1900 || yearEnd < 1900) {
            return { hasWeekends: false, hasSaturday: false, hasSunday: false };
        }

        // Create date objects
        const startDate = new Date(yearStart, monthStart - 1, dayStart);
        const endDate = new Date(yearEnd, monthEnd - 1, dayEnd);

        // Validate that dates are valid
        if (isNaN(startDate.getTime()) || isNaN(endDate.getTime())) {
            return { hasWeekends: false, hasSaturday: false, hasSunday: false };
        }

        if (startDate > endDate) return { hasWeekends: false, hasSaturday: false, hasSunday: false };

        // Loop through the date range to check for weekends
        let hasSaturday = false;
        let hasSunday = false;
        const current = new Date(startDate);
        
        while (current <= endDate) {
            const day = current.getDay(); // 0 = Sunday, 6 = Saturday
            if (day === 6) hasSaturday = true;
            if (day === 0) hasSunday = true;
            
            // If we found both, no need to continue
            if (hasSaturday && hasSunday) break;
            
            current.setDate(current.getDate() + 1);
        }

        return { 
            hasWeekends: hasSaturday || hasSunday, 
            hasSaturday: hasSaturday, 
            hasSunday: hasSunday 
        };
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
        
        // Set initial state of save button based on total_leave_days value
        var initialTotalLeaveDays = parseInt($('#total_leave_days').val()) || 0;
        if (initialTotalLeaveDays <= 0) {
            $('#btn-submit-document').attr('disabled', true);
        } else {
            $('#btn-submit-document').attr('disabled', false);
        }

        var today = new Date();
        var twoWeeksLater = new Date();
        twoWeeksLater.setDate(today.getDate() + 14);

        $('#leave_start_date, #leave_end_date').datepicker({
            autoclose: true,
            format: 'dd-mm-yyyy',
            startDate: today,
        }).on('changeDate', function () {
            updateLeaveDays();
        });

        // Event untuk radio button weekend options
        $('input[name="weekend_option"]').on('change', function () {
            updateLeaveDays();
        });

        // Monitor perubahan pada total_leave_days untuk menampilkan modal dan disable/enable button
        $('#total_leave_days').on('change input', function() {
            var currentValue = parseInt($(this).val()) || 0;
            var previousValue = parseInt(previousTotalLeaveDays) || 0;
            var weekendOption = $('input[name="weekend_option"]:checked').val();
            
            // Disable/enable save button based on total_leave_days value
            if (currentValue <= 0) {
                $('#btn-submit-document').attr('disabled', true);
            } else {
                $('#btn-submit-document').attr('disabled', false);
            }
            
            // Cek apakah tanggal yang dipilih mengandung hari sabtu/minggu
            var weekendInfo = checkDateRangeHasWeekends();
            
            // Cek apakah ada perubahan nilai, nilai lebih besar dari sebelumnya, masih include_all, dan ada weekend di range tanggal
            if (currentValue != previousValue && currentValue > previousValue && weekendOption === 'include_all' && currentValue > 0 && weekendInfo.hasWeekends) {
                $('#weekendOptionsModal').modal('show');
            }
            
            previousTotalLeaveDays = currentValue;
        });

        // Handle button Terapkan di modal
        $('#btnTerapkan').on('click', function() {
            var selectedOption = $('input[name="modal_weekend_option"]:checked').val();
            $('input[name="weekend_option"][value="' + selectedOption + '"]').prop('checked', true);
            $('#weekendOptionsModal').modal('hide');
            updateLeaveDays();
        });

        // Handle button Batal di modal
        $('#btnBatal').on('click', function() {
            $('#weekendOptionsModal').modal('hide');
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
            // twoWeeksLater.setDate(today.getDate() + 14);

            if (leave_code === 'L01') {
                getAnnualLeave();
                $('#left_leave_group').show();
                // $('#is_reserved_group').show();
                $('#leave_start_date').datepicker('setStartDate', twoWeeksLater);
                $('#leave_end_date').datepicker('setStartDate', twoWeeksLater);

            } else if(leave_code === 'L07'){
                getLongLeave();
                $('#left_leave_group').show();
                $('#leave_start_date').datepicker('setStartDate', twoWeeksLater);
                $('#leave_end_date').datepicker('setStartDate', twoWeeksLater);
            }  else if(leave_code === 'L04'){
                getMaternityLeave();
                $('#left_leave_group').show();
                $('#leave_start_date').datepicker('setStartDate', twoWeeksLater);
                $('#leave_end_date').datepicker('setStartDate', twoWeeksLater);
            } else if(leave_code === 'L02'){
                $('#left_leave_group').hide();
                $('#leave_start_date').datepicker('setStartDate', today);
                $('#leave_end_date').datepicker('setStartDate', today);
                // $('#is_reserved_group').hide();
            } else if(leave_code === 'L08') {
                $('#left_leave_group').hide();
                // $('#is_reserved_group').hide();
                $('#leave_start_date').datepicker('setStartDate', twoWeeksLater);
                $('#leave_end_date').datepicker('setStartDate', twoWeeksLater);

            } else {
                $('#left_leave_group').hide();
                // $('#is_reserved_group').hide();
                $('#leave_start_date').datepicker('setStartDate', today);
                $('#leave_end_date').datepicker('setStartDate', today);

            }
            
        });
        


        $(buttonSubmitDocument).on('click', function (e) {
            e.preventDefault();
            var button = $(this);
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
        // This datepicker config is for other date fields, not leave dates
        $('[data-provide="datepicker"]:not(#leave_start_date):not(#leave_end_date)').datepicker({
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
        
    });

    $('#employee_number').change(function () {
        var sourceUrl = $('#type_leave').data('source-get-type-leave-list');
        // var type = $('#type_leave').val();
        var id_leave_plan = $('#id_leave_plan').val();

        var warehouse = $('#employee_number option:selected').data('get-warehouse');  
        var gender = $('#employee_number option:selected').data('gender');
        var employee_number = $('#employee_number option:selected').data('employee-number');
        $('#warehouse').val(warehouse).trigger('change');
        var warehouse2 = $('#warehouse').val();
        console.log('Init Employee');
        console.log('Init Employee2');
        console.log(warehouse);
        console.log(warehouse2);
        console.log('Init gender');
        console.log(gender);
        
        var today = new Date();
        var twoWeeksLater = new Date();
        //twoWeeksLater.setDate(today.getDate() + 14);
        $.ajax({
            url: sourceUrl,
            type: 'GET',
            data: { gender: gender ,id_leave_plan: id_leave_plan,employee_number: employee_number },
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
                $select.trigger('change');
                
            },
            error: function () {
                toastr.error('Failed to update benefits. Please try again.');
            }
        });


        var leave_code = $('#type_leave option:selected').data('leave-code');  
        if (leave_code === 'L01') {
            console.log('Init L01');
            getAnnualLeave();
            $('#left_leave_group').show();
            // $('#is_reserved_group').show();
            $('#leave_start_date').datepicker('setStartDate', twoWeeksLater);
            $('#leave_end_date').datepicker('setStartDate', twoWeeksLater);


        } else if(leave_code === 'L07'){
            getLongLeave();
            $('#left_leave_group').show();
            $('#leave_start_date').datepicker('setStartDate', twoWeeksLater);
            $('#leave_end_date').datepicker('setStartDate', twoWeeksLater);


        } else if(leave_code === 'L02'){
            $('#left_leave_group').hide();
            $('#leave_start_date').datepicker('setStartDate', today);
            $('#leave_end_date').datepicker('setStartDate', today);

            // $('#is_reserved_group').hide();
        } else {
            $('#left_leave_group').hide();
            // $('#is_reserved_group').hide();
            $('#leave_start_date').datepicker('setStartDate', twoWeeksLater);
            $('#leave_end_date').datepicker('setStartDate', twoWeeksLater);

        }
    });

    function getLongLeave() {
        console.log('InitLongLeave');
        var employee_number = $('#employee_number').val();                        
        var url = $('#employee_number').data('source-get-longleave');
        var type = $('#type_leave').val();
        console.log('URL:' +url);
        
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                employee_number   : employee_number,
                type      : type,
            },
            success: function(data) {
                console.log(data);
                var obj = $.parseJSON(data);

                if(obj.status=='success'){
                    $('#left_leave').val('').trigger('change');
                    $('#left_leave').val(obj.left_leave).trigger('change');
                    console.log('Sukses');
                    console.log(obj);

                    $('#employee_has_leave_id').val(obj.employee_has_leave_id).trigger('change');
                    //harus update type_leave nya
                    var leave_type_data = $('#type_leave').val();
                    $('#leave_type').val(leave_type_data).trigger('change');

                    var warehouse_data = $('#warehouse').val();
                    $('#warehouse').val(warehouse_data).trigger('change');

                    var head_data = $('#head_dept').val();
                    $('#head_dept').val(head_data).trigger('change');


                    var employee_has_leave_id = $('#employee_has_leave_id').val();
                    $('#employee_has_leave_id').val(employee_has_leave_id).trigger('change');

                }else{
                    console.log('gagal');
                    toastr.options.timeOut = 2000;
                    toastr.options.positionClass = 'toast-top-right';
                    
                    var leave_type_data = $('#type_leave').val();
                    $('#leave_type').val(leave_type_data).trigger('change');
                    if(obj.status=='error'){
                        toastr.error(obj.message);
                    }else if(obj.status=='warning'){
                        toastr.warning(obj.message);
                    }
                }        
            }
        });
    };

    function getAnnualLeave() {
        console.log('InitAnnualLeave');
        var employee_number = $('#employee_number').val();                        
        var url = $('#employee_number').data('source-get-annual');
        var type = $('#type_leave').val();
        console.log('URL:' +url);
        
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                employee_number   : employee_number,
                type      : type,
            },
            success: function(data) {
                console.log(data);
                var obj = $.parseJSON(data);

                if(obj.status=='success'){
                    $('#left_leave').val('').trigger('change');
                    $('#left_leave').val(obj.left_leave).trigger('change');
                    console.log('Sukses');
                    console.log(obj);

                    $('#employee_has_leave_id').val(obj.employee_has_leave_id).trigger('change');
                    //harus update type_leave nya
                    var leave_type_data = $('#type_leave').val();
                    $('#leave_type').val(leave_type_data).trigger('change');

                    var warehouse_data = $('#warehouse').val();
                    $('#warehouse').val(warehouse_data).trigger('change');

                    var head_data = $('#head_dept').val();
                    $('#head_dept').val(head_data).trigger('change');


                    var employee_has_leave_id = $('#employee_has_leave_id').val();
                    $('#employee_has_leave_id').val(employee_has_leave_id).trigger('change');

                }else{
                    console.log('gagal');
                    toastr.options.timeOut = 2000;
                    toastr.options.positionClass = 'toast-top-right';
                    
                    var leave_type_data = $('#type_leave').val();
                    $('#leave_type').val(leave_type_data).trigger('change');
                    if(obj.status=='error'){
                        toastr.error(obj.message);
                    }else if(obj.status=='warning'){
                        toastr.warning(obj.message);
                    }
                }        
            }
        });
    };

    function getMaternityLeave() {
        console.log('InitAnnualLeave');
        var employee_number = $('#employee_number').val();                        
        var url = $('#employee_number').data('source-get-maternity');
        var type = $('#type_leave').val();
        console.log('URL:' +url);
        
        $.ajax({
            url: url,
            type: 'GET',
            data: {
                employee_number   : employee_number,
                type      : type,
            },
            success: function(data) {
                console.log(data);
                var obj = $.parseJSON(data);

                if(obj.status=='success'){
                    $('#left_leave').val('').trigger('change');
                    $('#left_leave').val(obj.left_leave).trigger('change');
                    console.log('Sukses');
                    console.log(obj);

                    $('#employee_has_leave_id').val(obj.employee_has_leave_id).trigger('change');
                    //harus update type_leave nya
                    var leave_type_data = $('#type_leave').val();
                    $('#leave_type').val(leave_type_data).trigger('change');

                    var warehouse_data = $('#warehouse').val();
                    $('#warehouse').val(warehouse_data).trigger('change');

                    var head_data = $('#head_dept').val();
                    $('#head_dept').val(head_data).trigger('change');


                    var employee_has_leave_id = $('#employee_has_leave_id').val();
                    $('#employee_has_leave_id').val(employee_has_leave_id).trigger('change');

                }else{
                    console.log('gagal');
                    toastr.options.timeOut = 2000;
                    toastr.options.positionClass = 'toast-top-right';
                    
                    var leave_type_data = $('#type_leave').val();
                    $('#leave_type').val(leave_type_data).trigger('change');
                    if(obj.status=='error'){
                        toastr.error(obj.message);
                    }else if(obj.status=='warning'){
                        toastr.warning(obj.message);
                    }
                }        
            }
        });
    };

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

    var selectedBenefit = "<?= isset($_SESSION['leave']['type']) ? $_SESSION['leave']['type'] : ''; ?>";


    document.getElementById("attachment").addEventListener("change", function() {
        var file = this.files[0];
        var errorMessage = document.getElementById("file-error");

        if (file && file.size > 1048576) { // 1MB = 1048576 bytes
            errorMessage.style.display = "block"; // Show error message
            this.value = ""; // Clear the file input
        } else {
            errorMessage.style.display = "none"; // Hide error message if valid
        }
    });


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
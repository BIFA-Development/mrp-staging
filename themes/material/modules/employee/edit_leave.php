<?= form_open_multipart(site_url($module['route'] . '/save_edit_leave'), array(
    'autocomplete'  => 'off',
    // 'id'            => 'form-create-data',
    'class'         => 'form form-validate form-xhr ui-front',
    // 'role'          => 'form'
)); ?>

<div class="card style-default-bright">
    <div class="card-head style-primary-dark">
        <header>Edit Leave <?= $module['label']; ?></header>

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
                    <div class="col-sm-12 col-lg-4">
                        

                        <div class="form-group">
                            <input type="number" name="amount_leave" id="amount_leave" class="form-control number" value="<?=$entity['amount_leave'];?>" step="1" min="0">
                            <label for="amount_leave">Jumlah Cuti</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="left_leave" id="left_leave" class="form-control number" value="<?=$entity['left_leave'];?>" step="1" min="0" readonly>
                            <label for="left_leave">Sisa Cuti</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="used_leave" id="used_leave" class="form-control number" value="<?=$entity['used_leave'];?>" step="1" min="0">
                            <label for="used_leave">Cuti Digunakan</label>
                        </div>

                        <div class="form-group">
                            <div class="alert alert-info">
                                <strong>Info Edit:</strong> Data ini sudah diedit <?= isset($entity['edit_count']) ? $entity['edit_count'] : 0; ?> kali dari maksimal 10 kali edit yang diizinkan.
                                <?php if ((isset($entity['edit_count']) ? $entity['edit_count'] : 0) >= 10): ?>
                                <br><strong style="color: red;">⚠️ Batas maksimal edit telah tercapai. Data tidak dapat diedit lagi.</strong>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <input type="hidden" name="id" id="id" class="form-control" value="<?=$entity['id'];?>" readonly>
        <input type="hidden" name="contract_number_rexception" id="contract_number_rexception" class="form-control" value="<?=$entity['contract_number'];?>" readonly>
        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create" <?= ((isset($entity['edit_count']) ? $entity['edit_count'] : 0) >= 10) ? 'disabled' : ''; ?>>
        <i class="md md-save"></i>
        </button>
    </div>
</div>

<?= form_close(); ?>

<script>
$(document).ready(function() {
    // Debounce timers
    var amountLeaveTimer;
    var usedLeaveTimer;
    var leftLeaveTimer;
    
    // Debounce delay in milliseconds
    var debounceDelay = 300;
    
    // Function to calculate and update fields
    function calculateLeave() {
        var amountLeave = parseInt($('#amount_leave').val()) || 0;
        var leftLeave = parseInt($('#left_leave').val()) || 0;
        var usedLeave = parseInt($('#used_leave').val()) || 0;
        
        return {
            amount: amountLeave,
            left: leftLeave,
            used: usedLeave
        };
    }
    
    // When amount_leave (jumlah cuti) changes
    $('#amount_leave').on('input', function() {
        clearTimeout(amountLeaveTimer);
        var $this = $(this);
        
        amountLeaveTimer = setTimeout(function() {
            var amountLeave = parseInt($this.val()) || 0;
            var usedLeave = parseInt($('#used_leave').val()) || 0;
            
            // Calculate new left_leave (cuti digunakan tetap, yang berubah adalah sisa cuti)
            var newLeftLeave = amountLeave - usedLeave;
            
            // Ensure left_leave is not negative
            if (newLeftLeave < 0) {
                newLeftLeave = 0;
                // Adjust used_leave if necessary
                $('#used_leave').val(amountLeave);
            }
            
            $('#left_leave').val(newLeftLeave);
        }, debounceDelay);
    });
    
    // Immediate calculation on blur/change (when user leaves the field)
    $('#amount_leave').on('blur change', function() {
        clearTimeout(amountLeaveTimer);
        var amountLeave = parseInt($(this).val()) || 0;
        var usedLeave = parseInt($('#used_leave').val()) || 0;
        
        // Calculate new left_leave (cuti digunakan tetap, yang berubah adalah sisa cuti)
        var newLeftLeave = amountLeave - usedLeave;
        
        // Ensure left_leave is not negative
        if (newLeftLeave < 0) {
            newLeftLeave = 0;
            // Adjust used_leave if necessary
            $('#used_leave').val(amountLeave);
        }
        
        $('#left_leave').val(newLeftLeave);
    });
    
    // When used_leave (cuti digunakan) changes
    $('#used_leave').on('input', function() {
        clearTimeout(usedLeaveTimer);
        var $this = $(this);
        
        usedLeaveTimer = setTimeout(function() {
            var usedLeave = parseInt($this.val()) || 0;
            var amountLeave = parseInt($('#amount_leave').val()) || 0;
            
            // Calculate new left_leave
            var newLeftLeave = amountLeave - usedLeave;
            
            // Ensure used_leave doesn't exceed amount_leave
            if (usedLeave > amountLeave) {
                $this.val(amountLeave);
                newLeftLeave = 0;
            } else if (newLeftLeave < 0) {
                newLeftLeave = 0;
            }
            
            $('#left_leave').val(newLeftLeave);
        }, debounceDelay);
    });
    
    // Immediate calculation on blur/change
    $('#used_leave').on('blur change', function() {
        clearTimeout(usedLeaveTimer);
        var usedLeave = parseInt($(this).val()) || 0;
        var amountLeave = parseInt($('#amount_leave').val()) || 0;
        
        // Calculate new left_leave
        var newLeftLeave = amountLeave - usedLeave;
        
        // Ensure used_leave doesn't exceed amount_leave
        if (usedLeave > amountLeave) {
            $(this).val(amountLeave);
            newLeftLeave = 0;
        } else if (newLeftLeave < 0) {
            newLeftLeave = 0;
        }
        
        $('#left_leave').val(newLeftLeave);
    });
    
    // When left_leave (sisa cuti) changes
    $('#left_leave').on('input', function() {
        clearTimeout(leftLeaveTimer);
        var $this = $(this);
        
        leftLeaveTimer = setTimeout(function() {
            var leftLeave = parseInt($this.val()) || 0;
            var amountLeave = parseInt($('#amount_leave').val()) || 0;
            
            // Calculate new used_leave
            var newUsedLeave = amountLeave - leftLeave;
            
            // Ensure left_leave doesn't exceed amount_leave
            if (leftLeave > amountLeave) {
                $this.val(amountLeave);
                newUsedLeave = 0;
            } else if (newUsedLeave < 0) {
                newUsedLeave = 0;
                $this.val(amountLeave);
            }
            
            $('#used_leave').val(newUsedLeave);
        }, debounceDelay);
    });
    
    // Immediate calculation on blur/change
    $('#left_leave').on('blur change', function() {
        clearTimeout(leftLeaveTimer);
        var leftLeave = parseInt($(this).val()) || 0;
        var amountLeave = parseInt($('#amount_leave').val()) || 0;
        
        // Calculate new used_leave
        var newUsedLeave = amountLeave - leftLeave;
        
        // Ensure left_leave doesn't exceed amount_leave
        if (leftLeave > amountLeave) {
            $(this).val(amountLeave);
            newUsedLeave = 0;
        } else if (newUsedLeave < 0) {
            newUsedLeave = 0;
            $(this).val(amountLeave);
        }
        
        $('#used_leave').val(newUsedLeave);
    });
    
    // Initial validation on page load
    $('#amount_leave').trigger('change');
});
</script>
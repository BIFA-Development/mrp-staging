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
                            <input type="text" name="amount_leave" id="amount_leave" class="form-control" value="<?=$entity['amount_leave'];?>" readonly>
                            <label for="amount_leave">Jumlah Cuti</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="left_leave" id="left_leave" class="form-control number" value="<?=$entity['left_leave'];?>" step="1" <?= ($entity['used_leave'] != 0) ? 'readonly' : ''; ?>>
                            <label for="left_leave">Sisa Cuti</label>
                        </div>

                        <div class="form-group">
                            <input type="number" name="used_leave" id="used_leave" class="form-control number" value="<?=$entity['used_leave'];?>" step="1" <?= ($entity['used_leave'] != 0) ? 'readonly' : ''; ?>>
                            <label for="used_leave">Cuti Digunakan</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-foot">
        <input type="hidden" name="id" id="id" class="form-control" value="<?=$entity['id'];?>" readonly>
        <input type="hidden" name="contract_number_rexception" id="contract_number_rexception" class="form-control" value="<?=$entity['contract_number'];?>" readonly>
        <button type="submit" id="modal-create-data-submit" class="btn btn-floating-action btn-primary btn-xhr-submit ink-reaction pull-right" data-title="save and create">
        <i class="md md-save"></i>
        </button>
    </div>
</div>

<?= form_close(); ?>
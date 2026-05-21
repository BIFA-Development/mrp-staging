<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
<div class="page-header">
    <h1><i class="md md-security text-danger"></i> Expense Cleanup Tool</h1>
</div>
<?php endblock() ?>

<?php startblock('page_body') ?>
<style>
    /* Paksa hapus overlay transparan yang sering menyangkut */
    .progress-overlay, .pace, .modal-backdrop, .offcanvas-backdrop {
        display: none !important;
        visibility: hidden !important;
        pointer-events: none !important;
    }
    
    /* Pastikan layar bisa menerima klik dan scroll */
    body {
        overflow: auto !important;
        pointer-events: auto !important;
    }

    /* Pastikan tabel dan tombol berada di lapisan terdepan */
    .section-body {
        position: relative;
        z-index: 1000 !important;
    }

    .btn, a, input {
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    .style-primary-dark {
        background-color: #21313f !important;
        color: #fff !important;
    }
</style>

<div class="section-body">
    <div class="card">
        <div class="card-head style-primary-dark">
            <header>Detected Duplicates in Budget Control</header>
            <div class="tools" id="bulk-fix-tool" style="display:none;">
                <a href="<?= site_url($module['route'].'/fix_all_duplicates'); ?>" 
                   class="btn btn-danger btn-raised ink-reaction" 
                   onclick="return confirm('Sistem akan menyisir SEMUA data duplikat, menyisakan 1 baris sah, dan mengembalikan saldo budget lainnya. Lanjutkan?')">
                    <i class="md md-flash-on"></i> FIX ALL DUPLICATES
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <?php if($this->session->flashdata('alert')): ?>
                <div class="alert alert-callout alert-info no-margin">
                    <?= $this->session->flashdata('alert')['info']; ?>
                </div>
                <br>
            <?php endif; ?>

            <div id="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-hover" id="table-cleanup">
                        <thead>
                            <tr class="style-default-light">
                                <th>No. Document</th>
                                <th>Person</th>
                                <th>Original Amount</th>
                                <th class="text-center">Duplicate Count</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <di<div class="card" style="margin-top: 25px;">
    <div class="card-head style-default-bright">
        <header><i class="md md-history"></i> Recent Activity Logs</header>
    </div>
    <div class="card-body no-padding">
        <div class="table-responsive">
            <table class="table table-condensed table-hover no-margin">
                <thead>
                    <tr>
                        <th>Execution Date</th>
                        <th>User</th>
                        <th class="text-center">Items Fixed</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Menggunakan database default ($this->db)
                    $logs = $this->db->order_by('executed_at', 'DESC')->limit(5)->get('tb_cleanup_logs')->result();
                    if($logs):
                        foreach($logs as $log): ?>
                        <tr>
                            <td><?= date('d M Y H:i', strtotime($log->executed_at)) ?></td>
                            <td><?= $log->executed_by ?></td>
                            <td class="text-center"><span class="badge style-info"><?= $log->total_cleaned ?></span></td>
                            <td><small class="text-muted">ID Log: #<?= $log->id ?></small></td>
                        </tr>
                    <?php endforeach; else: ?>
                        <tr><td colspan="4" class="text-center">No logs recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

            <div id="empty-state" class="text-center" style="display:none; padding: 80px 20px;">
                <i class="md md-verified-user text-success" style="font-size: 100px; opacity: 0.3;"></i>
                <h3 class="text-light">Database is Clean!</h3>
                <p class="text-muted">Tidak ditemukan duplikasi data di Expense Request Budget Control.</p>
                <a href="<?= site_url($module['route']); ?>" class="btn btn-raised btn-primary ink-reaction">
                    Back to Reimbursement List
                </a>
            </div>
        </div>
    </div>
</div>
<?php endblock() ?>

<?php startblock('scripts') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('vendors/pace/pace.min.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?= html_script('themes/material/assets/js/libs/nanoscroller/jquery.nanoscroller.min.js') ?>
<?= html_script('themes/material/assets/js/libs/toastr/toastr.js') ?>
<?= html_script('vendors/DataTables-1.10.12/datatables.min.js') ?>

<script type="text/javascript">
    // Menjalankan script setelah semua library selesai dimuat
    window.onload = function() {
        if (window.jQuery) {
            $(document).ready(function() {
                // Hapus paksa penghalang klik jika masih ada
                $('.progress-overlay, .modal-backdrop').remove();
                $('body').removeClass('modal-open').css('padding-right', '');

                // Konfigurasi DataTable Server-Side
                var tableCleanup = $('#table-cleanup').DataTable({
                    "ajax": "<?= site_url($module['route'].'/cleanup_data_index'); ?>",
                    "columns": [
                        { "data": "doc" },
                        { "data": "person" },
                        { "data": "total" },
                        { "data": "status", "className": "text-center" },
                        { "data": "action", "className": "text-right" }
                    ],
                    "processing": true,
                    "language": {
                        "processing": "Scanning Database...",
                        "emptyTable": "No duplicates found"
                    },
                    "drawCallback": function(settings) {
                        var rows = this.api().rows().count();
                        if (rows === 0) {
                            $('#table-wrapper, #bulk-fix-tool').hide();
                            $('#empty-state').show();
                        } else {
                            $('#table-wrapper, #bulk-fix-tool').show();
                            $('#empty-state').hide();
                        }
                    }
                });
            });
        }
    };
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
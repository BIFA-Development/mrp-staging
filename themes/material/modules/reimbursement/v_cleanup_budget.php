<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
<div class="page-header">
    <h1><i class="md md-security text-danger"></i> Expense Cleanup Tool</h1>
</div>
<?php endblock() ?>

<?php startblock('page_body') ?>
<style>
    /* Reset CSS agar halaman tetap interaktif */
    .progress-overlay,
    .pace,
    .modal-backdrop.in {
        display: none !important;
    }

    body {
        overflow: auto !important;
        pointer-events: auto !important;
    }

    .section-body {
        position: relative;
        z-index: 10;
    }

    .btn,
    a {
        cursor: pointer !important;
    }

    .style-primary-dark {
        background-color: #21313f !important;
        color: #fff !important;
    }

    .text-bold {
        font-weight: bold;
    }

    .table-detail-custom {
        font-size: 11px;
    }

    /* Ukuran font diperkecil sedikit agar muat banyak kolom */
</style>

<div class="section-body">
    <div class="card">
        <div class="card-head style-primary-dark">
            <header>Scanning: Duplicates in Budget Control</header>
            <div class="tools" id="bulk-fix-tool" style="display:none;">
                <a href="<?= site_url($module['route'] . '/fix_all_duplicates'); ?>"
                    class="btn btn-danger btn-raised ink-reaction"
                    onclick="return confirm('Sistem akan menyisir SEMUA data duplikat dan menyisakan 1 baris sah. Lanjutkan?')">
                    <i class="md md-flash-on"></i> FIX ALL DUPLICATES
                </a>
            </div>
        </div>

        <div class="card-body">
            <?php if ($this->session->flashdata('alert')): ?>
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
                                <th>Ref. Reimbursement</th>
                                <th>Employee Name</th>
                                <th>Original Amount</th>
                                <th class="text-center">Total Rows</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div id="empty-state" class="text-center" style="display:none; padding: 40px 20px;">
                <i class="md md-verified-user text-success" style="font-size: 80px; opacity: 0.3;"></i>
                <h3 class="text-light">Database is Clean!</h3>
                <p class="text-muted">Tidak ditemukan duplikasi data baru di Budget Control.</p>
            </div>

            <hr style="margin: 40px 0;">

            <div class="card" style="border: 1px solid #e5e5e5; box-shadow: none;">
                <div class="card-head style-default-bright">
                    <header><i class="md md-history"></i> Recent Activity Logs (Cleaned Documents)</header>
                </div>
                <div class="card-body no-padding">
                    <div class="table-responsive">
                        <table class="table table-condensed table-hover no-margin">
                            <thead>
                                <tr>
                                    <th>Execution Date</th>
                                    <th>Executed By</th>
                                    <th class="text-center">Docs Fixed</th>
                                    <th class="text-right">History Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $logs = $this->db->order_by('executed_at', 'DESC')->limit(10)->get('tb_cleanup_logs')->result();
                                if ($logs):
                                    foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?= date('d M Y H:i', strtotime($log->executed_at)) ?></td>
                                            <td><span class="text-primary"><?= $log->executed_by ?></span></td>
                                            <td class="text-center">
                                                <span class="badge style-info"><?= $log->total_cleaned ?> Documents</span>
                                            </td>
                                            <td class="text-right">
                                                <button type="button" class="btn btn-xs btn-flat btn-primary btn-view-log"
                                                    data-json='<?= htmlspecialchars($log->details_json, ENT_QUOTES, 'UTF-8') ?>'
                                                    data-date="<?= date('d M Y H:i', strtotime($log->executed_at)) ?>">
                                                    <i class="md md-remove-red-eye"></i> SHOW ER DETAILS
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No history logs recorded.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalLogDetail" tabindex="-1" role="dialog" aria-labelledby="modalLogDetailLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width:90%;">
        <div class="modal-content">
            <div class="modal-header style-primary-dark">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalLogDetailLabel"><i class="md md-assignment-turned-in"></i> Details of
                    Fixed Expenses</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="text-lg text-medium">Execution Date: <span id="log-date-text"
                                class="text-primary"></span></p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-condensed table-detail-custom" id="table-detail-log">
                        <thead>
                            <tr class="style-default-light">
                                <th width="5%">ID</th>
                                <th width="15%">Ref. Doc Number</th>
                                <th width="20%">No. ER (Fixed)</th>
                                <th>Employee</th>
                                <th class="text-right">Amount (Rp)</th>
                                <th class="text-center">Status</th>
                                <th class="text-right" width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="alert alert-callout alert-warning small no-margin">
                    <i class="md md-info-outline"></i> Tombol <b>RESTORE</b> digunakan jika data di Budget Control
                    terhapus secara tidak sengaja dan perlu dibangun ulang.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endblock() ?>

<?php startblock('scripts') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('themes/material/assets/js/libs/bootstrap/bootstrap.min.js') ?>
<?= html_script('vendors/DataTables-1.10.12/datatables.min.js') ?>

<script type="text/javascript">
    $(document).ready(function () {
        // 1. Load Tabel Scanning
        var tableCleanup = $('#table-cleanup').DataTable({
            "ajax": "<?= site_url($module['route'] . '/cleanup_data_index'); ?>",
            "columns": [
                { "data": "doc" },
                { "data": "person" },
                { "data": "total" },
                { "data": "status", "className": "text-center" },
                { "data": "action", "className": "text-right" }
            ],
            "processing": true,
            "drawCallback": function (settings) {
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

        // 2. Logic Tombol VIEW LIST & RESTORE
        $('body').on('click', '.btn-view-log', function () {
            var data = $(this).data('json');
            var date = $(this).data('date');
            var tbody = $('#table-detail-log tbody');

            $('#log-date-text').text(date);
            tbody.empty();

            if (data && data.length > 0) {
                $.each(data, function (index, item) {
                    var cleanedRows = parseInt(item.duplicate_count) - 1;
                    var noER = item.pr_number ? item.pr_number : 'N/A';

                    var restoreUrl = "<?= site_url($module['route']); ?>/fix_missing_er/" + item.reimbursement_id;

                    // Tambahkan variabel tombol
                    var btnRestore = '';

                    // PENGAMAN UI: Jika No ER bukan N/A (berarti sudah ada), matikan tombolnya
                    if (item.pr_number && item.pr_number !== '') {
                        // Tombol disabled jika sudah ada No ER
                        btnRestore = '<button class="btn btn-xs btn-default" disabled title="Already restored"><i class="md md-block"></i> RESTORED</button>';
                    } else {
                        // Tombol aktif jika No ER kosong
                        btnRestore = '<a href="' + restoreUrl + '" class="btn btn-xs btn-warning btn-raised" onclick="return confirm(\'Bangun ulang data Expense Request untuk ID ini?\')">' +
                            '<i class="md md-settings-backup-restore"></i> RESTORE</a>';
                    }

                    var row = '<tr>' +
                        '<td class="text-bold">#' + item.reimbursement_id + '</td>' +
                        '<td>' + item.document_number + '</td>' +
                        '<td class="text-bold text-primary">' + noER + '</td>' +
                        '<td>' + item.employee + '</td>' +
                        '<td class="text-right">' + Number(item.amount).toLocaleString('id-ID') + '</td>' +
                        '<td class="text-center"><span class="label style-success">Fixed (-' + cleanedRows + ')</span></td>' +
                        '<td class="text-right">' + btnRestore + '</td>' + // Masukkan variabel tombol di sini
                        '</tr>';
                    tbody.append(row);
                });
            } else {
                tbody.append('<tr><td colspan="7" class="text-center">No details available.</td></tr>');
            }

            $('#modalLogDetail').modal('show');
        });

        $('#modalLogDetail').on('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
        });
    });
</script>

<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>
<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
<div class="page-header">
    <h1><i class="md md-security text-danger"></i> Auto-Detect Double Expense</h1>
</div>
<?php endblock() ?>

<?php startblock('page_body') ?>
<div class="section-body">
    <div class="card">
        <div class="card-head style-danger">
            <header>Daftar Duplikasi Expense Request Terdeteksi</header>
        </div>
        <div class="card-body">
            <?php if($this->session->flashdata('alert')): ?>
                <div class="alert alert-info"><?= $this->session->flashdata('alert')['info']; ?></div>
            <?php endif; ?>

            <div class="alert alert-callout alert-warning">
                Sistem mendeteksi data di bawah ini memiliki lebih dari satu baris di <b>Expense Request</b> untuk satu nomor Reimbursement yang sama.
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr style="background: #f5f5f5;">
                            <th>ID</th>
                            <th>No. Reimbursement</th>
                            <th>Nama Karyawan</th>
                            <th>Nominal (Original)</th>
                            <th>Terdeteksi di ER</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($duplicates)): ?>
                            <tr>
                                <td colspan="6" class="text-center"><b>Hebat! Tidak ada data double yang terdeteksi.</b></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($duplicates as $row): ?>
                                <tr>
                                    <td><?= $row['reimb_id']; ?></td>
                                    <td><?= $row['doc_number']; ?></td>
                                    <td><?= $row['person_name']; ?></td>
                                    <td>Rp <?= number_format($row['total_amount'], 0, ',', '.'); ?></td>
                                    <td><span class="badge style-danger"><?= $row['found_count']; ?> Data</span></td>
                                    <td class="text-right">
                                        <a href="<?= site_url($module['route'].'/process_cleanup_auto/'.$row['reimb_id']); ?>" 
                                           class="btn btn-danger btn-raised btn-sm ink-reaction"
                                           onclick="return confirm('Sistem akan menyisakan 1 ER dan mengembalikan budget lainnya. Lanjutkan?')">
                                            <i class="md md-settings-backup-restore"></i> FIX BUDGET & DELETE DUPLICATE
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <a href="<?= site_url($module['route']); ?>" class="btn btn-default-bright">Kembali ke List Utama</a>
        </div>
    </div>
</div>
<?php endblock() ?>
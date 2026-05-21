<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
<div class="page-header">
    <h1><i class="md md-warning text-danger"></i> Duplicate Expense Detector</h1>
</div>
<?php endblock() ?>

<?php startblock('page_body') ?>
<div class="section-body">
    <div class="card">
        <div class="card-head style-primary-dark">
            <header>Terdeteksi <?= count($duplicates); ?> Kasus Duplikasi</header>
            <div class="tools">
                <?php if(count($duplicates) > 0): ?>
                    <a href="<?= site_url($module['route'].'/fix_all_duplicates'); ?>" 
                       class="btn btn-danger btn-raised ink-reaction" 
                       onclick="return confirm('Sistem akan menyisakan 1 data per dokumen dan mengembalikan budget sisanya. Lanjutkan?')">
                        <i class="md md-flash-on"></i> FIX ALL DUPLICATES NOW
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card-body no-padding">
            <?php if($this->session->flashdata('alert')): ?>
                <div class="alert alert-callout alert-success no-margin">
                    <?= $this->session->flashdata('alert')['info']; ?>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-hover no-margin">
                    <thead>
                        <tr class="style-default-light">
                            <th>No. Document</th>
                            <th>Karyawan</th>
                            <th>Nominal Original</th>
                            <th class="text-center">Jumlah Baris di ER</th>
                            <th>Status Budget</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($duplicates)): ?>
                            <tr>
                                <td colspan="5" class="text-center" style="padding: 100px;">
                                    <i class="md md-check-circle text-success" style="font-size: 60px;"></i>
                                    <p style="font-size: 18px; margin-top: 20px;">Database Bersih! Tidak ditemukan duplikasi.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($duplicates as $row): ?>
                                <tr>
                                    <td><strong><?= $row['document_number']; ?></strong></td>
                                    <td><?= $row['person_name']; ?></td>
                                    <td>Rp <?= number_format($row['total'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <span class="badge style-danger"><?= $row['duplicate_count']; ?> Baris</span>
                                    </td>
                                    <td><span class="text-danger">Overbudget <?= $row['duplicate_count']-1; ?>x lipat</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endblock() ?>
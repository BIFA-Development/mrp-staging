<?php include 'themes/material/page.php' ?>

<?php startblock('page_head_tools') ?>
<div class="page-header">
    <h1><i class="md md-settings-backup-restore text-danger"></i> Budget Reversal Tool</h1>
</div>
<?php endblock() ?>

<?php startblock('page_body') ?>
<div class="section-body">
    <div class="card">
        <div class="card-head style-danger">
            <header>Fix Double Entry - Expense Request (Full Width Mode)</header>
        </div>
        
        <div class="card-body">
            <?php if($this->session->flashdata('alert')): ?>
                <div class="alert alert-info" role="alert">
                    <?= $this->session->flashdata('alert')['info']; ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-callout alert-warning" role="alert">
                <strong>Instruksi:</strong> Gunakan tool ini untuk membersihkan duplikasi Expense Request yang menyebabkan budget terpotong double.
            </div>

            <form class="form" action="<?= site_url($module['route'] . '/process_cleanup'); ?>" method="post" id="form-reversal">
                
                <div class="form-group floating-label" style="margin-bottom: 30px;">
                    <input type="number" class="form-control input-lg" id="reimb_id" name="reimb_id" required autofocus 
                           style="font-size: 20px; font-weight: bold; border-bottom: 2px solid #f44336;">
                    <label for="reimb_id" style="font-size: 16px;">Masukkan ID Reimbursement (Contoh: 125)</label>
                    <p class="help-block">Input ID dari data yang bermasalah.</p>
                </div>

                <div class="form-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" id="btn-exec" class="btn btn-raised btn-block btn-danger ink-reaction" style="height: 50px; font-size: 16px;">
                                <i class="md md-sync"></i> JALANKAN PROSES REVERSAL SEKARANG
                            </button>
                            <br>
                            <a href="<?= site_url($module['route']); ?>" class="btn btn-block btn-default-bright">
                                BATAL DAN KEMBALI
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .section-body { padding: 20px; }
    .form-control:focus { border-bottom-color: #f44336 !important; box-shadow: none; }
    /* Menghilangkan overlay transparan jika ada */
    .progress-overlay, .pace { display: none !important; }
</style>
<?php endblock() ?>

<?php startblock('scripts') ?>
<script>
    $(document).ready(function() {
        // Paksa input mendapatkan fokus agar bisa langsung diketik
        setTimeout(function() {
            $('#reimb_id').focus().click();
        }, 300);

        // Menghapus elemen penghalang klik jika ada
        $('.modal-backdrop').remove();

        $('#form-reversal').on('submit', function() {
            return confirm('Konfirmasi: Anda akan menghapus duplikat dan mengembalikan saldo budget. Lanjutkan?');
        });
    });
</script>
<?php endblock() ?> 
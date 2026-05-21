<?php include 'themes/material/page.php' ?>

<?php startblock('page_body') ?>
<style>
    .progress-overlay, .pace, .modal-backdrop { display: none !important; pointer-events: none !important; }
    body { pointer-events: auto !important; overflow: auto !important; }
    .card, .section-body { position: relative; z-index: 99999 !important; }
    
    /* Memastikan tombol terlihat seperti tombol dan bisa diklik */
    .btn-force-click {
        cursor: pointer !important;
        position: relative !important;
        z-index: 100000 !important;
        pointer-events: auto !important;
    }
</style>

<div class="section-body">
    <div class="card">
        <div class="card-head style-primary-dark">
            <header>Detected Duplicates</header>
            <div class="tools">
                <button type="button" 
                        class="btn btn-danger btn-raised btn-force-click" 
                        onclick="if(confirm('Fix All Duplicates?')){ window.location.href='<?= site_url($module['route'].'/fix_all_duplicates'); ?>'; }">
                    <i class="md md-flash-on"></i> FIX ALL DUPLICATES NOW
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="table-wrapper">
                <table class="table table-hover" id="table-cleanup">
                    <thead>
                        <tr>
                            <th>No. Document</th>
                            <th>Person</th>
                            <th>Amount</th>
                            <th class="text-center">Count</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endblock() ?>

<?php startblock('scripts') ?>
<?= html_script('vendors/jQuery/jQuery-2.2.1.min.js') ?>
<?= html_script('vendors/DataTables-1.10.12/datatables.min.js') ?>

<script>
    window.onload = function() {
        // Hilangkan paksa semua penghalang saat page load
        var overlays = document.querySelectorAll('.progress-overlay, .modal-backdrop, .pace');
        overlays.forEach(function(el) { el.remove(); });

        $(document).ready(function() {
            $('#table-cleanup').DataTable({
                "ajax": "<?= site_url($module['route'].'/cleanup_data_index'); ?>",
                "columns": [
                    { "data": "doc" },
                    { "data": "person" },
                    { "data": "total" },
                    { "data": "status", "className": "text-center" },
                    { 
                        "data": "action", 
                        "className": "text-right",
                        "render": function(data, type, row) {
                            // Render tombol dengan onclick manual agar tidak butuh bind event
                            return data; 
                        }
                    }
                ]
            });
        });
    };
</script>
<?php endblock() ?>
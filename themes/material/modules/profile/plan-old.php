<?php include 'themes/material/template.php' ?>

<?php startblock('content') ?>
<section class="style-default">
  <div class="section-body">
    <div class="row">
      <div class="col-md-4">
        <?php $this->load->view('material/modules/profile/sidemenu') ?>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-head style-primary">
            <header>Rencana Cuti</header>
          </div>
          <div class="card-body no-padding">
            <?php
              if ($this->session->flashdata('alert'))
                  render_alert($this->session->flashdata('alert')['info'], $this->session->flashdata('alert')['type']);
            ?>
            <div class="p-2" style="padding: 20px;">
              <div id="calendar"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endblock() ?>

<?php startblock('scripts') ?>

<!-- ✅ jQuery & moment & FullCalendar v3.10.2 -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

<style>
  #calendar {
    background: #fff;
    z-index: 10;
    pointer-events: auto;
    padding: 10px;
  }
  .fc-button {
    pointer-events: all !important;
    z-index: 11;
  }
</style>

<script>

  
  $(document).ready(function () {
   
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
      },
      height: 600,
      defaultView: 'month',
      editable: false,
      events: [
        {
          title: 'Cuti Tahunan',
          start: '2025-07-15',
          end: '2025-07-17',
          color: '#3c8dbc'
        },
        {
          title: 'Libur Nasional',
          start: '2025-07-20',
          color: '#f44336'
        }
      ]
    });
  });
</script>

<!-- ✅ Include JS bawaan template -->
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
<?= html_script('vendors/select2-4.0.3/dist/js/select2.min.js') ?>
<?= html_script('themes/script/jquery.number.js') ?>
<?= html_script('themes/material/assets/js/core/source/App.min.js') ?>
<?php endblock() ?>

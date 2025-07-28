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
            <header>Employee Leave Plan Calendar</header>
          </div>
          <div class="card-body no-padding">
            <?php
              if ($this->session->flashdata('alert')) {
                render_alert(
                  $this->session->flashdata('alert')['info'],
                  $this->session->flashdata('alert')['type']
                );
              }
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

<!-- Modal for event details -->
<div class="modal fade" id="data-modal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Leave Details</h4>
        
      </div>
      <div class="modal-body">
        <!-- Filled by JavaScript -->
      </div>
    </div>
    
  </div>
</div>
<?php endblock() ?>

<?php startblock('scripts') ?>

<!-- ✅ Scripts -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar-year-view@0.0.3/year-view.css" />
<script src="https://cdn.jsdelivr.net/npm/fullcalendar-year-view@0.0.3/year-view.js"></script> -->
<!-- Core + plugins -->
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/daygrid@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/timegrid@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/interaction@6.1.8/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/multimonth@6.1.8/index.global.min.js"></script>





<style>

.modal-header .close {
  margin-top: -0.3rem;
}

  #calendar {
    background: #fff;
    z-index: 10;
    pointer-events: auto;
    padding: 10px;
  }
  .fc-button {
    background-color: #0aa89e !important;
    pointer-events: all !important;
    border-color: #0aa89e !important;
    z-index: 11;
  }
</style>

<!-- ✅ Inject PHP data -->
<script>
  const rawLeavePlanEvents = <?= isset($leave_plan) ? json_encode($leave_plan, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '""'; ?>;

  // Parse it into a usable JavaScript array
  let leavePlanEvents = [];
  try {
    leavePlanEvents = JSON.parse(rawLeavePlanEvents);
  } catch (e) {
    console.error("Failed to parse leavePlanEvents:", e);
    leavePlanEvents = [];
  }

  console.log("Parsed leavePlanEvents:", leavePlanEvents);

  const formattedEvents = leavePlanEvents
    .filter(item => item.leave_start_date && item.leave_end_date)
    .map(item => ({
      title: `${item.leave_type_name} - ${item.person_name}`,
      start: item.leave_start_date,
      end: moment(item.leave_end_date).add(1, 'days').format('YYYY-MM-DD'), // end is exclusive
      color: getColor(item.status),
      description: item.reason || '-',
      status: item.status,
      document_number: item.document_number || '-',
      idDataLeave: item.id
    }));

  function getColor(status) {
    switch (status) {
      case 'APPROVED': return '#4caf50';
      case 'REJECT': return '#f44336';
      case 'REVISED': return '#ff9800';
      default: return '#3c8dbc'; // Waiting approval or other
    }
  }

  $(document).ready(function () {
    console.log('Raw leavePlanEvents:', rawLeavePlanEvents);
    console.log('Final leavePlanEvents:', leavePlanEvents);

    console.log('FullCalendar:', FullCalendar);
    console.log('MultiMonthPlugin:', FullCalendar.MultiMonth);

    Pace.on('start', function() {
        $('.progress-overlay').show();
    });

    Pace.on('done', function() {
        $('.progress-overlay').hide();
    });

    const calendarEl = document.getElementById('calendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
      plugins: [FullCalendar.MultiMonth.default],
      initialView: 'multiMonthYear',
      events: formattedEvents,
      eventClick: function(info) {
        const event = info.event;
        console.log('Event clicked:', info.event);

        const siteUrl = '<?= site_url(); ?>';

        $('#data-modal .modal-body').html(`
          <div class="position-relative" style="padding-bottom: 30px;">
          <dl class="dl-inline">
            <dt>Title</dt>
            <dd>${event.title}</dd>
            <dt>Nomor Dokumen</dt>
            <dd>${event.extendedProps.document_number}</dd>
            <dt>Status</dt>
            <dd>${event.extendedProps.status}</dd>
            <dt>Mulai</dt>
            <dd>${moment(event.start).format('YYYY-MM-DD')}</dd>
            <dt>Mulai</dt>
            <dd>${moment(event.end).format('YYYY-MM-DD')}</dd>
            <dt>Alasan</dt>
            <dd>${event.extendedProps.description}</dd>
            <div class="pull-right">
              <button 
                onclick="window.open('${siteUrl}leave/create/0/${event.extendedProps.idDataLeave}', '_blank')" 
                class="btn btn-primary position-absolute" 
                style="bottom: 15px; right: 15px;">
                Create Request Leave
              </button>
            </div>
            </dl>
          </div>
        `);


        $('#data-modal').modal('show');
      }
    });

    calendar.render();


    // $('#calendar').fullCalendar({
    //   defaultView: 'year',  
    //   views: {
    //     year: { type: 'year', buttonText: 'Year' }
    //   },
    //   height: 600,
    //   defaultView: 'month',
    //   editable: false,
    //   events: formattedEvents,
    //   eventClick: function (event) {
    //     $('#data-modal .modal-body').html(`
    //       <div class="p-3">
    //         <h4>${event.title}</h4>
    //         <p><strong>Nomor Dokumen:</strong> ${event.document_number}</p>
    //         <p><strong>Status:</strong> ${event.status}</p>
    //         <p><strong>Mulai:</strong> ${event.start.format('YYYY-MM-DD')}</p>
    //         ${event.end ? `<p><strong>Selesai:</strong> ${moment(event.end).subtract(1, 'days').format('YYYY-MM-DD')}</p>` : ''}
    //         <p><strong>Alasan:</strong> ${event.description}</p>
    //       </div>
    //     `);
    //     $('#data-modal').modal('show');
    //   }
    // });
  });
</script>

<!-- ✅ Template Scripts -->
<?= html_script('vendors/pace/pace.min.js') ?>
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

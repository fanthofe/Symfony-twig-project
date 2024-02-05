import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['modal'];

  initialize() {
    this.modalTarget
  }

  connect(){
    this.displayCalendar();
  }

  showModal({ params: { id, title } }) {
    $('#eventModal').addClass('show');
    $('html').css('overflow-y', 'hidden');
    this.loadForm(id, title);
  }
  
  loadForm(id, title){
    $.ajax({
      url: `/app/component/${id}/get-event-form`,
      async: true,
      success: function(data) {
        $('#modalBody').html(data);
      },
    });
    $('#modalTitle').text(title);
  }

  hideModal(){
    $('html').css('overflow-y', 'visible');      
    $('#eventModal').removeClass('show');    
  }

  displayCalendar(){
    const calendar = new FullCalendar.Calendar($('#calendar')[0], {
      timeZone: 'UTC',
      editable: true,
      selectable: true,
      initialView: 'dayGridMonth',
      events: '/app/component/get-events-ajax',
      eventClick: function(event){
        try {
          $.ajax({
            url: `/app/component/${event.event._def.publicId}/get-event-id`,
            method: 'GET',
            async: true,
            beforeSend: function(){
              $('#loaderModal').removeClass('display-none')
            },
            success: function(data){ 
              $('#modalBody').removeClass('display-none') 
              $('#modalBody').html(data);
              
              $('#edit-event-btn').attr({
                'data-action': 'click->form-modal#showModal',
                'data-form-modal-id-param': event.event._def.publicId,
                'data-form-modal-title-param': event.event.title,
              });

            },
            complete: function () {
              $('#loaderModal').addClass('display-none')
            },
          });

          $('#modalTitle').text(event.event.title);
          $('#eventModal').addClass('show');
          $('html').css('overflow-y', 'hidden');

        } catch (error) {
          throw new ErrorException(error);
        }
      },
      eventDrop: function(event){
        var startDate = event.event.startStr + ' 01:00';
        var endDate = event.event.endStr;
        
        if(event.event.end == null){
          endDate = event.event.startStr + ' 10:00';
        } else {
          endDate += ' 10:00';
        }

        try {
          $.ajax({
            url: `/app/component/${event.event.id}/update-event-ajax`,
            method: 'POST',
            async: true,
            data: {
              'startDate': startDate,
              'endDate': endDate,
            },
            beforeSend: function(){
              $('#loader').removeClass('display-none')
            },
            success: function(){   
              $(".page-content").prepend("<div class='alert alert-success alert-dismissible fade show' role='alert'>L'évènement a bien été mis à jour<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>");
            },
            complete: function () {
              $('#loader').addClass('display-none')
            },
          });
          
        } catch (error) {
          throw new ErrorException(error);
        }
      },
      headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
      },
      buttonText: {
          today: "Aujourd'hui",
          month: 'Mois',
          week: 'Semaine',
          day: 'Jour',
          list: 'Liste',
      },
      select: function(select){
        $('#eventModal').addClass('show');
        $('html').css('overflow-y', 'hidden');
        
        $.ajax({
          url: `/app/component/new-event/get-event-form`,
          async: true,
          beforeSend: function(){
            $('#loaderModal').removeClass('display-none')
          },
          success: function(data) {
            $('#closeButton').attr({'data-action': 'click->form-modal#hideModal'});
            $('#modalBody').removeClass('display-none') 
            $("#modalBody").html(data);
            $('#date_event_form_startDate').attr("value", select.startStr + ' 00:00')
            $('#date_event_form_endDate').attr("value", select.endStr + ' 00:00')
          },
          complete: function () {
            $('#loaderModal').addClass('display-none')
          },

        });
        $('#modalTitle').text('Ajouter un évènement');
      }
    });
  
    calendar.render();
    calendar.updateSize();
    calendar.setOption('locale', 'fr');

    $('.fc-today-button').removeClass('fc-today-button');
  }

  submitForm({params: {urlSubmit}}) {
    const form = $("#modalBody").find('form');

    try {
      $.ajax({
        url: `/app/component/${urlSubmit}/update-event-ajax`,
        method: form.prop('method'),
        async: true,
        data: form.serializeArray(),
        error: function(){
          console.log(form.serializeArray())
        },
        success: function(){   
            $('#eventModal').removeClass('show');
            $('html').css('overflow-y', 'visible');  

            if(urlSubmit == 'new-event'){
              $(".page-content").prepend("<div class='alert alert-success alert-dismissible fade show' role='alert'>Un évènement a été créé<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>");
            } else {
              $(".page-content").prepend("<div class='alert alert-success alert-dismissible fade show' role='alert'>Cet évènement a été modifié<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>");
            }
          }
      });

      this.displayCalendar();
      
    } catch (error) {
      throw new ErrorException(error);
    }
  }
}
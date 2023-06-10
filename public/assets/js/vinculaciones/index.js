function preguntar(action) {    
  $('#vinculacion-equipo').fireModal({
        title: 'Vinculación de equipo',
        body: `¿Desea modificar los KM y/o Fechas inicales del mantenimiento rutinario?`,
        footerClass: 'bg-whitesmoke',
        autofocus: false,
        closeButton: true,           
        removeOnDismiss: false,
        size: 'modal-md',      
        buttons: [
          {
            text: 'Cancelar',
            submit: false,
            class: 'btn btn-light btn-shadow',
            handler: function(modal) {                            
              $.destroyModal(modal)              
              document.getElementById(modal[0].id).remove()
            }
          },
          {
            text: 'Modificar antes de guardar',
            submit: false,
            class: 'btn btn-warning btn-shadow',
            handler: function(modal) {                              
              $.destroyModal(modal)                             
              document.getElementById(modal[0].id).remove()                                                      
              
              if(createTableComponentes(action)) {
                modificar()                            
              }              
            }
          },          
          {
            text: 'Guardar sin modificar',
            submit: false,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {
              var input = document.createElement("input");
              input.setAttribute("type", "hidden");              
              input.setAttribute("name", "action");              
              input.setAttribute("value", action);            
              document.getElementById('form-vinculacions-equipos').appendChild(input)
              
              if(confirm("¿Desea continuar SIN modificar?") == true) {
                document.getElementById('form-vinculacions-equipos').submit();
              }
            }
          }
        ]
  })
}

function modificar() {      
  $('#vinculacion-equipo').fireModal({
        title: 'Mantenimiento Rutinario',
        body: $('#mantenimiento-rutinario-inicial').clone(),
        footerClass: 'bg-whitesmoke',
        autofocus: true,
        closeButton: false,                   
        removeOnDismiss: false,
        size: 'modal-xl',
        created: function(modal) {          
          setTimeout(()=> {
            var newDiv = document.createElement('div');
            newDiv.id = 'modal-backdrop-custom'
            newDiv.classList.add('modal-backdrop');
            newDiv.classList.add('fade') 
            newDiv.classList.add('show')
            document.body.appendChild(newDiv);            

            var element = document.getElementById(modal[0].id)
                        
            element.classList.add('show')
            element.style.paddingRight = '15px'
            element.style.display = 'block'                        
          },100)     
        },
        onFormSubmit: function(modal, e, _form) {        
          var km_inicial = document.getElementById('km_inicial').value;
          var fecha = document.getElementById('fecha').value;

          var modal = document.getElementById(modal[0].id)              
          var form = modal.getElementsByTagName("form")   
          /*                         
          for (var i=0; i<form[0].elements.length; i++) {
            if(form[0].elements[i].tagName == 'INPUT') {
              if(form[0].elements[i].name.includes('mr_fecha_')) {
                if(moment(fecha) < moment(form[0].elements[i].value)) {
                  alert('La FECHA no pueden ser superiores a ' + moment(fecha).format('DD/MM/YYYY') + '. Revise el siguiente valor: ' + moment(form[0].elements[i].value).format('DD/MM/YYYY'))
                  e.preventDefault();
                  _form.stopProgress()
                  return false
                }
              }

              if(form[0].elements[i].name.includes('mr_km_')) {                    
                if(parseFloat(km_inicial) < parseFloat(form[0].elements[i].value)) {
                  alert('Los KM no pueden ser superiores a ' + km_inicial + '. Revise el siguiente valor: ' + form[0].elements[i].value)
                  e.preventDefault();
                  _form.stopProgress()
                  return false
                }
              }

              if(form[0].elements[i].name.includes('mr_c_fecha_')) {
                if(moment(fecha) < moment(form[0].elements[i].value)) {
                  alert('La FECHA no pueden ser superiores a ' + moment(fecha).format('DD/MM/YYYY') + '. Revise el siguiente valor: ' + moment(form[0].elements[i].value).format('DD/MM/YYYY'))
                  e.preventDefault();
                  _form.stopProgress()
                  return false                      
                }
              }

              if(form[0].elements[i].name.includes('mr_c_km_')) {                    
                if(parseFloat(km_inicial) < parseFloat(form[0].elements[i].value)) {
                  alert('Los KM no pueden ser superiores a ' + km_inicial + '. Revise el siguiente valor: ' + form[0].elements[i].value)
                  e.preventDefault();
                  _form.stopProgress()
                  return false
                }                    
              }                                    
            }
          } 
          */

          if(confirm("¿Desea guardar los cambios?") == false) {
            e.preventDefault();
            _form.stopProgress()
            return false
          }                                         
        },
        buttons: [
          {
            text: 'Cancelar',
            submit: false,
            class: 'btn btn-light btn-shadow',
            handler: function(modal) {                            
              if(confirm("Si cancela la edición perderá los cambios. ¿Desea Continuar?") == true) {
                $.destroyModal(modal)              
                document.getElementById(modal[0].id).remove()
                document.getElementById('modal-backdrop-custom').remove()
              }
            }
          },          
          {
            text: 'Guardar',
            submit: true,
            class: 'btn btn-primary btn-shadow',
            handler: function(modal) {}
         }
        ]
  })
}

function cancelar(route) {
  document.location = route;
}

function createTableComponentes(action) {  
  var unidad = document.getElementById('unidad_id');
  var plan = document.getElementById('plan');
  var km_inicial = document.getElementById('km_inicial');
  var fecha = document.getElementById('fecha');
  var estimativo = document.getElementById('estimativo');

  if(!unidad.value) {
    alert("No se agregó la únidad.")
    return false
  }

  if(!plan.value) {
    alert("No se agregó el plan.")
    return false    
  }

  if(!km_inicial.value) {
    alert("No se agregó los KM iniciales.")
    return false    
  }

  if(!fecha.value) {
    alert("No se agregó la fecha.")
    return false    
  }

  if(!estimativo.value) {
    alert("No se agregó el estimativo mensual.")
    return false    
  }

  var hidden = ''
  hidden = hidden + `<input type="hidden" name="unidad" value="${unidad.value}">`
  hidden = hidden + `<input type="hidden" name="plan" value="${plan.value}">`
  hidden = hidden + `<input type="hidden" name="km_inicial" value="${km_inicial.value}">`
  hidden = hidden + `<input type="hidden" name="fecha" value="${fecha.value}">`
  hidden = hidden + `<input type="hidden" name="estimativo" value="${estimativo.value}">`
  hidden = hidden + `<input type="hidden" name="action" value="${action}">`

  var table = hidden
  table = table + 
  `<table class='table table-sm table-hover table-bordered table-mr-custom'>
    <thead>
      <tr class="table-dark">
        <th width="20%">Parte</th>
        <th width="55%">Actividad</th>
        <th width="25%">Último mant. realizado</th>
      </tr>
    </thead>
    <tbody>`

  arrComponente.forEach(element => {
    if(element.plan_id == plan.value) {
      table = table + `        
        <tr>
          <td class="align-middle td-parte" title="${element.nombre}">${element.nombre}</td>
          <td class="align-middle td-actividad">`          
            table = table + `<table class='table table-sm table-hover mb-0'>`            
            element.tareas.forEach(tarea => {
              table = table + `<tr>`
              table = table + `<td class="align-middle td-actividad" title="${tarea.descripcion}">${tarea.descripcion}</td>`
              table = table + `</tr>`
            });
            table = table + `</table>`

          table = table +`</td>
          <td class="align-middle td-inputs">`            
            table = table + `<table class='table table-sm table-hover mb-0'>`     
            element.tareas.forEach(tarea => {                            
              table = table + `<tr>`       
              if(tarea.frecuencia == 'kms') {
                table = table + `<td class="p-0">
                                    <input type="number" name="mr_km_${element.id}_${tarea.id}" class="form-control form-control-sm" value="${km_inicial.value}"></input>
                                </td>`
              } 
              if(tarea.frecuencia == 'dias') {
                table = table + `<td class="p-0">
                                  <input type="date" name="mr_fecha_${element.id}_${tarea.id}" class="form-control form-control-sm" value="${fecha.value}"></input>
                                </td>`
              }              
              if(tarea.frecuencia == 'combinado') {
                table = table + `<td class="p-0">
                                  <div class="row">
                                    <div class="col-md-6 pr-0">
                                      <input type="number" name="mr_c_km_${element.id}_${tarea.id}" class="form-control form-control-sm" value="${km_inicial.value}"></input>
                                    </div>
                                    <div class="col-md-6 pl-0">
                                      <input type="date" name="mr_c_fecha_${element.id}_${tarea.id}" class="form-control form-control-sm" value="${fecha.value}"></input>
                                    </div>
                                  </div>
                                </td>`
              }                            
              table = table + `</tr>`       
            });      
            table = table + `</table>`

          table = table + 
          `</td>
        </tr>`
    }
  });

  table = table + `
      </tbody>
    </table>`

  document.getElementById('container-mantenimiento-rutinario-inicial').innerHTML = table

  return true
}
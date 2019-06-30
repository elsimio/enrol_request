<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Enrollment Request plugin: language string definitions.
 * 
 * @package   enrol_request
 * @copyright 2012 Inter-American Development Bank (http://www.iadb.org) (bid-indes@iadb.org)
 * @author    Maiquel Sampaio de Melo - Melvyn Gomez (melvyng@openranger.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * Based on work of Michael Avelar <mavelar@moodlerooms.com> and Lucas Sa (lucas.sa@gmail.com)
 */
$string['pluginname'] = 'Requisición de Postulación INDES';
$string['pluginname_desc'] = 'Postulación basada en el análisis de administradores del curso.';

$string['addinstancebeforequestions'] = 'Por favor salvar primero la nueva Requisición de Postulación antes de agregar preguntas';
$string['additionalquestions'] = 'Preguntas';
$string['additionalquestionshelp'] = 'Adicionar Preguntas Adicionales';
$string['additionalquestionstitle'] = 'Preguntas del Curso';
$string['addmorequestions'] = 'Adicionar pregunta';
$string['basicdatatitle'] = 'Datos Básicos';
$string['beforetoday'] = 'La fecha debe ser por lo menos un año antes a la fecha de hoy';
$string['canresubmit'] = 'Puede volver a ingresar las solicitudes';
$string['changestatus'] = 'Cambiar Estado';
$string['chooseacourse'] = 'Debe elegir un curso';
$string['city'] = 'Ciudad';
$string['confirmchangestatus'] = '¿Esta seguro que desea cambiar permanentemente todos los estados de solicitudes visibles y notificar a los usuarios seleccionados?';
$string['confirmdeleterequest'] = '¿Esta seguro que desea eliminar permanentemente esta solicitud pendiente?';
$string['country'] = 'País';
$string['course_courseurl'] = 'Enlace del curso';
$string['customtitle'] = 'Educación y Trabajo';
$string['dateofbirth'] = 'fecha de Nascimiento';
$string['deadline'] = 'Fecha Límite';
$string['deadlineHasPassed'] = 'Por el momento este curso no admite matriculaciones. Si tiene alguna consulta por favor no dude en comunicarse con BID-INDES@iadb.org';
$string['defaultname'] = 'Finalizar Postulación';
$string['department'] = 'Seción / Departamento / División';
$string['educationtitle'] = 'Educación';
$string['email'] = 'E-mail';
$string['emailsubject'] = 'El estado de su postulación se ha actualizado.';
$string['emailsubjectselected'] = 'Curso virtual BID-INDES: Seleccionado {$a->coursefullname}';
$string['emailsubjectselectedscholarship'] = 'Curso virtual BID-INDES: Seleccionado con Beca {$a->coursefullname}';
$string['emailsubjectnotselected'] = 'Curso virtual BID-INDES: No Seleccionado {$a->coursefullname}';
$string['emailsubjectpaid'] = 'Curso virtual BID-INDES: Pago {$a->coursefullname}';
$string['emailsubjectenrolled'] = 'Curso virtual BID-INDES: Matriculado {$a->coursefullname}';
$string['emailsubjectenrollednoindes'] = 'Curso virtual BID-INDES: Matriculado No INDES {$a->coursefullname}';
$string['emailsubjectpending'] = 'Curso virtual BID-INDES: Pendiente {$a->coursefullname}';
$string['emailsubjectwaitinglist'] = 'Curso virtual BID-INDES: Lista de Espera {$a->coursefullname}';
$string['emailsubjectpaymentnotreceived'] = 'Curso virtual BID-INDES: Pago No Recibido {$a->coursefullname}';
$string['emailsubjectearlybird'] = 'Curso virtual BID-INDES: Pago-Pronto Recibido {$a->coursefullname}';
$string['emailbodyhtml'] = '{$a->firstname} {$a->lastname}, curso : {$a->coursefullname} se ha actualizado.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['selected1'] = '
<p>Estimado(a) Postulante,</p>
<p>Nos es grato comunicarle que usted ha sido seleccionado(a) para participar en el curso virtual "nombre del curso" <b>a iniciar XXX (fecha)</b>. Confiamos que su aporte ser&aacute; valioso y esperamos que le resulte altamente relevante y provechoso.</p>
<p>El cupo para este curso es limitado. La inscripci&oacute;n se realizar&aacute; por un estricto orden de llegada. Por lo tanto, le agradecemos realizar el pago de la matr&iacute;cula a la brevedad.</p>
<p>El curso se desarrolla totalmente en espa&ntilde;ol, lo cual implica que de no ser &eacute;sta su lengua materna, deber&aacute; tener dominio de la misma para poder realizar y entregar  las actividades, adem&aacute;s de participar en los foros en este idioma.</p>
<p>Le pedimos que lea detenidamente toda la informaci&oacute;n que le ofrecemos a continuaci&oacute;n para asegurar su matr&iacute;cula y excelentes resultados en el curso.</p>
<p><b>PAGO DEL CURSO:</b></p>
<p>Para participar en el curso, debe realizar el pago del costo de matr&iacute;cula igual a <b>US$ importe, </b> <b><u>antes del XX de XX de XXX.</u></b></p>
<p>El pago de matr&iacute;cula lo podr&aacute; realizar de dos formas:</p>
<p>1. Tarjeta de cr&eacute;dito (Internacional). Ingrese a su cuenta INDES a trav&eacute;s del link www.indes.org, cursos virtuales - Programados, y luego haga clic en el nombre del curso "(link al nombre del curso)" y siga las instrucciones descritas en la p&aacute;gina. 
Una vez realizado el pago, recibir&aacute; una confirmaci&oacute;n autom&aacute;tica de su pago a trav&eacute;s de su correo electr&oacute;nico registrado en nuestro sistema.</p>
<p>2. Por dep&oacute;sito o transferencia electr&oacute;nica en moneda nacional a la cuenta del BID en su pa&iacute;s de residencia. Siga las instrucciones a continuaci&oacute;n:</p>
<p>a. El pago se debe realizar en moneda nacional a nombre del BANCO INTERAMERICANO DE DESARROLLO. En el siguiente enlace se incluye el listado de las cuentas bancarias por pa&iacute;s donde puede realizar dicho dep&oacute;sito:
<a href="http://www.iadb.org/es/indes/instrucciones-para-pago-y-cuentas-bancarias-del-bid-en-los-paises,6564.html"></a>
<a href="http://www.iadb.org/es/indes/instrucciones-para-pago-y-cuentas-bancarias-del-bid-en-los-paises,6564.html">http://www.iadb.org/es/indes/instrucciones-para-pago-y-cuentas-bancarias-del-bid-en-los-paises,6564.html</a></p>
<p>b. Una vez realizado el pago, es importante <b>ENVIAR UN MENSAJE CONFIRMATORIO a bid-indes@iadb.org, con copia a la(s) cuenta(s) de correo de su pa&iacute;s indicadas en el listado del enlace anterior</b>. 
Adjunte al mensaje una COPIA ESCANEADA de su constancia de pago (formato GIF o JPG), incluyendo claramente su nombre y el nombre del curso. <b><u>Este env&iacute;o de confirmaci&oacute;n por los medios indicados se tiene que realizar antes XXX de 2014 y es la &uacute;nica forma de acreditaci&oacute;n del pago realizado.</u></b></p>
<p><b>EXPECTATIVAS:</b></p>
<p>La modalidad virtual del curso requiere de constancia en su participaci&oacute;n durante sus<b> <span>10 semanas</b> de duraci&oacute;n. Como sabe, el curso demandar&aacute; aproximadamente <b><u>10-12 horas de trabajo cada semana</u></b>, en el horario de su mayor conveniencia.</p>
<p>Las expectativas concretas de su trabajo ser&aacute;n las siguientes:</p>
<p>* Ingresar al aula virtual (se accede por Internet) al menos una vez al d&iacute;a para mantenerse informado sobre propuestas de actividades, lecturas y tareas del curso.</p>
<p>* Notificar al profesor tutor las circunstancias que excepcionalmente le pueden impedir tener un desarrollo normal del curso y adquirir el compromiso de ponerse al d&iacute;a seg&uacute;n propuesta del profesor tutor.</p>
<p>* Desarrollar todas las actividades obligatorias del curso dentro del plazo estipulado.</p>
<p>Agradecemos nuevamente su inter&eacute;s y aprovechamos la oportunidad para hacerle llegar nuestros m&aacute;s cordiales saludos.</p>
<p>&nbsp;</p>
<p>Atentamente,</p>
<p><b>INDES</b></p>
<p>Instituto Interamericano para el Desarrollo Econ&oacute;mico Social</p>
<p>Sector de Conocimiento y Aprendizaje</p>
<p>1300 New York Avenue, N.W.</p>
<p>Washington, D.C. 20577</p>
<p>USA</p>
<p><a href="http://www.indes.org/">www.indes.org</a></p>
';
$string['emailbodyhtmlselected'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlselectedscholarship'] = '{$a->recipientfullname}, curso: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlnotselected'] = '{$a->recipientfullname}, curso: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlpaid'] = '{$a->recipientfullname}, curso: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlenrolled'] = '{$a->recipientfullname}, curso: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlenrollednoindes'] = '{$a->recipientfullname}, curso: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlpending'] = '{$a->recipientfullname}, curso: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlwaitinglist'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlpaymentnotreceived'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['emailbodyhtmlearlybird'] = '{$a->recipientfullname}, course: <a href=\"{$a->courseurl}\">{$a->coursefullname}</a>.<br/> Su estado ha cambiado para <i>{$a->status}</i>.';
$string['enablestatususe'] = 'Activar estado';
$string['enablestatususedesc'] = 'Desmarcar la opción para desactivar el uso de esta condición en todo el sitio';
$string['enrolrequest'] = 'Complete Postulación';
$string['erremail'] = 'No es posible enviar un correo electronico al usuario';
$string['errenrol'] = 'No es posible inscribir al usuario en el curso';
$string['errupdatestatus'] = 'No es posible actuallizar el registro';
$string['female'] = 'Femenino';
$string['fieldofstudy'] = 'Campo de Estudio';
$string['firstname'] = 'Nombre';
$string['gender'] = 'Género';
$string['generalhelp'] = 'Configuraciones Generales';
$string['graduationyear'] = 'Año de Graduación';
$string['guestnotallowed'] = 'Lo sentimos, el curso no permite acceso a invitados.';
$string['guestlogininfo'] = 'Por favor ingresar con su cuenta INDES para ver los métodos de registro activos del curso. En este momento está usando el acceso para invitados';
$string['highestgrade'] = 'Último grado obtenido';
$string['institutename'] = 'Nombre Institución';
$string['isselfenrollment'] = 'Habilitar Auto-Inscripción';
$string['lastname'] = 'Apellido';
$string['major'] = 'Especialidad';
$string['male'] = 'Masculino';
$string['manageruser'] = 'Gestionar Usuario';
$string['maxenroldays'] = 'Número Máximo de Días de Inscripción';
$string['maxenroldaysdesc'] = 'Número máximo de días que un usuario registrado puede estar en el estado \"Seleccionado\" sin ser cambiado. Si el número máximo de dias permitido es excedido por un usuario, el estado de la solicitud del usuario cambiará a \"No Seleccionado\". (Poner en 0 para desactivar este cambio automático de estado)';
$string['modname'] = 'Solicitud de Postulación';
$string['modulename'] = 'Solicitud de Postulación';
$string['modulenameplural'] = 'Solicitudes de Postulación';
$string['mustchoose'] = 'Debe elegir una opción';
$string['nationality'] = 'Nacionalidad';
$string['newrequest'] = 'Postular';
$string['noEnrollments'] = 'Todavía no hay postulación para este curso';
$string['noEnrollmentsFound'] = 'No hay postulación con este estado';
$string['notified'] = 'Fecha de Notificación';
$string['notify'] = 'Notificar';
$string['numrequests'] = '# Solicitudes Pendientes';
$string['of'] = 'de';
$string['perpage'] = 'Por Página';
$string['phone'] = 'Teléfono';
$string['position'] = 'Cargo';
$string['question'] = 'Pregunta';
$string['questionname'] = 'Título de la Pregunta';
$string['questiontext'] = 'Texto de la Pregunta';
$string['request:config'] = 'Permite a los gerentes del curso configurar la postulación';
$string['request:delete'] = 'Permite al usuario borrar solicitudes de postulación';
$string['request:editquestion'] = 'Permite a los gerentes del curso adicionar o editar preguntas';
$string['request:enabledisable'] = 'Permite al usuario habilitar/deshabilitar solicitudes de postulación';
$string['request:export'] = 'Permite al usuario exportar las postulaciones en un archivo';
$string['request:manage'] = 'Permite a los gerentes del curso revisar postulaciones y también actualizar el estatus de acceptación';
$string['request:request'] = 'Permite al postulante solicitar ser enrolado en un curso';
$string['request:unenrol'] = 'Permite al postulante cancelar su registro';
$string['request:view'] = 'Permite al usuario vizualizar la solicitudes de postulación';
$string['request:viewrequests'] = 'Permite al usuario vizualizar todas las solicitudes de postulación, editar estado y notificar';
$string['requestcourse'] = 'Solicitud de Curso';
$string['requestedcourse'] = 'Curso Solicitado';
$string['requestenrollment'] = 'Finalizar Postulación';
$string['requestnum'] = 'Solicitud #';
$string['requestuser'] = 'Usuario que Solicitó';
$string['responsibilities'] = 'En resumen, las tres funciones más importantes y las responsabilidades de su cargo';
$string['responsibilityone'] = '1:';
$string['responsibilitythree'] = '3:';
$string['responsibilitytwo'] = '2:';
$string['showing'] = 'Mostrando';
$string['siteonlytxt'] = 'El módulo de Solicitud de Postulación sólo se puede instalar en la Página de Inicio del sitio.';
$string['status'] = 'Estado';
$string['status0'] = 'Completa';
$string['status1'] = 'Matriculado';
$string['status2'] = 'Pago';
$string['status3'] = 'No seleccionado';
$string['status4'] = 'Seleccionado';
$string['status5'] = 'Seleccionado con Beca';
$string['status6'] = 'Lista de Espera';
$string['status7'] = 'Pago no recibido';
$string['status8'] = 'Pronto-Pago';
$string['status9'] = 'Matriculado No INDES';
$string['statusenrolled'] = 'Estado de la Postulación';
$string['statusenrolleddesc'] = 'Opciones del estado <b><i>Inscrito</b></i> de la requisición de postulación';
$string['statusenrollednoindes'] = 'Estado de la Postulación';
$string['statusenrollednoindesdesc'] = 'Opciones del estado <b><i>Inscrito No INDES</b></i> de la requisición de postulación';
$string['statusmessage'] = 'Su actual estado de matrícula es: <i>{$a}</i>';
$string['statusnotselected'] = 'El Estado no fue seleccionado';
$string['statusnotselecteddesc'] = 'Opciones del estado <b><i>No Seleccionado</b></i> de la requisición de postulación';
$string['statuspaid'] = 'Estado Pagado';
$string['statuspaiddesc'] = 'Opciones del estado <b><i>Pagado</b></i> de la requisición de postulación';
$string['statuspending'] = 'Estado Pendiente';
$string['statuspendingdesc'] = 'Opciones del estado <b><i>Pendiente</b></i> de la requisición de postulación';
$string['statusselected'] = 'Estado Seleccionado';
$string['statusselecteddesc'] = 'Opciones del estado <b><i>Seleccionado</b></i> de la requisición de postulación';
$string['statusselectedscholarship'] = 'Estado Seleccionado con Beca';
$string['statusselectedscholarshipdesc'] = 'Opciones del estado <b><i>Seleccionado con Beca</b></i> de la requisición de postulación';
$string['timesubmitted'] = 'Fecha de Postulación';
$string['success'] = 'Su solicitud de postulación ha sido ingresada exitosamente. El INDES notificará al postulante sobre su selección una semana después de la fecha cierre de postulaciones. En caso de ser seleccionado, se brindarán las indicaciones sobre cómo realizar el pago de matrícula correspondiente y su plazo.';
$string['totalpending'] = 'Total de Pendientes';
$string['updatestatusandnotify'] = 'Actualizar/ Notificar Solicitudes';
$string['updatestatustooltip'] = 'Actualiza el estado de las postulaciones visibles y notifica a los usuarios seleccionados';
$string['worktitle'] = 'Trabajo';
$string['emailbodytxt'] = '{$a->firstname} {$a->lastname}, su solicitud de Postulación al curso : {$a->coursefullname} se ha actualizado.<br/> Su estado ha cambiado para <i>{$a->status}</i>.'; // ORPHANED
$string['requestenrollmentcourse'] = 'Postular a este curso';
$string['buttonreviewenrollment'] = 'Ver mi postulación';
$string['buttondeleteenrollment'] = 'Borrar mi postulación';
$string['unenrolconfirm'] = '¿Está seguro que quiere borrar tu postulación en el curso "{$a}"?';
$string['disableenrollmentconfirm'] = '¿Está seguro que quiere deshabilitar la postulación del participante "{$a->user}" en el curso "{$a->course}"?';
$string['enableenrollmentconfirm'] = '¿Está seguro que quiere habilitar la postulación del participante "{$a->user}" en el curso "{$a->course}"?';
$string['disableenrollment'] = 'Deshabilitar postulación de participante';
$string['enableenrollment'] = 'Habilitar postulación de participante';
$string['deleteenrollmentconfirm'] = '¿Está seguro que quiere borrar la postulación del participante "{$a->user}" en el curso "{$a->course}"?';
$string['deleteenrollment'] = 'Borrar postulación de participante';
$string['deletequestionconfirm'] = '¿Está seguro que quiere borrar la siguiente pregunta?';
$string['headeraddquestion'] = 'Adicionar Nueva Pregunta';
$string['headereditquestion'] = 'Editar Pregunta';

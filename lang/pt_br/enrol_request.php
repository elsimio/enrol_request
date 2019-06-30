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
$string['pluginname'] = 'Solicitação de Matrícula do INDES';
$string['pluginname_desc'] = 'Solicitação de matrícula baseada em análise feita pelos administradores do curso.';

$string['addinstancebeforequestions'] = 'Por favor salve o novo formulário de Solicitação de Matrícula antes de adicionar perguntas';
$string['additionalquestions'] = 'Perguntas Adicionais';
$string['additionalquestionshelp'] = 'Adicionar Perguntas';
$string['additionalquestionstitle'] = 'Perguntas do Curso';
$string['addmorequestions'] = 'Adicionar 1 pergunta';
$string['basicdatatitle'] = 'Dados Básicos';
$string['changestatus'] = 'Mudar Status';
$string['chooseacourse'] = 'Deve escolher um curso';
$string['city'] = 'Cidade';
$string['country'] = 'País';
$string['customtitle'] = 'Educação e Trabalho';
$string['dateofbirth'] = 'Data de Nascimento';
$string['defaultname'] = 'Finalizar Inscrição';
$string['department'] = 'Seção/ Dept./ Divisão';
$string['educationtitle'] = 'Educação';
$string['email'] = 'E-mail';
$string['emailbodyhtml'] = '{$a->firstname} {$a->lastname}, seu pedido de matrícula para o curso: {$a->coursefullname} foi atualizado.<br/> Seu status foi modificado para <i>{$a->status}</i>.
<br>
Sugerimos manter-se informado através de nossa página Web www.indes.org dos diferentes programas e atividades que desenvolvemos, esperando poder contar com você em um futuro próximo.
<br>
Aproveitamos a oportunidade para enviar nossas cordiais saudações.

<b>INDES<br>
Coordenação Operacional</b>';
$string['enrolrequest'] = 'Finalize sua Inscrição';
$string['female'] = 'Feminino';
$string['fieldofstudy'] = 'Campo de Estudo';
$string['firstname'] = 'Nome';
$string['guestnotallowed'] = 'Desculpe, este curso não permite acceso a visitantes.';
$string['guestlogininfo'] = 'Por favor entre com sua conta INDES para ver os métodos de mátricula ativos deste curso. Neste momento você está acessando a plataforma como visitante';
$string['gender'] = 'Gênero';
$string['graduationyear'] = 'Ano de Graduação';
$string['highestgrade'] = 'Último grau obtido';
$string['institutename'] = 'Nome da Instituição';
$string['lastname'] = 'Sobrenome';
$string['male'] = 'Masculino';
$string['modulename'] = 'Solicitação de Matrícula';
$string['nationality'] = 'Nacionalidade';
$string['newrequest'] = 'Inscrever-se';
$string['numrequests'] = 'Solicitações Pendentes';
$string['perpage'] = 'Por Página';
$string['phone'] = 'Telefone';
$string['position'] = 'Cargo';
$string['questionname'] = 'Título da Pergunta';
$string['questiontext'] = 'Texto da Pergunta';

$string['request:config'] = 'Permite aos gerentes do curso configurar o plugin de solicitação de matrícula';
$string['request:delete'] = 'Permite ao usuário apagar solicitações de matrícula';
$string['request:editquestion'] = 'Permite aos gerentes de curso adicionar ou editar perguntas';
$string['request:enabledisable'] = 'Permite ao usuário habilitar/desabilitar solicitações de matrícula';
$string['request:export'] = 'Permite ao usuario exportar solicitações de matrícula a um arquivo';
$string['request:manage'] = 'Permite aos gerentes de curso revisar soliticações de matrícula e atualizar o estado de aceitação';
$string['request:request'] = 'Permite que o usuário para solicitar a inscrição';
$string['request:unenrol'] = 'Permite ao usuario cancelar seu pedido de registro';
$string['request:view'] = 'Permite ao usuário vizualizar as solicitações de matrícula para um curso';
$string['request:viewrequests'] = 'Permite ao usuário visualizar/editar e mudar o estado todas solicitações de matrícula, além da possibilidade de notificar aos solicitantes';

$string['requestcourse'] = 'Curso Requerido';
$string['requestenrollment'] = 'Finalizar Inscrição';
$string['requestnum'] = 'Solicitação';
$string['status'] = 'Status';
$string['status0'] = 'Completa';
$string['status1'] = 'Matriculado';
$string['status2'] = 'Pago';
$string['status3'] = 'Não Selecionado';
$string['status4'] = 'Seleccionado';
$string['status5'] = 'Seleccionado com Bolsa de Estudos';
$string['status6'] = 'Lista de espera';
$string['status7'] = 'Pagamento não Recebido';
$string['status8'] = 'Pronto-Pago';
$string['status9'] = 'Matriculado No INDES';
$string['timesubmitted'] = 'Data de Submissão';
$string['updatestatusandnotify'] = 'Atualizar/ Notificar Solicitações';
$string['worktitle'] = 'Trabalho';

$string['success'] = 'Sua solicitação foi submetida com êxito. INDES te notificará sobre os resultados da seleção que ocorrerá uma semana depois do prazo final de inscrição. Se você for selecionado iremos fornecer-lhe instruções sobre como pagar a taxa de matrícula e os termos do pagamento.';
$string['requestenrollmentcourse'] = 'Inscrever-se neste curso';
$string['buttonreviewenrollment'] = 'Revisar minha matrícula';
$string['buttondeleteenrollment'] = 'Cancelar minha matrícula';
$string['unenrolconfirm'] = 'Tem certeza que quer apagar sua matrícula no curso "{$a}"?';
$string['noEnrollments'] = 'Ainda não há matrícula em este curso';
$string['noEnrollmentsFound'] = 'Não há nenhuma matrícula com o estado selecionado';
$string['deadlineHasPassed'] = 'Neste momento, este curso não permite novas matrículas. Se você tiver alguma dúvida, por favor não hesite em contactar-nos: BID-INDES@iadb.org'; 
$string['disableenrollmentconfirm'] = 'Tem certeza que você quer desabilitar a matrícula do participante "{$a->user}" no curso "{$a->course}"?';
$string['enableenrollmentconfirm'] = 'Tem certeza que você quer habilitar a matrícula do participante "{$a->user}" no curso "{$a->course}"?';
$string['disableenrollment'] = 'Desabilitar matrícula de participante';
$string['enableenrollment'] = 'Habilitar matrícula de participante';
$string['deleteenrollmentconfirm'] = 'Tem certeza que você quer remover a matrícula do participante "{$a->user}" no curso "{$a->course}"?';
$string['deleteenrollment'] = 'Remover matrícula de participante';
$string['request:enabledisable'] = 'Permite ao usuário habilitar/desabilitar matrículas';
$string['request:delete'] = 'Permite ao usuário remover matrículas';
$string['deletequestionconfirm'] = '¿Tem certeza que você quer remover a pergunta abaixo?';
$string['headeraddquestion'] = 'Adicionar Nova Pergunta';
$string['headereditquestion'] = 'Editar Pergunta';

<?php
require_once 'funcoes.php';
apenasPost();


$cargos = [
    'recepcionista' => ['nome' => 'Recepcionista', 'piso' => 1800.0],
    'camareira'     => ['nome' => 'Camareira',     'piso' => 1600.0],
    'cozinheiro'    => ['nome' => 'Cozinheiro',    'piso' => 2200.0],
    'manobrista'    => ['nome' => 'Manobrista',    'piso' => 1600.0],
    'gerente'       => ['nome' => 'Gerente',       'piso' => 4500.0],
];

$erros   = [];
$nome    = campo('nome');
$cpf     = campo('cpf');
$cargo   = campo('cargo');
$salario = numero(campo('salario'));
$adm     = dataValida(campo('admissao'));
$email   = campo('email');

if (tamanho($nome) < 3)                       $erros[] = 'Informe o nome completo.';
if (!cpfValido($cpf))                           $erros[] = 'CPF inválido.';
if (!isset($cargos[$cargo]))                    $erros[] = 'Selecione um cargo válido.';
if ($salario === null || $salario <= 0)         $erros[] = 'O salário deve ser maior que zero.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';

$hoje = new DateTime('today');
if (!$adm)               $erros[] = 'Data de admissão inválida.';
elseif ($adm > $hoje)    $erros[] = 'A data de admissão não pode ser futura.';

if (isset($cargos[$cargo]) && $salario !== null && $salario > 0 && $salario < $cargos[$cargo]['piso']) {
    $erros[] = 'Salário abaixo do piso do cargo ' . $cargos[$cargo]['nome'] . ' (' . moeda($cargos[$cargo]['piso']) . ').';
}

if ($erros) {
    responder(false, 'Funcionário não aceito.', ['erros' => $erros], 422);
}


$anos = $adm->diff($hoje)->y;
$ferias = $anos >= 1 ? 'Já tem direito a férias.' : 'Ainda não completou o período aquisitivo de férias.';


responder(true, "$nome contratado como {$cargos[$cargo]['nome']} ($anos ano(s) de casa). $ferias");

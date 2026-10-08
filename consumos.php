<?php
require_once 'funcoes.php';
apenasPost();


$servicos = [
    'restaurante' => 'Restaurante',
    'frigobar'    => 'Frigobar',
    'lavanderia'  => 'Lavanderia',
    'spa'         => 'Spa',
    'transfer'    => 'Transfer',
];

$erros    = [];
$reserva  = filter_var(campo('reserva'), FILTER_VALIDATE_INT);
$servico  = campo('servico');
$qtd      = filter_var(campo('quantidade'), FILTER_VALIDATE_INT);
$valor    = numero(campo('valor_unitario'));
$data     = dataValida(campo('data_consumo'));
$func     = campo('funcionario');

if ($reserva === false || $reserva < 1)    $erros[] = 'Informe um número de reserva válido.';
if (!isset($servicos[$servico]))           $erros[] = 'Selecione um serviço válido.';
if ($qtd === false || $qtd < 1)            $erros[] = 'A quantidade deve ser de pelo menos 1.';
if ($valor === null || $valor <= 0)        $erros[] = 'O valor unitário deve ser maior que zero.';
if (tamanho($func) < 3)                  $erros[] = 'Informe o nome do funcionário responsável.';

if (!$data)                                $erros[] = 'Data do consumo inválida.';
elseif ($data > new DateTime('today'))     $erros[] = 'Não é possível lançar consumo com data futura.';

if ($erros) {
    responder(false, 'Consumo não aceito.', ['erros' => $erros], 422);
}


$subtotal = $qtd * $valor;
$taxa     = $subtotal * 0.10;
$total    = $subtotal + $taxa;


responder(true,
    "Lançado na reserva #$reserva: $qtd × {$servicos[$servico]}. Subtotal " . moeda($subtotal) .
    ' + taxa de serviço ' . moeda($taxa) . ' = ' . moeda($total) . '.'
);

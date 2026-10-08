<?php
require_once 'funcoes.php';
apenasPost();

$tipos = tiposQuarto();
$erros = [];
$cpf   = campo('cpf');
$tipo  = campo('tipo_quarto');
$in    = dataValida(campo('checkin'));
$out   = dataValida(campo('checkout'));
$qtd   = filter_var(campo('num_hospedes'), FILTER_VALIDATE_INT);
$obs   = campo('observacoes');

if (!cpfValido($cpf))      $erros[] = 'CPF do hóspede inválido.';
if (!isset($tipos[$tipo])) $erros[] = 'Selecione um tipo de quarto válido.';

if (!$in || !$out) {
    $erros[] = 'Informe datas de check-in e check-out válidas.';
} elseif ($in < new DateTime('today')) {
    $erros[] = 'O check-in não pode estar no passado.';
} elseif ($out <= $in) {
    $erros[] = 'O check-out deve ser depois do check-in.';
}

if ($qtd === false || $qtd < 1) {
    $erros[] = 'Informe a quantidade de hóspedes (mínimo 1).';
} elseif (isset($tipos[$tipo]) && $qtd > $tipos[$tipo]['capacidade']) {
    $erros[] = 'O quarto ' . $tipos[$tipo]['nome'] . ' comporta no máximo ' . $tipos[$tipo]['capacidade'] . ' pessoa(s).';
}

if (tamanho($obs) > 200) $erros[] = 'As observações devem ter no máximo 200 caracteres.';

if ($erros) {
    responder(false, 'Reserva não aceita.', ['erros' => $erros], 422);
}


$noites = $in->diff($out)->days;
$total  = $noites * $tipos[$tipo]['diaria'];
$desconto = '';
if ($noites >= 7) {
    $total *= 0.90;
    $desconto = ' (desconto de 10% para 7 noites ou mais)';
}


responder(true, "Reserva válida: $noites noite(s) em quarto {$tipos[$tipo]['nome']}. Total: " . moeda($total) . $desconto . '.');

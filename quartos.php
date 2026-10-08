<?php
require_once 'funcoes.php';
apenasPost();

$tipos    = tiposQuarto();
$erros    = [];
$numero   = filter_var(campo('numero'), FILTER_VALIDATE_INT);
$andar    = filter_var(campo('andar'), FILTER_VALIDATE_INT);
$tipo     = campo('tipo');
$cap      = filter_var(campo('capacidade'), FILTER_VALIDATE_INT);
$diaria   = numero(campo('diaria'));
$situacao = campo('situacao');

if ($numero === false || $numero < 1 || $numero > 999) $erros[] = 'Número do quarto deve estar entre 1 e 999.';
if ($andar === false || $andar < 0 || $andar > 20)     $erros[] = 'Andar deve estar entre 0 e 20.';
if (!isset($tipos[$tipo]))                             $erros[] = 'Selecione um tipo de quarto válido.';
if ($cap === false || $cap < 1)                        $erros[] = 'Capacidade deve ser de pelo menos 1 pessoa.';
if ($diaria === null || $diaria <= 0)                  $erros[] = 'A diária deve ser maior que zero.';
if (!in_array($situacao, ['disponivel', 'ocupado', 'manutencao'], true)) $erros[] = 'Situação inválida.';

if (isset($tipos[$tipo]) && $cap !== false && $cap > $tipos[$tipo]['capacidade']) {
    $erros[] = 'O tipo ' . $tipos[$tipo]['nome'] . ' comporta no máximo ' . $tipos[$tipo]['capacidade'] . ' pessoa(s).';
}

if ($erros) {
    responder(false, 'Quarto não aceito.', ['erros' => $erros], 422);
}

$base = $tipos[$tipo]['diaria'];
$aviso = '';
if (abs($diaria - $base) / $base > 0.30) {
    $aviso = ' Atenção: a diária difere mais de 30% do valor de tabela (' . moeda($base) . ').';
}
if ($situacao === 'manutencao') {
    $aviso .= ' Quartos em manutenção não aparecerão para novas reservas.';
}

responder(true, "Quarto $numero ({$tipos[$tipo]['nome']}, andar $andar) registrado com diária de " . moeda($diaria) . '.' . $aviso);

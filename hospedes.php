<?php
require_once 'funcoes.php';
apenasPost();

$erros = [];
$nome  = campo('nome');
$cpf   = somenteDigitos(campo('cpf'));
$email = campo('email');
$tel   = somenteDigitos(campo('telefone'));
$nasc  = dataValida(campo('data_nascimento'));

if (tamanho($nome) < 3)                       $erros[] = 'Informe o nome completo.';
if (!cpfValido($cpf))                           $erros[] = 'CPF inválido.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
if (strlen($tel) < 10 || strlen($tel) > 11)     $erros[] = 'Telefone deve ter DDD + número (10 ou 11 dígitos).';

$idade = 0;
if (!$nasc) {
    $erros[] = 'Data de nascimento inválida.';
} else {
    $idade = $nasc->diff(new DateTime('today'))->y;
    if ($nasc > new DateTime('today')) $erros[] = 'A data de nascimento não pode ser futura.';
    elseif ($idade < 18)               $erros[] = 'O hóspede titular deve ter 18 anos ou mais.';
}

if ($erros) {
    responder(false, 'Corrija os campos abaixo.', ['erros' => $erros], 422);
}

$categoria = $idade >= 60 ? 'Sênior (10% de desconto nas diárias)' : 'Padrão';

responder(true, "Hóspede $nome cadastrado ($idade anos). Categoria: $categoria.");

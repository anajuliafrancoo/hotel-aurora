<?php

header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Sao_Paulo');

function responder(bool $sucesso, string $mensagem, array $extra = [], int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode(
        array_merge(['sucesso' => $sucesso, 'mensagem' => $mensagem], $extra),
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

function apenasPost(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        responder(false, 'Método não permitido. Use POST.', [], 405);
    }
}

function campo(string $nome): string
{
    return trim($_POST[$nome] ?? '');
}

function tamanho(string $texto): int
{
    return preg_match_all('/./us', $texto);
}

function somenteDigitos(string $valor): string
{
    return preg_replace('/\D/', '', $valor);
}

function cpfValido(string $cpf): bool
{
    $cpf = somenteDigitos($cpf);
    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

function dataValida(string $texto): ?DateTime
{
    $d = DateTime::createFromFormat('!Y-m-d', $texto);
    return ($d && $d->format('Y-m-d') === $texto) ? $d : null;
}

function numero(string $texto): ?float
{
    $v = filter_var(str_replace(',', '.', $texto), FILTER_VALIDATE_FLOAT);
    return $v === false ? null : (float) $v;
}

function moeda(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function tiposQuarto(): array
{
    return [
        'solteiro' => ['nome' => 'Solteiro',  'diaria' => 180.0, 'capacidade' => 1],
        'casal'    => ['nome' => 'Casal',     'diaria' => 250.0, 'capacidade' => 2],
        'familia'  => ['nome' => 'Família',   'diaria' => 400.0, 'capacidade' => 4],
        'suite'    => ['nome' => 'Suíte',     'diaria' => 600.0, 'capacidade' => 3],
    ];
}

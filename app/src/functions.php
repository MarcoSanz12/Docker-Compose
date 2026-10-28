<?php

/**
 * Calcula una operación aritmética entre dos números.
 */
function calculate(float $num01, float $num02, string $operator): float
{
    return match ($operator) {
        'add' => $num01 + $num02,
        'minus' => $num01 - $num02,
        'multiply' => $num01 * $num02,
        'divide' => $num02 === 0.0
            ? throw new DivisionByZeroError('No se puede dividir entre cero.')
            : $num01 / $num02,
        default => throw new InvalidArgumentException('Operador no válido.'),
    };
}

<?php

/**
 * Middleware de Validación
 *
 * Sistema de Biblioteca Digital de Videos Educativos
 * Unidad Educativa San Francisco Xavier
 *
 * @author Roger Omar Luna Yujra
 * @version 1.0
 */

declare(strict_types=1);

namespace Middleware;

use Exception;

/**
 * Clase ValidationMiddleware
 *
 * Valida los datos de entrada de las peticiones HTTP
 * Previene ataques de inyección y asegura la integridad de los datos
 */
class ValidationMiddleware
{
    /**
     * Valida que los campos requeridos estén presentes
     *
     * @param array<string, mixed> $datos Datos a validar
     * @param array<string> $camposRequeridos Campos requeridos
     * @return void
     * @throws Exception Si falta algún campo requerido
     */
    public static function validarCamposRequeridos(array $datos, array $camposRequeridos): void
    {
        $camposFaltantes = [];

        foreach ($camposRequeridos as $campo) {
            if (!isset($datos[$campo]) || $datos[$campo] === '' || $datos[$campo] === null) {
                $camposFaltantes[] = $campo;
            }
        }

        if (!empty($camposFaltantes)) {
            self::enviarError(
                'Campos requeridos faltantes: ' . implode(', ', $camposFaltantes),
                400
            );
        }
    }

    /**
     * Valida un email
     *
     * @param string $email Email a validar
     * @return void
     * @throws Exception Si el email no es válido
     */
    public static function validarEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            self::enviarError('Email no válido', 400);
        }
    }

    /**
     * Valida una contraseña
     *
     * @param string $password Contraseña a validar
     * @param int $longitudMinima Longitud mínima (por defecto 8)
     * @return void
     * @throws Exception Si la contraseña no cumple los requisitos
     */
    public static function validarPassword(string $password, int $longitudMinima = 8): void
    {
        if (strlen($password) < $longitudMinima) {
            self::enviarError(
                "La contraseña debe tener al menos {$longitudMinima} caracteres",
                400
            );
        }

        // Opcional: Agregar validaciones adicionales de seguridad
        // (mayúsculas, minúsculas, números, caracteres especiales)
    }

    /**
     * Sanitiza una cadena de texto
     *
     * @param string $texto Texto a sanitizar
     * @return string Texto sanitizado
     */
    public static function sanitizarTexto(string $texto): string
    {
        // Eliminar etiquetas HTML
        $texto = strip_tags($texto);

        // Convertir caracteres especiales a entidades HTML
        $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');

        // Eliminar espacios en blanco al inicio y final
        $texto = trim($texto);

        return $texto;
    }

    /**
     * Valida un número entero
     *
     * @param mixed $valor Valor a validar
     * @param int|null $minimo Valor mínimo permitido
     * @param int|null $maximo Valor máximo permitido
     * @return int Valor validado
     * @throws Exception Si el valor no es un entero válido
     */
    public static function validarEntero($valor, ?int $minimo = null, ?int $maximo = null): int
    {
        if (!is_numeric($valor)) {
            self::enviarError('El valor debe ser un número entero', 400);
        }

        $valor = (int)$valor;

        if ($minimo !== null && $valor < $minimo) {
            self::enviarError("El valor debe ser mayor o igual a {$minimo}", 400);
        }

        if ($maximo !== null && $valor > $maximo) {
            self::enviarError("El valor debe ser menor o igual a {$maximo}", 400);
        }

        return $valor;
    }

    /**
     * Valida un array de IDs
     *
     * @param mixed $valor Valor a validar
     * @return array<int> Array de IDs validados
     * @throws Exception Si el valor no es un array válido de IDs
     */
    public static function validarArrayIds($valor): array
    {
        if (!is_array($valor)) {
            self::enviarError('El valor debe ser un array', 400);
        }

        $ids = [];

        foreach ($valor as $id) {
            if (!is_numeric($id)) {
                self::enviarError('Todos los IDs deben ser números', 400);
            }

            $ids[] = (int)$id;
        }

        return $ids;
    }

    /**
     * Valida que un valor esté dentro de una lista de opciones
     *
     * @param mixed $valor Valor a validar
     * @param array<mixed> $opciones Opciones válidas
     * @return mixed Valor validado
     * @throws Exception Si el valor no está en las opciones
     */
    public static function validarOpcion($valor, array $opciones)
    {
        if (!in_array($valor, $opciones, true)) {
            self::enviarError(
                'Valor no válido. Opciones permitidas: ' . implode(', ', $opciones),
                400
            );
        }

        return $valor;
    }

    /**
     * Sanitiza datos de entrada recursivamente
     *
     * @param mixed $datos Datos a sanitizar
     * @return mixed Datos sanitizados
     */
    public static function sanitizarDatos($datos)
    {
        if (is_array($datos)) {
            return array_map([self::class, 'sanitizarDatos'], $datos);
        }

        if (is_string($datos)) {
            return self::sanitizarTexto($datos);
        }

        return $datos;
    }

    /**
     * Envía una respuesta de error y termina la ejecución
     *
     * @param string $mensaje Mensaje de error
     * @param int $codigo Código HTTP
     * @return never
     */
    private static function enviarError(string $mensaje, int $codigo): never
    {
        http_response_code($codigo);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $mensaje,
            'error' => true,
        ]);
        exit;
    }
}

<?php namespace Michalsn\Minifier\Exceptions;

class MinifierException extends \RuntimeException implements ExceptionInterface
{
    public static function forWrongFileExtension(string $ext)
    {
        return new self(lang('Minifier.wrongFileExtension', [$ext]));
    }

    public static function forNoVersioningFile()
    {
        return new self(lang('Minifier.noVersioningFile'));
    }

    public static function forIncorrectDeploymentMode(string $mode)
    {
        return new self(lang('Minifier.incorrectDeploymentMode', [$mode]));
    }

    public static function forWrongReturnType(string $type)
    {
        return new self(lang('Minifier.wrongReturnType', [$type]));
    }
}

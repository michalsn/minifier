<?php

namespace Michalsn\Minifier\Exceptions;

use RuntimeException;

final class MinifierException extends RuntimeException
{
    public static function forWrongFileExtension(string $ext): static
    {
        return new self(lang('Minifier.wrongFileExtension', [$ext]));
    }

    public static function forNoVersioningFile(): static
    {
        return new self(lang('Minifier.noVersioningFile'));
    }

    public static function forIncorrectDeploymentMode(string $mode): static
    {
        return new self(lang('Minifier.incorrectDeploymentMode', [$mode]));
    }

    public static function forWrongReturnType(string $type): static
    {
        return new self(lang('Minifier.wrongReturnType', [$type]));
    }

    public static function forFileCopyError(string $file1, string $file2): static
    {
        return new self(lang('Minifier.fileCopyError', [$file1, $file2]));
    }
}

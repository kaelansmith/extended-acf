<?php

namespace Extended\ACF\Fields\Transformers;

class FieldTransformerRegistry
{
    private static ?FieldTransformer $defaultTransformer = null;
    private static array $typeTransformers = [];

    public static function setDefaultTransformer(FieldTransformer $transformer): void
    {
        self::$defaultTransformer = $transformer;
    }

    public static function setTypeTransformer(string $type, FieldTransformer $transformer): void
    {
        self::$typeTransformers[$type] = $transformer;
    }

    public static function getTransformer(string|null $type): FieldTransformer
    {
        // First check for type-specific transformer
        if ($type !== null && isset(self::$typeTransformers[$type])) {
            return self::$typeTransformers[$type];
        }

        // Fall back to default transformer
        return self::$defaultTransformer ?? new DefaultFieldTransformer();
    }

    public static function removeTypeTransformer(string $type): void
    {
        unset(self::$typeTransformers[$type]);
    }
}

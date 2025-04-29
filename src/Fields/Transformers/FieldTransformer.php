<?php

namespace Extended\ACF\Fields\Transformers;

interface FieldTransformer
{
    /**
     * Transform a field's settings into ACF's format
     *
     * @param array $settings The field settings to transform
     * @param string $key The field key
     * @param string|null $parentKey The parent key for nested fields
     * @return array The transformed field settings
     */
    public function transform(array $settings, string $key, string|null $parentKey = null): array;
}

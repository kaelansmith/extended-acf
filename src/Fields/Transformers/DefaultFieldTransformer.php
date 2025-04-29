<?php

namespace Extended\ACF\Fields\Transformers;

class DefaultFieldTransformer implements FieldTransformer
{
    public function transform(array $settings, string $key, string|null $parentKey = null): array
    {
        $result = $settings;

        if (isset($settings['conditional_logic'])) {
            $result['conditional_logic'] = array_map(
                fn($rules) => $rules->get($parentKey),
                $settings['conditional_logic']
            );
        }

        if (isset($settings['layouts'])) {
            $result['layouts'] = array_map(
                fn($layout) => $layout->get($key),
                $settings['layouts']
            );
        }

        if (isset($settings['sub_fields'])) {
            $result['sub_fields'] = array_map(
                fn($field) => $field->get($key),
                $settings['sub_fields']
            );
        }

        if (isset($settings['collapsed'], $result['sub_fields'])) {
            foreach ($result['sub_fields'] as $field) {
                if ($field['name'] === $settings['collapsed']) {
                    $result['collapsed'] = $field['key'];
                    break;
                }
            }
        }

        return $result;
    }
}

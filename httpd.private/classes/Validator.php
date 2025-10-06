<?php
class Validator {
    public static function validate($data, $rules) {
        foreach ($rules as $key => $rule) {
            if (!isset($data[$key])) {
                return ['success' => false, 'message' => "$key absent"];
            }

            if ($rule['type'] === 'int' && filter_var($data[$key], FILTER_VALIDATE_INT) === false) {
                return ['success' => false, 'message' => "$key non conforme"];
            }

            if ($rule['type'] === 'string' && (!is_string($data[$key]) || strlen($data[$key]) > $rule['max_length'])) {
                return ['success' => false, 'message' => "$key non conforme ou trop long"];
            }

            if ($rule['type'] === 'array' && !is_array($data[$key])) {
                return ['success' => false, 'message' => "$key doit être un tableau"];
            }

            // Vérification optionnelle du contenu du tableau si nécessaire
            if ($rule['type'] === 'array' && isset($rule['sub_rules']) && is_array($data[$key])) {
                foreach ($data[$key] as $index => $item) {
                    foreach ($rule['sub_rules'] as $subKey => $subRule) {
                        if (!isset($item[$subKey])) {
                            return ['success' => false, 'message' => "$key[$index] manque la clé $subKey"];
                        }

                        if ($subRule['type'] === 'string' && !is_string($item[$subKey])) {
                            return ['success' => false, 'message' => "$key[$index][$subKey] doit être une chaîne"];
                        }

                        if ($subRule['type'] === 'int' && !filter_var($item[$subKey], FILTER_VALIDATE_INT)) {
                            return ['success' => false, 'message' => "$key[$index][$subKey] doit être un entier"];
                        }
                    }
                }
            }
        }

        return ['success' => true];
    }
}



?>
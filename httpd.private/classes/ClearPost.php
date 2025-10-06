<?php
class ClearPost {
    public static function clearPost($post) {
        $cleanPost = [];
        foreach ($post as $key => $value) {
            $key = strip_tags(trim($key));
            if (is_array($value)) {
                $cleanPost[$key] = self::clearPost($value); // Appel récursif pour les tableaux imbriqués
            } else {
                $cleanPost[$key] = strip_tags(trim($value));
            }
        }
        return $cleanPost;
    }
}

  

  
?>
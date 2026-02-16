<?php


    function pathImage($image) {
        $path = "/images/" . $image;
        if (file_exists(__DIR__ . '/../../../public' . $path)) { // Check file existence using absolute path on server if possible, or just return URL. 
            // The previous check file_exists($path) was checking absolute path on FS starting from root /images which is likely wrong unless it was relative to CWD.
            // Assuming $path is relative to public for browser, we return BASE_URL . $path
            return BASE_URL . $path;
        } else {
            return BASE_URL . "/images/default.png"; // Chemin vers une image par défaut
        }
    }

    function afficheprix($prix) {
        // number_format($objet['prix'] ?? 0, 2) ? €
        return number_format($prix ?? 0, 2, ',', ' ') . ' €';
    }

?>
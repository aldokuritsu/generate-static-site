<?php

// Fonction principale pour générer des pages HTML statiques à partir des pages dynamiques WordPress.
function parcourir_et_generer_html() {
    global $pages_generees; // Variable globale pour stocker les URLs des pages générées.
    $pages_generees = array(); // Initialise le tableau des pages générées.
    $dossier = ABSPATH . 'wp-content/uploads/pages-html/'; // Définit le dossier de destination pour les fichiers HTML statiques.

    // Vérifie si le dossier de destination existe, sinon le crée.
    if (!file_exists($dossier)) {
        // Tente de créer le dossier avec des permissions 0755.
        if (!mkdir($dossier, 0755, true)) {
            // Si la création échoue, ajoute un message d'erreur dans l'interface d'administration.
            add_settings_error('generation-html-messages', 'folder_error', 'Erreur : Impossible de créer le dossier de destination.');
            return;
        }
    } else {
        // Si le dossier existe déjà, le vide pour éviter les fichiers obsolètes.
        vider_dossier($dossier);
    }

    // Crée les sous-dossiers nécessaires pour organiser les ressources CSS, JS, et images.
    foreach (['css', 'js', 'img'] as $subdir) {
        $subdir_path = $dossier . $subdir . '/';
        if (!file_exists($subdir_path)) {
            mkdir($subdir_path, 0755, true);
        }
    }

    // Génère la page d'accueil en récupérant son HTML et en le sauvegardant en tant qu'index.html.
    $homepage_url = home_url();
    $homepage_html = get_html_from_url($homepage_url, $dossier);
    if ($homepage_html === false) {
        // Si la récupération échoue, ajoute un message d'erreur pour la page d'accueil.
        add_settings_error('generation-html-messages', 'homepage_error', 'Erreur : Impossible de récupérer la page d’accueil.');
        return;
    }
    // Sauvegarde le HTML de la page d'accueil dans le fichier index.html dans le dossier de destination.
    file_put_contents($dossier . 'index.html', $homepage_html);
    $pages_generees[] = $homepage_url; // Ajoute l'URL de la page d'accueil au tableau des pages générées.

    // Prépare une requête pour récupérer toutes les pages et articles publiés, sauf la page d'accueil.
    $args = array(
        'post_type' => array('post', 'page'), // Types de contenu à récupérer.
        'posts_per_page' => -1, // Récupère tous les articles et pages.
        'post_status' => 'publish', // Limite aux contenus publiés.
        'post__not_in' => array(get_option('page_on_front')) // Exclut la page d'accueil.
    );

    $query = new WP_Query($args);
    if ($query->have_posts()) {
        // Parcourt chaque page et article récupéré.
        while ($query->have_posts()) {
            $query->the_post();
            $url = get_permalink(); // Récupère l'URL de la page ou de l'article.
            $output = get_html_from_url($url, $dossier); // Récupère le HTML et le traite pour les ressources externes.
            if ($output === false) {
                // Si la récupération échoue, ajoute un message d'erreur pour cette page spécifique.
                add_settings_error('generation-html-messages', 'post_error', 'Erreur : Impossible de récupérer la page ' . esc_url($url));
                continue;
            }
            $slug = sanitize_title(get_the_title()); // Génère un nom de fichier unique en utilisant le titre.
            // Si le slug est vide, utilise un index basé sur l'ID de la page ou article.
            $nom_fichier = empty($slug) ? 'index-' . get_the_ID() : $slug;
            // Sauvegarde le HTML de la page ou de l'article dans un fichier HTML distinct.
            file_put_contents($dossier . $nom_fichier . '.html', $output);
            $pages_generees[] = $url; // Ajoute l'URL de la page au tableau des pages générées.
        }
        wp_reset_postdata(); // Réinitialise les données de post pour éviter les conflits avec d'autres requêtes.
    }

    // Sauvegarde la liste des pages générées dans les options WordPress pour affichage ou consultation ultérieure.
    update_option('dernieres_pages_generees', $pages_generees);
    wp_cache_flush(); // Vide le cache de WordPress pour s'assurer que les nouvelles pages générées sont servies immédiatement.
    // Ajoute un message de succès dans l'interface d'administration.
    add_settings_error('generation-html-messages', 'success', 'La génération de pages HTML a réussi.', 'updated');
}

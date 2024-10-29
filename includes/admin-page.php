<?php

// Ajoute une nouvelle entrée de menu dans le panneau d'administration de WordPress.
function ajouter_menu_admin_html() {
    // Crée une page de menu dans la section d'administration pour accéder à la génération de pages HTML.
    add_menu_page(
        'Génération HTML',               // Titre de la page.
        'Génération HTML',               // Libellé du menu.
        'manage_options',                // Capacité requise pour voir le menu (admin uniquement).
        'generation-html',               // Slug unique de la page.
        'page_generation_html'           // Fonction de rappel pour afficher le contenu de la page.
    );
}

// Affiche la page d'administration pour générer les pages HTML statiques.
function page_generation_html() {
    ?>
    <div class="wrap">
        <h1>Génération de Pages HTML</h1>
        <!-- Formulaire pour lancer la génération HTML -->
        <form method="post" action="">
            <?php 
            // Ajoute un champ nonce pour la sécurité, protégeant contre les attaques CSRF.
            wp_nonce_field('generation_html_action', 'generation_html_nonce'); 
            ?>
            <!-- Bouton de soumission pour déclencher la génération -->
            <input type="submit" name="generate_html" value="Générer HTML" class="button button-primary">
        </form>
        
        <?php
        // Récupère la liste des pages générées précédemment (stockées dans les options WordPress).
        $pages_generees = get_option('dernieres_pages_generees');
        if (!empty($pages_generees)) {
            // Affiche la liste des pages générées si elle n'est pas vide.
            echo '<h2>Pages Générées</h2>';
            echo '<ul>';
            foreach ($pages_generees as $page) {
                // Affiche chaque URL générée en la sécurisant avec esc_url pour éviter les injections XSS.
                echo '<li>' . esc_url($page) . '</li>';
            }
            echo '</ul>';
        }
        ?>
    </div>
    <?php
    // Vérifie si le formulaire a été soumis et valide le nonce pour la sécurité.
    if (isset($_POST['generate_html']) && check_admin_referer('generation_html_action', 'generation_html_nonce')) {
        // Appelle la fonction pour générer les pages HTML statiques.
        parcourir_et_generer_html(); 
    }
}

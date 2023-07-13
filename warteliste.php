<?php
/**
 * Plugin Name:       Warteliste
 * Plugin URI:        https://webae.de
 * Description:       Dieses Plugin enthält ein Formular, wo sich Personen in eine Warteliste eintragen können. Außerdem können durch Hinzufügen durch Beschwerden, Kriterien zum Ankreuzen angegeben werden. Der Administrator der Website erhält eine E-Mail, sobald sich jemand in die Warteliste eingetragen hat. Die komplette Liste ist im Dashboard ersichtlich und kann als CSV exportiert werden. Außerdem kann das Formular flexibel mittels Shortcode platziert werden.
 * Version:           1.0.0
 * Author:            Daniel Carvalho
 * Author URI:        https://webae.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       warteliste
 */



// Funktion zum Erstellen des benutzerdefinierten Beitragstyps "Warteliste"
function erstelle_warteliste_post_type()
{
    $labels = array(
        'name'               => 'Warteliste',
        'singular_name'      => 'Warteliste',
        'menu_name'          => 'Warteliste',
        'name_admin_bar'     => 'Warteliste',
        'add_new'            => 'Neue Person hinzufügen',
        'add_new_item'       => 'Neue Person zur Warteliste hinzufügen',
        'new_item'           => 'Neue Person',
        'edit_item'          => 'Person bearbeiten',
        'view_item'          => 'Person anzeigen',
        'all_items'          => 'Alle Personen',
        'search_items'       => 'Personen durchsuchen',
        'not_found'          => 'Keine Personen gefunden',
        'not_found_in_trash' => 'Keine Personen im Papierkorb gefunden'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'menu_icon'           => 'dashicons-businessman',
        'supports'            => array('title', 'editor'),
        'show_in_rest'        => true,
    );

    register_post_type('warteliste', $args);
}
add_action('init', 'erstelle_warteliste_post_type');

// Funktion zum Erstellen der Taxonomie "Beschwerden"
function erstelle_beschwerden_taxonomie()
{
    $labels = array(
        'name'                       => 'Beschwerden',
        'singular_name'              => 'Beschwerde',
        'search_items'               => 'Beschwerden durchsuchen',
        'popular_items'              => 'Beliebte Beschwerden',
        'all_items'                  => 'Alle Beschwerden',
        'edit_item'                  => 'Beschwerde bearbeiten',
        'update_item'                => 'Beschwerde aktualisieren',
        'add_new_item'               => 'Neue Beschwerde hinzufügen',
        'new_item_name'              => 'Name der neuen Beschwerde',
        'separate_items_with_commas' => 'Beschwerden mit Kommas trennen',
        'add_or_remove_items'        => 'Beschwerden hinzufügen oder entfernen',
        'choose_from_most_used'      => 'Aus den am häufigsten verwendeten Beschwerden auswählen',
        'not_found'                  => 'Keine Beschwerden gefunden',
        'menu_name'                  => 'Beschwerden',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'beschwerden'),
    );

    register_taxonomy('beschwerden', 'warteliste', $args);
}
add_action('init', 'erstelle_beschwerden_taxonomie');

// Funktion zum Hinzufügen der Metaboxen
function warteliste_metaboxen()
{
    add_meta_box('warteliste_vorname', 'Vorname', 'warteliste_vorname_feld', 'warteliste', 'normal', 'default');
    add_meta_box('warteliste_nachname', 'Nachname', 'warteliste_nachname_feld', 'warteliste', 'normal', 'default');
    add_meta_box('warteliste_geburtsdatum', 'Geburtsdatum', 'warteliste_geburtsdatum_feld', 'warteliste', 'normal', 'default');
    add_meta_box('warteliste_telefonnummer', 'Telefonnummer', 'warteliste_telefonnummer_feld', 'warteliste', 'normal', 'default');
    add_meta_box('warteliste_email', 'E-Mail', 'warteliste_email_feld', 'warteliste', 'normal', 'default');
}
add_action('add_meta_boxes', 'warteliste_metaboxen');

// Vorname Metabox Feld
function warteliste_vorname_feld($post)
{
    $vorname = get_post_meta($post->ID, 'warteliste_vorname', true);
    echo '<input type="text" name="warteliste_vorname" value="' . esc_attr($vorname) . '" style="width: 100%;">';
}

// Nachname Metabox Feld
function warteliste_nachname_feld($post)
{
    $nachname = get_post_meta($post->ID, 'warteliste_nachname', true);
    echo '<input type="text" name="warteliste_nachname" value="' . esc_attr($nachname) . '" style="width: 100%;">';
}

// Geburtsdatum Metabox Feld
function warteliste_geburtsdatum_feld($post)
{
    $geburtsdatum = get_post_meta($post->ID, 'warteliste_geburtsdatum', true);
    echo '<input type="date" name="warteliste_geburtsdatum" value="' . esc_attr($geburtsdatum) . '" style="width: 100%;" placeholder="d.m.Y" pattern="[0-9]{2}.[0-9]{2}.[0-9]{4}">';
}

// Telefonnummer Metabox Feld
function warteliste_telefonnummer_feld($post)
{
    $telefonnummer = get_post_meta($post->ID, 'warteliste_telefonnummer', true);
    echo '<input type="text" name="warteliste_telefonnummer" value="' . esc_attr($telefonnummer) . '" style="width: 100%;">';
}

// E-Mail Metabox Feld
function warteliste_email_feld($post)
{
    $email = get_post_meta($post->ID, 'warteliste_email', true);
    echo '<input type="email" name="warteliste_email" value="' . esc_attr($email) . '" style="width: 100%;">';
}

// Funktion zum Speichern der Metabox-Daten
function speichern_warteliste_metabox($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if (isset($_POST['warteliste_vorname'])) {
        update_post_meta($post_id, 'warteliste_vorname', sanitize_text_field($_POST['warteliste_vorname']));
    }

    if (isset($_POST['warteliste_geburtsdatum'])) {
        update_post_meta($post_id, 'warteliste_geburtsdatum', sanitize_text_field($_POST['warteliste_geburtsdatum']));
    }

    if (isset($_POST['warteliste_telefonnummer'])) {
        update_post_meta($post_id, 'warteliste_telefonnummer', sanitize_text_field($_POST['warteliste_telefonnummer']));
    }

    if (isset($_POST['warteliste_email'])) {
        update_post_meta($post_id, 'warteliste_email', sanitize_email($_POST['warteliste_email']));
    }
}
add_action('save_post', 'speichern_warteliste_metabox');

	session_start();
// Erstelle eine benutzerdefinierte Shortcode-Funktion für die Eingabemaske
function warteliste_eingabeformular_shortcode() {

    ob_start();
   
   ?>

    <form method="post" action="" id="warteliste_form">
        <div class="mb-3">
            <label for="vorname" class="form-label">Vorname:</label>
            <input type="text" name="vorname" id="vorname" class="form-control">
        </div>

        <div class="mb-3">
            <label for="nachname" class="form-label">Nachname:</label>
            <input type="text" name="nachname" id="nachname" class="form-control">
        </div>

        <div class="mb-3">
            <label for="geburtsdatum" class="form-label">Geburtsdatum:</label>
            <input type="date" name="geburtsdatum" id="geburtsdatum" class="form-control">
        </div>

        <div class="mb-3">
            <label for="telefonnummer" class="form-label">Telefonnummer:</label>
            <input type="text" name="telefonnummer" id="telefonnummer" class="form-control">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-Mail:</label>
            <input type="email" name="email" id="email" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Beschwerden:</label>
            <?php
            $beschwerden_terms = get_terms(array(
                'taxonomy' => 'beschwerden',
                'hide_empty' => false,
            ));
            foreach ($beschwerden_terms as $beschwerde) {
                ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="beschwerden[]" id="<?php echo $beschwerde->slug; ?>" value="<?php echo $beschwerde->term_id; ?>">
                    <label class="form-check-label" for="<?php echo $beschwerde->slug; ?>">
                        <?php echo $beschwerde->name; ?>
                    </label>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="datenschutz" id="datenschutz">
                <label class="form-check-label" for="datenschutz">
                    Ich habe die Datenschutzbestimmungen gelesen und stimme ihnen zu.
                </label>
            </div>
        </div>

        <div class="mb-3">
            <label for="captcha" class="form-label">Bitte gib den angezeigten Code ein:</label>
            <input type="text" name="captcha" id="captcha" class="form-control">
            <img class="captcha" src="<?php echo plugins_url('captcha2.php', __FILE__); ?>" alt="Captcha">
        </div>

        <div id="validation-messages"></div>

        <button type="submit" class="button">Senden</button>
    </form>

    <script>
        document.getElementById("warteliste_form").addEventListener("submit", function(event) {
            event.preventDefault(); // Verhindere den Standard-Formularsubmit
            var vornameInput = document.getElementById("vorname");
            var nachnameInput = document.getElementById("nachname");
            var geburtsdatumInput = document.getElementById("geburtsdatum");
            var telefonnummerInput = document.getElementById("telefonnummer");
            var emailInput = document.getElementById("email");
            var datenschutzCheckbox = document.getElementById("datenschutz");
            var captchaInput = document.getElementById("captcha");
            var captchaImage = document.querySelector(".captcha");

            // Überprüfe, ob alle Felder ausgefüllt sind
            if (
                vornameInput.value.trim() === "" ||
                nachnameInput.value.trim() === "" ||
                geburtsdatumInput.value.trim() === "" ||
                telefonnummerInput.value.trim() === "" ||
                emailInput.value.trim() === "" ||
                captchaInput.value.trim() === ""
            ) {
                document.getElementById("validation-messages").innerHTML =
                    '<div class="alert alert-danger">Bitte füllen Sie alle erforderlichen Felder aus.</div>';
                return;
            }

            // Überprüfe das E-Mail-Format
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailInput.value)) {
                document.getElementById("validation-messages").innerHTML =
                    '<div class="alert alert-danger">Bitte geben Sie eine gültige E-Mail-Adresse ein.</div>';
                return;
            }

            // Überprüfe, ob die Datenschutzbestimmungen akzeptiert wurden
            if (!datenschutzCheckbox.checked) {
                document.getElementById("validation-messages").innerHTML =
                    '<div class="alert alert-danger">Bitte akzeptieren Sie die Datenschutzbestimmungen.</div>';
                return;
            }

            var captchaInput = document.getElementById("captcha");

            // Überprüfe das Captcha
            var captchaValue = captchaInput.value.toLowerCase();
            var captchaText = "<?php echo $_SESSION['captcha']; ?>";
            if (captchaValue !== captchaText) {
                document.getElementById("validation-messages").innerHTML =
                    '<div class="alert alert-danger">Das eingegebene Captcha ist ungültig.</div>';
                return;
            }

            // Erstelle ein FormData-Objekt und füge die Formulardaten hinzu
            var formData = new FormData(this);

            // Führe eine AJAX-Anfrage zum Speichern der Formulardaten durch
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    // Erfolgreiche AJAX-Anfrage
                    document.getElementById("validation-messages").innerHTML =
                        '<div class="alert alert-success">Die Anmeldung wurde erfolgreich gesendet.</div>';
                    document.getElementById("warteliste_form").reset();
                } else if (xhr.readyState === XMLHttpRequest.DONE && xhr.status !== 200) {
                    // Fehlerhafte AJAX-Anfrage
                    document.getElementById("validation-messages").innerHTML =
                        '<div class="alert alert-danger">Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.</div>';
                }
            };
            xhr.send(formData);
        });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('warteliste_formular', 'warteliste_eingabeformular_shortcode');

// Verarbeite die Formulardaten
function warteliste_formular_verarbeiten()
{
    if (isset($_POST['vorname']) && isset($_POST['nachname']) && isset($_POST['geburtsdatum']) && isset($_POST['telefonnummer']) && isset($_POST['email'])) {
        $vorname = sanitize_text_field($_POST['vorname']);
        $nachname = sanitize_text_field($_POST['nachname']);
        $geburtsdatum = sanitize_text_field($_POST['geburtsdatum']);
        $telefonnummer = sanitize_text_field($_POST['telefonnummer']);
        $email = sanitize_email($_POST['email']);
        $beschwerden = isset($_POST['beschwerden']) ? $_POST['beschwerden'] : array();

        // Erstelle einen neuen Wartelisteneintrag
        $post_args = array(
            'post_title'    => $vorname . ' ' . $nachname,
            'post_type'     => 'warteliste',
            'post_status'   => 'publish'
        );

        $post_id = wp_insert_post($post_args);

        if (!is_wp_error($post_id)) {
            // Speichere die Metadaten für den Wartelisteneintrag
            update_post_meta($post_id, 'warteliste_vorname', $vorname);
            update_post_meta($post_id, 'warteliste_nachname', $nachname);
            update_post_meta($post_id, 'warteliste_geburtsdatum', $geburtsdatum);
            update_post_meta($post_id, 'warteliste_telefonnummer', $telefonnummer);
            update_post_meta($post_id, 'warteliste_email', $email);

            // Speichere die ausgewählten Beschwerden als Terms
            wp_set_post_terms($post_id, $beschwerden, 'beschwerden');

            // Sende eine E-Mail an den Administrator
            $admin_email = get_option('admin_email');
            $subject = 'Neuer Eintrag in die Warteliste';
            $message = "Es wurde ein neuer Eintrag in die Warteliste erstellt.\n\n";
            $message .= "Vorname: $vorname\n";
            $message .= "Nachname: $nachname\n";
            $message .= "Geburtsdatum: $geburtsdatum\n";
            $message .= "Telefonnummer: $telefonnummer\n";
            $message .= "E-Mail: $email\n";
            $message .= "Beschwerden: " . implode(', ', $beschwerden) . "\n";

            wp_mail($admin_email, $subject, $message);

            echo 'Vielen Dank! Deine Anmeldung wurde erfolgreich gesendet.';
        } else {
            echo 'Fehler beim Speichern des Wartelisteneintrags.';
        }
    }
}
add_action('init', 'warteliste_formular_verarbeiten');

// Funktion zum Erstellen der Dashboard-Seite im benutzerdefinierten Beitragstyp "Warteliste"
function warteliste_plugin_dashboard()
{
    // Überprüfe, ob der Benutzer die erforderlichen Berechtigungen hat
    if (!current_user_can('manage_options')) {
        return;
    }

    // Rufe alle Wartelisteneinträge ab
    $wartelisten = get_posts(array(
        'post_type' => 'warteliste',
        'posts_per_page' => -1,
    ));

    // Starte den Output-Buffer
    ob_start();
    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">Dashboard</h1>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vorname</th>
                    <th>Nachname</th>
                    <th>Geburtsdatum</th>
                    <th>Telefonnummer</th>
                    <th>E-Mail</th>
                    <th>Beschwerden</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wartelisten as $warteliste) : ?>
                    <?php
                    $vorname = get_post_meta($warteliste->ID, 'warteliste_vorname', true);
                    $nachname = get_post_meta($warteliste->ID, 'warteliste_nachname', true);
                    $geburtsdatum = get_post_meta($warteliste->ID, 'warteliste_geburtsdatum', true);
                    $telefonnummer = get_post_meta($warteliste->ID, 'warteliste_telefonnummer', true);
                    $email = get_post_meta($warteliste->ID, 'warteliste_email', true);
                    $beschwerden = get_the_terms($warteliste->ID, 'beschwerden');
                    $beschwerden_names = array();

                    if ($beschwerden && !is_wp_error($beschwerden)) {
                        foreach ($beschwerden as $beschwerde) {
                            $beschwerden_names[] = $beschwerde->name;
                        }
                    }
                    ?>
                    <tr>
                        <td><?php echo $warteliste->ID; ?></td>
                        <td><?php echo $vorname; ?></td>
                        <td><?php echo $nachname; ?></td>
                        <td><?php echo date('d.m.Y', strtotime($geburtsdatum)); ?></td>
                        <td><?php echo $telefonnummer; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><?php echo implode(', ', $beschwerden_names); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <br />

        <form method="post" action="">
            <input type="hidden" name="warteliste_exportieren" value="1">
            <button type="submit" class="button">Warteliste exportieren</button>
        </form>

        <br />

        <h2>Shortcode</h2>
        <p>Kopiere den folgenden Shortcode und füge ihn in den Inhalt einer Seite oder eines Beitrags ein:</p>
        <pre>[warteliste_formular]</pre>
    </div>

    <?php
    // Gib den Inhalt des Output-Buffers aus
    echo ob_get_clean();
}

// Funktion zum Exportieren der Warteliste als CSV
function warteliste_exportieren()
{
    // Überprüfe, ob der Export-Button geklickt wurde
    if (isset($_POST['warteliste_exportieren'])) {
        // Rufe alle Wartelisteneinträge ab
        $wartelisten = get_posts(array(
            'post_type' => 'warteliste',
            'posts_per_page' => -1,
        ));

        // Erzeuge den CSV-Header
        $csv = "ID,Vorname,Nachname,Geburtsdatum,Telefonnummer,E-Mail,Beschwerden\n";

        // Füge die Daten der Wartelisteneinträge zur CSV hinzu
        foreach ($wartelisten as $warteliste) {
            $vorname = get_post_meta($warteliste->ID, 'warteliste_vorname', true);
            $nachname = get_post_meta($warteliste->ID, 'warteliste_nachname', true);
            $geburtsdatum = get_post_meta($warteliste->ID, 'warteliste_geburtsdatum', true);
            $telefonnummer = get_post_meta($warteliste->ID, 'warteliste_telefonnummer', true);
            $email = get_post_meta($warteliste->ID, 'warteliste_email', true);
            $beschwerden = get_the_terms($warteliste->ID, 'beschwerden');
            $beschwerden_names = array();

            if ($beschwerden && !is_wp_error($beschwerden)) {
                foreach ($beschwerden as $beschwerde) {
                    $beschwerden_names[] = $beschwerde->name;
                }
            }

            $csv .= "$warteliste->ID,$vorname,$nachname,$geburtsdatum,$telefonnummer,$email," . implode(', ', $beschwerden_names) . "\n";
        }

        // Sende die CSV zum Download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=warteliste.csv');
        echo $csv;
        exit;
    }
}

add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=warteliste',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'warteliste-plugin-dashboard',
        'warteliste_plugin_dashboard'
    );
    add_action('admin_init', 'warteliste_exportieren');
});
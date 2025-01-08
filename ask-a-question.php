<?php

/**
 * Plugin Name: Ask a Question
 * Description: Plugin um eine Frage an die Leser zu stellen. Wird mittels Shortcode eingebunden
 * Version: 1.1.1
 * Author: Michael Homeister
 * Author URI: https://budigital.de/
 * Update URI:    https://budigital.de
 */


// Theme Updater
require 'includes/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
	'https://budigital.de/updates/?action=get_metadata&slug=ask-a-question',
	__FILE__,
	'monday-api'
);


function sc_question($atts, $content = null){
    $atts = shortcode_atts( [
        'question' => "Was sagst du zu Glööcklers Trennung?",
        'answers' => "Die beiden raufen sich wieder zusammen. Ganz sicher.;Neee das Ding is durch, was soll man da noch zu sagen?;Wer ist Harald Glööckler?",
        'cookie' => 1,
        'as_percent' => 0,
        'show_results' => 0,
        'bar_color' => "",
    ], $atts);

    extract($atts);

	$question_id = "aaq_" . md5($question);

    $answers = explode(";", $answers);

    wp_enqueue_style( 'ask-a-question', plugins_url( "assets/style.css", __FILE__ ) );
    wp_enqueue_script( 'ask-a-question', plugins_url( "assets/script.js", __FILE__), ['jquery'] );

    ob_start();
    ?>

    <div class="aaq-wrapper" id="<?php echo $question_id ?>">
        <style>
            <?php if($atts["bar_color"] != ""){ ?>
                #<?php echo $question_id ?> .aaq-progress{
                    background-color: <?php echo $atts["bar_color"] ?>;
                }
            <?php } ?>
        </style>

        <p><b><?php echo $question ?></b></p>
        <?php $i = 0; foreach($answers as $answer){ ?>
            <label><input type="radio" data-answer="<?php echo $i ?>" data-answer-text="<?php echo esc_html( $answer ) ?>" name="<?php echo $question_id ?>" <?php echo (isset($_COOKIE[$question_id]) && $_COOKIE[$question_id] == $i ? "checked" : "") ?> /> <?php echo $answer ?></label>
            <div class="aaq-progress-bar" style="<?php echo ($atts["show_results"] == 1 ? '' : 'display: none') ?>">
                <div id="<?php echo $question_id ?>-progressbar-<?php echo $i ?>" class="aaq-progress"></div>
                <span id="<?php echo $question_id ?>-progresspercentage-<?php echo $i ?>"></span>
            </div>
        <?php $i++; } ?>

		<?php if(current_user_can('administrator')){ ?>
			<button class="reset-aaq-<?php echo $question_id ?>" data-question="<?php echo $question_id ?>">Umfrage zurücksetzen</button>
			<script>
				jQuery(".reset-aaq-<?php echo $question_id ?>").click(function(){
					jQuery.ajax({
				        type: "POST",
				        url: wp_ajax_url,
				        data: {
				            question: jQuery(this).data("question"),
				            action: 'question_reset',
				        }, // serializes the form's elements.
				        success: function(data){

							alert("Erfolgreich zurückgesetzt")

				        }
				    });
				})

			</script>
		<?php } ?>

        <script>
            var wp_ajax_url = '<?php echo admin_url( 'admin-ajax.php' ) ?>'

            jQuery(document).ready(function(){
                jQuery("#<?php echo $question_id ?> input").change(function(){
                    var n_answer = jQuery(this).data('answer')
                    var answer = jQuery(this).data('answer-text')

                    aaqSendAnswer('<?php echo $question_id; ?>', n_answer, <?php echo count($answers) ?>, <?php echo $atts["cookie"] ?>, <?php echo $atts['as_percent']; ?>)

                    jQuery("#<?php echo $question_id ?> .aaq-progress-bar").slideDown()

					if( typeof _paq !== 'undefined' ){
						_paq.push(['trackEvent', 'Ask-A-Question', '<?php echo $question ?>', answer]);
					}
                })

                aaqRefreshResults('<?php echo $question_id; ?>', <?php echo $atts['as_percent']; ?>)

                <?php if( $atts["cookie"] == 1 && isset( $_COOKIE[$question_id] ) ){ ?>
                    aaqDisableInputs('<?php echo $question_id; ?>')
                    jQuery("#<?php echo $question_id ?> .aaq-progress-bar").slideDown()
                <?php } ?>

				<?php if(current_user_can('administrator')){ ?>
					jQuery("#<?php echo $question_id ?> .aaq-progress-bar").slideDown()
				<?php } ?>
            })

            // setInterval(function(){
            //     aaqRefreshResults('<?php echo $question_id; ?>')
            // }, 40000)

        </script>
    </div>

    <?php
	return ob_get_clean();
}
add_shortcode("question", 'sc_question');


/*
	WPBakery den Shortcode beibringen und Datenfelder Beschreiben für das Konfig-Feld
*/
add_action( 'vc_before_init', function(){
    vc_map( array(
        "name" => "Umfrage",
        "description" => "Ask-A-Question Umfrage",
        "base" => "question",
        "class" => "",
        "icon" => plugins_url("assets/vc-icon.svg", __FILE__), // Simply pass url to your icon here
        "category" => "Umfrage",
        "content_element" => true,
        // "is_container" => true,
        "params" => array(
            array(
                "type" => "textfield",
                "class" => "",
                "heading" => "Frage",
                "param_name" => 'question',
                "value" => '',
                "description" => "",
                "admin_label" => true,
            ),
            array(
                "type" => "textfield",
                "class" => "",
                "heading" => "Antworten",
                "param_name" => 'answers',
                "value" => '',
                "description" => "Antwortmöglichkeiten mit Semikolon trennen",
            ),
            array(
                "type" => "dropdown",
                "class" => "",
                "heading" => "Cookie",
                "param_name" => 'cookie',
                "value" => ["Ja" => 1, "Nein" => 0],
				"std" => "1",
                "description" => "Wenn aktiviert kann ein User nur 1 mal abstimmen.",
            ),
            array(
                "type" => "dropdown",
                "class" => "",
                "heading" => "Ergebniss vor Abstimmung anzeigen?",
                "param_name" => 'show_results',
                "value" => ["Nein" => 0, "Ja" => 1],
				"std" => "0",
                "description" => "Wenn aktiviert kann ein User nur 1 mal abstimmen.",
            ),
            array(
                "type" => "dropdown",
                "class" => "",
                "heading" => "Ergebniss in Prozent anzeigen?",
                "param_name" => 'as_percent',
                "value" => ["Nein" => 0, "Ja" => 1],
				"std" => "0",
                "description" => "Wenn aktiviert werden die Ergebnisse in Prozent angezeigt.",
            ),

        )
    ) );
} );



add_action( 'wp_ajax_question_answers', 'ajax_question_answers');
add_action( 'wp_ajax_nopriv_question_answers', 'ajax_question_answers');

function ajax_question_answers(){

    if( get_option($_REQUEST["question"]) == false ){
        echo json_encode( array_fill(0, 100, 0));
        wp_die();
    }

    echo json_encode( json_decode( get_option($_REQUEST["question"]) ) );
    wp_die();

}



add_action( 'wp_ajax_question_set_answer', 'ajax_question_set_answer');
add_action( 'wp_ajax_nopriv_question_set_answer', 'ajax_question_set_answer');

function ajax_question_set_answer(){
    $question = $_REQUEST["question"];
    $val = $_REQUEST["value"];
    $count = $_REQUEST["count_answers"];
    $cookie = $_REQUEST["cookie"];

    if( $cookie == 1 ){
        $name = $question;
        $wert = $val;
        $t = time()+60*60*24*30;
        setcookie($name, $wert, $t, '/');
    }

    if( get_option($_REQUEST["question"]) == false ){

        $data = array_fill(0, $count, 0);
        $data[$val]++;
        update_option($_REQUEST["question"], json_encode($data) );

    }else{

        $data = json_decode( get_option($_REQUEST["question"]) );
        $data[$val]++;
        update_option($_REQUEST["question"], json_encode($data) );

    }

    echo "1";

    wp_die();

}


add_action( 'wp_ajax_question_reset', 'ajax_question_reset');

function ajax_question_reset(){
    $question = $_REQUEST["question"];

	update_option($question, "" );

	wp_die();
}


?>

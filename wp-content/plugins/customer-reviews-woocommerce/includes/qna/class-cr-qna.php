<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CR_Qna' ) ) :

class CR_Qna {

  private $per_page = 5;
  private $recaptcha = '';
  private $recaptcha_score = 0.5;

  public function __construct() {
    if( 'yes' === get_option( 'ivole_questions_answers', 'no' ) ) {
      if( 'yes' === get_option( 'ivole_qna_enable_captcha', 'no' ) ) {
        add_action( 'wp_enqueue_scripts', array( $this, 'recaptcha_script' ) );
      }
      add_filter( 'woocommerce_product_tabs', array( $this, 'create_qna_tab' ) );
      add_action( 'wp_ajax_cr_new_qna', array( $this, 'new_qna' ) );
      add_action( 'wp_ajax_nopriv_cr_new_qna', array( $this, 'new_qna' ) );
    }
    add_filter( 'preprocess_comment', array( $this, 'update_answer_type' ) );
    add_action( 'pre_get_comments', array( $this, 'filter_out_qna' ) );
  }

  public function create_qna_tab( $tabs ) {
    $tab_title = __( 'Q & A', 'customer-reviews-woocommerce' );
    $qna_count = $this->get_qna_count();
    if( $qna_count ) {
      $tab_title = sprintf( __( 'Q & A (%d)', 'customer-reviews-woocommerce' ), $qna_count );
    }
  	$tabs['cr_qna'] = array(
  		'title' 	=> apply_filters( 'cr_qna_tab_title', $tab_title ),
  		'priority' 	=> apply_filters( 'cr_qna_tab_priority', 40 ),
  		'callback' 	=> array( $this, 'display_qna_tab' )
  	);
  	return $tabs;
  }

  public function display_qna_tab() {
    global $product;
    if( isset( $product ) ) {
      $cr_product_id  = $product->get_id();
      $qna = $this->get_qna( $cr_product_id, 0 );
      $date_format = get_option( 'date_format', 'F j, Y' );
      $template = wc_locate_template(
  			'qna-tab.php',
  			'customer-reviews-woocommerce',
  			__DIR__ . '/../../templates/'
  		);
      $cr_recaptcha = $this->recaptcha;
      include( $template );
    }
  }

  public function get_qna( $product_id, $page ) {
    $return_qna = array();
    // fetch questions
    $args = array(
      'post_id' => $product_id,
      'status' => 'approve',
      'type' => 'cr_qna',
      'parent' => 0,
      'number' => $this->per_page,
      'offset' => $page * $this->per_page
    );
    $qna = get_comments( $args );
    // fetch answers
    foreach ( $qna as $q ) {
      $ans = $q->get_children( array(
        'type' => 'cr_qna',
        'format' => 'tree',
        'status' => 'approve',
        'hierarchical' => false
      ) );
      $return_ans = array();
      foreach ($ans as $a) {
        $return_ans[] = array(
          'id' => $a->comment_ID,
          'answer' => sanitize_textarea_field( $a->comment_content ),
          'author' => sanitize_text_field( $a->comment_author ),
          'date' => $a->comment_date
        );
      }
      $return_qna[] = array(
        'id' => $q->comment_ID,
        'question' => sanitize_textarea_field( $q->comment_content ),
        'author' => sanitize_text_field( $q->comment_author ),
        'date' => $q->comment_date,
        'answers' => $return_ans
      );
    }
    return $return_qna;
  }

  public function new_qna() {
    $return = array(
      'code' => 2,
      'description' => __( 'Data validation error.', 'customer-reviews-woocommerce' )
    );
    if( isset( $_POST['productID'] ) ) {
      $product_id = intval( $_POST['productID'] );
      if( 0 < $product_id ) {
        $question_id = 0;
        $nonce = 'cr_qna_';
        if( isset( $_POST['questionID'] ) && 0 < intval( $_POST['questionID'] ) ) {
          $question_id = intval( $_POST['questionID'] );
          $nonce = 'cr_qna_a_';
        }
        if( check_ajax_referer( $nonce . $_POST['productID'], 'security', false ) ) {
          $captcha_correct = true;
          if( 'yes' === get_option( 'ivole_qna_enable_captcha', 'no' ) ) {
            $secret_key = get_option( 'ivole_qna_captcha_secret_key', '' );
            if( isset( $_POST['cptcha'] ) && 0 < strlen( $_POST['cptcha'] ) ) {
              $captch_response = json_decode( wp_remote_retrieve_body( wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', array( 'body' => array( 'secret' => $secret_key, 'response' => $_POST['cptcha'], 'remoteip' => $_SERVER['REMOTE_ADDR'] ) ) ) ), true );
              if( $captch_response['success'] ) {
                if( $captch_response['score'] && $this->recaptcha_score > $captch_response['score'] ) {
                  $captcha_correct = false;
                  $return['description'] = __( 'reCAPTCHA score is below the threshold.', 'customer-reviews-woocommerce' );
                }
              } else {
      					$captcha_correct = false;
                $return['code'] = 3;
                $return['description'] =  sprintf( __( 'reCAPTCHA validation error (%s).', 'customer-reviews-woocommerce' ), implode(', ', $captch_response["error-codes"] ) );
      				}
            } else {
              $captcha_correct = false;
              $return['code'] = 4;
              $return['description'] = __( 'reCAPTCHA response is missing.', 'customer-reviews-woocommerce' );
            }
          }
          if( $captcha_correct ) {
            $data_is_available = true;
            $question = '';
            $name = '';
            $email = '';
            if( isset( $_POST['text'] ) ) {
              $question = sanitize_textarea_field( trim( $_POST['text'] ) );
            }
            if( isset( $_POST['name'] ) ) {
              $name = sanitize_text_field( trim( $_POST['name'] ) );
            }
            if( isset( $_POST['email'] ) ) {
              $email = sanitize_email( trim( $_POST['email'] ) );
            }
            if( $question && $name && is_email( $email ) ) {
              $user = get_user_by( 'email', $email );
              if( $user ) {
                $user = $user->ID;
              } else {
                $user = 0;
              }
              $commentdata = array(
                'comment_author' => $name,
                'comment_author_email' => $email,
                'comment_author_url' => '',
                'comment_content' => $question,
                'comment_type' => 'cr_qna',
                'comment_post_ID' => $product_id,
                'comment_parent' => $question_id,
                'user_id' => $user
              );
              $result = wp_new_comment( $commentdata, true );
              if( 0 < $question_id ) {
                $error_description = __( 'An error when adding the answer.', 'customer-reviews-woocommerce' );
                $success_description = __( 'The answer was successfully added.', 'customer-reviews-woocommerce' );
              } else {
                $error_description = __( 'An error when adding the question.', 'customer-reviews-woocommerce' );
                $success_description = __( 'The question was successfully added.', 'customer-reviews-woocommerce' );
              }
              if( !$result || is_wp_error( $result ) ) {
                if( is_wp_error( $result ) ) {
                  $error_description = $result->get_error_message();
                }
                $return = array(
                  'code' => 1,
                  'description' => $error_description
                );
              } else {
                $return = array(
                  'code' => 0,
                  'description' => $success_description
                );
              }
            }
          }
        }
      }
    }
    wp_send_json( $return );
  }

  public function update_answer_type( $commentdata ) {
    // if a new comment is a reply to a question, then set its type to 'cr_qna'
    if( isset( $commentdata['comment_parent'] ) && 0 < $commentdata['comment_parent'] ) {
      if( 'cr_qna' === get_comment_type( $commentdata['comment_parent'] ) ) {
        $commentdata['comment_type'] = 'cr_qna';
      }
    }
    return $commentdata;
  }

  private function get_qna_count() {
    global $product;
    $count = 0;
    if( isset( $product ) ) {
      $product_id  = $product->get_id();
      // fetch questions
      $args = array(
        'post_id' => $product_id,
        'status' => 'approve',
        'type' => 'cr_qna',
        'parent' => 0,
        'count' => true
      );
      $qna_count = get_comments( $args );
      if( $qna_count ) {
        $count = intval( $qna_count );
      }
    }
    return $count;
  }

  public function recaptcha_script() {
    if( is_product() ) {
      $lang = Ivole_Trust_Badge::get_badge_language();
      $site_key = get_option( 'ivole_qna_captcha_site_key', '' );
      $this->recaptcha = $site_key;
      wp_register_script( 'cr-recaptcha', 'https://www.google.com/recaptcha/api.js?hl=' . $lang . '&render=' . $site_key , array(), null, true );
      wp_enqueue_script( 'cr-recaptcha' );
    }
  }

  public function filter_out_qna( &$query ) {
    if( is_product() ) {
      if( isset( $query->query_vars ) && isset( $query->query_vars['type'] ) && 'cr_qna' !== $query->query_vars['type'] ) {
        if( isset( $query->query_vars['type__not_in'] ) && is_array( $query->query_vars['type__not_in'] ) ) {
          $query->query_vars['type__not_in'][] = 'cr_qna';
        } else {
          $query->query_vars['type__not_in'] = array( 'cr_qna' );
        }
      }
    }
  }

}

endif;

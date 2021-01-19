<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $product;
//check for old WooCommerce versions
if( method_exists( $product, 'get_id' ) ) {
	$cr_product_id  = $product->get_id();
} else {
	$cr_product_id  = $product->id;
}
$nonce = wp_create_nonce( "cr_qna_" . $cr_product_id );
$nonce_ans = wp_create_nonce( "cr_qna_a_" . $cr_product_id );
?>
<div id="cr_qna" class="cr-qna-block">
	<h2><?php _e( 'Q & A', 'customer-reviews-woocommerce' ); ?></h2>
	<div class="cr-qna-search-block">
		<button type="button" class="cr-qna-ask-button"><?php _e( 'Ask a question', 'customer-reviews-woocommerce' ); ?></button>
	</div>
	<div class="cr-qna-list-block">
		<?php
		if( isset( $qna ) && is_array( $qna ) && 0 < count( $qna ) ) :
			foreach ($qna as $q) {
				?>
				<div class="cr-qna-list-q-cont">
					<div class="cr-qna-list-q-q">
						<div class="cr-qna-list-q-q-l">
							<img class="cr-qna-list-q-icon" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/question.svg'; ?>" alt="Question" />
						</div>
						<div class="cr-qna-list-q-q-r">
							<span class="cr-qna-list-question"><?php echo esc_html( $q['question'] ); ?></span>
							<span class="cr-qna-list-q-author"><?php echo sprintf( __( '%s asked on %s', 'customer-reviews-woocommerce' ), '<span class="cr-qna-list-q-author-b">' . esc_html( $q['author'] ) . '</span>', date_i18n( $date_format, strtotime( $q['date'] ) ) ); ?></span>
						</div>
					</div>
					<?php
					if( isset( $q['answers'] ) && is_array( $q['answers'] ) && 0 < count( $q['answers'] ) ) :
					?>
					<div class="cr-qna-list-q-a">
						<div class="cr-qna-list-q-a-l">
							<img class="cr-qna-list-q-icon" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/answer.svg'; ?>" alt="Question" />
						</div>
						<div class="cr-qna-list-q-a-r">
							<?php
							$cr_i = 0;
							$cr_len = count( $q['answers'] );
							foreach ($q['answers'] as $a) {
								if( $cr_i === $cr_len-1 ) {
									$cr_class_qna_list_answer = 'cr-qna-list-answer cr-qna-list-last';
								} else {
									$cr_class_qna_list_answer = 'cr-qna-list-answer';
								}
								?>
								<div class="<?php echo $cr_class_qna_list_answer; ?>">
									<span class="cr-qna-list-answer-s"><?php echo esc_html( $a['answer'] ); ?></span>
									<span class="cr-qna-list-q-author"><?php echo sprintf( __( '%s answered on %s', 'customer-reviews-woocommerce' ), '<span class="cr-qna-list-q-author-b">' . esc_html( $a['author'] ) . '</span>', date_i18n( $date_format, strtotime( $a['date'] ) ) ); ?></span>
								</div>
								<?php
								$cr_i++;
							}
							?>
						</div>
					</div>
					<?php
					endif;
					?>
					<div class="cr-qna-list-q-b">
						<div class="cr-qna-list-q-b-l"></div>
						<div class="cr-qna-list-q-b-r">
							<button type="button" data-question="<?php echo $q['id']; ?>" class="cr-qna-ans-button"><?php _e( 'Answer the question', 'customer-reviews-woocommerce' ); ?></button>
						</div>
					</div>
				</div>
				<?php
			}
		else:
		?>
		<div class="cr-qna-list-empty"><?php _e( 'There are no questions yet', 'customer-reviews-woocommerce' ); ?></div>
		<?php
		endif;
		?>
	</div>
	<div class="cr-qna-new-q-overlay">
		<div class="cr-qna-new-q-form">
			<button class="cr-qna-new-q-form-close"><span class="dashicons dashicons-no"></span></button>
			<div class="cr-qna-new-q-form-input">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Ask a question', 'customer-reviews-woocommerce' ); ?></p>
				<p class="cr-qna-new-q-form-text"><?php _e( 'Your question will be answered by a store representative or other customers.', 'customer-reviews-woocommerce' ); ?></p>
				<textarea name="question" class="cr-qna-new-q-form-q" rows="3" placeholder="<?php _e( 'Start your question with \'What\', \'How\', \'Why\', etc.', 'customer-reviews-woocommerce' ); ?>"></textarea>
				<input type="text" name="name" class="cr-qna-new-q-form-name" placeholder="<?php _e( 'Your name', 'customer-reviews-woocommerce' ); ?>"></input>
				<input type="email" name="email" class="cr-qna-new-q-form-email" placeholder="<?php _e( 'Your email', 'customer-reviews-woocommerce' ); ?>"></input>
				<div class="cr-qna-new-q-form-s">
					<?php
					if( 0 < strlen( $cr_recaptcha ) ) {
						echo '<p>' . sprintf( __( 'This site is protected by reCAPTCHA and the Google %1$sPrivacy Policy%2$s and %3$sTerms of Service%4$s apply.', 'customer-reviews-woocommerce' ), '<a href="https://policies.google.com/privacy" rel="noopener noreferrer nofollow" target="_blank">', '</a>', '<a href="https://policies.google.com/terms" rel="noopener noreferrer nofollow" target="_blank">', '</a>' ) . '</p>';
					}
					?>
					<button type="button" data-nonce="<?php echo $nonce; ?>" data-product="<?php echo $cr_product_id; ?>" data-crcptcha="<?php echo $cr_recaptcha; ?>" class="cr-qna-new-q-form-s-b"><?php _e( 'Submit', 'customer-reviews-woocommerce' ); ?></button>
					<button type="button" class="cr-qna-new-q-form-s-b cr-qna-new-q-form-s-p"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/spinner-dots.svg'; ?>" alt="Loading" /></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-ok">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Thank you for the question!', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/mail.svg'; ?>" alt="Mail" />
				<p class="cr-qna-new-q-form-text"><?php _e( 'Your question has been received and will be answered soon. Please do not submit the same question again.', 'customer-reviews-woocommerce' ); ?></p>
				<div class="cr-qna-new-q-form-s">
					<button type="button" class="cr-qna-new-q-form-s-b"><?php _e( 'OK', 'customer-reviews-woocommerce' ); ?></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-error">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Error', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/warning.svg'; ?>" alt="Warning" height="32px" />
				<p class="cr-qna-new-q-form-text"><?php _e( 'An error occurred when saving your question. Please report it to the website administrator. Additional information:', 'customer-reviews-woocommerce' ); ?></p>
			</div>
		</div>
		<div class="cr-qna-new-q-form cr-qna-new-a-form">
			<button class="cr-qna-new-q-form-close"><span class="dashicons dashicons-no"></span></button>
			<div class="cr-qna-new-q-form-input">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Add an answer', 'customer-reviews-woocommerce' ); ?></p>
				<p class="cr-qna-new-q-form-text"></p>
				<textarea name="question" class="cr-qna-new-q-form-q" rows="3" placeholder="<?php _e( 'Write your answer', 'customer-reviews-woocommerce' ); ?>"></textarea>
				<input type="text" name="name" class="cr-qna-new-q-form-name" placeholder="<?php _e( 'Your name', 'customer-reviews-woocommerce' ); ?>"></input>
				<input type="email" name="email" class="cr-qna-new-q-form-email" placeholder="<?php _e( 'Your email', 'customer-reviews-woocommerce' ); ?>"></input>
				<div class="cr-qna-new-q-form-s">
					<?php
					if( 0 < strlen( $cr_recaptcha ) ) {
						echo '<p>' . sprintf( __( 'This site is protected by reCAPTCHA and the Google %1$sPrivacy Policy%2$s and %3$sTerms of Service%4$s apply.', 'customer-reviews-woocommerce' ), '<a href="https://policies.google.com/privacy" rel="noopener noreferrer nofollow" target="_blank">', '</a>', '<a href="https://policies.google.com/terms" rel="noopener noreferrer nofollow" target="_blank">', '</a>' ) . '</p>';
					}
					?>
					<button type="button" data-nonce="<?php echo $nonce_ans; ?>" data-product="<?php echo $cr_product_id; ?>" data-crcptcha="<?php echo $cr_recaptcha; ?>" class="cr-qna-new-q-form-s-b"><?php _e( 'Submit', 'customer-reviews-woocommerce' ); ?></button>
					<button type="button" class="cr-qna-new-q-form-s-b cr-qna-new-q-form-s-p"><img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/spinner-dots.svg'; ?>" alt="Loading" /></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-ok">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Thank you for the answer!', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/mail.svg'; ?>" alt="Mail" />
				<p class="cr-qna-new-q-form-text"><?php _e( 'Your answer has been received and will be published soon. Please do not submit the same answer again.', 'customer-reviews-woocommerce' ); ?></p>
				<div class="cr-qna-new-q-form-s">
					<button type="button" class="cr-qna-new-q-form-s-b"><?php _e( 'OK', 'customer-reviews-woocommerce' ); ?></button>
				</div>
			</div>
			<div class="cr-qna-new-q-form-error">
				<p class="cr-qna-new-q-form-title"><?php _e( 'Error', 'customer-reviews-woocommerce' ); ?></p>
				<img class="cr-qna-new-q-form-mail" src="<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'img/warning.svg'; ?>" alt="Warning" height="32px" />
				<p class="cr-qna-new-q-form-text"><?php _e( 'An error occurred when saving your question. Please report it to the website administrator. Additional information:', 'customer-reviews-woocommerce' ); ?></p>
			</div>
		</div>
	</div>
</div>

<?php
/**
 * The template for displaying the footer.
 */

global $vh_is_footer; ?>

	<?php
		$vh_is_footer = true;
		
		if ( empty($footer_style) ) {
			$footer_style = '';
		}

		// Check if show social icons
		$show_social_icons = get_option('vh_show_social_icons') ? get_option('vh_show_social_icons') : 'false';
		//$show_rss_icon     = get_option('vh_show_rss_icon') ? get_option('vh_show_rss_icon') : 'false';
		$facebook_url      = get_option('vh_facebook_url');
		$bbh_url       = get_option('vh_bbh_url');
		$bbh_url = "https://www.bbh.co.nz/hd448/Riverstone-Backpackers-in-Turangi-New-Zealand.html";
		$tripadvisor_url   = get_option('vh_tripadvisor_url');

		$get_footer_style  = get_option('vh_footer_style')


	/*	$youtube_url       = get_option('vh_youtube_url');
		$linkedin_url      = get_option('vh_linkedin_url');
		$foursquare_url    = get_option('vh_foursquare_url');
		$delicious_url     = get_option('vh_delicious_url');
		$digg_url          = get_option('vh_digg_url');*/
	?>
		</div><!--end of wrapper-->
		<?php if ($get_footer_style == 'true') {
			$footer_style = ' modern';
		} ?>
		<div class="row-fluid<?php echo $footer_style ?>">
			<div class="span12">
				<div class="footer-bg">
					<div class="footer-line"></div>
					<div class="footer container">
						<div class="footer-content">
							<?php
								// How many footer columns to show?
								$footer_columns = get_option( 'vh_footer_columns' );
								if ( $footer_columns == false ) {
									$footer_columns = 4;
								}
							?>
							<div class="footer-links-container columns_count_<?php echo $footer_columns; ?>">
								<?php get_sidebar( 'footer' ); ?>
								<div class="clearfix"></div>
							</div><!--end of footer-links-container-->
						</div>
					</div><!--end of footer-->
					<div class="copyrights-container">
						<div class="container">
							<?php
								$footer_logo = get_option('vh_footerlogo');

								$footer_logo_retina_ready = filter_var(get_option('vh_footer_logo_retina'), FILTER_VALIDATE_BOOLEAN);
								if ((bool)$footer_logo_retina_ready != false) {
									$footer_logo_size = getimagesize($footer_logo);
									$footer_logo_size_html = ' style="margin-top: 4px; float: left; margin-right: 30px; width: ' . ($footer_logo_size[0] / 2) . 'px; height:' . ($footer_logo_size[1] / 2) . 'px; width="' . ($footer_logo_size[0] / 2) . '" height="' . ($footer_logo_size[1] / 2) . '"';
								}

								if ( $footer_logo != false ) {
									echo '<a href="mailto:redladybird-webdesign@gmail.com"><img src="' . $footer_logo . '"' . $footer_logo_size_html . ' style="float: left; margin-top: 5px; margin-right: 25px;"></a>';
								}
							?>
							<?php
								$copyrights = get_option('vh_footer_copyright') ? get_option('vh_footer_copyright') : '&copy; [year] Copyright by Company. All rights reserved.';
								$copyrights = str_replace( '[year]', date('Y'), $copyrights);
							?>
							<p class="copyrights"><?php echo $copyrights; ?></p>
							<?php if($show_social_icons == "true") { ?>
							<div class="soc-icons">
								<?php if (!empty($facebook_url)) { ?><a href="<?php echo $facebook_url; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/facebook-rounded.png" width="40" height="40"></a><?php } ?>
								<?php if (!empty($bbh_url)) { ?><a target="_blank" href="<?php echo $bbh_url; ?>"><img src="http://riverstonebackpackers.com/wp-content/uploads/2014/08/bbh-logo44x37.jpg" width="40" height="40"></a><?php } ?>
								<?php if (!empty($tripadvisor_url)) { ?><a href="<?php echo $tripadvisor_url; ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/tripadvisor-rounded.png" width="40" height="40"></a><?php } ?>
								
							</div>
							<?php } ?>
						</div>
					</div>
				</div><!--end of footer-bg-->
			</div>
		</div>
		<?php
			$tracking_code = get_option( 'vh_tracking_code' ) ? get_option( 'vh_tracking_code' ) : '';
			if ( !empty( $tracking_code ) ) { ?>
				<!-- Tracking Code -->
				<?php
				echo '
					' . $tracking_code;
			}
			wp_footer();
		?>
	</body>
</html>
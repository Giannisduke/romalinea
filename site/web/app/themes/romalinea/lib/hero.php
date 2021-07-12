<?php
/**
 */

function core_hero_carousel_video_1_play() {
  if ( is_shop() ){
		$page_id = get_option( 'woocommerce_shop_page_id' );
	}
	else {
		$page_id = get_the_ID();
	}
        if( have_rows('carousel', $page_id) ):$counter = 0;?>
        <!--Carousel Section-->
      <section class="hero collapse show test" id="herocollapse" >
        <!--Carousel Wrapper-->
        <div id="video-carousel" class="carousel slide carousel-fade home-section" data-interval="false">
          <!--Slides-->
          <div class="carousel-inner" role="listbox">
                <?php while( have_rows('carousel', $page_id) ): the_row();
                  //  $slide_title = get_sub_field('slide_title');
                  //  $slide_subtitle = get_sub_field('slide_subtitle');
                    $slide_text = get_sub_field('slide_text', $page_id);
                    $slide_image = get_sub_field('slide_image_background', $page_id);
                  //  $slide_video = get_sub_field('slide_video');
                    $slide_external_video = get_sub_field('slide_external_video');
                    ?>
                    <div class="carousel-item <?php if($counter === 0){ echo "active";} ?>" data-slide-no="<?php echo $counter;?>" style="background: url('<?php echo $slide_image;?>') no-repeat center; background-size: cover;">
                        <div class="carousel-caption">
                            <?php  if (get_sub_field('slide_text', $page_id)) { ?>
                              <div class="container">
                                <div class="row">
                                  <div class="col">
                                    <?php echo $slide_text;?>
                                    <?php } ?>
                                  </div>
                                </div>
                              </div>
                            </div>
                      <?php if (get_sub_field('slide_external_video' ))  { ?>
                        <div class="overlay-div"></div>
                        <video class="video-fluid" controls="top" controlsList="nofullscreen nodownload noremoteplayback" id="player" preload="auto" playsinline muted autoplay="true" loop="true">
                            <source src="<?php echo $slide_external_video;?>"  />
                        </video>
                      <?php } else if (get_sub_field('slide_video' )) { ?>
                        <video class="video-fluid" controls="top" controlsList="nofullscreen nodownload noremoteplayback" id="player" preload="auto" playsinline muted autoplay="true" loop="true">
                            <source src="<?php echo $slide_video;?>"  />
                        </video>
                        <?php  } ?>
                    </div>
                    <?php $counter++; endwhile; ?>
                  </div> <!--/.Slides-->
                  </div> <!--Carousel Wrapper-->
                  <div class="accordion_spacer">
                    <div class="row">
                      <div class="devider">
                        <hr class="devider_hero">
                        <a class="icon-collapse accordion-toggle" data-toggle="collapse" href="#herocollapse" role="button" aria-expanded="false" aria-controls="herocollapse">
                          <span class="text">Link with href</span>
                          <span class="icon-collapse"></span>
                        </a>
                      </div>
                    </div>
                  </div>
</section>
<?php endif;
}
add_action ('core_hero_carousel_video_1', 'core_hero_carousel_video_1_play', 10 );

function romalinea_section_shop_play() {

}
add_action ('romalinea_section_shop', 'romalinea_section_shop_play', 10 );

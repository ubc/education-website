<?php  

/*
  Plugin Name: UBC Education Website
  Plugin URI:  http://educ.ubc.ca
  Description: Transforms the UBC Collab Theme into an Education website | Note: This plugin will only work on wp-hybrid-clf theme
  Version: 2.20
  Author: David Brabbins | UBCIT & Amir Entezaralmahdi | Arts ISIT & Caitlin Davis | Faculty of Education
  Licence: GPLv2
  Author URI: http://educ.ubc.ca
 */

Class UBC_Education_Theme_Options {
    static $prefix;
    static $faculty_main_homepage;
    static $add_script;

    /**
     * init function.
     * 
     * @access public
     * @return void 
     */
    static function init() {
    
        self::$prefix = 'wp-hybrid-clf'; // function hybrid_get_prefix() is not available within the plugin
        
        self::$faculty_main_homepage = 'http://www.educ.ubc.ca';
        
        $theme = wp_get_theme();
        
        if( "UBC Collab" != $theme->name )
          return true;
        //add_action( 'init', array(__CLASS__, 'register_scripts' ), 12 );

        //add_action('ubc_collab_theme_options_ui', array(__CLASS__, 'education_ui'));

        add_action( 'admin_init',array(__CLASS__, 'admin' ), 1);

        add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );

        add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );  

        add_action( 'wp_head', array( __CLASS__,'wp_head' ) );

        add_action( 'wp_footer', array( __CLASS__,'education_return' ), 10, 1 );
        //foe reg and enque
        add_action('admin_enqueue_scripts', array(__CLASS__,'foe_enqueue' ) );
        //Adds FOE Stylesheet
        add_action('wp_enqueue_scripts', array(__CLASS__, 'education_theme_styles'));    
        //Add FOE Brand Header
        add_action( 'wp-hybrid-clf_after_header', array(__CLASS__, 'faculty_plugin_before_header_widget') , 10);
         //Add FOE Featured Images to WordPress if one is present
        add_filter('wp-hybrid-clf_before_content', array(__CLASS__,'output_foe_featured_img'), 11, 3);
        //Add FOE Back to top link
        add_action('wp-hybrid-clf_after_content', array(__CLASS__, 'output_back_to_top') , 10);
    }
    /**
     * foe_uploader_options_enqueue_scripts function.
     * 
     * @access public
     * @return void
     */
    static function foe_enqueue() {
      $screen = get_current_screen();
      //wp_die( print_r( $screen, true ) ); // $screen->id
        if( $screen->id !== 'appearance_page_theme_options' )
          return;
      wp_register_style('education-theme-option-style', plugins_url('education-website') . '/css/style.css');
      wp_register_script('education-theme-option-script', plugins_url('education-website') . '/js/script.js');
      wp_register_script( 'foe-upload', plugins_url('education-website') .'/js/foe-upload.js', array('jquery','media-upload','thickbox') );

      // Enqueue scripts and styles
      wp_enqueue_style('education-theme-option-style');
      wp_enqueue_script('education-theme-option-script');
      wp_enqueue_script('foe-upload');     
    
      if(function_exists( 'wp_enqueue_media' )) :
            wp_enqueue_media();
          else :
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_script('thickbox');
      endif;

  }

  /**
   * print_script function.
   * 
   * @access public
   * @static
   * @return void
   */
  static function print_script() {
    if ( ! self::$add_script )
      return;
                
    wp_print_scripts( 'ubc-collab-education' );
  }  
    /**
     * admin function.
     * 
     * @access public
     * @return void
     */
    static function admin(){
        
        //Add Education Options tab in the theme options
        add_settings_section(
                'foe-options', // Unique identifier for the settings section
                'Faculty of Education Settings', // Section title
                '__return_false', // Section callback (we don't want anything)
                'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
        );

          //Add Colour options
          add_settings_field(
              'foe-colours', // Unique identifier for the field for this section
              'Colour Options', // Setting field label
              array(__CLASS__,'foe_colour_options'), // Function that renders the settings field
              'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
              'foe-options' // Settings section. Same as the first argument in the add_settings_section() above
          );
          //Add faculty of Education options
          add_settings_field(
              'foe-brand-options', // Unique identifier for the field for this section
              'Faculty of Education, Chevron, Banner, and Font', // Setting field label
              array(__CLASS__,'foe_brand_options'), // Function that renders the settings field
              'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
              'foe-options' // Settings section. Same as the first argument in the add_settings_section() above
          ); 
          //Add Hardcoded list
          add_settings_field(
              'foe-hardcoded-options', // Unique identifier for the field for this section
              'Hardcoded Features and Resources', // Setting field label
              array(__CLASS__,'foe_hardcoded_options'), // Function that renders the settings field
              'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
              'foe-options' // Settings section. Same as the first argument in the add_settings_section() above
          );  
    }  
     
    /**
     *  foe_colour_options.
     * Display colour options for Education specific template
     * @access public
     * @return void
     */   
    static function foe_colour_options(){ ?>

<div class="explanation"><a href="#" class="explanation-help">Info</a>
  <div>These colours are specific to each unit.</div>
</div>
<div id="education-unit-colour-box"> <b>Unit/Website Main Colour:</b><br />
  Read more about <a href="http://clf.educ.ubc.ca/design-style-guide/clf-specifications/#contrast" target="_blank">colour contrast</a> and <a href="http://clf.educ.ubc.ca/design-style-guide/clf-specifications/#contrast" target="_blank">web accesibility</a>.
  <div class="education-colour-item main-color"><br />
    <span>Main colour </span>
    <?php  UBC_Collab_Theme_Options::text( 'education-main-colour' ); ?>
  </div>
  <br/>
  <div class="education-colour-item secondary-color"><span>Secondary colour: </span>
    <?php  UBC_Collab_Theme_Options::text( 'education-gradient-colour' ); ?>
  </div>
</div>
<br/>
<!--          <div class="education-colour-item"><span>(C) Hover colour: </span>
                    <?php  //UBC_Collab_Theme_Options::text( 'education-hover-colour' ); ?>
                  </div>
                  <br/>
        -->
<?php
    }
  
    /**
     * foe_brand_options.
     * Display Faculty images
     * @access public
     * @return void
     */      
    static function foe_brand_options(){ ?>
<div class="explanation"><a href="#" class="explanation-help">Info</a>
  <div>
    <p><strong>This section is used to upload the department or units banner and chevron.</strong></p>
    <p>The chevron needs two files:</p>
    <ol>
      <li>one image for regular screens and devices</li>
      <li>and one image for high resolution screens such as retina displays</li>
    </ol>
    <p>Find dimensions and templates for the <a href="http://clf.educ.ubc.ca/design-style-guide/dimensions/#chevron" target="_blank">chevron</a> and <a href="http://clf.educ.ubc.ca/design-style-guide/dimensions/#banner" target="_blank">banner</a>.</p>
  </div>
</div>
<b>Unit/Website Banner and Chevron Options:</b><br />
Find resources for the <a href="http://clf.educ.ubc.ca/design-style-guide/dimensions/#chevron" target="_blank">chevron</a> and <a href="http://clf.educ.ubc.ca/design-style-guide/dimensions/#banner" target="_blank">banner</a>.
<div class="brand-img-upload"><br />
    <script>
jQuery(document).ready( function($) {
  $( '#submit' ) .bind("change keyup keydown blur click on",function(){
    var value = $('#foe-chevron-image-regular').val();
    console.log( value );
    $('.regular_chevron').attr('src',value);

    var value = $('#foe-banner-image').val();
    console.log( value );
    $('.banner').attr('src',value);

    var value = $('#foe-chevron-image-retina').val();
    console.log( value );
    $('.retina_chevron').attr('src',value);

  })
  .bind();

    $( '#foe-chevron-acronym' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-acronym').val();
      //console.log( value ); 
      value = value.replace(/[^a-zA-Z 0-9.]+/g,'');
      $('.chevron-text,.laptop-chevron,.mobile-chevron').text(value);
    })
    .bind();

    // Full Screen
    $( '#foe-chevron-font-size' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-font-size').val();
      //console.log( value );
      $('.chevron-text').css( "font-size", value + "px");
    })
    $( '#foe-chevron-padding-top' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-padding-top').val();
      //console.log( value );
      $('.chevron-text').css(  "padding-top", value + "px");
    })
    .bind();
    $( '#foe-chevron-letter-spacing' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-letter-spacing').val();
      //console.log( value );
      $('.chevron-text').css(  "letter-spacing", value + "px");
    })
    .bind();

  // Laptop
    $( '#foe-chevron-font-size-laptop' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-font-size-laptop').val();
      //console.log( value );
      $('.laptop-chevron').css( "font-size", value + "px");
    })
    $( '#foe-chevron-padding-top-laptop' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-padding-top-laptop').val();
      //console.log( value );
      $('.laptop-chevron').css(  "padding-top", value + "px");
    })
    .bind();
    $( '#foe-chevron-letter-spacing-laptop' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-letter-spacing-laptop').val();
      //console.log( value );
      $('.laptop-chevron').css(  "letter-spacing", value + "px");
    })
    .bind();

  // Mobile
    $( '#foe-chevron-font-size-mobile' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-font-size-mobile').val();
      //console.log( value );
      $('.mobile-chevron').css( "font-size", value + "px");
    })
    $( '#foe-chevron-padding-top-mobile' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-padding-top-mobile').val();
      //console.log( value );
      $('.mobile-chevron').css(  "padding-top", value + "px");
    })
    .bind();
    $( '#foe-chevron-letter-spacing-mobile' ) .bind("change keyup keydown blur click on",function(){
      var value = $('#foe-chevron-letter-spacing-mobile').val();
      //console.log( value );
      $('.mobile-chevron').css(  "letter-spacing", value + "px");
    })
    .bind();
    });
  
</script>
<p><strong>Banner Image:</strong></p>
  <p>Check to add banner. Faculty of Education default is solid blue.</p>
  <?php UBC_Collab_Theme_Options::checkbox( 'education-enable-banner', 0, 'Enable Banner image upload?' ); ?>
</div>
<div class="brand-img-upload banner-enable"> 
  <p>* The Faculty of Education default is a solid blue banner. Browse to select and upload the custom banner image you have created.  Banner images should be 1200px by 67px. The banner image is aligned to the center.<br /></p>
  <?php  UBC_Collab_Theme_Options::text( 'foe-banner-image' ); ?>
  <input id="upload_banner_button" type="button" class="button" value="Upload">
  <img  class="banner" src="<?php echo UBC_Collab_Theme_Options::get( 'foe-banner-image' ); ?>" /> </div>
  <br />
    <strong>Chevron Options:</strong><br />
    <?php UBC_Collab_Theme_Options::checkbox( 'foe-chevron-type', 1, 'Use the customized CSS chevron?' ); ?><br /><small>This option will remove the chevron image and will allow the use of a customized chevron that uses html and css.</small><br /><br />
    <div class="custom-chevron-setup">
      <b>Custom Chevron</b>
      <p>Fill in the acronym or title for chevron.</p>
      <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-acronym' ); ?>
      <style type="text/css">
      <?php if (class_exists('UBC_Full_Width_Theme_Options')) : ?>
    .brand-container {
     background: <?php if (UBC_Collab_Theme_Options::get( 'education-enable-banner') == '1') {
     echo 'url(' . UBC_Collab_Theme_Options::get('foe-banner-image'). ') ';
    }
    else {
     echo '';
    }
     ?><?php echo UBC_Collab_Theme_Options::get('education-main-colour')?> repeat-x;
    }
      <?php else: ?>
    .brand-container {
     background: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
    }
    <?php endif; ?>
    <?php if (class_exists('UBC_Full_Width_Theme_Options')) : ?>
    .dept-brand {
     background: none;
    }
    <?php else: ?>
    .dept-brand {
    background: <?php if (UBC_Collab_Theme_Options::get( 'education-enable-banner') == '1') {
     echo 'url(' . UBC_Collab_Theme_Options::get('foe-banner-image'). ') ';
    }
    else {
     echo '';
    }
     ?><?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
    }
    <?php endif; ?>

      .chevron-text {
          padding-top: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-padding-top')?>px;
          font-size: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-font-size')?>px;
          letter-spacing: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-letter-spacing')?>px;
      }

       .custom-chevron.laptop {
        width: 230px;
      }
      .custom-chevron.laptop:after {
        border-left: 115px solid transparent;
        border-right: 115px solid transparent;
      }
      .laptop-chevron {
          padding-top: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-padding-top-laptop')?>px;
          font-size: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-font-size-laptop')?>px;
          letter-spacing: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-letter-spacing-laptop')?>px; 
      }
      .custom-chevron.mobile {
        width: auto;
        background-color: transparent;
      }
      .custom-chevron.mobile:after {
        border-left: none;
        border-right: none;
      }
      .mobile-chevron {
          padding-top: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-padding-top-mobile')?>px;
          font-size: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-font-size-mobile')?>px;
          letter-spacing: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-letter-spacing-mobile')?>px;
      }   
      .custom-chevron {
        background: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
      }
    .custom-chevron:after {
        border-top-color: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>!important;
        -moz-transform: scale(.9999);
    }
}
<?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>
      </style>
      <br />
      <br /> 
      <p><strong>*Use the live preivew to adjust the chevrons font size, padding top and letter spacing. Live preview will show the chevrons appearance in desktop, laptop and mobile.</strong></p>
      <p><img style="width: 30px; height: auto;" src="<?php echo plugins_url('education-website'); ?>/img/desktop.png"> <strong>Chevron Desktop</strong></p>
      <div class="chevron-container educ-clearfix">
        <div class="educ-one-third"><p>Font size</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-font-size' ); ?>px</div>
       <div class="educ-one-third"> <p>Padding top</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-padding-top' ); ?>px</div>
        <div class="educ-one-third"> <p>letter spacing</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-letter-spacing' ); ?>px</div>
        <br />
        </div>

      <div class="brand-container expand">
        <div id="dept-brand" class="row-fluid expand dept-brand">
          <div id="education-chevron" class="custom-chevron"><div class="chevron-text"><a tite="<?php echo bloginfo(); ?>" href="<?php echo bloginfo('url'); ?>"><?php echo UBC_Collab_Theme_Options::get('foe-chevron-acronym')?></a></div></div>
        </div>
      </div>
      <br />  <br /><br />
        <p><img style="width: 30px; height: auto;" src="<?php echo plugins_url('education-website'); ?>/img/laptop.png"> <strong>Chevron Laptop Size</strong></p>
       <div class="chevron-container educ-clearfix">
        <div class="educ-one-third"><p>Font size</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-font-size-laptop' ); ?>px</div>
       <div class="educ-one-third"> <p>Padding top</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-padding-top-laptop' ); ?>px</div>
        <div class="educ-one-third"> <p>letter spacing</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-letter-spacing-laptop' ); ?>px</div>
        <br />
        </div>
      <div class="brand-container expand">
        <div id="dept-brand" class="row-fluid expand dept-brand">
          <div id="education-chevron" class="custom-chevron laptop"><div class="laptop-chevron"><a tilte="<?php echo bloginfo(); ?>" href="<?php echo bloginfo('url'); ?>"><?php echo UBC_Collab_Theme_Options::get('foe-chevron-acronym')?></a></div></div>
        </div>
      </div>
      <br />  <br /><br />
        <p><img style="width: 30px; height: auto;" src="<?php echo plugins_url('education-website'); ?>/img/mobile.png"> <strong>Chevron Mobile Size</strong></p>
       <div class="chevron-container educ-clearfix">
        <div class="educ-one-third"><p>Font size</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-font-size-mobile' ); ?>px</div>
       <div class="educ-one-third"> <p>Padding top</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-padding-top-mobile' ); ?>px</div>
        <div class="educ-one-third"> <p>letter spacing</p>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-letter-spacing-mobile' ); ?>px</div>
        <br />
        </div>
      <div class="brand-container expand">
        <div id="dept-brand" class="row-fluid expand dept-brand">
          <div id="education-chevron" class="custom-chevron mobile"><div class="mobile-chevron"><a title="<?php echo bloginfo(); ?>" href="<?php echo bloginfo('url'); ?>"><?php echo UBC_Collab_Theme_Options::get('foe-chevron-acronym')?></a></div></div>
        </div>
      </div>
    </div> <!-- end .custom-chevron-setup -->

    <div class="image-chevron">
      <div class="brand-img-upload"><span><strong>Regular Chevron Image:</strong></span>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-image-regular' ); ?>
        <input id="upload_regular_button" type="button" class="button" value="Upload">
        <img class="regular_chevron" src="<?php echo UBC_Collab_Theme_Options::get( 'foe-chevron-image-regular' ); ?>" /> </div>
      <div class="brand-img-upload"><span><strong>Retina Chevron Image:</strong></span>
        <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-image-retina' ); ?>
        <input id="upload_retina_button" type="button" class="button" value="Upload">
        <img class="retina_chevron" src="<?php echo UBC_Collab_Theme_Options::get( 'foe-chevron-image-retina' ); ?>" /> </div>
    </div> <!-- end .image-chevron -->

  <hr />
  <b>Open Sans Font</b><br />
  <p>Open Sans is a font supplied, freely, by <a target="_blank" href="http://www.google.com/fonts/specimen/Open+Sans">Google fonts</a>. This font is similar to UBC font choice of Whitney.</p>
  <?php UBC_Collab_Theme_Options::checkbox( 'open-sans-add', 1, 'Add Open Sans web font?' ); ?>
  <hr />
  <b>Faculty of Education Home Button</b><br />
  <p>Select this option to add the Faculty of Education home icon. Activating this option will add an icon the right side of the menu bar that links to the Faculty of Education home website http://educ.ubc.ca. The Faculty home site will open in a new page.</p>
  <?php UBC_Collab_Theme_Options::checkbox( 'FOE-button-add', 1, 'Add the Faculty of Education home icon?' ); ?>
  <hr />
  
<br />

<?php 
      } 
    
    /**
     * foe_hardcoded_options.
     * Display Hardcoded info and Faculty Resources
     * @access public
     * @return void
     */      
    static function foe_hardcoded_options(){ ?>
<div class="explanation"><a href="#" class="explanation-help">Info</a>
  <div>The following are the description of hardcoded items and resources for a Faculty of Education Theme.</div>
</div>
<div id="education-hardcoded-box"> <b>The following features are hardcoded:</b><br />
  <ol>
    <li>Blue on White UBC header</li>
    <li>Faculty of Education Style</li>
    <li>Department/ Unit Header</li>
    <li>Circled <strong>Featured Images</strong> for pages. <a href="http://clf.educ.ubc.ca/features/featured-images/" target="_blank">Find out more.</a></li>
    <li>Body classes based on partent and category slugs. <a href="http://clf.educ.ubc.ca/features/featured-images/" target="_blank">Find out more.</a></li>
    <li>Back to Top link</li>
  </ol>
</div>
<div id="education-resources"> <b>Faculty of Education Resources:</b><br />
  <ol>
    <li><a href="http://clf.educ.ubc.ca/" target="_blank">Faculty of Education CLF</a></li>
    <li><a href="http://wiki.ubc.ca/Documentation:UBC_Content_Management_System" target="_blank">WordPress/ CMS Wiki site</a></li>
    <li><a href="http://clf.ubc.ca" target="_blank">UBC CLF</a></li>
  </ol>
</div>
<?php 
    UBC_Education_Theme_Options::education_defaults();
    }    
    
    static function education_defaults(){
        UBC_Collab_Theme_Options::update('clf-unit-colour', '#002145');
        UBC_Collab_Theme_Options::update('clf-colour-theme', 'bw');
    }
  
    /*********** 
     * Default Options
     * 
     * Returns the options array for education fields.
     *
     * @since ubc-clf 1.0
     */
    static function default_values( $options ) {

            if (!is_array($options)) { 
                    $options = array();
            }

            $defaults = array(
                'education-main-colour'              =>  "#2F5D7C",
                'education-gradient-colour'          =>  "#3f7ea7",
                'education-hover-colour'             =>  "#002145",
                'education-enable-banner'            =>  "1",
                'open-sans-add'                      =>  "0",
        'FOE-button-add'                             =>  "1",
                 'foe-button-white'                  =>  plugins_url('education-website')."/img/FOE-icon-white.png",
             'foe-button-lightblue'                  =>  plugins_url('education-website')."/img/FOE-icon-lightblue.png",
             'foe-button-darkblue'                   =>  plugins_url('education-website')."/img/FOE-icon-darkblue.png",
                'foe-chevron-type'                   =>   0,
                'foe-banner-image'                   =>  plugins_url('education-website')."/img/banner.png",
                'foe-chevron-image-regular'          =>  plugins_url('education-website')."/img/faculty-chevron.png",
                'foe-chevron-image-retina'           =>  plugins_url('education-website')."/img/faculty-chevron-@2x.png",
                'foe-chevron-acronym'                =>  "EDUC",
                'foe-chevron-font-size'              =>  "77",
                'foe-chevron-padding-top'            =>  "25",
                'foe-chevron-letter-spacing'         =>  "-4",
                'foe-chevron-font-size-laptop'       =>  "75",
                'foe-chevron-padding-top-laptop'     =>  "25",
                'foe-chevron-letter-spacing-laptop'  =>  "-4",
                'foe-chevron-font-size-mobile'       =>  "77",
                'foe-chevron-padding-top-mobile'     =>  "22",
                'foe-chevron-letter-spacing-mobile'  =>  "-4"
            );

            $options = array_merge( $options, $defaults );

            return $options;
    }  
  
  /**
   * Sanitize and validate form input. Accepts an array, return a sanitized array.
   *
   *
   * @todo set up Reset Options action
   *
   * @param array $input Unknown values.
   * @return array Sanitized theme options ready to be stored in the database.
   *
   */
  static function validate( $output, $input ) {
    
    // Grab default values as base
    $starter = UBC_Education_Theme_Options::default_values( array() );

      // Validate Unit Colour Options A, B, and C
      $starter['education-main-colour'] = UBC_Collab_Theme_Options::validate_text($input['education-main-colour'], $starter['education-main-colour'] );
      $starter['education-gradient-colour'] = UBC_Collab_Theme_Options::validate_text($input['education-gradient-colour'], $starter['education-gradient-colour'] );
      $starter['education-hover-colour'] = UBC_Collab_Theme_Options::validate_text($input['education-hover-colour'], $starter['education-hover-colour'] );      
      // Validate Image URLs for Education Branding

      $starter['education-enable-banner'] = (bool)$input['education-enable-banner'];
      $starter['open-sans-add'] = (bool)$input['open-sans-add'];
        $starter['FOE-button-add'] = (bool)$input['FOE-button-add'];
      $starter['foe-chevron-type'] = (bool)$input['foe-chevron-type'];

      $starter['foe-banner-image'] = UBC_Collab_Theme_Options::validate_text($input['foe-banner-image'], $starter['foe-banner-image'] );
      $starter['foe-chevron-image-regular'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-image-regular'], $starter['foe-chevron-image-regular'] );
      $starter['foe-chevron-image-retina'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-image-retina'], $starter['foe-chevron-image-retina'] );
      $starter['foe-chevron-acronym'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-acronym'], $starter['foe-chevron-acronym'] );
      $starter['foe-chevron-font-size'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-font-size'], $starter['foe-chevron-font-size'] );
      $starter['foe-chevron-padding-top'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-padding-top'], $starter['foe-chevron-padding-top'] );
      $starter['foe-chevron-letter-spacing'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-letter-spacing'], $starter['foe-chevron-letter-spacing'] );
      $starter['foe-chevron-font-size-laptop'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-font-size-laptop'], $starter['foe-chevron-font-size-laptop'] );
      $starter['foe-chevron-padding-top-laptop'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-padding-top-laptop'], $starter['foe-chevron-padding-top-laptop'] );
      $starter['foe-chevron-letter-spacing-laptop'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-letter-spacing-laptop'], $starter['foe-chevron-letter-spacing-laptop'] );
      $starter['foe-chevron-font-size-mobile'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-font-size-mobile'], $starter['foe-chevron-font-size-mobile'] );
      $starter['foe-chevron-padding-top-mobile'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-padding-top-mobile'], $starter['foe-chevron-padding-top-mobile'] );
      $starter['foe-chevron-letter-spacing-mobile'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-letter-spacing-mobile'], $starter['foe-chevron-letter-spacing-mobile'] );
      
      $output = array_merge($output, $starter);

           return $output;            
        }
    
   /**
     * education_theme_styles
     * Adds the Faculty of Education Stylesheet
     */         
    static function education_theme_styles()  
      { 
        wp_register_style( 'education-clf', plugins_url('education-website') . "/css/global.css", true );
      
        // enqueing:
        wp_enqueue_style( 'education-clf' );
        
    }
          
   /**
     * education_return
     * Adds the Faculty of Education Stylesheet
     */         
    static function education_return()  
      { 
        // enqueing:
        wp_enqueue_script( 'education-go' );
        
    }
              
   /**
     * output_foe_brand_header
     * Adds the FOE brand header
     */         

  static function faculty_plugin_before_header_widget(){ ?>
<div class="brand-container expand">
  <div id="dept-brand" class="row-fluid expand dept-brand">
    <?php if (UBC_Collab_Theme_Options::get( 'foe-chevron-type') == 0) : ?>
    <div id="department-logo" class="row-fluid department-logo"> <a title="<?php echo get_bloginfo() ?>"  href="<?php echo get_bloginfo('url') ?>"><?php echo get_bloginfo() ?></a> </div>
     <?php elseif (UBC_Collab_Theme_Options::get( 'foe-chevron-type') == 1) : ?>
    <div id="education-chevron" class="custom-chevron"><div class="chevron-text"><a title="<?php echo bloginfo(); ?>" href="<?php echo bloginfo('url'); ?>"><?php echo UBC_Collab_Theme_Options::get('foe-chevron-acronym')?></a></div></div>
<?php endif ?>
<?php if (UBC_Collab_Theme_Options::get( 'FOE-button-add') == 1) : ?><div id="FOE-button-outer"><a href="http://educ.ubc.ca" target="_blank" title="Faculty of Education Home"><div id="FOE-button"></div></a></div><?php endif ?>
  </div>    
</div>
<?php
                }


         /**
         * output_foe_featured_img
         * Adds featured images to WP pages
         */         
         static function output_foe_featured_img(){

           if ( is_page() && ! is_front_page() && ! is_home() ) {
                                set_post_thumbnail_size( 150, 150, true );
                if (has_post_thumbnail()) {

                  $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(),'large', true);
                  $image_size = wp_get_attachment_image_src(get_post_thumbnail_id(),'large', true);
                      echo "<div class=\"featured-images-pages\"><img class=\"featured-images\" src=\"" . $image_url[0] ."\" title=\"" . the_title_attribute('echo=0') . "\" alt=\"" . the_title_attribute('echo=0') . "\" /></div>";
        
      } else {
                    echo "";
                }
            }
        }
    
   /**
     * output_back_to_top
     * Adds the back to top below content
     */         

  static function output_back_to_top(){
        echo "<div id=\"section-6\" class=\"widget section-widget widget-section-widget content-back-top\">
      <div class=\"widget-wrap widget-inside\">
      <hr />
      <div class=\"row-fluid ubc7-back-to-top clearfix\">
        <div class=\"span4\"><a href=\"#\" title=\"Back to top\">Back to top <span class=\"ubc7-arrow up-arrow grey\"></span></a></div>
      </div>
      </div>
    </div>
    <!-- Back to Top End --> 
    ";
     }
   
      /**
     * wp_head
     * Appends some of the dynamic css and js to the wordpress header
     */        
        static function wp_head(){ 
        $hex = UBC_Collab_Theme_Options::get('education-main-colour'); 
        $hex = str_replace("#", "", $hex);

           if(strlen($hex) == 3) {
              $r = hexdec(substr($hex,0,1).substr($hex,0,1));
              $g = hexdec(substr($hex,1,1).substr($hex,1,1));
              $b = hexdec(substr($hex,2,1).substr($hex,2,1));
           } else {
              $r = hexdec(substr($hex,0,2));
              $g = hexdec(substr($hex,2,2));
              $b = hexdec(substr($hex,4,2));
           }
           $rgb = array($r, $g, $b);
           $rgb = implode(",", $rgb); // returns the rgb values separated by commas


          ?>
<style type="text/css" media="screen">
<?php if (UBC_Collab_Theme_Options::get( 'open-sans-add') == '1') : ?>
@import url(http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,700italic,400,300,800,700);


.entry-content, h1, h2, h3, h4, h5, h6, p, #ubc7-unit-menu .nav a, .lead, .sidenav a, ul, li, .btn {
    font-family: 'Open Sans', sans-serif;
    font-style: normal;
    font-weight: normal;
    text-decoration: inherit;
    text-rendering: optimizelegibility;
}
h1, h2, h3, h4, h5, h6 { font-weight: 300 }
<?php endif; ?>

<?php if (UBC_Collab_Theme_Options::get( 'FOE-button-add') == '1') : ?>
<?php if (UBC_Collab_Theme_Options::get( 'foe-chevron-type') == 1) : ?>
@media (min-width:1100px){

#FOE-button-outer
{position: relative;
}
#FOE-button {
  background: url(<?php echo UBC_Collab_Theme_Options::get('foe-button-darkblue')?>) no-repeat;
    height: 30px;
  width: 30px;
  position: absolute;
  top: -105px;
  left: 1150px;
}
#FOE-button:hover {
  background: url(<?php echo UBC_Collab_Theme_Options::get('foe-button-lightblue')?>) no-repeat;
}
}
<?php endif; ?>

<?php if (UBC_Collab_Theme_Options::get( 'foe-chevron-type') == 0) : ?>
@media (min-width:1100px){

#FOE-button-outer
{position: relative;
}
#FOE-button {
  background: url(<?php echo UBC_Collab_Theme_Options::get('foe-button-darkblue')?>) no-repeat;
    height: 30px;
  width: 30px;
  position: absolute;
  top: -137px;
  left: 1150px;
}
#FOE-button:hover {
  background: url(<?php echo UBC_Collab_Theme_Options::get('foe-button-lightblue')?>) no-repeat;
}
}
<?php endif; ?>

<?php endif; ?>

#ubc7-unit {
  background: #002145 !important;
}
/*-- Top Navigation forced importants !importants ---------------------------*/
        /*-- Top Navigation Hovers ---------------------------*/
#ubc7-unit-menu .nav-collapse .nav>li>a:hover, #ubc7-unit-menu .nav-collapse .dropdown-menu a:hover, #ubc7-unit-alternate-navigation .nav-collapse .nav>li>a:hover, #ubc7-unit-alternate-navigation .nav-collapse .dropdown-menu a:hover {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>!important;
}
#ubc7-unit-menu .btn-group button:hover, #ubc7-unit-alternate-navigation .btn-group button:hover {
 background: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>!important;
}
/*-- Top Navigation  ---------------------------*/
        /*-- Top Navigation Current pages ---------------------------*/
#ubc7-unit-menu .nav > li.active > a, #ubc7-unit-alternate-navigation .nav > li.active > a, #ubc7-unit-menu .nav > li.active > .btn-group > a, #ubc7-unit-alternate-navigation .nav > li.active > .btn-group > a, #ubc7-unit-menu .nav > li.current-page-ancestor > a, #ubc7-unit-alternate-navigation .nav > li.current-page-ancestor > a, #ubc7-unit-menu .nav > li.current-post-parent > a, #ubc7-unit-alternate-navigation .nav > li.current-post-parent > a, #ubc7-unit-menu .nav > li.current-page-ancestor .btn-group > a, #ubc7-unit-alternate-navigation .nav > li.current-page-ancestor .btn-group > a, #ubc7-unit-menu .nav > li.current-post-parent .btn-group > a, #ubc7-unit-alternate-navigation .nav > li.current-post-parent .btn-group > a, #ubc7-unit-menu .nav > li.active > .btn-group, #ubc7-unit-alternate-navigation .nav > li.active > .btn-group, #ubc7-unit-menu .nav > li.current-page-ancestor > .btn-group, #ubc7-unit-alternate-navigation .nav > li.current-page-ancestor > .btn-group, #ubc7-unit-menu .nav > li.current-menu-parent > .btn-group, #ubc7-unit-alternate-navigation .nav > li.current-menu-parent > .btn-group, #ubc7-unit-menu .nav > li.current-post-parent > a, #ubc7-unit-alternate-navigation .nav > li.current-post-parent > a, #ubc7-unit-alternate-navigation .nav > li.current-menu-parent a, #ubc7-unit-menu .dropdown .btn-group:hover .btn, #ubc7-unit-alternate-navigation .dropdown .btn-group:hover .btn, #ubc7-unit-menu .dropdown .btn-group:hover .dropdown-toggle, #ubc7-unit-alternate-navigation .dropdown .btn-group:hover .dropdown-toggle, #ubc7-unit-menu .current-page-ancestor .btn-group .btn, #ubc7-unit-alternate-navigation .current-page-ancestor .btn-group .btn, #ubc7-unit-menu .current-menu-parent .btn-group .btn, #ubc7-unit-alternate-navigation .current-menu-parent .btn-group .btn, #ubc7-unit-menu .current-post-parent .btn-group .btn, #ubc7-unit-alternate-navigation .current-post-parent .btn-group .btn {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
/*-- Top Navigation Hovers ---------------------------*/
#ubc7-unit-alternate-navigation .nav>li.active>.btn-group:hover .btn, #ubc7-unit-alternate-navigation .dropdown .btn-group:hover .btn, #ubc7-unit-alternate-navigation .dropdown .btn-group:hover .dropdown-toggle, #ubc7-unit-menu .nav>li.active>.btn-group:hover .btn {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
/*-- Sidebar Navigation  ---------------------------*/
        /*-- Current Pages  ---------------------------*/
.sidenavigation .accordion-group .accordion-heading.active, .sidenavigation .accordion-group .accordion-heading.active a, .sidenavigation .accordion-group .accordion-heading.active .accordion-toggle, .accordion.sidenav .single a.opened, .simple-custom-menu a .current-post-ancestor a, .simple-custom-menu .current-menu-parent a, .simple-custom-menu.current-post-parent a, .simple-custom-menu .active a, .sidenav .accordion-group .accordion-inner>a.opened, .sidenav .accordion>a.opened {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
/*-- Hover Pages  ---------------------------*/
.sidenav .single a:hover, .simple-custom-menu.sidenav .menu-item a:hover, .sidenav .accordion-inner a:hover, .sidenav .single a:hover, .simple-custom-menu.sidenav .menu-item a:hover, .sidenav .accordion-inner a:hover {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
.sidenav .accordion-heading:hover, .sidenav .accordion-heading a:hover, .sidenav .accordion-heading:hover a:focus, .sidenav .accordion-heading:hover a:active, .sidenav .accordion-heading:hover .accordion-toggle {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>!important;
}
.sidenav .accordion-heading .accordion-toggle:hover {
 background: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>!important;
}
/*-- Accordion Hover  ---------------------------*/
.accordion-heading a:hover, .accordion-heading a:active, .accordion-heading a:focus {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
/*-- tab Hover  ---------------------------*/
.nav-tabs>li>a {
  background: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
  border-color: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
}
.nav-tabs>li>a:hover, .nav-tabs>li>a:focus {
  background-color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
  border-color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
.nav-tabs>.active>a, .nav-tabs>.active>a:hover {
  background-color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
  border-color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;  
}
/*-- Slider  ---------------------------*/
#ubc7-carousel .carousel-caption, .ubc-carousel .carousel-caption {
 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
h1, h2, h3, h4, h5, h6, #content .hero-unit h1, #frontpage-siderbar .tab-pane a, a {
 color: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
}
a:hover {
 color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
}
.sidenav.accordion {
  border: 0;
 border-bottom: 10px solid <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
}
<?php if (class_exists('UBC_Full_Width_Theme_Options')) : ?>
.brand-container {
 background: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?> repeat-x;
}
<?php endif; ?>
<?php if (class_exists('UBC_Full_Width_Theme_Options')) : ?>
.dept-brand {
 background: <?php if (UBC_Collab_Theme_Options::get( 'education-enable-banner') == '1') {
 echo 'url(' . UBC_Collab_Theme_Options::get('foe-banner-image'). ') ';
}
else {
 echo '';
}
 ?><?php echo UBC_Collab_Theme_Options::get('education-main-colour')?> no-repeat;
}
<?php else: ?>
.dept-brand {
background: <?php if (UBC_Collab_Theme_Options::get( 'education-enable-banner') == '1') {
 echo 'url(' . UBC_Collab_Theme_Options::get('foe-banner-image'). ') ';
}
else {
 echo '';
}
 ?><?php echo UBC_Collab_Theme_Options::get('education-main-colour')?> no-repeat;
}

<?php endif; ?>

.department-logo {
 background-image: url(<?php echo UBC_Collab_Theme_Options::get('foe-chevron-image-regular')?>);
}
 @media(-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
#department-logo {
 background-image: url(<?php echo UBC_Collab_Theme_Options::get('foe-chevron-image-retina')?>);
}
}
.custom-chevron {
   background-color: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
   background-color: rgba(<?php echo $rgb; ?>,1);
}
.custom-chevron:after {
border-top-color: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;;
border-top-color: rgba(<?php echo $rgb; ?>,1);
}
.chevron-text {
        padding-top: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-padding-top')?>px;
        font-size: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-font-size')?>px;
        letter-spacing: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-letter-spacing')?>px;
}

@media (max-width: 979px) {
  .chevron-text {
        padding-top: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-padding-top-laptop')?>px;
        font-size: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-font-size-laptop')?>px;
        letter-spacing: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-letter-spacing-laptop')?>px;
  }

}
@media (max-width: 767px) {
  .custom-chevron {
    background-color: transparent;
  }
  .chevron-text {
        padding-top: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-padding-top-mobile')?>px;
        font-size: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-font-size-mobile')?>px;
        letter-spacing: <?php echo UBC_Collab_Theme_Options::get('foe-chevron-letter-spacing-mobile')?>px;
  }
}
.ie7 .custom-chevron, .ie7 .chevron-text {
    border: none !important;
  background-color: transparent !important;
  position: relative !important;
  width: auto !important;
  height: auto !important;
  padding: 0 !important;
  margin: 0 !important;
  overflow: visible !important;
  text-align: left !important;
}
 .ie7 .chevron-text {
  margin-top: 22px !important;   
 }
.ie7 .custom-chevron {
  height: 67px !important;   
}
.ie8 #container, ie8 .home-default #container {
  margin-top: 0 !important;
  padding-top: 0 !important;
  border-top: 35px solid #FFF !important;
}
.ie8 .home-layout-option1 #container, .ie8 .home-layout-option2 #container, .ie8 .home-layout-option3 #container, .ie8 .home-layout-option4 #container, .ie8 .home-layout-option5 #container {
  border-top: none !important;
}
</style>
<?php 
    } 
}

UBC_Education_Theme_Options::init();
//var_dump( get_option( 'ubc-collab-theme-options' ));



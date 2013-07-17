<?php  

/*
  Plugin Name: UBC Education Website
  Plugin URI:  http://educ.ubc.ca
  Description: Transforms the UBC Collab Theme into an Education website | Note: This plugin will only work on wp-hybrid-clf theme
  Version: 1.0.1
  Author: Amir Entezaralmahdi | Arts ISIT & David brabbins
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
    function init() {
		
        self::$prefix = 'wp-hybrid-clf'; // function hybrid_get_prefix() is not available within the plugin
        
        self::$faculty_main_homepage = 'http://www.educ.ubc.ca';
        
        $theme = wp_get_theme();
       	
        if( "UBC Collab" != $theme->name )
        	return true;
        // include Education specific css file
        wp_register_style('education-theme-option-style', plugins_url('education-website') . '/css/style.css');
        // include Education specific javascript file
        wp_register_script('education-theme-option-script', plugins_url('education-website') . '/js/script.js');

        add_action( 'init', array(__CLASS__, 'register_scripts' ), 12 );
		
        add_action('ubc_collab_theme_options_ui', array(__CLASS__, 'education_ui'));
		
        add_action( 'admin_init',array(__CLASS__, 'admin' ), 1);
		
        add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );
		
        add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );  
		    	
        add_action( 'wp_head', array( __CLASS__,'wp_head' ) );
				
		//foe uploader scripts
		add_action('admin_print_scripts', array(__CLASS__,'education_upload_scripts' ) ); 
		add_action('admin_enqueue_scripts', array(__CLASS__,'foe_uploader_options_enqueue_scripts' ) );
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
    function foe_uploader_options_enqueue_scripts() {
		wp_register_script( 'foe-upload', plugins_url('education-website') .'/js/foe-upload.js', array('jquery','media-upload','thickbox') );			
	}
	
	/**
	 * education_upload_scripts function.
	 * 
	 * @access public
	 * @return void
	 */
	function education_upload_scripts() { 
		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}
	}
    
    /**
     * register_scripts function.
     * 
     * @access public
     * @return void
     */
    function register_scripts() {
		// register the spotlight functions
        if( !is_admin() ):
        	wp_register_script( 'ubc-collab-education', plugins_url('education-website').'/js/education-website.js', array( 'jquery' ), '0.1', true );
			wp_register_script( 'foe-upload', plugins_url('education-website') .'/js/foe-upload.js', array('jquery','media-upload','thickbox') );
        	//wp_enqueue_style('ubc-collab-education', plugins_url('education-website').'/css/education-website.css');
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
        
    /*
     * This function includes the css and js for this specifc admin option
     *
     * @access public
     * @return void
     */
     function education_ui(){
        wp_enqueue_style('education-theme-option-style');
        wp_enqueue_script('education-theme-option-script' );
		wp_enqueue_script('education-theme-option-media-script' );
		wp_enqueue_script('foe-upload');
     }

    /**
     * admin function.
     * 
     * @access public
     * @return void
     */
    function admin(){
        
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
						'Faculty of Education Chevron and Banner', // Setting field label
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
    function foe_colour_options(){ ?>

        <div class="explanation"><a href="#" class="explanation-help">Info</a>
          <div>These colours are specific to each unit.</div>
        </div>
        <div id="education-unit-colour-box">
            <b>Unit/Website Main Colour:</b><br />
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
    function foe_brand_options(){ ?>
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
            <?php UBC_Collab_Theme_Options::checkbox( 'education-enable-banner', 1, 'Enable Banner image upload?' ); ?>
          </div>
          <div class="brand-img-upload banner-enable">
          Uncheck to remove banner.
          <span><br />
            <strong>Banner Image:</strong></span>
            <p>* Leave the form field empty to use the Faculty of Education banner.</p>
            <?php  UBC_Collab_Theme_Options::text( 'foe-banner-image' ); ?>
            <input id="upload_banner_button" type="button" class="button" value="Upload">
            <img src="<?php echo UBC_Collab_Theme_Options::get( 'foe-banner-image' ); ?>" /> </div>
            
          <div class="brand-img-upload"><span><strong>Regular Chevron Image:</strong></span>
            <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-image-regular' ); ?>
            <input id="upload_regular_button" type="button" class="button" value="Upload">
            <img src="<?php echo UBC_Collab_Theme_Options::get( 'foe-chevron-image-regular' ); ?>" /> </div>
            
          <div class="brand-img-upload"><span><strong>Retina Chevron Image:</strong></span>
            <?php  UBC_Collab_Theme_Options::text( 'foe-chevron-image-retina' ); ?>
            <input id="upload_retina_button" type="button" class="button" value="Upload">
            <img src="<?php echo UBC_Collab_Theme_Options::get( 'foe-chevron-image-retina' ); ?>" /> </div>
	<?php 
      } 
		
    /**
     * foe_hardcoded_options.
     * Display Hardcoded info and Faculty Resources
     * @access public
     * @return void
     */      
    function foe_hardcoded_options(){ ?>
        <div class="explanation"><a href="#" class="explanation-help">Info</a>
          <div>The following are the description of hardcoded items and resources for a Faculty of Education Theme.</div>
        </div>
        <div id="education-hardcoded-box">
          <b>The following features are hardcoded:</b><br />
          <ol>
            <li>Blue on White UBC header</li>
            <li>Faculty of Education Style</li>
            <li>Department/ Unit Header</li>
            <li>Circled <strong>Featured Images</strong> for pages. <a href="http://clf.educ.ubc.ca/features/featured-images/" target="_blank">Find out more.</a></li>
            <li>Back to Top link</li>
          </ol>
        </div>
        <div id="education-resources">
          <b>Faculty of Education Resources:</b><br />
          <ol>
            <li><a href="http://clf.educ.ubc.ca/" target="_blank">Faculty of Education CLF</a></li>
            <li><a href="http://wiki.ubc.ca/Documentation:UBC_Content_Management_System" target="_blank">WordPress/ CMS Wiki site</a></li>
            <li><a href="http://clf.ubc.ca" target="_blank">UBC CLF</a></li>
          </ol>
        </div>

<?php	
    UBC_Education_Theme_Options::education_defaults();
    }    
    
    function education_defaults(){
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
    function default_values( $options ) {

            if (!is_array($options)) { 
                    $options = array();
            }

            $defaults = array(
                'education-main-colour'			=> "#2F5D7C",
                'education-gradient-colour'		=> "#3f7ea7",
                'education-hover-colour'		=> "#002145",
				'education-enable-banner' 		=> "1",
				'foe-banner-image'    			=> plugins_url('education-website')."/img/banner.png",
				'foe-chevron-image-regular'    	=>  plugins_url('education-website')."/img/faculty-chevron.png",
				'foe-chevron-image-retina'    	=> plugins_url('education-website')."/img/faculty-chevron-@2x.png"
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
	function validate( $output, $input ) {
		
		// Grab default values as base
		$starter = UBC_Education_Theme_Options::default_values( array() );
		

	    // Validate Unit Colour Options A, B, and C
            $starter['education-main-colour'] = UBC_Collab_Theme_Options::validate_text($input['education-main-colour'], $starter['education-main-colour'] );
            $starter['education-gradient-colour'] = UBC_Collab_Theme_Options::validate_text($input['education-gradient-colour'], $starter['education-gradient-colour'] );
            $starter['education-hover-colour'] = UBC_Collab_Theme_Options::validate_text($input['education-hover-colour'], $starter['education-hover-colour'] );
			
	    // Validate Image URLs for Education Branding
			$starter['education-enable-banner'] = (bool)$input['education-enable-banner'];
			$starter['foe-banner-image'] = UBC_Collab_Theme_Options::validate_text($input['foe-banner-image'], $starter['foe-banner-image'] );
			$starter['foe-chevron-image-regular'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-image-regular'], $starter['foe-chevron-image-regular'] );
			$starter['foe-chevron-image-retina'] = UBC_Collab_Theme_Options::validate_text($input['foe-chevron-image-retina'], $starter['foe-chevron-image-retina'] );
			
			$output = array_merge($output, $starter);

           return $output;            
        }
		
	 /**
     * education_theme_styles
     * Adds the Faculty of Education Stylesheet
     */         
		function education_theme_styles()  
			{ 
			  wp_register_style( 'education-clf', plugins_url('education-website') . "/css/global.css", true );
			
			  // enqueing:
			  wp_enqueue_style( 'education-clf' );
		}
					
	 /**
     * output_foe_brand_header
     * Adds the FOE brand header
     */         

	function faculty_plugin_before_header_widget(){ ?>
    
            <div id="dept-brand" class=" row-fluid expand">
                 <div id="department-logo" class="row-fluid">
                   <a title="<?php echo get_bloginfo() ?>"  href="<?php echo get_bloginfo('url') ?>"><?php echo get_bloginfo() ?></a>
               </div>
            </div>
       <?php
                }

         /**
         * output_foe_featured_img
         * Adds featured images to WP pages
         */         
         function output_foe_featured_img(){
            if ( is_page() && ! is_front_page() && ! is_home() ) {
                if (has_post_thumbnail()) {
                  $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(),'full', true);
                      echo "<img class=\"pull-right visible-desktop visible-tablet alignright featured-images-pages\" src=\"" . $image_url[0] ."\" title=\"" . the_title_attribute('echo=0') . "\" alt=\"" . the_title_attribute('echo=0') . "\" />";
				
			} else {
                    echo "";
                }
            }
        }
		
	 /**
     * output_back_to_top
     * Adds the back to top below content
     */         

	function output_back_to_top(){
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
        function wp_head(){ ?>
			<style type="text/css" media="screen">
                #ubc7-unit {
				 background: #002145 !important;
				}
				#ubc7-unit-menu .nav-collapse .nav > li > a:hover, #ubc7-unit-menu .nav > li.active > a:hover, #ubc7-unit-menu .nav-collapse .dropdown-menu a:hover, #ubc7-unit-menu .btn-group:hover .btn, #ubc7-unit-menu .nav > li.active > .btn-group:hover .btn, #ubc7-unit-menu .nav > li.active > a, #ubc7-unit-menu .nav > li.active > .btn-group, #ubc7-unit-menu .current-menu-ancestor, .current-menu-parent, #ubc7-unit-menu .current-page-parent, .current_page_parent, #ubc7-unit-menu .current-page-ancestor, #ubc7-unit-menu .nav > li.current-page-ancestor > a, #ubc7-unit-menu .nav > li.current-page-ancestor .btn-group > a, #ubc7-unit-menu .nav > li.current-page-ancestor.dropdown .btn-group button,  .sidenav a:hover,  .accordion.sidenav .single a.opened,  .accordion.sidenav .single a.opened:hover,  .sidenav .accordion-heading .accordion-toggle:hover,  .sidenav .accordion-group .accordion-inner>a.opened,  .sidenav .accordion-heading:hover,  .sidenav .accordion-heading a:hover,  .sidenav .accordion-heading:hover a:focus,  .sidenav .accordion-heading:hover a:active,  .sidenav .accordion-heading:hover .accordion-toggle,  .simple-custom-menu .active a,  .simple-custom-menu a:hover,  .carousel-caption {
				 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>!important;
				}
				h1,  h2,  h3,  h4,  h5,  h6,  #content .hero-unit h1,  #frontpage-siderbar .tab-pane a,  a {
				 color: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
				}
				a:hover {
				 color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>;
				}
				.sidenav {
				 border: 0;
				 border-bottom: 10px solid <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>!important;
				}
				#dept-brand {
				 background: <?php if (UBC_Collab_Theme_Options::get( 'education-enable-banner') == '1') {
				 echo 'url(' . UBC_Collab_Theme_Options::get('foe-banner-image'). ') ';
				}
				else {
				 echo '';
				}
				?><?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>;
				}
				#department-logo {
				 background-image: url(<?php echo UBC_Collab_Theme_Options::get('foe-chevron-image-regular')?>);
				}
				ul.nav-tabs li a {
				 background: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>!important;
				 border-color: <?php echo UBC_Collab_Theme_Options::get('education-main-colour')?>!important;
				}
				ul.nav-tabs li.active a {
				 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>!important;
				 border-color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>!important;
				}
				ul.nav-tabs>li>a:hover {
				 background: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>!important;
				 border-color: <?php echo UBC_Collab_Theme_Options::get('education-gradient-colour')?>!important;
				}
				 @media(-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
					#department-logo {
					 background-image: url(<?php echo UBC_Collab_Theme_Options::get('foe-chevron-image-retina')?>);
					}
				}
            </style>
<?php 
    } 
}

UBC_Education_Theme_Options::init();
//var_dump( get_option( 'ubc-collab-theme-options' ));


<?php
/*
Plugin Name: Gravity Forms - Avala API Add-On
Plugin URI: http://www.ninthlink.com
Description: A Gravity Forms add-on to connect GForms submits to Avala Aimbase CRM
Version: 1.0
Author: TimS @ Ninthlink
Author URI: http://www.ninthlink.com
Documentation: http://www.gravityhelp.com/documentation/page/GFAddOn

------------------------------------------------------------------------
Copyright 2014 Ninthlink, Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

//exit if accessed directly
if(!defined('ABSPATH')) exit;

//------------------------------------------
if (class_exists("GFForms")) {
    GFForms::include_feed_addon_framework();

    class GFAvalaAPIAddOn extends GFFeedAddOn {

        protected $_version = "1.0";
        protected $_min_gravityforms_version = "1.7.9999";
        protected $_slug = "avala-api-gforms-feed";
        protected $_path = "avala-api-gforms-feed/avala-api-gforms-feed.php";
        protected $_full_path = __FILE__;
        protected $_title = "Avala API Plugin Settings";
        protected $_short_title = "Avala API";

        // custom data vars for use outside class
        public $_avala_result = array();
        // these will be access when creating cutom GForm Fields below
        public $_custom_product_id_list = array();
        public $_default_country = 'US';

        public $_debug;

        // constructor to assign plugin setting data to custom vars above
        public function __construct() {
            parent::__construct();
            $this->_custom_product_id_list = $this->get_plugin_setting('avala_customProductIdList');
            $this->_default_country = $this->get_plugin_setting('avala_defaultCountry');
        }

        // Plugin Settings Page :: Forms -> Avala API Feed
        public function plugin_page() {
            ?>
            <p>Avala API Settings are handled within Gravity Forms settings page at:<br />
                <b>Forms</b> -> <b>Settings</b> -> <b><a href="<?php echo get_bloginfo(); ?>/wp-admin/admin.php?page=gf_settings&subview=avala-api-gforms-feed">Avala API</a>a></b>
            </p>
            <h2>How to use this plugin</h2>
            <h3>A step-by-step guide</h3>
            <ol>
                <li>Update Plugin Settings by going to "Forms -> Settings -> Avala API"<br>
                    You will need the following:
                    <ul style="margin-left: 20px;">
                        <li>Aimbase submit URL(s) - live and/or QA</li>
                        <li>Any custom lead categories, sources, and types not included in this plugin defaults</li>
                        <li>Your product ID list - this can be exported directly from Aimbase</li>
                        <li>Any opt-in list ID(s)</li>
                    </ul>
                </li>
                <li>Create your forms</li>
                <li>Add custom Feeds to your form<br />
                    From the form edit/view page, go to "My Form -> Form Settings -> Avala API Feeds"</li>
                <li>Click "Add New" to create a new feed</li>
                <li>Update your feed settings per your requirements</li>
                <li>Map necessary form fields to your Avala fields to be submitted - some fields are required<br />
                    Hidden fields can be used to pass data not entered by the customer (ie: Brand)</li>
                <li>Set up a feed submit condition as necessary</li>
                <li>Save your changes! You are all set</li>
            </ol>
            <h3>Why do I need conditions for my feeds?</h3>
            <p>A new feed must be created for every variation of form submit. Conditionals allow you to pick and choose which feed will be used at which time, for example if you are changing lead source based on entry.</p>
            <?php
            //wp_redirect( 'admin.php?page=gf_settings&subview=Avala+API+Feed' );
        }

        /**
         *  Feed Settings Fields
         *
         *  Each form uses unique feed settings to connect with Avala. This allows extra refining for each circumstance
         *
         **/
        public function feed_settings_fields() {
            
            // array of settings fields
            $a = array(
                array(
                    "title"  => "Avala API Settings",
                    "fields" => array(
                        array(
                            "label"   => "Avala Feed Name",
                            "type"    => "text",
                            "name"    => "avalaFeedName",
                            "tooltip" => "This is the tooltip",
                            "class"   => "small"
                        ),
                        array(
                            "label"   => "Submit Form to",
                            "type"    => "radio",
                            "name"    => "avalaApiFeedSubmit",
                            "tooltip" => "Production API or Developer API for testing",
                            "choices" => array(
                                array("label" => "Live API", "value" => 1),
                                array("label" => "Developer Mode", "value" => 2),
                                array("label" => "None (do not submit to Avala)", "value" => 0),
                            )
                        ),
                        array(
                            "label"   => "Lead Source",
                            "type"    => "select",
                            "name"    => "avalaLeadsourcename",
                            "tooltip" => "Default Lead Source for this form",
                            "choices" => array(
                                //array("label" => get_bloginfo('name')),
                                array("label" => "--- Default Lead Source(s) ---", "value" => ''),
                                array("label" => "Affiliate"),
                                array("label" => "Billboard"),
                                array("label" => "BRC Card"),
                                array("label" => "Buyerzone-Abandoned"),
                                array("label" => "BuyerZone-Qualified"),
                                array("label" => "Call Center"),
                                array("label" => "Co-Brand Out of Market"),
                                array("label" => "Consumer iPad"),
                                array("label" => "Dealer Import"),
                                array("label" => "Direct Mail"),
                                array("label" => "Historical"),
                                array("label" => "Kiosk"),
                                array("label" => "Media-AdRoll"),
                                array("label" => "Media-FutureAds"),
                                array("label" => "Media-PointRoll"),
                                array("label" => "Media-Rocket_Fuel"),
                                array("label" => "Media-Turn"),
                                array("label" => "Media-Videology"),
                                array("label" => "Microsite"),
                                array("label" => "Newsletter"),
                                array("label" => "Other"),
                                array("label" => "Page Retargeting"),
                                array("label" => "PicMktg"),
                                array("label" => "Print Ad"),
                                array("label" => "Promo"),
                                array("label" => "Radio"),
                                array("label" => "Referral and Rewards"),
                                array("label" => "Rock-n-Roll"),
                                array("label" => "Search - Organic"),
                                array("label" => "Search - Paid"),
                                array("label" => "Sponsored Email"),
                                array("label" => "Television"),
                                array("label" => "Third Party"),
                            ),
                        ),
                        array(
                            "label"   => "Lead Category",
                            "type"    => "select",
                            "name"    => "avalaLeadcategoryname",
                            "tooltip" => "Default Lead Category for this form",
                            "choices" => array(
                                //array("label" => get_bloginfo('name')),
                                array("label" => "--- Default Lead Category(s) ---", "value" => ''),
                                array("label" => "Affiliate"),
                                array("label" => "Buyerzone"),
                                array("label" => "Co-Brand"),
                                array("label" => "Dealer Entry"),
                                array("label" => "Display Advertising"),
                                array("label" => "Email"),
                                array("label" => "Event"),
                                array("label" => "Other"),
                                array("label" => "Print Ad"),
                                array("label" => "Referral Program"),
                                array("label" => "Third Party"),
                            ),
                        ),
                        array(
                            "label"   => "Lead Type",
                            "type"    => "select",
                            "name"    => "avalaLeadtypename",
                            "tooltip" => "Default Lead Category for this form",
                            "choices" => array(
                                //array("label" => get_bloginfo('name')),
                                array("label" => "--- Default Lead Type(s) ---", "value" => ''),
                                array("label" => "Campaign"),
                                array("label" => "Contact Dealer"),
                                array("label" => "Dealer Entry"),
                                array("label" => "Request Appointment"),
                                array("label" => "Request Brochure Download"),
                                array("label" => "Request Brochure Download & DVD"),
                                array("label" => "Request Brochure Mail"),
                                array("label" => "Request Brochure Mail & Download"),
                                array("label" => "Request Brochure Mail & Download & DVD"),
                                array("label" => "Request Brochure Mail & DVD"),
                                array("label" => "Request Buyer's Guide"),
                                array("label" => "Request DVD"),
                                array("label" => "Request Financing"),
                                array("label" => "Request Quote"),
                                array("label" => "Request Test Drive"),
                                array("label" => "Request Trade In"),
                                array("label" => "Subscriber"),
                                array("label" => "Sweepstakes"),
                                array("label" => "Truck Load"),
                                array("label" => "Other"),
                            ),
                        ),
                        array(
                            "name" => "avalaMappedFields_Contact",
                            "label" => "Map Contact Fields",
                            "type" => "field_map",
                            "tooltip" => "Map each Avala Field to Gravity Form Field",
                            "field_map" => array(
                                array("name" => "FirstName","label" => "First Name","required" => 1),
                                array("name" => "LastName","label" => "Last Name","required" => 1),
                                array("name" => "EmailAddress","label" => "Email Address","required" => 1),
                                array("name" => "HomePhone","label" => "Phone (Home)","required" => 0),
                                array("name" => "MobilePhone","label" => "Phone (Mobile)","required" => 0),
                                array("name" => "WorkPhone","label" => "Phone (Work)","required" => 0),
                                array("name" => "Comments","label" => "Comments","required" => 0),
                            )
                        ),
                        array(
                            "name" => "avalaMappedFields_Address",
                            "label" => "Map Address Fields",
                            "type" => "field_map",
                            "tooltip" => "Map each Avala Field to Gravity Form Field",
                            "field_map" => array(
                                array("name" => "Address1","label" => "Address","required" => 0),
                                array("name" => "Address2","label" => "Address (line 2)","required" => 0),
                                array("name" => "City","label" => "City","required" => 0),
                                array("name" => "State","label" => "State","required" => 0),
                                array("name" => "CountryCode","label" => "Country","required" => 0),
                                array("name" => "PostalCode","label" => "Zip / Postal Code","required" => 1),
                            )
                        ),
                        array(
                            "name" => "avalaMappedFields_Subscription",
                            "label" => "Map Subscription Fields",
                            "type" => "field_map",
                            "tooltip" => "Map each Avala Field to Gravity Form Field",
                            "field_map" => array(
                                array("name" => "RecieveEmailCampaigns","label" => "<code>Recieve Email Campaigns</code><br /><small>Please send me exclusive sale alerts...</small>","required" => 0),
                                array("name" => "ReceiveNewsletter","label" => "<code>Receive Newsletter</code>","required" => 0),
                                array("name" => "ReceiveSmsCampaigns","label" => "<code>Receive SMS Campaigns</code>","required" => 0),
                            )
                        ),
                        array(
                            "name" => "avalaMappedFields_AddlData",
                            "label" => "Map Additional Fields",
                            "type" => "field_map",
                            "tooltip" => "Map each Avala Field to Gravity Form Field",
                            "field_map" => array(
                                array("name" => "AccountId","label" => "Account Id","required" => 0),
                                array("name" => "Brand","label" => "Brand","required" => 0),
                                array("name" => "Campaign","label" => "Campaign","required" => 0),
                                array("name" => "CampaignId","label" => "Campaign Id","required" => 0),
                                array("name" => "DealerId","label" => "Dealer Id","required" => 0),
                                array("name" => "DealerNumber","label" => "Dealer Number","required" => 0),
                                array("name" => "ExactTargetOptInListIds","label" => "Exact Target Opt-In List Ids","required" => 0),
                                array("name" => "ExactTargetCustomAttributes","label" => "Exact Target Custom Attributes","required" => 0),
                                array("name" => "LeadDate","label" => "Lead Date","required" => 0),
                                array("name" => "ProductCode","label" => "Product Code","required" => 0),
                                array("name" => "TriggeredSend","label" => "Triggered Send","required" => 0),
                                array("name" => "ProductIdList","label" => "<code>Product Id List</code><br /><small>Which product are you interested in purchasing most?</small>","required" => 0),
                            ),
                        ),
                        array(
                            "name" => "avalaMappedFields_CustomData",
                            "label" => "Map Custom Data Fields",
                            "type" => "field_map",
                            "tooltip" => "Map each Avala Field to Gravity Form Field",
                            "field_map" => array(
                                array("name" => "PromoCode","label" => "<code>Promo Code</code>","required" => 0),
                                array("name" => "Event","label" => "<code>Event</code>","required" => 0),
                                array("name" => "CurrentlyOwn","label" => "<code>Currently Own</code><br /><small>Do you currently own or have you ever owned a hot tub?</small>","required" => 0),
                                array("name" => "InterestedInOwning","label" => "<code>Interested In Owning</code><br /><small>Are you interested in owning a hot tub? (yes / no)</small><br /><small>For <i>Product Interest In</i> see additional fields above</small>","required" => 0),
                                array("name" => "BuyTimeFrame","label" => "<code>Buy Time Frame</code><br /><small>When do you plan to purchase?</small>","required" => 0),
                                array("name" => "HomeOwner","label" => "<code>Home Owner</code><br /><small>Are you a home owner?</small>","required" => 0),
                                array("name" => "ProductUse","label" => "<code>Product Use</code><br /><small>What is the primary reason you are considering purchase?</small>","required" => 0),
                                array("name" => "TradeInMake","label" => "<code>Trade In Make</code>","required" => 0),
                                array("name" => "TradeInYear","label" => "<code>Trade In Year</code>","required" => 0),
                                array("name" => "Condition","label" => "<code>Trade In Condition</code>","required" => 0),
                                array("name" => "PayoffLeft","label" => "<code>Payoff Left</code>","required" => 0),
                            )
                        ),
                        
                    )
                ),
                array(
                    "title"  => "Web Session Data",
                    "fields" => array(
                        array(
                            "label"   => "Medium / Source",
                            "type"    => "text",
                            "name"    => "avalaMediumSource",
                            "tooltip" => "For example \"Adwords\". Use conditional settings below to process feed accordingly.",
                            "class"   => "small"
                        ),
                        array(
                            "name" => "avalaMappedFields_WebSession",
                            "label" => "Mapped Fields",
                            "type" => "field_map",
                            "tooltip" => "Map each Avala Field to Gravity Form Field",
                            "field_map" => array(
                                array("name" => "DeliveryMethod","label" => "<code>Delivery Method</code>","required" => 0),
                                //array("name" => "Medium","label" => "Medium / Source","required" => 0),
                                //array("name" => "KeyWords","label" => "Key Words","required" => 0),
                                //array("name" => "PagesViewed","label" => "Pages Viewed","required" => 0),
                                //array("name" => "PageViews","label" => "Page Views","required" => 0),
                                //array("name" => "TimeOnSite","label" => "Time On Site","required" => 0),
                            )
                        )
                    )
                ),
                array(
                    "title"  => "Feed Settings",
                    "fields" => array(
                        array(
                            "name" => "avalaCondition",
                            "label" => __("Conditional", "avala-api-gforms-feed"),
                            "type" => "feed_condition",
                            "checkbox_label" => __('Enable Feed Condition', 'avala-api-gforms-feed'),
                            "instructions" => __("Process this Avala feed if", "avala-api-gforms-feed")
                        ),
                    )
                )
            );

            // add Custom Lead Source plugin settings field to feed settings field array
            if ( $this->get_plugin_setting('avala_customLeadSource') ) {
                $custom_lead_source = explode( "\r\n", $this->get_plugin_setting('avala_customLeadSource') );
                $a[0]['fields'][2]['choices'][] = array("label" => '--- Custom Lead Source(s) ---', "value" => '');
                foreach ($custom_lead_source as $key => $value) {
                    $a[0]['fields'][2]['choices'][] = array("label" => $value, "value" => $value);
                }
            }

            // add Custom Lead Category plugin settings field to feed settings field array
            if ( $this->get_plugin_setting('avala_customLeadCategory') ) {
                $custom_lead_category = explode( "\r\n", $this->get_plugin_setting('avala_customLeadCategory') );
                $a[0]['fields'][3]['choices'][] = array("label" => '--- Custom Lead Category(s) ---', "value" => '');
                foreach ($custom_lead_category as $key => $value) {
                    $a[0]['fields'][3]['choices'][] = array("label" => $value, "value" => $value);
                }
            }

            // add Custom Lead Type plugin settings field to feed settings field array
            if ( $this->get_plugin_setting('avala_customLeadType') ) {
                $custom_lead_type = explode( "\r\n", $this->get_plugin_setting('avala_customLeadType') );
                $a[0]['fields'][4]['choices'][] = array("label" => '--- Custom Lead Type(s) ---', "value" => '');
                foreach ($custom_lead_type as $key => $value) {
                    $a[0]['fields'][4]['choices'][] = array("label" => $value, "value" => $value);
                }
            }

            return $a;
        }

        /**
         *  Columns displayed on Feed overview / list page
         *
         **/
        public function feed_list_columns() {
            return array(
                'avalaFeedName' => __('Name', 'avala-api-gforms-feed'),
                'avalaApiFeedSubmit' => __('Submit To', 'avala-api-gforms-feed'),
                'avalaLeadsourcename' => __('Lead Source', 'avala-api-gforms-feed'),
                'avalaLeadcategoryname' => __('Lead Category', 'avala-api-gforms-feed'),
                'avalaLeadtypename' => __('Lead Type', 'avala-api-gforms-feed'),
                'avalaCondition' => __('Condition(s)', 'avala-api-gforms-feed'),
            );
        }
        // customize the value of mytext before it is rendered to the list
        public function get_column_value_avalaCondition( $feed ){
            $output = 'N/A';
            $rules = array();
            if ( $feed['meta']['feed_condition_conditional_logic'] == 1 ) {
                foreach ( $feed['meta']['feed_condition_conditional_logic_object']['conditionalLogic']['rules'] as $key => $value ) {
                    $rules[] = sprintf( 'field_%d %s %s' , $value['fieldId'], ( $value['operator'] === 'is' ? 'is' : 'is not' ), $value['value'] );
                }
                $andor = $feed['meta']['feed_condition_conditional_logic_object']['conditionalLogic']['logicType'] === 'any' ? 'or' : 'and';
                $output = implode(', ' . $andor . ' ', $rules);
            }
            return $output;
        }

        /**
         *  Change numeric field to textual output on overview page for human readability
         *
         **/
        public function get_column_value_avalaApiFeedSubmit($feed) {
            $output = ( $feed["meta"]["avalaApiFeedSubmit"] == 1 ) ? 'Live' : ( ( $feed["meta"]["avalaApiFeedSubmit"] == 2 ) ? 'Dev' : 'N/A' );
            return "<b>" . $output ."</b>";
        }

        /**
         *  Plugin Settings Fields
         *
         *  These setting apply to entire plugin, not just individual feeds
         *
         **/
        public function plugin_settings_fields() {
            return array(
                array(
                    "title"  => "Avala API Settings",
                    "fields" => array(
                        array(
                            "name"    => "avala_liveApiUrl",
                            "tooltip" => "URL for production CURL submits",
                            "label"   => "Live API URL",
                            "type"    => "text",
                            "class"   => "medium"
                        ),
                        array(
                            "name"    => "avala_devApiUrl",
                            "tooltip" => "URL for development CURL submits",
                            "label"   => "Dev API URL",
                            "type"    => "text",
                            "class"   => "medium"
                        ),
                        array(
                            "name"    => "avala_customLeadCategory",
                            "tooltip" => "Add your own Lead Category(ies), one per line",
                            "label"   => "Custom Lead Category(ies)",
                            "type"    => "textarea",
                            "class"   => "small"
                        ),
                        array(
                            "name"    => "avala_customLeadSource",
                            "tooltip" => "Add your own Lead Source(s), one per line",
                            "label"   => "Custom Lead Source(s)",
                            "type"    => "textarea",
                            "class"   => "small"
                        ),
                        array(
                            "name"    => "avala_customLeadType",
                            "tooltip" => "Add your own Lead Type(s), one per line",
                            "label"   => "Custom Lead Type(s)",
                            "type"    => "textarea",
                            "class"   => "small"
                        ),
                        array(
                            "name"    => "avala_customProductIdList",
                            "tooltip" => "Add Product List in the form of<br />\"Product Name, Product ID#\"<br />(without quotes), one per line<br />Adds custom advanced field if used",
                            "label"   => "Product ID List",
                            "type"    => "textarea",
                            "class"   => "small"
                        ),
                        array(
                            "name"    => "avala_defaultOptInListId",
                            "tooltip" => "ID(s) used for opt-in lists (provided by Avala)<br />Comma seperated values",
                            "label"   => "Opt-In List ID(s)",
                            "type"    => "text",
                            "class"   => "small"
                        ),
                        array(
                            "name"    => "avala_defaultCountry",
                            "tooltip" => "Lead country will default to this value if no user entry<br/>Uses \"US\" if this field left blank",
                            "label"   => "Default Country",
                            "type"    => "radio",
                            "class"   => "small",
                            "choices" => array(
                                array("label" => "United States (US)", "value" => "US"),
                                array("label" => "Canada (CA)", "value" => "CA"),
                            )
                        ),
                        array(
                            "name"    => "avala_defaultPostalCode",
                            "tooltip" => "Default postal code to be used if no user entry<br />Uses \"00000\" if this field left blank",
                            "label"   => "Default Postal Code",
                            "type"    => "text",
                            "class"   => "small"
                        ),
                        array(
                            "name"    => "avala_debugMode",
                            "tooltip" => "Show debug arrays on all form submits",
                            "label"   => "Debug Mode",
                            "type"    => "radio",
                            "class"   => "small",
                            "choices" => array(
                                array("label" => "On", "value" => "1"),
                                array("label" => "Off", "value" => "0"),
                            ),
                        ),
                    ),
                ),
            );
        }

        /**
         *  Plugin Scripts
         *
         *  Call scripts that we may want to run on form pages
         *
         **/
        public function scripts() {
            $scripts = array(
                array("handle"  => "avala_api_script_js",
                      "src"     => $this->get_base_url() . "/js/avala_api_script.js",
                      "version" => $this->_version,
                      "deps"    => array("jquery"),
                      // [strings] An array of strings that can be accessed in JavaScript through the global variable [script handle]_strings
                      "strings" => array(
                          'first'  => __("First Choice", "avala-api-gforms-feed"),
                          'second' => __("Second Choice", "avala-api-gforms-feed"),
                          'third'  => __("Third Choice", "avala-api-gforms-feed")
                      ),
                      "enqueue" => array(
                          array(
                              "admin_page" => array("form_settings"),
                              "tab"        => "avala-api-gforms-feed"
                          )
                      )
                ),

            );

            return array_merge(parent::scripts(), $scripts);
        }

        /**
         *  Plugin Styles
         *
         *  Call styles that we may want apply on form pages
         *
         **/
        public function styles() {

            $styles = array(
                // call style on the admin page: form-editor
                // for example, in this case we change the color of Avala specific advanced form fields (for differentiation)
                array("handle"  => "avala_api_styles_form_edit_css",
                      "src"     => $this->get_base_url() . "/css/avala_api_styles_form_edit.css",
                      "version" => $this->_version,
                      "enqueue" => array(
                          array("admin_page" => array("form_editor"))
                      )
                ),
                array("handle"  => "avala_api_styles_frontend_css",
                      "src"     => $this->get_base_url() . "/css/avala_api_styles_frontend.css",
                      "version" => $this->_version,
                      "enqueue" => array(
                          array("admin_page" => array("results"))
                      )
                )
            );

            return array_merge(parent::styles(), $styles);
        }

        /**
         *  Feed Processor
         *
         *  This is the nuts and bolts: all actions to happen on form submit happen here
         *  Feed processing happens after submit, but before page redirect/thanks message
         *
         **/
        public function process_feed($feed, $entry, $form){

            // working vars
            $avalaApiFeedSubmit = $feed['meta']['avalaApiFeedSubmit'];
            $url = null;
			
			// current user info
			global $current_user;
			get_currentuserinfo();

            // get submit to location (exit if none)
            if ( $avalaApiFeedSubmit == 1 ) :
                $url = $this->get_plugin_setting('avala_liveApiUrl'); // submit to live
            elseif ( $avalaApiFeedSubmit == 2 ) :
                $url = $this->get_plugin_setting('avala_devApiUrl'); // submit to dev
            else :
                return false; // do nothing - GForm submits as normal without Avala API
            endif;

            // we will use Google Analytics cookies for some data if available
            if ( isset($_COOKIE['__utmz']) && !empty($_COOKIE['__utmz']) )
                $ga_cookie = $this->parse_ga_cookie( $_COOKIE['__utmz'] );

            // The full array of data that will be translated into Avala API data
            $jsonArray = array(
                'LeadSourceName'                => $feed['meta']['avalaLeadsourcename'],
                'LeadTypeName'                  => $feed['meta']['avalaLeadtypename'],
                'LeadCategoryName'              => $feed['meta']['avalaLeadcategoryname'],
                //mapped fields - contact
                'FirstName'                     => is_user_logged_in() ? $current_user->user_firstname : '',
                'LastName'                      => is_user_logged_in() ? $current_user->user_lastname : '',
                'EmailAddress'                  => is_user_logged_in() ? $current_user->user_email : '',
                'HomePhone'                     => '',
                'MobilePhone'                   => '',
                'WorkPhone'                     => '',
                'Comments'                      => '',
                //mapped fields - address
                'Address1'                      => '',
                'Address2'                      => '',
                'City'                          => '',
                'State'                         => '',
                'County'                        => '',
                'District'                      => '',
                'CountryCode'                   => $this->get_plugin_setting('avala_defaultCountry'),
                'PostalCode'                    => ( $this->get_plugin_setting('avala_defaultPostalCode') != '' ) ? $this->get_plugin_setting('avala_defaultPostalCode') : '00000',
                //mapped fields - subscription
                'RecieveEmailCampaigns'         => '',
                'ReceiveNewsletter'             => '',
                'ReceiveSmsCampaigns'           => '',
                //mapped fields - addl data
                'AccountId'                     => '',
                'Brand'                         => '',
                'Campaign'                      => '',
                'CampaignId'                    => '',
                'DealerId'                      => '',
                'DealerNumber'                  => '',
                'ExactTargetOptInListIds'       => ( $this->get_plugin_setting('avala_defaultOptInListId') ) ? $this->get_plugin_setting('avala_defaultOptInListId') : '',
                'ExactTargetCustomAttributes'   => '',
                'LeadDate'                      => '',
                'ProductCode'                   => '',
                'ProductIdList'                 => '',
                'TriggeredSend'                 => '',
                //mapped fields - custom data
                'CustomData'                    => array(
                    'BuyTimeFrame'              => '',
                    'Condition'                 => '',
                    'CurrentlyOwn'              => '',
                    'HomeOwner'                 => '',
                    'InterestedInOwning'        => '',
                    'PayoffLeft'                => '',
                    'ProductUse'                => '',
                    'TradeInMake'               => '',
                    'TradeInYear'               => '',
                    'PromoCode'                 => '',
                    'Event'                     => '',
                    ),
                //mapped fields - websession data
                'WebSessionData'                => array(
                    'DeliveryMethod'            => '',
                    'FormPage'                  => $entry['source_url'],
                    'IPaddress'                 => $entry['ip'],
                    'KeyWords'                  => ( isset($ga_cookie['keyword']) && !empty($ga_cookie['keyword']) ) ? $ga_cookie['keyword'] : '',
                    'Medium'                    => ( isset($feed['meta']['avalaMediumSource']) ? $feed['meta']['avalaMediumSource'] : ( ( isset($ga_cookie['medium']) && !empty($ga_cookie['medium']) ) ? $ga_cookie['medium'] : '' ) ),
                    'PagesViewed'               => $this->get_pages_viewed(),
                    'PageViews'                 => $this->get_page_views(),
                    'TimeOnSite'                => $this->get_time_on_site(),
                    'Useragent'                 => $entry['user_agent'],
                    'VisitCount'                => ( isset($ga_cookie['visits']) && !empty($ga_cookie['visits']) ) ? $ga_cookie['visits'] : 1,
                    ),
            );

            // iterate over meta data mapped fields (from feed fields) and apply to the big array above
            foreach ($feed['meta'] as $k => $v) {
                $l = explode("_", $k);
                if ( $l[0] == 'avalaMappedFields' ) {
                    if ( $l[1] == 'CustomData' && array_key_exists( $l[2], $jsonArray['CustomData'] ) && !empty( $v ) ) :
                        $jsonArray['CustomData'][ $l[2] ] = $entry[ $v ];
                    elseif ( $l[1] == 'WebSession' && array_key_exists( $l[2], $jsonArray['WebSessionData'] ) && !empty( $v ) ) :
                        $jsonArray['WebSessionData'][ $l[2] ] = $entry[ $v ];
                    elseif ( array_key_exists( $l[2], $jsonArray ) && !empty( $v ) ) :
                        $jsonArray[ $l[2] ] = $entry[ $v ];
                    endif;
                }
            }
            
            // Remove empty ARRAY fields so we do not submit blank data
            $jsonArray['CustomData'] = array_filter( $jsonArray['CustomData'] );
            $jsonArray['WebSessionData'] = array_filter( $jsonArray['WebSessionData'] );
            $jsonArray = array_filter( $jsonArray );

            // wrap string in [ ] per Avala API requirements
            $jsonString = '[' . json_encode( $jsonArray ) . ']';

            // cURL :: this sends off the data to Avala
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonString);
            curl_setopt($ch, CURLOPT_PROXY, null);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($jsonString) ) );
            $apiResult = curl_exec($ch);
            $httpResult = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $result = array( 0 => $httpResult, 1 => $apiResult );

            // debug things
            if ( $this->get_plugin_setting('avala_debugMode') == 1 )
            {
                $this->_avala_result['cURL'] = $result;
                $this->_avala_result['JSON'] = $jsonArray;
                $this->_avala_result['FEED'] = $feed;
                $this->_avala_result['ENTRY'] = $entry;
                add_action('wp_footer', array( $this, 'avala_debug') );
                add_filter("gform_confirmation", "avala_debug_confirm", 10, 4);
            }
            
        }

        /**
         *  helper functions
         *
         *  Useful functions for parsing data, formatting, etc.
         *
         **/

        // Debug builder
        public function avala_debug()
        {
            $arrays = $this->_avala_result;
            $o = '<div id="avala-gform-debug" class=""><h3>Avala Debug Details</h3><hr>';
            foreach ($arrays as $array => $value)
            {
                $o .='<h4>'.$array.'</h4><pre>'.print_r($value, true).'</pre><hr>';
            }
            $o .= '</div>';
            if ( current_user_can( 'activate_plugins' ) )
                print($o);
        }
        public function avala_debug_confirm($confirmation, $form, $lead, $ajax)
        {
            $arrays = $this->_avala_result;
            $o = '<div id="avala-gform-debug" class="avala_confirm"><h3>Avala Debug Details</h3><hr>';
            foreach ($arrays as $array => $value)
            {
                $o .='<h4>'.$array.'</h4><pre>'.print_r($value, true).'</pre><hr>';
            }
            $o .= '</div>';
            if ( current_user_can( 'activate_plugins' ) )
                return $o;
            return false;
        }

        // Google Analytics cookie parser
        public function parse_ga_cookie($cookie)
        {
            $values = sscanf( $cookie, "%d.%d.%d.%d.utmcsr=%[^|]|utmccn=%[^|]|utmcmd=%[^|]|utmctr=%[^|]");
            $keys = array('domain', 'timestamp', 'visits', 'sources', 'campaign', 'source', 'medium', 'keyword');
            return array_combine($keys, $values);
        }

        // get pages viewed from cookie
        public function get_pages_viewed( $pages = true )
        {
            // Custom cookie reader :: requires "nlk-custom-shortcodes" plugin to generate these tracking cookies
            if ( ! function_exists( 'is_plugin_active') )
                include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            if ( is_plugin_active( 'nlk-custom-shortcodes/nlk-custom-shortcodes.php' ) )
            {
                if ( !empty( $_COOKIE['__nlkpv'] ) )
                {
                    $nlkc =  json_decode( str_replace('\"', '"', $_COOKIE['__nlkpv'] ), true );
                    $i = 0;
                    $li = '';
                    foreach ($nlkc as $k => $v)
                    {
                        $li .= '<li><a href="'.$v['url'].'">'.$v['title'].'</a></li>';
                        $i++;
                    }
                    if( $pages )
                    {
                        return sprintf( '<ul>%s</ul>', $li ); // pages viewed as unordered list
                    }
                    else
                    {
                        return $i; // page views count
                    }
                }
            }
            return false; // if nlk-custom-shortcodes plugin not installed OR cookie is empty
        }
        public function get_page_views()
        {
            return $this->get_pages_viewed(false);
        }

        // get time on site from cookie
        public function get_time_on_site()
        {
            if ( !empty( $_COOKIE['__nlken'] ) )
            {
                $then = $_COOKIE['__nlken'];
                $now = time();
                $tos = $now - $then;
                return gmdate("H:i:s", $tos);
            }
            return false;
        }

        // Phone number formatter
        public function format_phone( $phone = '', $format='standard', $convert = true, $trim = true )
        {
            if ( empty( $phone ) ) {
                return false;
            }
            // Strip out non alphanumeric
            $phone = preg_replace( "/[^0-9A-Za-z]/", "", $phone );
            // Keep original phone in case of problems later on but without special characters
            $originalPhone = $phone;
            // If we have a number longer than 11 digits cut the string down to only 11
            // This is also only ran if we want to limit only to 11 characters
            if ( $trim == true && strlen( $phone ) > 11 ) {
                $phone = substr( $phone, 0, 11 );
            }
            // letters to their number equivalent
            if ( $convert == true && !is_numeric( $phone ) ) {
                $replace = array(
                    '2'=>array('a','b','c'),
                    '3'=>array('d','e','f'),
                    '4'=>array('g','h','i'),
                    '5'=>array('j','k','l'),
                    '6'=>array('m','n','o'),
                    '7'=>array('p','q','r','s'),
                    '8'=>array('t','u','v'),
                    '9'=>array('w','x','y','z'),
                    );
                foreach ( $replace as $digit => $letters ) {
                    $phone = str_ireplace( $letters, $digit, $phone );
                }
            }
            $a = $b = $c = $d = null;
            switch ( $format ) {
                case 'decimal':
                case 'period':
                    $a = '';
                    $b = '.';
                    $c = '.';
                    $d = '.';
                    break;
                case 'hypen':
                case 'dash':
                    $a = '';
                    $b = '-';
                    $c = '-';
                    $d = '-';
                    break;
                case 'space':
                    $a = '';
                    $b = ' ';
                    $c = ' ';
                    $d = ' ';
                    break;
                case 'standard':
                default:
                    $a = '(';
                    $b = ') ';
                    $c = '-';
                    $d = '(';
                    break;
            }
            $length = strlen( $phone );
            // Perform phone number formatting here
            switch ( $length ) {
                case 7:
                    // Format: xxx-xxxx / xxx.xxxx / xxx-xxxx / xxx xxxx
                    return preg_replace( "/([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1$c$2", $phone );
                case 10:
                    // Format: (xxx) xxx-xxxx / xxx.xxx.xxxx / xxx-xxx-xxxx / xxx xxx xxxx
                    return preg_replace( "/([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$a$1$b$2$c$3", $phone );
                case 11:
                    // Format: x(xxx) xxx-xxxx / x.xxx.xxx.xxxx / x-xxx-xxx-xxxx / x xxx xxx xxxx
                    return preg_replace( "/([0-9a-zA-Z]{1})([0-9a-zA-Z]{3})([0-9a-zA-Z]{3})([0-9a-zA-Z]{4})/", "$1$d$2$b$3$c$4", $phone );
                default:
                    // Return original phone if not 7, 10 or 11 digits long
                    return $originalPhone;
            }
        }

        /**
         *  END of AVAL API ADD-ON CLASS
         *
         **/
    }



    // Instantiate the class - this triggers everything, makes the magic happen
    $gfa = new GFAvalaAPIAddOn();


    /**
     *  Custom Gravity Form Fields
     *
     *  The following fields added are Avala specific fields
     *
     **/
    if ( $gfa )
    {
        /**
         *  Product ID List - Custom Advanced Field
         *
         */
        if ( !empty( $gfa->_custom_product_id_list ) )
        {
            add_action( "gform_field_input" , "avala_field_product_id_input", 10, 5 );
            function avala_field_product_id_input( $input, $field, $value, $lead_id, $form_id ) {
                global $gfa;
                if ( $field["type"] == "avalaFieldProductId" ) {
                    $options = explode( "\r\n", $gfa->_custom_product_id_list );
                    $opts = '';
                    foreach ($options as $option) {
                        $o = explode(',', $option);
                        //$selected = ( trim($o[1]) == $value || trim($o[0]) == $value ) ? 'selected="selected"' : '';
                        $opts .= '<option value="' . trim($o[1]) . '" >' . trim($o[0]) . '</option>';
                    }
                    $input_name = $form_id .'_' . $field["id"];
                    $tabindex = GFCommon::get_tabindex();
                    $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
                    return sprintf("<div class=\"ginput_container\"><select name=\"%s\" id=\"%s\" class=\"%s gfield_select\" $tabindex >%s</select></div>", 'input_'.$field["id"], 'input_'.$input_name, $field["type"] . ' ' . esc_attr( $css ) . ' ' . $field['size'], $opts);
                }
                return $input;
            }
        }

        /**
         *  Purchase Timeframe - Custom Advanced Field
         *
         */
        add_action( "gform_field_input" , "avala_field_purchase_timeframe_input", 10, 5 );
        function avala_field_purchase_timeframe_input( $input, $field, $value, $lead_id, $form_id )
        {
            if ( $field["type"] == "avalaFieldPurchaseTimeframe" ) {
                $input_name = $form_id .'_' . $field["id"];
                $tabindex = GFCommon::get_tabindex();
                $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
                $opts = '<option value="">Not Selected</option>
                        <option value="Within 1 month">Within 1 month</option>
                        <option value="1-3 months">1-3 months</option>
                        <option value="4-6 months">4-6 months</option>
                        <option value="6+ months">6+ months</option>';
                return sprintf("<div class=\"ginput_container\"><select name=\"%s\" id=\"%s\" class=\"%s gfield_select\" $tabindex >%s</select></div>", 'input_'.$field["id"], 'input_'.$input_name, $field["type"] . ' ' . esc_attr( $css ) . ' ' . $field['size'], $opts);
            }
            return $input;
        }

        /**
         *  Product Use - Custom Advanced Field
         *
         */
        add_action( "gform_field_input" , "avala_field_product_use_input", 10, 5 );
        function avala_field_product_use_input( $input, $field, $value, $lead_id, $form_id )
        {
            if ( $field["type"] == "avalaFieldProductUse" ) {
                $input_name = $form_id .'_' . $field["id"];
                $tabindex = GFCommon::get_tabindex();
                $css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
                $opts = '<option value="">Not Selected</option>
                        <option value="Relaxation">Relaxation</option>
                        <option value="Pain Relief/Therapy">Pain Relief/Therapy</option>
                        <option value="Bonding/Family">Bonding/Family</option>
                        <option value="Backyard Entertaining">Backyard Entertaining</option>';
                return sprintf("<div class=\"ginput_container\"><select name=\"%s\" id=\"%s\" class=\"%s gfield_select\" $tabindex >%s</select></div>", 'input_'.$field["id"], 'input_'.$input_name, $field["type"] . ' ' . esc_attr( $css ) . ' ' . $field['size'], $opts);
            }
            return $input;
        }

        /**
         *  This code adds new buttons to the Advanced Fields section in form creator
         */
        add_filter("gform_add_field_buttons", "add_avala_product_id_field");
        function add_avala_product_id_field($field_groups)
        {
            foreach($field_groups as &$group)
            {
                if($group["name"] == "advanced_fields")
                {
                    $group["fields"][] = array(
                        "class"=>"button avala-button",
                        "value" => __("Select Product", "avala-api-gforms-feed"),
                        "onclick" => "StartAddField('avalaFieldProductId');"
                        );
                    $group["fields"][] = array(
                        "class"=>"button avala-button",
                        "value" => __("Buy Timeframe", "avala-api-gforms-feed"),
                        "onclick" => "StartAddField('avalaFieldPurchaseTimeframe');"
                        );
                    $group["fields"][] = array(
                        "class"=>"button avala-button",
                        "value" => __("Product Use", "avala-api-gforms-feed"),
                        "onclick" => "StartAddField('avalaFieldProductUse');"
                        );
                    break;
                }
            }
            return $field_groups;
        }

        /**
         *  This block sets the field display name (left side block title) in form creator
         */
        add_filter( 'gform_field_type_title' , 'avala_field_titles' );
        function avala_field_titles( $type )
        {
            switch( $type )
            {
                case 'avalaFieldProductUse':
                    return __( 'Select Product Use' , 'avala-api-gforms-feed' );
                    break;
                case 'avalaFieldPurchaseTimeframe':
                    return __( 'Select Purchase Timeframe' , 'avala-api-gforms-feed' );
                    break;
                case 'avalaFieldProductId':
                    return __( 'Select Product ID' , 'avala-api-gforms-feed' );
                    break;
            }
        }

        /*
         *  Javascript technicalitites for the field to load correctly and to display default/custom field options
         */
        add_action( "gform_editor_js", "avala_field_gform_editor_js" );
        function avala_field_gform_editor_js()
        {
            ?>
            <script type='text/javascript'>
                jQuery(document).ready(function($){
                    fieldSettings["avalaFieldProductId"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .visibility_setting, .avalaConditional_logic_field_setting";
                    fieldSettings["avalaFieldProductUse"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .visibility_setting, .avalaConditional_logic_field_setting";
                    fieldSettings["avalaFieldPurchaseTimeframe"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .error_message_setting, .css_class_setting, .visibility_setting, .avalaConditional_logic_field_setting";
                });
            </script>
            <?php
        }
    }


    // Rewrite Gravity Forms field Country (select values) as Country Codes instead of country name :: eg CA instead of Canada
    // This is necessary for Avala API to understand data
    // An alternative would be to use a filter function to do this during Feed Processing, but here we can also set US and CA as our top two choices
    add_filter("gform_countries", "change_countries");
    function change_countries($countries)
    {
        return array( "US" => __('UNITED STATES', 'gravityforms'), "CA" => __('CANADA', 'gravityforms'), "AF" => __('AFGHANISTAN', 'gravityforms'), "AL" => __('ALBANIA', 'gravityforms'), "DZ" => __('ALGERIA', 'gravityforms'), "AS" => __('AMERICAN SAMOA', 'gravityforms'), "AD" => __('ANDORRA', 'gravityforms'), "AO" => __('ANGOLA', 'gravityforms'), "AG" => __('ANTIGUA AND BARBUDA', 'gravityforms'), "AR" => __('ARGENTINA', 'gravityforms'), "AM" => __('ARMENIA', 'gravityforms'), "AU" => __('AUSTRALIA', 'gravityforms'), "AT" => __('AUSTRIA', 'gravityforms'), "AZ" => __('AZERBAIJAN', 'gravityforms'), "BS" => __('BAHAMAS', 'gravityforms'), "BH" => __('BAHRAIN', 'gravityforms'), "BD" => __('BANGLADESH', 'gravityforms'), "BB" => __('BARBADOS', 'gravityforms'), "BY" => __('BELARUS', 'gravityforms'), "BE" => __('BELGIUM', 'gravityforms'), "BZ" => __('BELIZE', 'gravityforms'), "BJ" => __('BENIN', 'gravityforms'), "BM" => __('BERMUDA', 'gravityforms'), "BT" => __('BHUTAN', 'gravityforms'), "BO" => __('BOLIVIA', 'gravityforms'), "BA" => __('BOSNIA AND HERZEGOVINA', 'gravityforms'), "BW" => __('BOTSWANA', 'gravityforms'), "BR" => __('BRAZIL', 'gravityforms'), "BN" => __('BRUNEI', 'gravityforms'), "BG" => __('BULGARIA', 'gravityforms'), "BF" => __('BURKINA FASO', 'gravityforms'), "BI" => __('BURUNDI', 'gravityforms'), "KH" => __('CAMBODIA', 'gravityforms'), "CM" => __('CAMEROON', 'gravityforms'), "CA" => __('CANADA', 'gravityforms'), "CV" => __('CAPE VERDE', 'gravityforms'), "KY" => __('CAYMAN ISLANDS', 'gravityforms'), "CF" => __('CENTRAL AFRICAN REPUBLIC', 'gravityforms'), "TD" => __('CHAD', 'gravityforms'), "CL" => __('CHILE', 'gravityforms'), "CN" => __('CHINA', 'gravityforms'), "CO" => __('COLOMBIA', 'gravityforms'), "KM" => __('COMOROS', 'gravityforms'), "CD" => __('CONGO, DEMOCRATIC REPUBLIC OF THE', 'gravityforms'), "CG" => __('CONGO, REPUBLIC OF THE', 'gravityforms'), "CR" => __('COSTA RICA', 'gravityforms'), "CI" => __('C&OCIRC;TE D\'IVOIRE', 'gravityforms'), "HR" => __('CROATIA', 'gravityforms'), "CU" => __('CUBA', 'gravityforms'), "CY" => __('CYPRUS', 'gravityforms'), "CZ" => __('CZECH REPUBLIC', 'gravityforms'), "DK" => __('DENMARK', 'gravityforms'), "DJ" => __('DJIBOUTI', 'gravityforms'), "DM" => __('DOMINICA', 'gravityforms'), "DO" => __('DOMINICAN REPUBLIC', 'gravityforms'), "TL" => __('EAST TIMOR', 'gravityforms'), "EC" => __('ECUADOR', 'gravityforms'), "EG" => __('EGYPT', 'gravityforms'), "SV" => __('EL SALVADOR', 'gravityforms'), "GQ" => __('EQUATORIAL GUINEA', 'gravityforms'), "ER" => __('ERITREA', 'gravityforms'), "EE" => __('ESTONIA', 'gravityforms'), "ET" => __('ETHIOPIA', 'gravityforms'), "FJ" => __('FIJI', 'gravityforms'), "FI" => __('FINLAND', 'gravityforms'), "FR" => __('FRANCE', 'gravityforms'), "GA" => __('GABON', 'gravityforms'), "GM" => __('GAMBIA', 'gravityforms'), "GE" => __('GEORGIA', 'gravityforms'), "DE" => __('GERMANY', 'gravityforms'), "GH" => __('GHANA', 'gravityforms'), "GR" => __('GREECE', 'gravityforms'), "GL" => __('GREENLAND', 'gravityforms'), "GD" => __('GRENADA', 'gravityforms'), "GU" => __('GUAM', 'gravityforms'), "GT" => __('GUATEMALA', 'gravityforms'), "GN" => __('GUINEA', 'gravityforms'), "GW" => __('GUINEA-BISSAU', 'gravityforms'), "GY" => __('GUYANA', 'gravityforms'), "HT" => __('HAITI', 'gravityforms'), "HN" => __('HONDURAS', 'gravityforms'), "HK" => __('HONG KONG', 'gravityforms'), "HU" => __('HUNGARY', 'gravityforms'), "IS" => __('ICELAND', 'gravityforms'), "IN" => __('INDIA', 'gravityforms'), "ID" => __('INDONESIA', 'gravityforms'), "IR" => __('IRAN', 'gravityforms'), "IQ" => __('IRAQ', 'gravityforms'), "IE" => __('IRELAND', 'gravityforms'), "IL" => __('ISRAEL', 'gravityforms'), "IT" => __('ITALY', 'gravityforms'), "JM" => __('JAMAICA', 'gravityforms'), "JP" => __('JAPAN', 'gravityforms'), "JO" => __('JORDAN', 'gravityforms'), "KZ" => __('KAZAKHSTAN', 'gravityforms'), "KE" => __('KENYA', 'gravityforms'), "KI" => __('KIRIBATI', 'gravityforms'), "KP" => __('NORTH KOREA', 'gravityforms'), "KR" => __('SOUTH KOREA', 'gravityforms'), "KV" => __('KOSOVO', 'gravityforms'), "KW" => __('KUWAIT', 'gravityforms'), "KG" => __('KYRGYZSTAN', 'gravityforms'), "LA" => __('LAOS', 'gravityforms'), "LV" => __('LATVIA', 'gravityforms'), "LB" => __('LEBANON', 'gravityforms'), "LS" => __('LESOTHO', 'gravityforms'), "LR" => __('LIBERIA', 'gravityforms'), "LY" => __('LIBYA', 'gravityforms'), "LI" => __('LIECHTENSTEIN', 'gravityforms'), "LT" => __('LITHUANIA', 'gravityforms'), "LU" => __('LUXEMBOURG', 'gravityforms'), "MK" => __('MACEDONIA', 'gravityforms'), "MG" => __('MADAGASCAR', 'gravityforms'), "MW" => __('MALAWI', 'gravityforms'), "MY" => __('MALAYSIA', 'gravityforms'), "MV" => __('MALDIVES', 'gravityforms'), "ML" => __('MALI', 'gravityforms'), "MT" => __('MALTA', 'gravityforms'), "MH" => __('MARSHALL ISLANDS', 'gravityforms'), "MR" => __('MAURITANIA', 'gravityforms'), "MU" => __('MAURITIUS', 'gravityforms'), "MX" => __('MEXICO', 'gravityforms'), "FM" => __('MICRONESIA', 'gravityforms'), "MD" => __('MOLDOVA', 'gravityforms'), "MC" => __('MONACO', 'gravityforms'), "MN" => __('MONGOLIA', 'gravityforms'), "ME" => __('MONTENEGRO', 'gravityforms'), "MA" => __('MOROCCO', 'gravityforms'), "MZ" => __('MOZAMBIQUE', 'gravityforms'), "MM" => __('MYANMAR', 'gravityforms'), "NA" => __('NAMIBIA', 'gravityforms'), "NR" => __('NAURU', 'gravityforms'), "NP" => __('NEPAL', 'gravityforms'), "NL" => __('NETHERLANDS', 'gravityforms'), "NZ" => __('NEW ZEALAND', 'gravityforms'), "NI" => __('NICARAGUA', 'gravityforms'), "NE" => __('NIGER', 'gravityforms'), "NG" => __('NIGERIA', 'gravityforms'), "MP" => __('NORTHERN MARIANA ISLANDS', 'gravityforms'), "NO" => __('NORWAY', 'gravityforms'), "OM" => __('OMAN', 'gravityforms'), "PK" => __('PAKISTAN', 'gravityforms'), "PW" => __('PALAU', 'gravityforms'), "PS" => __('PALESTINE', 'gravityforms'), "PA" => __('PANAMA', 'gravityforms'), "PG" => __('PAPUA NEW GUINEA', 'gravityforms'), "PY" => __('PARAGUAY', 'gravityforms'), "PE" => __('PERU', 'gravityforms'), "PH" => __('PHILIPPINES', 'gravityforms'), "PL" => __('POLAND', 'gravityforms'), "PT" => __('PORTUGAL', 'gravityforms'), "PR" => __('PUERTO RICO', 'gravityforms'), "QA" => __('QATAR', 'gravityforms'), "RO" => __('ROMANIA', 'gravityforms'), "RU" => __('RUSSIA', 'gravityforms'), "RW" => __('RWANDA', 'gravityforms'), "KN" => __('SAINT KITTS AND NEVIS', 'gravityforms'), "LC" => __('SAINT LUCIA', 'gravityforms'), "VC" => __('SAINT VINCENT AND THE GRENADINES', 'gravityforms'), "WS" => __('SAMOA', 'gravityforms'), "SM" => __('SAN MARINO', 'gravityforms'), "ST" => __('SAO TOME AND PRINCIPE', 'gravityforms'), "SA" => __('SAUDI ARABIA', 'gravityforms'), "SN" => __('SENEGAL', 'gravityforms'), "RS" => __('SERBIA AND MONTENEGRO', 'gravityforms'), "SC" => __('SEYCHELLES', 'gravityforms'), "SL" => __('SIERRA LEONE', 'gravityforms'), "SG" => __('SINGAPORE', 'gravityforms'), "SK" => __('SLOVAKIA', 'gravityforms'), "SI" => __('SLOVENIA', 'gravityforms'), "SB" => __('SOLOMON ISLANDS', 'gravityforms'), "SO" => __('SOMALIA', 'gravityforms'), "ZA" => __('SOUTH AFRICA', 'gravityforms'), "ES" => __('SPAIN', 'gravityforms'), "LK" => __('SRI LANKA', 'gravityforms'), "SD" => __('SUDAN', 'gravityforms'), "SS" => __('SUDAN, SOUTH', 'gravityforms'), "SR" => __('SURINAME', 'gravityforms'), "SZ" => __('SWAZILAND', 'gravityforms'), "SE" => __('SWEDEN', 'gravityforms'), "CH" => __('SWITZERLAND', 'gravityforms'), "SY" => __('SYRIA', 'gravityforms'), "TW" => __('TAIWAN', 'gravityforms'), "TJ" => __('TAJIKISTAN', 'gravityforms'), "TZ" => __('TANZANIA', 'gravityforms'), "TH" => __('THAILAND', 'gravityforms'), "TG" => __('TOGO', 'gravityforms'), "TO" => __('TONGA', 'gravityforms'), "TT" => __('TRINIDAD AND TOBAGO', 'gravityforms'), "TN" => __('TUNISIA', 'gravityforms'), "TR" => __('TURKEY', 'gravityforms'), "TM" => __('TURKMENISTAN', 'gravityforms'), "TV" => __('TUVALU', 'gravityforms'), "UG" => __('UGANDA', 'gravityforms'), "UA" => __('UKRAINE', 'gravityforms'), "AE" => __('UNITED ARAB EMIRATES', 'gravityforms'), "GB" => __('UNITED KINGDOM', 'gravityforms'), "US" => __('UNITED STATES', 'gravityforms'), "UY" => __('URUGUAY', 'gravityforms'), "UZ" => __('UZBEKISTAN', 'gravityforms'), "VU" => __('VANUATU', 'gravityforms'), "VC" => __('VATICAN CITY', 'gravityforms'), "VE" => __('VENEZUELA', 'gravityforms'), "VG" => __('VIRGIN ISLANDS, BRITISH', 'gravityforms'), "VI" => __('VIRGIN ISLANDS, U.S.', 'gravityforms'), "VN" => __('VIETNAM', 'gravityforms'), "YE" => __('YEMEN', 'gravityforms'), "ZM" => __('ZAMBIA', 'gravityforms'), "ZW" => __('ZIMBABWE', 'gravityforms'), );
    }

    // enqueue custom frontend styles
    if ( !function_exists('avala_register_frontend_scripts') )
    {
        if( !is_admin() )
        {
            add_action('wp_enqueue_scripts', 'avala_register_frontend_scripts');
        }

        function avala_register_frontend_scripts()
        {
            wp_register_script( 'avala-js', plugins_url( "/js/avala_api_script.js", __FILE__ ), array('jquery'), '1.0', true );
            wp_register_style( 'avala-style', plugins_url( "/css/avala_api_styles_frontend.css", __FILE__ ), array(), '1.0', 'all' );
            wp_enqueue_script( 'avala-js');
            wp_enqueue_style( 'avala-style');
        }
    }

}
<?php

/*
  Plugin Name: Clickworker SEO Texts
  Plugin URI: https://github.com/clickworker/Wordpress-SEO-Text-Plugin
  Description: Order and buy Content created by clickworker.com
  Version: 0.93
  Author: M. Tomicki
  Author URI: http://www.clickworker.com/about-us/team

  Prototype by: S.D.Moufarrege
  Copyright (c) 2011 humangrid GmbH

  Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
  The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

if (!defined('VERSION'))
    define('VERSION', "0.93");

//check for/ right scope

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

// clickworker button
add_action('admin_menu', 'clickworker_seo_menu');

// clickworker wordpress user rights
if (!defined('Clickworker_SEO_Capability'))
    define('Clickworker_SEO_Capability', "activate_plugins");


// CW_SERVER specifies which server to use.
// Production: https://api.clickworker.com/api/marketplace/v2/
// Sandbox: https://sandbox.clickworker.com/api/marketplace/v2/
// Sandbox beta: https://sandbox-beta.clickworker.com/api/marketplace/v2/
if (!defined('CW_SERVER'))
    define('CW_SERVER', "sandbox.clickworker.com");

// path to the api
if (!defined('API_PATH'))
    define('API_PATH', "/api/marketplace/v2/");

// option name for database entries
if (!defined('ADMINOPTIONNAME'))
    define('ADMINOPTIONNAME', "ClickworkerSEOTextOptions");

//queue for warnings
$warnings = array();

// get saved options
function getOptions() {

    $clickworkerOptions = array('clickworker_username' => '', 'clickworker_password' => '', 'clickworker_lowcredits' => 'false'); // Default values for each option
    $devOptions = get_option(ADMINOPTIONNAME); // Find previous values that might have been stored in the database

    if (!empty($devOptions)) { // if previous options were stored, overwrite defaults with them
        foreach ($devOptions as $key => $option) {
            $clickworkerOptions[$key] = $option;
        }
    }
    //update_option($adminOptionsName, $clickworkerOptions); // Store options in WordPress database
    return $clickworkerOptions;
}

/**
 * Function name: cw_command()
 * Description: Send commands through the API
 * Parameters: target url, httpmethod, data, boolean long url with http?
 * Returns: JSON response on succes, empty string on failure
 * */
function cw_command($url, $method, $data="", $longurl = false) {
    global $warnings;
    $options = getOptions();
    $username = $options['clickworker_username'];
    $password = $options['clickworker_password'];

    $opts = array(
        'method' => $method,
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/json; charset=utf-8',
            'HTTP-ACCEPT' => 'application/json',
            'Accept' => 'application/json',
            'Accept-Language' => 'en-us'
        ),
        'timeout' => 30,
        'body' => $data,
        'sslverify' => false
    );

    if (!$longurl) {
        $response = wp_remote_request('https://' . CW_SERVER . API_PATH . $url, $opts);
    } else {
        $response = wp_remote_request($url, $opts);
    }

    if (is_wp_error($response)) {
        array_push($warnings, $response->get_error_code() . " " . $response->get_error_message());
        return "";
    } elseif ($response['response']['code'] != 200) {
        array_push($warnings, CW_SERVER . " encountered a problem: " . $response['response']['code'] . " " . $response['response']['message']);
        return "";
    } else {
        return $response['body'];
    }
}

// show errors from the queue
function display_warnings() {
    global $warnings;
    if (isset($warnings) && count($warnings) > 0) {
        foreach ($warnings as $value) {
            echo "<div id='clickworker-warning' class='updated fade'><p>
                <strong>" . __('Attention!') . "</strong> " . sprintf($value) .
            "</p></div>";
        }
    }
}

// check for customer variables on the remote system
function customer_check() {
    global $warnings;
    
    $settings = getOptions();
    
    if (strlen($settings['clickworker_username']) < 2 || strlen($settings['clickworker_password']) < 2) {
        array_push($warnings, 'To get started with the Clickworker SEO Text plugin, you need to <a href="admin.php?page=clickworker_seo_login">enter your credentials.</a>');
        return;
    } 
    
    $customer_call = cw_command("customer", "GET");

    if (!is_wp_error($customer_call)) {
        $customer = json_decode($customer_call, true);
    }

    if (!empty($customer)) {
        $customer['username'] = $settings['clickworker_username'];
    }

    $balance = $customer['customer_response']['customer']['$balance'];

    if (is_wp_error($customer_call) && $customer_call->get_error_code() == '401') {
        array_push($warnings, 'Your Clickworker login information appears to be incorrect. Please make sure to <a href="admin.php?page=clickworker_seo_login">enter valid credentials.</a>');
    } else {
        if ($settings['clickworker_lowcredits'] == 'true' && $balance < 10) { // change to a more suitable number once we are sure this works
            array_push($warnings, "The balance on your Clickworker account is running low. Be sure to log into Clickworker to increase your balance.");
        }
        return $customer;
    }
}

// add subpages
function clickworker_seo_menu() {

    if (function_exists('add_menu_page')) {
          
        add_menu_page('Clickworker SEO-Texts', 'Clickworker SEO', Clickworker_SEO_Capability, "clickworker_seo", 'dashboard');
        
        add_submenu_page("clickworker_seo", 'Clickworker SEO Dashboard', 'Dashboard', Clickworker_SEO_Capability, "clickworker_seo", 'dashboard');
        
        add_submenu_page("clickworker_seo", 'Clickworker SEO Login / Register', 'Setup', Clickworker_SEO_Capability, "clickworker_seo_login", 'login_page');

        add_submenu_page("clickworker_seo", 'Clickworker SEO Charge Account', 'Charge Account', Clickworker_SEO_Capability, "clickworker_seo_charge", 'charge_page');

        add_submenu_page("clickworker_seo", 'Clickworker SEO Place Order', 'Order SEO Text', Clickworker_SEO_Capability, "clickworker_seo_order", 'order_page');

        add_submenu_page("clickworker_seo", 'Clickworker SEO Order Status', 'Order Status', Clickworker_SEO_Capability, "clickworker_seo_status", 'status_page');
    }
}

function page($target) {
   global $customer;
    set_time_limit(0); 
    if (isset($_POST['accept'])) {
        require_once dirname(__FILE__) . '/sites/' . $target . '.php';
    } elseif (isset($_POST['adminOptionsSubmit'])) {
        if (strlen($_POST['clickworker_username']) > 2) { // All usernames must be 3 or more characters long.
          $devOptions["clickworker_username"] = $_POST['clickworker_username'];
          $devOptions["clickworker_password"] = $_POST['clickworker_password'];
          if (isset($_POST['clickworker_lowcredits'])) {
              $devOptions["clickworker_lowcredits"] = 'true';
          } else {
              $devOptions["clickworker_lowcredits"] = 'false';
          }
          update_option(ADMINOPTIONNAME, $devOptions);
          $customer = customer_check();
        }
        require_once dirname(__FILE__) . '/sites/styles.php';
        require_once dirname(__FILE__) . '/sites/' . $target . '.php';
    } else {
        $customer = customer_check();
        require_once dirname(__FILE__) . '/sites/styles.php';
        require_once dirname(__FILE__) . '/sites/' . $target . '.php';
    }
}

function dashboard() {
    global $customer;
    if(!isset($_POST['adminOptionsSubmit'])){
      $customer = customer_check();
      
    }

    if(!empty($customer)) {
        page('dashboard');
    }else{
      page('login');
    }
}

function login_page() {
    page('login');
}

function order_page() {
    page('order');
}

function status_page() {
    page('status');
}

function price_page() {
    page('status');
}

function charge_page() {
    page('charge');
}



?>

<?php
/**
 * WHMCS Crosspay Payment Gateway Module
 *
 * @package    WHMCS
 * @author     WIDDX
 * @copyright  Copyright (c) 2024 WIDDX
 * @license    https://opensource.org/licenses/MIT MIT License
 * @version    1.0.0
 */

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

// Define constants
define('CROSSPAY_API_DATA', '82e4b4fd3a16ad99229af9911ce8e6d2');
define('CROSSPAY_API_ENDPOINT', 'https://crosspayonline.com/api/createInvoiceByAccountLahza');
define('CROSSPAY_LOGIN_ENDPOINT', 'https://crosspayonline.com/api/loginBill');
define('CROSSPAY_HTTP_OK', 200);
define('CROSSPAY_HTTP_REDIRECT', 302);

/**
 * Define module related meta data.
 *
 * @return array
 */
function widdx_crosspay_MetaData()
{
    return array(
        'DisplayName' => 'Crosspay Credit Card Lahza',
        'APIVersion' => '1.1',
    );
}

/**
 * Define gateway configuration options.
 *
 * @return array
 */
function widdx_crosspay_config()
{
    return array(
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Crosspay Credit Card',
        ),
        'apiKey' => array(
            'FriendlyName' => 'API Key',
            'Type' => 'password',
            'Size' => '40',
            'Default' => '',
            'Description' => 'Enter your Crosspay API Key here',
        ),
        'returnUrlSuccess' => array(
            'FriendlyName' => 'Success Return URL',
            'Type' => 'text',
            'Size' => '100',
            'Default' => '',
            'Description' => 'Enter the URL where users will be redirected after successful payment',
        ),
    );
}

/**
 * Payment link.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @return string
 */
function widdx_crosspay_link($params)
{
    try {
        // Validate required parameters
        $requiredParams = ['apiKey', 'invoiceid', 'amount', 'currency', 'clientdetails'];
        foreach ($requiredParams as $param) {
            if (empty($params[$param])) {
                throw new InvalidArgumentException("Missing required parameter: $param");
            }
        }

        logActivity("Initiating Crosspay payment for Invoice #" . $params['invoiceid']);

        // Gateway Configuration Parameters
        $apiKey = $params['apiKey'];

        // Invoice Parameters
        $invoiceId = $params['invoiceid'];
        $description = $params["description"];
        $amount = $params['amount'];
        $currencyCode = $params['currency'];

        // Client Parameters
        $firstname = $params['clientdetails']['firstname'];
        $lastname = $params['clientdetails']['lastname'];
        $email = $params['clientdetails']['email'];
        $phone = $params['clientdetails']['phonenumber'];

        // Validate and clean phone number
        $phone = validateAndCleanPhoneNumber($phone);
        
        // If phone number is empty, use a default value or leave it empty
        if (empty($phone)) {
            $phone = 'Not provided'; // or you can leave it empty if Crosspay accepts that
        }

        // System Parameters
        $returnUrlSuccess = $params['returnUrlSuccess'];
        $langPayNow = $params['langpaynow'];

        // Prepare the fields to send via the API
        $postfields = preparePostFields($params, $phone, $returnUrlSuccess);

        // Execute the API request using cURL
        $response = performCurlRequest(CROSSPAY_API_ENDPOINT, $postfields);

        if ($response['error']) {
            throw new Exception("Crosspay API Error: " . $response['error']);
        }

        $result = json_decode($response['response'], true);
        $httpCode = $response['httpCode'];
        $headers = $response['headers'];

        logActivity("Crosspay API Response: " . $response['response'] . " (HTTP Code: " . $httpCode . ")");

        return handleApiResponse($result, $httpCode, $langPayNow, $headers, $apiKey);
    } catch (Exception $e) {
        logActivity("Crosspay Error: " . $e->getMessage());
        return "Error: " . $e->getMessage();
    }
}

/**
 * Validate and clean phone number.
 *
 * @param string $phone
 * @return string
 */
function validateAndCleanPhoneNumber($phone)
{
    if (empty($phone)) {
        return ''; // Return empty string instead of throwing an exception
    }

    // Remove all non-digit characters
    $phone = preg_replace("/[^0-9]/", "", $phone);

    // Check if the number is between 7 and 15 digits
    if (strlen($phone) < 7 || strlen($phone) > 15) {
        logActivity("Warning: Phone number length is not standard: " . $phone);
        // Return the cleaned number even if it's not standard
        return $phone;
    }

    return $phone;
}

/**
 * Prepare post fields for API request.
 *
 * @param array $params WHMCS parameters
 * @param string $phone Validated phone number
 * @param string $returnUrlSuccess Success return URL
 * @return array Prepared post fields for API request
 */
function preparePostFields($params, $phone, $returnUrlSuccess)
{
    $invDetails = array(
        'inv_items' => array(
            array(
                'name' => $params["description"],
                'quntity' => '1.00',
                'unitPrice' => $params['amount'],
                'totalPrice' => $params['amount'],
                'currency' => $params['currency']
            )
        ),
        'inv_info' => array(
            array('row_title' => 'Vat', 'row_value' => '0'),
            array('row_title' => 'Delivery', 'row_value' => '0'),
            array('row_title' => 'Promo Code', 'row_value' => 0),
            array('row_title' => 'Discounts', 'row_value' => 0)
        ),
        'user' => array('userName' => $params['clientdetails']['firstname'] . ' ' . $params['clientdetails']['lastname'])
    );

    return array(
        'api_data' => CROSSPAY_API_DATA,
        'invoice_id' => $params['invoiceid'],
        'apiKey' => $params['apiKey'],
        'total' => $params['amount'],
        'currency' => $params['currency'],
        'return_url' => $returnUrlSuccess,
        'email' => $params['clientdetails']['email'],
        'mobile' => $phone,
        'name' => $params['clientdetails']['firstname'] . ' ' . $params['clientdetails']['lastname'],
        'inv_details' => json_encode($invDetails)
    );
}

/**
 * Perform cURL request.
 *
 * @param string $url
 * @param array $postfields
 * @return array
 */
function performCurlRequest($url, $postfields)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postfields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'WHMCS/Crosspay-Module');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return array('error' => $error, 'response' => null, 'httpCode' => null, 'headers' => null);
    }
    
    curl_close($ch);
    return array('error' => null, 'response' => $body, 'httpCode' => $httpCode, 'headers' => $headers);
}

/**
 * Handle API response for payment link.
 *
 * @param array $result
 * @param int $httpCode
 * @param string $langPayNow
 * @param string $headers
 * @param string $apiKey
 * @return string
 */
function handleApiResponse($result, $httpCode, $langPayNow, $headers, $apiKey)
{
    try {
        if ($httpCode == CROSSPAY_HTTP_REDIRECT) {
            preg_match('/Location: (.+)/', $headers, $matches);
            $redirectUrl = isset($matches[1]) ? trim($matches[1]) : '';

            if (!empty($redirectUrl)) {
                logActivity("Crosspay API Redirect: " . $redirectUrl);
                return '<a href="' . $redirectUrl . '" class="btn btn-primary">' . $langPayNow . '</a>';
            } else {
                throw new Exception("Redirect without Location header");
            }
        } elseif ($httpCode == CROSSPAY_HTTP_OK) {
            if (isset($result['payment_url'])) {
                return '<a href="' . $result['payment_url'] . '" class="btn btn-primary">' . $langPayNow . '</a>';
            } else {
                throw new Exception($result['msg'] ?? 'Unknown error');
            }
        } else {
            throw new Exception("Unexpected HTTP Code " . $httpCode);
        }
    } catch (Exception $e) {
        logActivity("Crosspay API Error: " . $e->getMessage() . " - Headers: " . $headers . " - Body: " . print_r($result, true));
        return "Error: Unable to process payment. Please try again later. (" . $e->getMessage() . ")";
    }
}

/**
 * Generate login link for Crosspay dashboard.
 *
 * @param string $apiKey
 * @return string
 */
function generateCrosspayLoginLink($apiKey)
{
    static $cachedLink = null;
    if ($cachedLink === null) {
        $loginUrl = CROSSPAY_LOGIN_ENDPOINT . '?api_data=' . CROSSPAY_API_DATA . '&apiKey=' . $apiKey;
        $cachedLink = '<a href="' . $loginUrl . '" target="_blank" class="btn btn-info">Log in to Crosspay Dashboard</a>';
    }
    return $cachedLink;
}

# Crosspay Credit Card Lahza - WHMCS Payment Gateway

This payment gateway module allows WHMCS users to accept credit card payments securely via Crosspay.

## Installation Instructions

### Step 1: Download the Package
You can download the Crosspay payment gateway module directly from our GitHub repository:

1. Visit the [GitHub repository](https://github.com/WIDDX-com/crosspay-gateway-lahza.git) (replace with your actual repository link).
2. Click on the green **Code** button and select **Download ZIP**.
3. Extract the downloaded ZIP file on your local machine.

### Step 2: Copy the Files
1. Copy the `widdx_crosspay.php` file and the `widdx_crosspay` folder to the following path within your WHMCS root directory:
   ```
   /modules/gateways/
   ```

### Step 3: Activate the Payment Gateway in WHMCS
1. Log in to your WHMCS admin control panel.
2. Navigate to **Apps & Integrations**.
3. Click on **Browse**.
4. In the left-hand sidebar, select **Payments**.
5. Scroll down to find **Crosspay Credit Card Lahza**.
6. A pop-up will appear. Click on the green **Activate** button.

### Step 4: Retrieve Your Crosspay API Key
1. Go to the [Crosspay Online website](https://crosspayonline.com/).
2. Log in to your Crosspay account.
3. Obtain your API key from your account dashboard.

### Step 5: Configure the Payment Gateway in WHMCS
1. Once the gateway is activated, go to **Setup > Payments > Payment Gateways** in your WHMCS admin panel.
2. Find **Crosspay Credit Card Lahza** and click on **Manage**.
3. Enter the API key you obtained from Crosspay and configure the other settings as needed.

### Step 6: Test the Integration
1. Make sure the integration is working by processing a test payment.

## Support
If you need help or encounter any issues, feel free to contact our support:
- Homepage: [https://widdx.com](https://widdx.com)
- Email: [support@widdx.com](mailto:support@widdx.com)
- Tickets: [https://widdx.com/tickets](https://widdx.com/tickets)
- Documentation: [Crosspay API Documentation](https://crosspayonline.com/bill/api/api-intro.php)


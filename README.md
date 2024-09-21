### Crosspay Credit Card Lahza - WHMCS Payment Gateway

This payment gateway module allows WHMCS users to securely accept credit card payments via **Crosspay**, exclusively for **Lahza**.

### Installation Instructions

#### Step 1: Download the Package
You can download the Crosspay payment gateway module directly from our GitHub repository:

1. Open **Terminal** (on macOS or Linux) or **Command Prompt** (on Windows).
2. Run the following command to download the repository:

   ```bash
   git clone https://github.com/WIDDX-com/whmcs-crosspay-gateway-lahza.git
   ```

3. After downloading, navigate to the downloaded folder and extract the files if necessary.

#### Step 2: Copy the Files
Copy the `widdx_crosspay.php` file and the `widdx_crosspay` folder to the following path within your WHMCS root directory:
```
/modules/gateways/
```

#### Step 3: Activate the Payment Gateway in WHMCS
1. Log in to your WHMCS admin control panel.
2. Navigate to **Apps & Integrations**.
3. Click on **Browse**.
4. In the left-hand sidebar, select **Payments**.
5. Scroll down to find **Crosspay Credit Card Lahza**.
6. A pop-up will appear. Click on the green **Activate** button.

#### Step 4: Retrieve Your Crosspay API Key
1. Go to the **Crosspay Online** website.
2. Log in to your Crosspay account.
3. Obtain your API key from your account dashboard.

#### Step 5: Configure the Payment Gateway in WHMCS
1. Once the gateway is activated, go to **Setup > Payments > Payment Gateways** in your WHMCS admin panel.
2. Find **Crosspay Credit Card Lahza** and click on **Manage**.
3. Enter the API key you obtained from Crosspay and configure the other settings as needed.

#### Step 6: Test the Integration
Ensure the integration is working by processing a test payment.

### Important Note
- This payment gateway is exclusively for **Lahza** and can only be used with **Crosspay**.

### Support
If you need help or encounter any issues, feel free to contact our support:

- Homepage: [https://widdx.com](https://widdx.com)
- Email: [support@widdx.com](mailto:support@widdx.com)
- Tickets: [https://widdx.com/tickets](https://widdx.com/tickets)
- Documentation: [Crosspay API Documentation](https://crosspayonline.com/docs)

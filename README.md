# iosChargingWebhook

![image](https://github.com/Niclassslua/iosChargingWebhook/assets/78554432/1e55c6b1-4bb9-4c0f-bbe9-c44a46152b01)

A (kinda) simple way to post your iPhone's charging status into your Discord Server utilizing [iOS Shortcuts](https://support.apple.com/guide/shortcuts/welcome/ios) and [PHP](https://www.php.net/)


## Requirements 📝

- Server with PHP running
- Discord Channel [with a webhook](https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks)
- iPhone (or even an iPad) that runs at least iOS 12


## Setup ⚙️

### Server:

1. Put the files from the repo on your server where they are accessible from the internet with a post request
  (I am using XAMPP. I put the files in C:\xampp\htdocs)
2. Change the `$webhookurl` string in `charging.php` to your individual webhook
3. Change the `avatar_url` string to your avatar url you want the webhook to have

### Shortcut:

1. Open the `Shortcut` app on your iPhone
2. Press `Automation` in the bottom navigator
3. Press the blue `+` to create a new automation
4. Search for `Charger`

![IMG_6786 (1)](https://github.com/Niclassslua/iosChargingWebhook/assets/78554432/10e8129e-89a2-44db-a95f-053f920f0bae)

*Is Connected:*
1. Make sure you tick `Run Immediately` and then click `Next`
2. Create a `New Blank Automation`
3. Click `Add Action`
4. Search for "Get Battery Status" and select it

![IMG_6787 (1)](https://github.com/Niclassslua/iosChargingWebhook/assets/78554432/ba0a72b1-8105-44e2-92b9-7f66e09814bb)

5. Search for "URL" and select it
6. Enter your server domain/ip e.g. `http://127.0.0.1/charging.php/?type=plug&percent=`
7. Click on the blue `+` and press `Battery State` above your keyboard
8. Now click on the suggested new acction `Get Contents of URL`
9. Done!

![IMG_6791 (1)](https://github.com/Niclassslua/iosChargingWebhook/assets/78554432/7160e230-5801-4a05-93f0-74b626c1ec62)


*Is Disconnected:*
1. Make sure you tick `Run Immediately` and then click `Next`
2. Create a `New Blank Automation`
3. Click `Add Action`
4. Search for "Get Battery Status" and select it
5. Search for "URL" and select it
6. Enter your server domain/ip e.g. `http://127.0.0.1/charging.php/?type=unplug&percent=`
7. Click on the blue `+` and press `Battery State` above your keyboard
8. Now click on the suggested new acction `Get Contents of URL`
9. Done!


## Security assessment 🔗

### I have been using this script personally for several months before deciding to publish it on GitHub. Before publishing it I reworked the whole Script to make it more safe. The following security things were implemented:

- Input Verification and sanitization: The code uses filter_input with FILTER_VALIDATE_INT for the percent GET variable, which is good to ensure that the value is a valid integer within a certain range. This particular usage is to prevent cross-site scripting (XSS) attacks.
- cURL usage: Since we have a hardcoded Webhook URL, there is a significant protection against Server-Side Request Forgery (SSRF) attacks. The URL is constant and cannot be altered through user input or external influence.
- CURLOPT_SSL_VERIFYPEER: The code ensures that SSL certificates are verified when making HTTPS requests. This SSL verification helps prevent Man-in-the-Middle (MitM) attacks, which could otherwise be a concern if an attack were trying to intercept or redirect requests.

### Although this seems pretty secure for such a small program, I still see some vulnerabilities in the script:

- Error Handling and Information Leakage: The code explicitly outputs error messages that could potentially reveal sensitive information or system details to users or attacks, aiding them in further attacks.
- File Handling Security: The usage of a `.txt` file for storing and retrieving data, as seen in the script, can be considered primitive and posess security and scalability concerns. For example: Race conditions from concurrent file access can corrupt data, Text files lack built-in access controls and encryption, Text files provide limited support for structured data and querying

*The file handling case may be resolved at a later time*

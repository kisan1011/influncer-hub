<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickFluence OTP Email</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: rgb(13, 13, 13);
            color: #ffffff;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header img {
            max-width: 240px;
            height: auto;
            margin-bottom: 5px;
        }
        .header h1 {
            font-size: 1.2em;
            margin: 5px 0;
        }
        .content {
            padding: 15px;
            line-height: 1.5;
        }
        .otp {
            background: rgb(13, 13, 13);
            padding: 10px 15px;
            color: #ffffff;
            border-radius: 4px;
            display: inline-block;
            margin: 15px 0;
            font-size: 1.2em;
        }
        .note {
            font-size: 0.9em;
            color: #666;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            font-size: 0.8em;
            color: #666;
            padding: 15px;
        }
        .footer a {
            color: rgb(13, 13, 13);
            text-decoration: none;
            margin: 0 5px;
            transition: color 0.3s;
        }
        .footer a:hover {
            color: #000;
        }
        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 15px 0;
        }
        .social-icons {
            margin-top: 10px;
        }
        .social-icons img {
            width: 24px;
            height: 24px;
            margin: 0 2px;
            vertical-align: middle;
        }
        .otp-expiry {
            font-size: 0.9em;
            color: #555;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Header Section -->
    <div class="header">
        <img src="{{ asset('public/QuickFluenceLogo.png') }}" alt="QuickFluence Logo">

        @if ($details['type'] == '1')
        <h1>Welcome to QuickFluence</h1>
        @else
        <h1>Password Reset Request</h1>
        @endif
    </div>

    <!-- Body Section -->
    <div class="content">
        <p>Hi, {{ $details['email'] }}</p>

        @if ($details['type'] == '1')
          <p>Thank you for choosing QuickFluence! Use the following OTP to verify your email address:</p>
        @else
          <p>It looks like you requested a password reset. Use the following OTP to reset your password:</p>
        @endif


        <div class="otp">{{ $details['otp'] }}</div>
        <p class="otp-expiry">This OTP will expire in <strong>5 minutes</strong>.</p>

        <p>Regards,<br>QuickFluence Team</p>

        <!-- Note Section -->
        <div class="note">
          If you did not request this OTP, please ignore this email. If you need assistance, feel free to <a href="mailto:contact@quickfluence.com">contact our support team</a>.</p>
        </div>

    </div>

    <!-- Footer Section -->
    <div class="footer">
        <hr>
        <p>&copy; 2024 QuickFluence. All rights reserved.</p>
        <p>
            <a href="https://www.quickfluence.com/privacy">Privacy Policy</a> |
            <a href="https://www.quickfluence.com/terms">Terms of Service</a>
        </p>

        <!-- Social Media Links -->
        <div class="social-icons">
            <a href="https://www.instagram.com">
                <img src="{{ asset('public/icons/instagram.png') }}" alt="Instagram">
            </a>
            <a href="https://www.facebook.com">
                <img src="{{ asset('public/icons/facebook.png') }}" alt="Facebook">
            </a>
            <a href="https://www.linkedin.com">
                <img src="{{ asset('public/icons/linkedin.png') }}" alt="LinkedIn">
            </a>
            <a href="https://www.twitter.com">
                <img src="{{ asset('public/icons/twitter.png') }}" alt="Twitter">
            </a>
        </div>
    </div>
</div>

</body>
</html>

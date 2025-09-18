<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Quickfluence!</title>
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
    </style>
</head>
<body>

<div class="container">
    <!-- Header Section -->
    <div class="header">
        <img src="{{ asset('public/QuickFluenceLogo.png') }}" alt="QuickFluence Logo">
        <h1>Welcome to Quickfluence!</h1>
    </div>

    <!-- Body Section -->
    <div class="content">
        <p>Hello Dear,</p>
        <p>Thank you for subscribing to Quickfluence! We’re thrilled to have you join our community dedicated to transforming influencer collaboration and empowering brands and influencers alike.</p>

        <h3>What Awaits You</h3>
        <ul>
            <li><strong>Tailored Insights:</strong> Discover strategies and tips designed to help you connect with the perfect influencers for your brand or amplify your voice as an influencer.</li>
            <li><strong>Effortless Collaboration:</strong> Learn how to plan, execute, and track your influencer campaigns seamlessly with our intuitive tools.</li>
            <li><strong>Data-Driven Success:</strong> Stay informed with real-time performance analytics to optimize your influencer marketing strategy.</li>
        </ul>

        <p>At Quickfluence, we believe in the power of authentic connections. We’re not just a platform; we’re a community of passionate individuals dedicated to creativity, transparency, and impactful partnerships.</p>

        <p>If you ever have questions or topics you’d like us to cover in our upcoming newsletters, feel free to reach out! We’d love to hear from you.</p>

        <p>Thank you once again for joining Quickfluence. Together, let's create stories that inspire, engage, and drive meaningful connections in the world of influencer marketing!</p>

        <p>Best Regards,<br>Quickfluence Team</p>

        <!-- Note Section -->
        <div class="note">
            If you have any questions or need further assistance, please feel free to <a href="mailto:support@quickfluence.com">contact us</a>.
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

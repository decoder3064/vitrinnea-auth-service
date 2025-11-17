<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset - Vitrinnea</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4F46E5;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
        }
        .password-box {
            background-color: #f3f4f6;
            border: 2px dashed #9ca3af;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .password {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            letter-spacing: 2px;
            font-family: monospace;
        }
        .warning {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6b7280;
            font-size: 14px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Password Reset</h1>
    </div>
    
    <div class="content">
        <p>Hello {{ $user->name }},</p>
        
        <p>Your password has been reset by an administrator. Please use the temporary password below to log in:</p>
        
        <div class="password-box">
            <p style="margin: 0 0 10px 0; font-size: 14px; color: #6b7280;">Temporary Password</p>
            <div class="password">{{ $temporaryPassword }}</div>
        </div>
        
        <div class="warning">
            <strong>⚠️ Important Security Notice:</strong>
            <ul style="margin: 10px 0 0 0; padding-left: 20px;">
                <li>This is a temporary password - please change it immediately after logging in</li>
                <li>Do not share this password with anyone</li>
                <li>This email should be deleted after you've changed your password</li>
            </ul>
        </div>
        
        <p><strong>Next steps:</strong></p>
        <ol>
            <li>Log in using the temporary password above</li>
            <li>Navigate to your account settings</li>
            <li>Change your password to something secure and memorable</li>
        </ol>
        
        <p>If you did not request a password reset or believe this was sent in error, please contact your administrator immediately.</p>
        
        <p>Best regards,<br>The Vitrinnea Team</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Vitrinnea. All rights reserved.</p>
        <p>This is an automated message, please do not reply to this email.</p>
    </div>
</body>
</html>

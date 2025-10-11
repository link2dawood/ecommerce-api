<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #D19C97; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { text-align: center; padding: 10px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Reply to Your Message</h2>
        </div>
        <div class="content">
            <p>Dear {{ $contact->name }},</p>
            <p>{{ $message }}</p>
            <hr>
            <p><small><strong>Your original message:</strong></small></p>
            <p><small>{{ $contact->message }}</small></p>
        </div>
        <div class="footer">
            <p>Thank you for contacting us!</p>
        </div>
    </div>
</body>
</html>
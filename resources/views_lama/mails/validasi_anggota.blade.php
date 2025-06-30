<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Akun Berhasil Divalidasi oleh Admin</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f6f8fb;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            padding: 32px 28px;
        }

        h2 {
            color: #2e7d32;
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 22px;
        }

        p {
            color: #333;
            font-size: 15px;
            line-height: 1.6;
        }

        .footer {
            margin-top: 32px;
            font-size: 13px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 16px;
            text-align: center;
        }

        .icon-success {
            display: block;
            margin: 0 auto 18px auto;
            width: 48px;
            height: 48px;
        }
    </style>
</head>

<body>
    <div class="container">
        <svg class="icon-success" viewBox="0 0 48 48" fill="none">
            <circle cx="24" cy="24" r="24" fill="#e8f5e9" />
            <path d="M16 25.5L22 31.5L34 19.5" stroke="#43a047" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        <h2>Akun Anda Berhasil Divalidasi!</h2>
        <p>Halo,</p>
        <p>
            Selamat! Akun Anda telah berhasil divalidasi oleh admin. Sekarang Anda dapat mengakses seluruh fitur yang tersedia di platform kami.
        </p>
        <p>
            Terima kasih telah bergabung bersama kami.
        </p>
        <p>
            Salam,<br>
            Tim Admin
        </p>
        <div class="footer">
            &copy; {{ date('Y') }} BTS-WORK. All rights reserved.
        </div>
    </div>
</body>

</html>
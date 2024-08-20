<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ważna informacja dotycząca Twojego zapytania ofertowego - EPH Polska</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header img {
            max-width: 200px;
        }
        .content {
            padding: 30px;
        }
        h1 {
            color: #4CAF50;
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 20px;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .highlight {
            background-color: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
        }
        .features {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .features ul {
            padding-left: 20px;
        }
        .contact {
            font-weight: bold;
            color: #4CAF50;
        }
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .social-icons {
            margin-top: 15px;
        }
        .social-icons a {
            display: inline-block;
            margin: 0 10px;
            color: white;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <img src="https://mega1000.pl/logo.webp" style="width: 60px" alt="EPH Polska Logo">
    </div>
    <div class="content">
        <h1>Ważna informacja dotycząca Twojego zapytania ofertowego</h1>

        <p>Szanowni Państwo,</p>

        <p>Dziękujemy za korzystanie z platformy EPH Polska. Zauważyliśmy, że w ciągu ostatnich 24 godzin nie została wykonana żadna akcja związana z Państwa zapytaniem ofertowym o ID: {{ $order->id }}.</p>

        <div class="highlight">
            <p><strong>Ważne informacje:</strong></p>
            <ul>
                <li>Jeśli opłacili Państwo proformę, prosimy o podłączenie potwierdzenia przelewu w panelu klienta: <a href="https://mega1000.pl/account">https://mega1000.pl/account</a></li>
                <li>W panelu klienta mogą Państwo również pobrać proformę do dokonania opłaty oraz edytować wszelkie dane związane z ofertą.</li>
            </ul>
        </div>

        <div class="features">
            <p><strong>Dodatkowe możliwości:</strong></p>
            <ul>
                <li>Aby otrzymać oferty specjalne od producentów, mogą Państwo stworzyć przetarg pod linkiem: <a href="https://admin.mega1000.pl/auctions/{{ $order->chat->id }}/create">https://admin.mega1000.pl/auctions/{{ $order->chat->id }}/create</a></li>
            </ul>
        </div>

        <p>Nasz zespół wsparcia jest gotowy, aby pomóc Państwu w każdej sytuacji:</p>
        <p class="contact">E-mail: styropiany@ephpolska.pl</p>
        <p class="contact">Infolinia: +48 576 205 389</p>

        <p>Dziękujemy za zaufanie i wybór platformy EPH Polska. Jesteśmy przekonani, że nasze narzędzie znacząco ułatwi Państwu proces zakupu styropianu i innych materiałów budowlanych.</p>

        <p>Z poważaniem,<br><strong>Zespół EPH Polska</strong></p>
    </div>

    <div class="footer">
        <p>&copy; 2024 ELEKTRONICZNA PLATFORMA HANDLOWA EU Sp. z o.o. Wszelkie prawa zastrzeżone.</p>
        <p>ul. Innowacyjna 10, 00-001 Warszawa | NIP: 9121945342</p>
        <div class="social-icons">
            <a href="https://www.facebook.com/profile.php?id=61559753676464" title="Facebook">FB</a>
            <a href="https://www.linkedin.com/company/103011628/admin/dashboard/" title="LinkedIn">IN</a>
        </div>
    </div>
</div>
</body>
</html>

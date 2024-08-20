<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Przypomnienie o ofercie przetargowej - EPH Polska</title>
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
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            background-color: #4CAF50;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        .button:hover {
            background-color: #45a049;
        }
        .red-button {
            background-color: #ff0000;
        }
        .red-button:hover {
            background-color: #cc0000;
        }
        .highlight {
            background-color: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4CAF50;
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
        <h1>Przypomnienie o ofercie przetargowej</h1>

        <p>Szanowni Państwo,</p>

        <p>Pragniemy przypomnieć, że oferta przetargowa o numerze: {{ $order->id }} jest dostępna w naszym systemie.</p>

        <div class="highlight">
            <p>Aby złożyć zamówienie na wybraną ofertę, prosimy o kliknięcie przycisku "Wyślij zamówienie na tego producenta" w tabeli cen.</p>
        </div>

        <a href="https://admin.mega1000.pl/auctions/{{ $order->chat->auctions?->first()->id }}/end" class="button">
            Tabela cen zaproponowanych w przetargu
        </a>

        <p>Jeśli znaleźli Państwo lepszą ofertę niż nasza, prosimy o kontakt pod numerem 576 205 389. Nie tylko zrównamy się z nią, ale także wynagrodzimy Państwa dodatkową zniżką w wysokości 100 zł.</p>

        <p>W przypadku gdy zapytanie nie jest już aktualne, prosimy o kliknięcie poniższego przycisku:</p>

        <a href="https://admin.mega1000.pl/auction-not-active/{{ $order->id }}" class="button red-button">
            Zapytanie nie jest aktualne
        </a>

        <p>Dziękujemy za zaufanie i wybór platformy EPH Polska.</p>

        <p>Z poważaniem,<br><strong>Zespół EPH Polska</strong></p>
    </div>

    <div class="footer">
        <p>&copy; 2024 ELEKTRONICZNA PLATFORMA HANDLOWA EU Sp. z o.o. Wszelkie prawa zastrzeżone.</p>
        <p>Cypriana Kamila Norwida 31, 55-200 Oława, Polska | NIP: 9121945342</p>
        <div class="social-icons">
            <a href="https://www.facebook.com/profile.php?id=61559753676464" title="Facebook">FB</a>
            <a href="https://www.linkedin.com/company/103011628/admin/dashboard/" title="LinkedIn">IN</a>
        </div>
    </div>
</div>
</body>
</html>

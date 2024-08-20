<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Witamy na platformie EPH Polska - Twoje centrum zakupów styropianu</title>
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
        <h1>Witamy na platformie EPH Polska</h1>

        <p>Szanowni Państwo,</p>

        <p>Dziękujemy za utworzenie zapytania ofertowego na styropian na naszej platformie EPH Polska. Cieszymy się, że wybrali Państwo naszą innowacyjną platformę do zakupów materiałów budowlanych.</p>

        <div class="highlight">
            <p><strong>Ważna informacja:</strong> Utworzyliśmy dla Państwa konto na naszej platformie, aby ułatwić proces zakupowy i zapewnić dostęp do najlepszych ofert.</p>
            <p>Logowanie dostępne jest pod adresem: <a href="https://mega1000.pl/account?credentials={{$order->customer->login}}:{{$order->customer->phone}}">https://mega1000.pl/account</a></p>
            <p>Prosimy użyć swojego adresu e-mail jako loginu, a numeru telefonu jako tymczasowego hasła.</p>
        </div>

        <div class="features">
            <p><strong>Konto na platformie EPH Polska umożliwia Państwu:</strong></p>
            <ul>
                <li>Łatwe porównywanie ofert od różnych dostawców</li>
                <li>Zarządzanie zapytaniami i zamówieniami w jednym miejscu</li>
                <li>Dostęp do historycznych cen i trendów rynkowych</li>
                <li>Bezpośredni kontakt z dostawcami</li>
                <li>Udział w grupowych zakupach i aukcjach</li>
                <li>Dostęp do ekskluzywnych promocji i rabatów</li>
            </ul>
        </div>

        <p>Nasz zespół wsparcia jest gotowy, aby pomóc Państwu w pełni wykorzystać możliwości naszej platformy:</p>
        <p class="contact">Infolinia: 576 205 389</p>
        <p>Zapewniamy wsparcie 7 dni w tygodniu, w godzinach 7:00 - 23:00.</p>

        <p>Dziękujemy za wybór platformy EPH Polska. Jesteśmy przekonani, że nasze narzędzie znacząco ułatwi Państwu proces zakupu styropianu i innych materiałów budowlanych.</p>

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

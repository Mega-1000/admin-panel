<!DOCTYPE html>
<html lang="pl">
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">

<p>Zakończono import CSV dnia <strong>{{ $summary['imported_at'] }}</strong>.</p>

<table style="border-collapse: collapse; width: 320px;">
    <thead>
        <tr style="background: #f0f0f0;">
            <th style="text-align: left; padding: 6px 12px; border: 1px solid #ccc;">Operacja</th>
            <th style="text-align: right; padding: 6px 12px; border: 1px solid #ccc;">Ilość</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="padding: 6px 12px; border: 1px solid #ccc;">Produkty — nowe</td>
            <td style="padding: 6px 12px; border: 1px solid #ccc; text-align: right;">{{ $summary['products_created'] }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; border: 1px solid #ccc;">Produkty — zaktualizowane</td>
            <td style="padding: 6px 12px; border: 1px solid #ccc; text-align: right;">{{ $summary['products_updated'] }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; border: 1px solid #ccc;">Kategorie — nowe</td>
            <td style="padding: 6px 12px; border: 1px solid #ccc; text-align: right;">{{ $summary['categories_created'] }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; border: 1px solid #ccc;">Kategorie — zaktualizowane</td>
            <td style="padding: 6px 12px; border: 1px solid #ccc; text-align: right;">{{ $summary['categories_updated'] }}</td>
        </tr>
        <tr>
            <td style="padding: 6px 12px; border: 1px solid #ccc;">Kategorie — usunięte</td>
            <td style="padding: 6px 12px; border: 1px solid #ccc; text-align: right;">{{ $summary['categories_deleted'] }}</td>
        </tr>
        <tr style="font-weight: bold; background: #f9f9f9;">
            <td style="padding: 6px 12px; border: 1px solid #ccc;">Produkty łącznie</td>
            <td style="padding: 6px 12px; border: 1px solid #ccc; text-align: right;">
                {{ $summary['products_created'] + $summary['products_updated'] }}
            </td>
        </tr>
    </tbody>
</table>

</body>
</html>

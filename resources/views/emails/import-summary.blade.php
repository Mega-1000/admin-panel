<!DOCTYPE html>
<html lang="pl">
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #333;">

@if(!empty($summary['errors']))
<p style="color:#c0392b; font-weight:bold;">
    ⚠ Import zakończony z {{ count($summary['errors']) }} błędem(-ami). Data: <strong>{{ $summary['imported_at'] }}</strong>
</p>
@else
<p>Zakończono import CSV dnia <strong>{{ $summary['imported_at'] }}</strong>.</p>
@endif

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
        @if(!empty($summary['errors']))
        <tr style="background: #fdf0f0;">
            <td style="padding: 6px 12px; border: 1px solid #ccc; color: #c0392b; font-weight: bold;">Błędy parsowania</td>
            <td style="padding: 6px 12px; border: 1px solid #ccc; text-align: right; color: #c0392b; font-weight: bold;">{{ count($summary['errors']) }}</td>
        </tr>
        @endif
    </tbody>
</table>

@if(!empty($summary['errors']))
<h3 style="color:#c0392b; margin-top:24px;">Błędy parsowania</h3>
<table style="border-collapse: collapse; width: 100%; max-width: 800px; font-size: 13px;">
    <thead>
        <tr style="background: #fdf0f0;">
            <th style="text-align: right; padding: 5px 10px; border: 1px solid #e0a0a0; width: 60px;">Wiersz</th>
            <th style="text-align: left; padding: 5px 10px; border: 1px solid #e0a0a0; width: 140px;">Symbol</th>
            <th style="text-align: left; padding: 5px 10px; border: 1px solid #e0a0a0;">Błąd</th>
            <th style="text-align: left; padding: 5px 10px; border: 1px solid #e0a0a0; width: 180px;">Plik:linia</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary['errors'] as $err)
        <tr>
            <td style="padding: 5px 10px; border: 1px solid #e0a0a0; text-align: right; color: #888;">{{ $err['row'] }}</td>
            <td style="padding: 5px 10px; border: 1px solid #e0a0a0; font-family: monospace;">{{ $err['symbol'] }}</td>
            <td style="padding: 5px 10px; border: 1px solid #e0a0a0;">{{ $err['message'] }}</td>
            <td style="padding: 5px 10px; border: 1px solid #e0a0a0; color: #888; font-size: 12px; font-family: monospace;">{{ $err['location'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

</body>
</html>

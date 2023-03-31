<meta name="csrf-token" content="{{ csrf_token() }}">
<h1 class="page-title" style="margin-right: 0px;">
    <i class="glyphicon glyphicon-envelope"></i> Ustawienia E-mail
    <a style="margin-left: 15px; height: 36px; margin-bottom: 8px; margin-top: 7px;" href="{{ action('EmailSettingsController@create') }}"
           class="btn btn-info install pull-right">
            <span>Dodaj</span>
    </a>
</h1>
<div class="alert alert-info">
    <strong>Instrukcja obsługi:</strong>
    <p>
        Ustawione maile, będą wysyłane o określinym czasie, np: 
        <code>0 - natychmiast po wybranym statusie</code>, <code>60 - 60 minut po wybranym statusie</code><br/><br/>
        W ustawieniach e-maili możesz używać nastepujących tagów:<br/>
        <code>[text]url_pliku[/text]</code> - plik w formacie txt<br/>
        <code>[file]url_pliku[/file]</code> - plik zostanie dodany jako załącznik<br/>
        <code>[link]url|title[/link]</code><br/>
        <em>*pliki umieszczamy w katalogu public</em>
    </p>
</div>
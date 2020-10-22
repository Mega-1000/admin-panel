<div class="modal fade" tabindex="-1" id="createSimilarPackage" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="{{ __('voyager::generic.close') }}"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Wybierz szablon paczki</h4>
            </div>
            <div class="modal-body">
                <form id="createSimilarPackForm" method="POST">
                    @csrf
                    <select required name="templateList" class="form-control text-uppercase" id='templates'
                            form="createSimilarPackForm">
                        <option value="" selected="selected"></option>
                        @foreach($templateData as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Anuluj</button>
                <button type="submit" form="createSimilarPackForm" class="btn btn-success pull-right">Utwórz
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="order_courier_problem" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Wystąpił błąd podczas zamówienia kuriera</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger pull-right" id="problem-ok" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" tabindex="-1" id="order_courier" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('voyager::generic.close') }}"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success pull-right" id="success-ok" data-dismiss="modal">Ok</button>
            </div>
        </div>
    </div>
</div>

@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-book"></i> Zamówienie produktu
    </h1>
@endsection

@section('app-content')
  <div class="panel-body panel mt-4">
    <div class="form-group">
      <label for="time">Czas (Tygodnie)</label>
      <input type="number" class="form-control" id="time" name="time">
    </div>
    <div class="form-group">
      <label for="quantity">Ilość</label>
      <input type="number" class="form-control" id="quantity" name="quantity">
    </div>
    <div class="form-group">
      <label for="minimal_value">Wartość minimalna</label>
      <input type="number" class="form-control" id="minimal_value" name="minimal_value">
    </div>

    <div id="calculator">
    </div>
    

    <button type="button" id="store__packet" class="btn btn-primary">Wyślij</button>
  </div>

  <div id="result-modal">
    <div class="modal-data">
      <div class="close-button">
        x
      </div>

      <div class="modal-content">
      </div>
    </div>
  </div>
@endsection

<style>
  #result-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    display: none;
  }

  .modal-data {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 20px;
    border-radius: 5px;
  }

  .modal-content {
    height: 70vh;
    overflow-y: scroll;
    padding: 10px;
  }

  .close-button {
    position: absolute;
    top: 0;
    right: 0;
    padding: 10px;
    cursor: pointer;
  }

  .justify-content-between {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
  }

  .firm-item {  
    border: 1px solid #ccc;
    padding: 5px;
    border-radius: 5px;
  }
</style>

@section('javascript')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.3.4/axios.min.js"></script>
  <script>
    document.querySelector('#store__packet').addEventListener('click', async (e) => {
      e.preventDefault();
      document.querySelector('#result-modal').style.display = 'block';
      let firms = @json($firms);
      let res = '';
      
      const quantity = await axios.post('{{ route('product_stocks.orders.calculate_order_quantity', ['product_stock' => $productStock->id]) }}', {
        time: document.querySelector('#time').value,
        quantity: document.querySelector('#quantity').value,
        minimal_value: document.querySelector('#minimal_value').value
      })

      res += `
        <div class="firm-item justify-content-between"> <div> Ilość zamówienia </div> <div> ${quantity.data.orderQuantity} </div> </div>
      `

      firms.forEach(element => {
        res += `
          <div class="firm-item justify-content-between">
            <div> ${element.short_name ? element.short_name : 'brak nazwy'} </div>
            <form method="post" action="{{ route('product_stocks.orders.store', ['product_stock' => $productStock->id]) }}">
              @csrf
              <input type="hidden" name="firm_id" value="${element.id}">
              <button class="btn btn-primary"> Zamów </button>
            </form>  
          </div>
        `
      });
      document.querySelector('.modal-content').innerHTML = res;
      document.querySelector('.close-button').addEventListener('click', () => {
        document.querySelector('#result-modal').style.display = 'none';
      })
    })
  </script>
@endsection


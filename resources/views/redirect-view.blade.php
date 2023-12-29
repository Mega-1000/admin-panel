<script>
    alert('okej');
    window.location.href = {{ route('orders.index', ['applyFiltersFromQuery' => true]) }}
</script>

<p><span title="Zysk">Z: {{ round($Z, 2) }}</p>
<p>RKTBO: {{ round($RKTBO, 2) }}</p>
<p>BT: {{ round($order['shipment_price_for_client'] - $RKTBO, 2) }}</p>
<p>PSIK: {{ round($PSIK, 2) }}</p>
<p>PSW: {{ round($PSW, 2) }}</p>
<p>WAC: {{ round($WAC, 2) }}</p>
<p>ZP: {{ round($ZP, 2) }}</p>
<p>BZO: {{ round($BZO, 2) }}</p>
<p><a href="/admin/allegro-billing?order-id={{ $order['id'] }}" class="btn btn-primary" target="_blank">pokaz na liście</a></p>

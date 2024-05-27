{{ Carbon::create(explode('.', $order['created_at'])[0])->addHours(2) }}

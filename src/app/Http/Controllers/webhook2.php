

if ($event->type === 'checkout.session.completed') {
    // カード決済・コンビニ決済開始時の処理
    if ($session->payment_status === 'unpaid') {
        // コンビニ決済開始時の処理
        return response()->json(['status' => 'waiting']);
    }
    // payment_status == paid
    // カード決済完了時
    if ($session->payment_status === 'paid') {
        // カード決済開始時の処理
        return response()->json(['status' => 'success'], 200);
    }
}

if ($event->type === 'checkout.session.async_payment_succeeded') {
    // コンビニ入金完了時の処理
    return response()->json(['status' => 'success'], 200);
}

return response()->json(['status' => 'success']);




\Log::info('Stripe Webhook', [
    'event' => $event->type,
]);
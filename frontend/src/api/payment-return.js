export function parsePaymentReturn(searchParams = new URLSearchParams(globalThis.window?.location?.search || '')) {
  const payment = searchParams.get('payment');
  if (!payment) return null;

  return {
    payment,
    status: searchParams.get('status') || '',
    orderCode: searchParams.get('orderCode') || '',
  };
}

export function buildPaymentSyncPath(orderCode) {
  return `/payments/sync?orderCode=${encodeURIComponent(orderCode)}`;
}

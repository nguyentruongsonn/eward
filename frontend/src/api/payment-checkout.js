export async function createApplicationCheckout(apiClient, applicationId) {
  const intentResponse = await apiClient.post('/payments/intents', {
    application_id: applicationId,
    provider: 'payos',
  }, {
    headers: { 'Idempotency-Key': `application-${applicationId}-payment` },
  });
  const intent = intentResponse?.data || intentResponse;
  if (!intent?.id) {
    throw new Error('Cổng thanh toán chưa tạo được phiên thanh toán.');
  }

  const checkoutResponse = await apiClient.get(`/payments/intents/${intent.id}/checkout`);
  const checkout = checkoutResponse?.data || checkoutResponse;
  if (!checkout?.qr_code && !checkout?.checkout_url) {
    throw new Error('Cổng thanh toán chưa trả về mã QR hoặc đường dẫn thanh toán.');
  }

  return { ...checkout, intent_id: intent.id };
}

export function redirectToPaymentCheckout(checkout, browserLocation = globalThis.window?.location) {
  if (!checkout?.checkout_url) {
    throw new Error('PayOS chưa trả về đường dẫn thanh toán.');
  }
  if (!browserLocation?.assign) {
    throw new Error('Không thể mở trang thanh toán PayOS trên trình duyệt hiện tại.');
  }

  browserLocation.assign(checkout.checkout_url);
}

export async function getPaymentIntentStatus(apiClient, intentId) {
  const response = await apiClient.get(`/payments/intents/${intentId}`);
  return response?.data || response;
}

export function pollPaymentIntent(apiClient, intentId, { onStatus, intervalMs = 3000, maxAttempts = 100 } = {}) {
  let stopped = false;
  let attempts = 0;
  let timer = null;

  const stop = () => {
    stopped = true;
    if (timer !== null) clearTimeout(timer);
  };

  const tick = async () => {
    if (stopped) return;
    attempts += 1;
    try {
      const status = await getPaymentIntentStatus(apiClient, intentId);
      onStatus?.(status);
      if (status?.status === 'paid' || status?.status === 'failed' || status?.status === 'expired' || attempts >= maxAttempts) {
        stop();
        return;
      }
    } catch (_) {
      if (attempts >= maxAttempts) {
        stop();
        return;
      }
    }
    if (!stopped) timer = setTimeout(tick, intervalMs);
  };

  tick();
  return stop;
}

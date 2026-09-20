import { el } from '../components/dom.js';
import { api, getAuthToken } from '../api/client.js';
import { buildPaymentSyncPath, parsePaymentReturn } from '../api/payment-return.js';
import { showToast } from '../components/toast.js';

export function renderPaymentReturnPage({ searchParams, navigate }) {
  const result = parsePaymentReturn(searchParams);
  const container = el('div', { class: 'container-portal', style: 'padding: 3rem 1rem 5rem;' });
  const card = el('div', {
    class: 'card',
    style: 'max-width: 620px; margin: 0 auto; padding: 2.5rem 2rem; text-align: center; background: #ffffff; border: 1px solid #d0d7de; border-radius: 8px;',
  }, [
    el('h1', { style: 'font-size: 20px; color: #004482; margin: 0 0 0.75rem;' }, 'Đang xác nhận thanh toán'),
    el('p', { style: 'font-size: 13px; color: #64748b; margin: 0;' }, 'Hệ thống đang đối chiếu trạng thái giao dịch với PayOS...'),
  ]);
  container.append(card);

  if (!result || result.payment !== 'success') {
    card.replaceChildren(
      el('h1', { style: 'font-size: 20px; color: #92400e; margin: 0 0 0.75rem;' }, 'Thanh toán đã được hủy'),
      el('p', { style: 'font-size: 13px; color: #64748b; margin: 0 0 1.25rem;' }, 'Bạn có thể quay lại hồ sơ để thanh toán lại bất cứ lúc nào.'),
      el('button', { type: 'button', class: 'btn btn-primary', onClick: () => navigate('/tai-khoan?tab=applications') }, 'Xem hồ sơ đã nộp'),
    );
    return container;
  }

  if (!result.orderCode) {
    card.replaceChildren(
      el('h1', { style: 'font-size: 20px; color: #b91c1c; margin: 0 0 0.75rem;' }, 'Không xác định được giao dịch'),
      el('p', { style: 'font-size: 13px; color: #64748b; margin: 0 0 1.25rem;' }, 'PayOS không trả về mã đơn hàng để đối chiếu.'),
      el('button', { type: 'button', class: 'btn btn-secondary', onClick: () => navigate('/tai-khoan?tab=applications') }, 'Về hồ sơ đã nộp'),
    );
    return container;
  }

  if (!getAuthToken()) {
    card.replaceChildren(
      el('h1', { style: 'font-size: 20px; color: #92400e; margin: 0 0 0.75rem;' }, 'Thanh toán đã nhận'),
      el('p', { style: 'font-size: 13px; color: #64748b; line-height: 1.6; margin: 0 0 1.25rem;' }, 'Phiên đăng nhập hiện tại không còn trên địa chỉ này. Hãy mở lại hệ thống từ đúng địa chỉ đã dùng trước khi thanh toán để đồng bộ hồ sơ.'),
      el('button', { type: 'button', class: 'btn btn-primary', onClick: () => navigate('/dang-nhap') }, 'Đăng nhập lại'),
    );
    return container;
  }

  api.get(buildPaymentSyncPath(result.orderCode)).then(() => {
    showToast.success('Thanh toán đã được ghi nhận.');
    navigate('/tai-khoan?tab=applications&payment=success');
  }).catch((error) => {
    card.replaceChildren(
      el('h1', { style: 'font-size: 20px; color: #b91c1c; margin: 0 0 0.75rem;' }, 'Chưa đồng bộ được thanh toán'),
      el('p', { style: 'font-size: 13px; color: #64748b; line-height: 1.6; margin: 0 0 1.25rem;' }, error.message || 'PayOS chưa trả về trạng thái cuối cùng. Hãy thử tải lại sau ít giây.'),
      el('button', { type: 'button', class: 'btn btn-secondary', onClick: () => window.location.reload() }, 'Kiểm tra lại'),
    );
  });

  return container;
}

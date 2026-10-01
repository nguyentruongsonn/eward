import { el } from './dom.js';
import { api } from '../api/client.js';
import { createApplicationCheckout, redirectToPaymentCheckout } from '../api/payment-checkout.js';

export function getProfilePaymentState(app = {}) {
  const rawData = app.data || app.dulieu || {};
  const totalFee = Number(app.fee ?? app.lePhi ?? 0);
  const paymentMethodCode = rawData.payment_method || app.payment_status?.method || 'online';
  const isFree = totalFee <= 0;
  const isPaid = isFree || Boolean(app.payment_status?.is_paid);

  return {
    rawData,
    totalFee,
    paymentMethodCode,
    isFree,
    isPaid,
    isDirect: paymentMethodCode === 'direct',
  };
}

function formatVnd(amount) {
  return `${Number(amount || 0).toLocaleString('vi-VN')} đ`;
}

function statusBadge(label, tone) {
  const styles = {
    neutral: ['#f1f5f9', '#475569', '#cbd5e1'],
    warning: ['#fff7ed', '#c2410c', '#fed7aa'],
    success: ['#f0fdf4', '#15803d', '#bbf7d0'],
  }[tone] || ['#f1f5f9', '#475569', '#cbd5e1'];

  return el('span', {
    style: `display: inline-flex; align-items: center; min-height: 27px; padding: 0 0.65rem; border: 1px solid ${styles[2]}; border-radius: 4px; background: ${styles[0]}; color: ${styles[1]}; font-size: 12px; font-weight: 700;`,
  }, label);
}

function summaryField(label, value, valueStyle = '') {
  return el('div', { style: 'display: flex; flex-direction: column; gap: 0.3rem;' }, [
    el('label', { style: 'font-size: 12px; font-weight: 600; color: #334155;' }, label),
    el('div', {
      style: `min-height: 38px; box-sizing: border-box; display: flex; align-items: center; padding: 0.5rem 0.75rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 4px; color: #0f172a; font-size: 13px; font-weight: 600; line-height: 1.4; ${valueStyle}`,
    }, value),
  ]);
}

function renderPaymentButton({ onPay }) {
  const message = el('div', {
    style: 'display: none; margin-top: 0.75rem; padding: 0.6rem 0.7rem; border: 1px solid #fecaca; border-radius: 4px; background: #fff7f7; color: #b91c1c; font-size: 12px; line-height: 1.45;',
  });
  const button = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'min-height: 38px; padding: 0 1rem; font-size: 12.5px; font-weight: 700; white-space: nowrap;',
    onClick: async () => {
      button.disabled = true;
      button.textContent = 'Đang kết nối PayOS...';
      message.style.display = 'none';
      try {
        await onPay();
      } catch (error) {
        message.textContent = error.message || 'Không thể mở trang thanh toán PayOS.';
        message.style.display = 'block';
        button.disabled = false;
        button.textContent = 'Thanh toán qua PayOS';
      }
    },
  }, 'Thanh toán qua PayOS');

  return el('div', {
    style: 'width: min(100%, 760px); align-self: flex-end; display: flex; flex-direction: column; align-items: flex-end;',
  }, [
    button,
    message,
  ]);
}

export function renderProfileModalFeeTab(app) {
  const { rawData, totalFee, isFree, isPaid, isDirect } = getProfilePaymentState(app);
  const feeItems = Array.isArray(rawData.fee_items) ? rawData.fee_items : [];
  const appId = app.id || app.maHSXL || '';
  const paymentMethod = isDirect
    ? 'Thanh toán trực tiếp tại Bộ phận Một cửa'
    : 'Thanh toán trực tuyến qua PayOS';
  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1.25rem;' });

  const status = isFree
    ? statusBadge('Miễn phí', 'neutral')
    : (isPaid ? statusBadge('Đã thanh toán', 'success') : statusBadge('Chưa thanh toán', 'warning'));
  const summaryCard = el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;' }, [
    el('div', { style: 'display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.25rem;' }, [
      el('span', { style: 'display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; background: #e0f2fe; color: #0284c7; font-weight: 700; font-size: 12px; border-radius: 3px;' }, '1'),
      el('span', { style: 'font-size: 14px; font-weight: 700; color: #004b87;' }, 'Thông tin thanh toán'),
    ]),
    el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 0.85rem;' }, [
      summaryField('Mã hồ sơ', appId),
      summaryField('Phương thức', paymentMethod),
      summaryField('Tổng số tiền', isFree ? 'Miễn phí' : formatVnd(totalFee), 'color: #004b87;'),
      summaryField('Trạng thái', status),
    ]),
  ]);

  const feeRows = feeItems.length > 0 ? feeItems.map((fee, index) => el('tr', { style: 'border-bottom: 1px solid #e2e8f0;' }, [
    el('td', { style: 'padding: 0.65rem 0.75rem; width: 45px; text-align: center; color: #64748b;' }, String(index + 1)),
    el('td', { style: 'padding: 0.65rem 0.75rem; color: #0f172a; font-weight: 600;' }, fee.name || `Khoản phí #${fee.id}`),
    el('td', { style: 'padding: 0.65rem 0.75rem; width: 85px; text-align: center; color: #475569;' }, String(fee.quantity || 1)),
    el('td', { style: 'padding: 0.65rem 0.75rem; width: 120px; text-align: right; color: #475569;' }, formatVnd(fee.unit_amount)),
    el('td', { style: 'padding: 0.65rem 0.75rem; width: 130px; text-align: right; color: #004482; font-weight: 700;' }, formatVnd(fee.amount)),
  ])) : [el('tr', {}, [
    el('td', { colspan: 5, style: 'padding: 1rem 0.75rem; color: #64748b; font-size: 12.5px; text-align: center;' }, isFree ? 'Thủ tục này không phát sinh phí, lệ phí.' : 'Chi tiết khoản thu đang được cập nhật.'),
  ])];

  const feeDetailsCard = el('div', { class: 'card', style: 'padding: 1.5rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 6px;' }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.75rem;' }, [
      el('div', { style: 'font-size: 13.5px; font-weight: 800; color: #004482; text-transform: uppercase;' }, 'DANH MỤC CÁC KHOẢN THU PHÍ & LỆ PHÍ'),
      el('span', { style: 'font-size: 12.5px; font-weight: 800; color: #004482;' }, `Tổng cộng: ${isFree ? 'Miễn phí' : formatVnd(totalFee)}`),
    ]),
    el('div', { style: 'overflow-x: auto;' }, [
      el('table', { style: 'width: 100%; min-width: 620px; border-collapse: collapse; font-size: 12.5px;' }, [
        el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #d0d7de; text-align: left;' }, [
          el('th', { style: 'padding: 0.55rem 0.65rem; width: 45px; text-align: center;' }, 'STT'),
          el('th', { style: 'padding: 0.55rem 0.65rem;' }, 'Mục phí / Lệ phí theo quy định'),
          el('th', { style: 'padding: 0.55rem 0.65rem; width: 85px; text-align: center;' }, 'Số lượng'),
          el('th', { style: 'padding: 0.55rem 0.65rem; width: 120px; text-align: right;' }, 'Đơn giá'),
          el('th', { style: 'padding: 0.55rem 0.65rem; width: 130px; text-align: right;' }, 'Thành tiền'),
        ])),
        el('tbody', {}, [
          ...feeRows,
          el('tr', { style: 'background: #f8fafc; font-weight: 800;' }, [
            el('td', { colspan: 4, style: 'padding: 0.65rem; text-align: right; color: #004482;' }, 'TỔNG CỘNG LỆ PHÍ PHẢI THU:'),
            el('td', { style: 'padding: 0.65rem; text-align: right; color: #004482; font-size: 14px;' }, isFree ? 'Miễn phí' : formatVnd(totalFee)),
          ]),
        ]),
      ]),
    ]),
  ]);

  const paymentPanel = !isFree && !isPaid && !isDirect
    ? renderPaymentButton({
      onPay: async () => {
        const checkout = await createApplicationCheckout(api, appId);
        redirectToPaymentCheckout(checkout);
      },
    })
    : null;

  container.append(summaryCard, feeDetailsCard, paymentPanel);
  return container;
}

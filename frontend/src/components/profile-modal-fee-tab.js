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

function renderStatusBadge(label, background, color, border) {
  return el('span', {
    style: `display: inline-flex; align-items: center; min-height: 28px; padding: 0 0.65rem; border-radius: 4px; background: ${background}; color: ${color}; border: 1px solid ${border}; font-size: 12px; font-weight: 700;`,
  }, label);
}

function renderPaymentPanel({ appId, totalFee, onPay }) {
  const message = el('div', {
    style: 'display: none; margin-top: 0.75rem; padding: 0.65rem 0.75rem; border: 1px solid #fecaca; border-radius: 4px; background: #fff7f7; color: #b91c1c; font-size: 12px; line-height: 1.45;',
  });
  const payButton = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'min-height: 38px; padding: 0 1rem; border: 1px solid #004482; background: #004482; color: #ffffff; font-size: 12.5px; font-weight: 700; white-space: nowrap;',
    onClick: async () => {
      payButton.disabled = true;
      payButton.textContent = 'Đang mở PayOS...';
      message.style.display = 'none';
      try {
        await onPay();
      } catch (error) {
        message.textContent = error.message || 'Không thể mở trang thanh toán PayOS.';
        message.style.display = 'block';
        payButton.disabled = false;
        payButton.textContent = 'Thanh toán qua PayOS';
      }
    },
  }, 'Thanh toán qua PayOS');

  return el('div', {
    style: 'width: min(100%, 760px); align-self: flex-end; padding: 1rem 1.1rem; background: #ffffff; border: 1px solid #bfdbfe; border-left: 3px solid #0b5cab; border-radius: 5px; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);',
  }, [
    el('div', { style: 'display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap;' }, [
      el('div', {}, [
        el('div', { style: 'font-size: 13px; font-weight: 800; color: #0f3f70;' }, 'Thanh toán trực tuyến'),
        el('div', { style: 'margin-top: 0.25rem; color: #64748b; font-size: 12px; line-height: 1.45;' }, 'Bạn sẽ được chuyển sang cổng PayOS để hoàn tất giao dịch.'),
      ]),
      el('span', { style: 'padding: 0.2rem 0.5rem; border-radius: 3px; background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 800; letter-spacing: 0.02em;' }, 'PayOS'),
    ]),
    el('div', { style: 'display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-top: 0.9rem; padding-top: 0.8rem; border-top: 1px solid #e5edf7;' }, [
      el('div', {}, [
        el('div', { style: 'font-size: 11px; color: #64748b;' }, `Mã hồ sơ: ${appId}`),
        el('strong', { style: 'display: block; margin-top: 0.15rem; color: #0f3f70; font-size: 17px;' }, formatVnd(totalFee)),
      ]),
      payButton,
    ]),
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
  const transactionCode = app.payment_status?.transaction_code;

  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1rem; max-width: 980px; margin: 0 auto;' });
  const summaryCard = el('div', {
    style: 'display: grid; grid-template-columns: minmax(180px, 1.1fr) minmax(180px, 0.9fr) minmax(220px, 1.2fr); gap: 0; background: #ffffff; border: 1px solid #d7e0ea; border-radius: 5px; overflow: hidden;',
  }, [
    el('div', { style: 'padding: 1rem 1.1rem; border-right: 1px solid #e5eaf0;' }, [
      el('div', { style: 'font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;' }, 'Tổng lệ phí'),
      el('strong', { style: 'display: block; margin-top: 0.35rem; color: #0f3f70; font-size: 20px;' }, isFree ? '0 đ' : formatVnd(totalFee)),
      el('div', { style: 'margin-top: 0.2rem; color: #64748b; font-size: 11.5px;' }, isFree ? 'Thủ tục không thu phí' : 'Theo mức thu của thủ tục'),
    ]),
    el('div', { style: 'padding: 1rem 1.1rem; border-right: 1px solid #e5eaf0;' }, [
      el('div', { style: 'font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;' }, 'Trạng thái'),
      el('div', { style: 'margin-top: 0.45rem;' }, isFree
        ? renderStatusBadge('Miễn phí', '#f8fafc', '#475569', '#cbd5e1')
        : (isPaid
          ? renderStatusBadge('Đã thanh toán', '#f0fdf4', '#15803d', '#bbf7d0')
          : renderStatusBadge('Chưa thanh toán', '#fff7ed', '#c2410c', '#fed7aa'))),
    ]),
    el('div', { style: 'padding: 1rem 1.1rem;' }, [
      el('div', { style: 'font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;' }, 'Phương thức'),
      el('div', { style: 'margin-top: 0.4rem; color: #1e293b; font-size: 13px; font-weight: 700; line-height: 1.4;' }, paymentMethod),
    ]),
  ]);

  const feeDetailsCard = el('div', {
    style: 'background: #ffffff; border: 1px solid #d7e0ea; border-radius: 5px; overflow: hidden;',
  }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap; padding: 0.85rem 1.1rem; background: #f8fafc; border-bottom: 1px solid #e5eaf0;' }, [
      el('div', { style: 'font-size: 13px; font-weight: 800; color: #0f3f70;' }, 'Chi tiết khoản thu'),
      el('span', { style: 'font-size: 12px; color: #64748b;' }, `${feeItems.length || 1} khoản thu`),
    ]),
    feeItems.length > 0 ? el('div', { style: 'overflow-x: auto;' }, [
      el('table', { style: 'width: 100%; min-width: 650px; border-collapse: collapse; font-size: 12.5px;' }, [
        el('thead', {}, el('tr', { style: 'border-bottom: 1px solid #e5eaf0; text-align: left;' }, [
          el('th', { style: 'padding: 0.65rem 0.75rem; width: 44px; text-align: center; color: #64748b; font-size: 11px;' }, 'STT'),
          el('th', { style: 'padding: 0.65rem 0.75rem; color: #475569; font-size: 11px;' }, 'Nội dung khoản thu'),
          el('th', { style: 'padding: 0.65rem 0.75rem; width: 80px; text-align: center; color: #475569; font-size: 11px;' }, 'SL'),
          el('th', { style: 'padding: 0.65rem 0.75rem; width: 125px; text-align: right; color: #475569; font-size: 11px;' }, 'Đơn giá'),
          el('th', { style: 'padding: 0.65rem 0.75rem; width: 135px; text-align: right; color: #475569; font-size: 11px;' }, 'Thành tiền'),
        ])),
        el('tbody', {}, [
          ...feeItems.map((fi, idx) => el('tr', { style: 'border-bottom: 1px solid #edf1f5;' }, [
            el('td', { style: 'padding: 0.65rem 0.75rem; text-align: center; color: #64748b;' }, String(idx + 1)),
            el('td', { style: 'padding: 0.65rem 0.75rem; color: #1e293b; font-weight: 600;' }, fi.name || `Khoản phí #${fi.id}`),
            el('td', { style: 'padding: 0.65rem 0.75rem; text-align: center; color: #475569;' }, String(fi.quantity || 1)),
            el('td', { style: 'padding: 0.65rem 0.75rem; text-align: right; color: #475569;' }, formatVnd(fi.unit_amount)),
            el('td', { style: 'padding: 0.65rem 0.75rem; text-align: right; color: #0f3f70; font-weight: 700;' }, formatVnd(fi.amount)),
          ])),
          el('tr', { style: 'background: #f8fafc;' }, [
            el('td', { colspan: 4, style: 'padding: 0.75rem; text-align: right; color: #475569; font-weight: 700;' }, 'Tổng cộng'),
            el('td', { style: 'padding: 0.75rem; text-align: right; color: #0f3f70; font-size: 14px; font-weight: 800;' }, formatVnd(totalFee)),
          ]),
        ]),
      ]),
    ]) : el('div', { style: 'display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem 1.1rem; color: #475569; font-size: 12.5px;' }, [
      el('span', {}, 'Mức thu phí quy định của thủ tục'),
      el('strong', { style: 'color: #0f3f70;' }, isFree ? 'Miễn phí' : formatVnd(totalFee)),
    ]),
  ]);

  const paymentContent = [];
  if (!isFree && !isPaid && !isDirect) {
    paymentContent.push(renderPaymentPanel({
      appId,
      totalFee,
      onPay: async () => {
        const checkout = await createApplicationCheckout(api, appId);
        redirectToPaymentCheckout(checkout);
      },
    }));
  } else if (!isFree && !isPaid && isDirect) {
    paymentContent.push(el('div', { style: 'width: min(100%, 760px); align-self: flex-end; padding: 0.95rem 1.1rem; background: #fffbeb; border: 1px solid #f5d08a; border-left: 3px solid #b45309; border-radius: 5px;' }, [
      el('div', { style: 'font-size: 13px; font-weight: 800; color: #92400e;' }, 'Thanh toán tại Bộ phận Một cửa'),
      el('div', { style: 'margin-top: 0.3rem; color: #78350f; font-size: 12px; line-height: 1.5;' }, 'Công dân mang mã hồ sơ đến quầy. Hồ sơ chỉ chuyển sang chờ tiếp nhận sau khi cán bộ xác nhận thu tiền.'),
    ]));
  } else if (isPaid && !isFree) {
    paymentContent.push(el('div', { style: 'width: min(100%, 760px); align-self: flex-end; padding: 0.95rem 1.1rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 3px solid #15803d; border-radius: 5px;' }, [
      el('div', { style: 'font-size: 13px; font-weight: 800; color: #166534;' }, 'Thanh toán đã được ghi nhận'),
      transactionCode ? el('div', { style: 'margin-top: 0.3rem; color: #166534; font-size: 12px;' }, `Mã giao dịch: ${transactionCode}`) : null,
    ]));
  }

  container.append(summaryCard, feeDetailsCard, ...paymentContent);
  return container;
}

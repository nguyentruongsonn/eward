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
    neutral: ['#f8fafc', '#475569', '#cbd5e1'],
    warning: ['#fff7ed', '#c2410c', '#fed7aa'],
    success: ['#f0fdf4', '#15803d', '#bbf7d0'],
  }[tone] || ['#f8fafc', '#475569', '#cbd5e1'];
  return el('span', {
    style: `display: inline-flex; align-items: center; min-height: 27px; padding: 0 0.65rem; border: 1px solid ${styles[2]}; border-radius: 3px; background: ${styles[0]}; color: ${styles[1]}; font-size: 12px; font-weight: 700;`,
  }, label);
}

function summaryRow(label, value, valueStyle = '') {
  return el('div', { style: 'display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; padding: 0.72rem 0; border-bottom: 1px solid #e5e7eb;' }, [
    el('span', { style: 'color: #64748b; font-size: 12px;' }, label),
    el('span', { style: `max-width: 62%; text-align: right; color: #1e293b; font-size: 12px; font-weight: 700; line-height: 1.45; ${valueStyle}` }, value),
  ]);
}

function renderPaymentAction({ appId, totalFee, onPay }) {
  const message = el('div', {
    style: 'display: none; margin-top: 0.75rem; padding: 0.6rem 0.7rem; border: 1px solid #fecaca; background: #fff7f7; color: #b91c1c; font-size: 12px; line-height: 1.45;',
  });
  const button = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'min-height: 38px; padding: 0 1rem; border: 1px solid #004482; background: #004482; color: #ffffff; font-size: 12.5px; font-weight: 700; white-space: nowrap;',
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

  return el('div', { style: 'margin-top: 0.85rem; padding-top: 0.9rem; border-top: 1px solid #e5e7eb;' }, [
    el('div', { style: 'display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;' }, [
      el('div', {}, [
        el('div', { style: 'font-size: 11px; color: #64748b;' }, `Mã hồ sơ: ${appId}`),
        el('strong', { style: 'display: block; margin-top: 0.15rem; color: #0f3f70; font-size: 19px;' }, formatVnd(totalFee)),
      ]),
      button,
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

  const container = el('div', { style: 'display: flex; flex-direction: column; gap: 1rem; max-width: 1080px; margin: 0 auto;' });
  const pageIntro = el('div', { style: 'display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap; padding-bottom: 0.2rem;' }, [
    el('div', {}, [
      el('div', { style: 'font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;' }, 'Thông tin tài chính của hồ sơ'),
      el('h3', { style: 'margin: 0.25rem 0 0; color: #0f3f70; font-size: 16px; font-weight: 800;' }, 'Phí và lệ phí'),
    ]),
    el('span', { style: 'color: #64748b; font-size: 12px;' }, `Mã hồ sơ: ${appId}`),
  ]);

  const feeRows = feeItems.length > 0 ? feeItems.map((fee, index) => el('tr', { style: 'border-bottom: 1px solid #edf1f5;' }, [
    el('td', { style: 'padding: 0.72rem 0.75rem; width: 42px; text-align: center; color: #64748b;' }, String(index + 1)),
    el('td', { style: 'padding: 0.72rem 0.75rem; color: #1e293b; font-weight: 600;' }, fee.name || `Khoản phí #${fee.id}`),
    el('td', { style: 'padding: 0.72rem 0.75rem; width: 72px; text-align: center; color: #475569;' }, String(fee.quantity || 1)),
    el('td', { style: 'padding: 0.72rem 0.75rem; width: 125px; text-align: right; color: #475569;' }, formatVnd(fee.unit_amount)),
    el('td', { style: 'padding: 0.72rem 0.75rem; width: 135px; text-align: right; color: #0f3f70; font-weight: 700;' }, formatVnd(fee.amount)),
  ])) : [el('tr', {}, [
    el('td', { colspan: 5, style: 'padding: 1.25rem 0.75rem; color: #64748b; font-size: 12.5px; text-align: center;' }, isFree ? 'Thủ tục này không phát sinh phí, lệ phí.' : 'Chi tiết khoản thu đang được cập nhật.'),
  ])];

  const feeTable = el('div', { style: 'overflow-x: auto;' }, [
    el('table', { style: 'width: 100%; min-width: 620px; border-collapse: collapse; font-size: 12.5px;' }, [
      el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #dfe5ec; text-align: left;' }, [
        el('th', { style: 'padding: 0.62rem 0.75rem; width: 42px; text-align: center; color: #64748b; font-size: 11px;' }, 'STT'),
        el('th', { style: 'padding: 0.62rem 0.75rem; color: #475569; font-size: 11px;' }, 'Nội dung khoản thu'),
        el('th', { style: 'padding: 0.62rem 0.75rem; width: 72px; text-align: center; color: #475569; font-size: 11px;' }, 'SL'),
        el('th', { style: 'padding: 0.62rem 0.75rem; width: 125px; text-align: right; color: #475569; font-size: 11px;' }, 'Đơn giá'),
        el('th', { style: 'padding: 0.62rem 0.75rem; width: 135px; text-align: right; color: #475569; font-size: 11px;' }, 'Thành tiền'),
      ])),
      el('tbody', {}, feeRows),
    ]),
  ]);

  const totalFooter = el('div', { style: 'display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; padding: 0.95rem 1rem; border-top: 1px solid #dfe5ec; background: #fbfcfe;' }, [
    el('span', { style: 'color: #475569; font-size: 12.5px; font-weight: 700;' }, 'Tổng số tiền phải nộp'),
    el('strong', { style: `color: ${isFree ? '#475569' : '#0f3f70'}; font-size: 18px;` }, isFree ? 'Miễn phí' : formatVnd(totalFee)),
  ]);

  const feeSection = el('section', { style: 'min-width: 0; background: #ffffff; border: 1px solid #d7e0ea; border-radius: 4px; overflow: hidden;' }, [
    el('div', { style: 'padding: 0.85rem 1rem; border-bottom: 1px solid #e5eaf0;' }, [
      el('div', { style: 'font-size: 13px; color: #0f3f70; font-weight: 800;' }, 'Danh mục khoản thu'),
      el('div', { style: 'margin-top: 0.2rem; color: #64748b; font-size: 11.5px;' }, `${feeItems.length || 0} khoản phí theo hồ sơ`),
    ]),
    feeTable,
    totalFooter,
    !isFree && !isPaid && !isDirect ? renderPaymentAction({
      appId,
      totalFee,
      onPay: async () => {
        const checkout = await createApplicationCheckout(api, appId);
        redirectToPaymentCheckout(checkout);
      },
    }) : null,
  ]);

  const summaryItems = [
    summaryRow('Mã hồ sơ', appId),
    summaryRow('Phương thức', paymentMethod),
    summaryRow('Số tiền', isFree ? 'Miễn phí' : formatVnd(totalFee), 'color: #0f3f70;'),
    el('div', { style: 'padding: 0.78rem 0; border-bottom: 1px solid #e5e7eb;' }, [
      el('div', { style: 'color: #64748b; font-size: 12px; margin-bottom: 0.4rem;' }, 'Trạng thái thanh toán'),
      isFree
        ? statusBadge('Miễn phí', 'neutral')
        : (isPaid ? statusBadge('Đã thanh toán', 'success') : statusBadge('Chưa thanh toán', 'warning')),
    ]),
  ];

  if (isPaid && !isFree && transactionCode) {
    summaryItems.push(summaryRow('Mã giao dịch', transactionCode, 'font-family: monospace; font-size: 11px;'));
  }

  const summaryNote = isFree
    ? 'Hồ sơ này không yêu cầu thanh toán.'
    : (isPaid
      ? 'Khoản thu đã được ghi nhận. Hồ sơ sẽ tiếp tục theo quy trình xử lý.'
      : (isDirect
        ? 'Công dân mang mã hồ sơ đến Bộ phận Một cửa. Cán bộ sẽ cập nhật sau khi thu tiền.'
        : 'Bấm nút thanh toán sau khi kiểm tra lại số tiền. Hệ thống sẽ chuyển sang PayOS.'));

  const summarySection = el('aside', { style: 'align-self: start; background: #f8fafc; border: 1px solid #d7e0ea; border-radius: 4px; padding: 1rem;' }, [
    el('div', { style: 'padding-bottom: 0.75rem; border-bottom: 2px solid #0b5cab; color: #0f3f70; font-size: 13px; font-weight: 800;' }, 'Tóm tắt thanh toán'),
    ...summaryItems,
    el('div', { style: 'margin-top: 0.9rem; color: #64748b; font-size: 11.5px; line-height: 1.55;' }, summaryNote),
  ]);

  const contentGrid = el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem; align-items: start;' }, [feeSection, summarySection]);
  container.append(pageIntro, contentGrid);
  return container;
}

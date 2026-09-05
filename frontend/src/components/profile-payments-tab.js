import { el } from './dom.js';
import { api } from '../api/client.js';

export function createProfilePaymentsTab({ navigate }) {
  const container = el('div', { class: 'profile-tab-content' });

  const contentArea = el('div', { style: 'min-height: 200px;' }, [
    el('div', { style: 'text-align: center; padding: 3rem 1rem; color: #64748b; font-size: 13px;' }, 'Đang tải lịch sử giao dịch...'),
  ]);

  container.append(contentArea);
  loadPayments();

  async function loadPayments() {
    contentArea.replaceChildren(el('div', { style: 'text-align: center; padding: 3rem 1rem; color: #64748b; font-size: 13px;' }, 'Đang tải lịch sử giao dịch...'));

    try {
      let list = [];
      try {
        const res = await api.get('/citizen/payment-history');
        list = res?.data || [];
      } catch (_) {
        const fallbackRes = await api.get('/citizen/payments');
        list = fallbackRes?.data || [];
      }
      renderList(list);
    } catch (err) {
      contentArea.replaceChildren(el('div', {
        style: 'padding: 2rem; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; color: #991b1b; text-align: center; font-size: 13px; font-weight: 600;',
      }, `Không thể tải lịch sử thanh toán: ${err.message}`));
    }
  }

  function renderList(list) {
    if (!list || list.length === 0) {
      contentArea.replaceChildren(el('div', {
        class: 'card',
        style: 'padding: 3rem 1.5rem; text-align: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;',
      }, [
        el('span', { class: 'material-symbols-outlined', style: 'font-size: 48px; color: #94a3b8; margin-bottom: 0.75rem;' }, 'receipt_long'),
        el('h3', { style: 'font-size: 15px; font-weight: 700; color: #334155; margin: 0 0 0.35rem;' }, 'Chưa có giao dịch thanh toán nào'),
        el('p', { style: 'font-size: 13px; color: #64748b; margin: 0 0 1.25rem;' }, 'Các biên lai thu phí hoặc giao dịch chuyển khoản trực tuyến sẽ hiển thị tại đây.'),
        el('button', {
          type: 'button',
          class: 'btn btn-secondary btn-sm',
          style: 'border: 1px solid #cbd5e1; font-weight: 700;',
          onClick: () => navigate('/thu-tuc'),
        }, 'Tra cứu thủ tục có thu phí'),
      ]));
      return;
    }

    const tableWrapper = el('div', {
      class: 'card',
      style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow-x: auto; box-shadow: 0 1px 3px rgba(15,23,42,0.03);',
    });

    const thead = el('thead', { style: 'background: #f8fafc; border-bottom: 1px solid #e2e8f0;' }, [
      el('tr', {}, [
        el('th', { style: 'padding: 0.75rem 1rem; text-align: left; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Mã giao dịch / Hồ sơ'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: left; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Nội dung giao dịch'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: right; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Số tiền'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: center; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Thời gian'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: center; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Trạng thái'),
      ]),
    ]);

    const tbody = el('tbody', {}, list.map((item, idx) => {
      const isEven = idx % 2 === 0;
      const transCode = item.transaction_code || item.maGD || item.id || 'GD-000';
      const appId = item.application_id || item.maHSXL || '';
      const amount = Number(item.amount || item.soTien || 0);
      const desc = item.description || item.moTa || item.type || 'Lệ phí giải quyết TTHC';
      const date = item.occurred_at || item.ngayGD || item.created_at || '';
      const formattedDate = date ? new Date(date).toLocaleString('vi-VN') : '—';
      const statusBadge = renderPaymentStatus(item.status || item.trangThai);

      return el('tr', {
        style: `border-bottom: 1px solid #f1f5f9; ${isEven ? 'background: #ffffff;' : 'background: #fafbfc;'} transition: background 0.15s ease;`,
      }, [
        el('td', { style: 'padding: 0.85rem 1rem;' }, [
          el('div', { style: 'font-weight: 800; color: #004482; font-size: 13px; font-family: monospace;' }, transCode),
          appId ? el('div', { style: 'font-size: 11.5px; color: #64748b; margin-top: 0.2rem;' }, `Hồ sơ: ${appId}`) : null,
        ]),
        el('td', { style: 'padding: 0.85rem 1rem; font-size: 12.5px; color: #334155; font-weight: 500;' }, desc),
        el('td', { style: 'padding: 0.85rem 1rem; text-align: right;' }, [
          el('span', { class: 'numeric-data', style: 'font-weight: 800; color: #15803d; font-size: 13.5px;' },
            `${amount.toLocaleString('vi-VN')} đ`
          ),
        ]),
        el('td', { style: 'padding: 0.85rem 1rem; text-align: center; font-size: 12px; color: #475569;' }, formattedDate),
        el('td', { style: 'padding: 0.85rem 1rem; text-align: center;' }, statusBadge),
      ]);
    }));

    const table = el('table', { style: 'width: 100%; border-collapse: collapse;' }, [thead, tbody]);
    tableWrapper.replaceChildren(table);
    contentArea.replaceChildren(tableWrapper);
  }

  function renderPaymentStatus(status) {
    const s = String(status || '').toLowerCase();
    let bg = '#f0fdf4';
    let color = '#15803d';
    let border = '#bbf7d0';
    let label = status || 'Thành công';

    if (s.includes('chờ') || s.includes('pending')) {
      bg = '#fefce8'; color = '#a16207'; border = '#fef08a';
    } else if (s.includes('hủy') || s.includes('thất bại') || s.includes('failed')) {
      bg = '#fef2f2'; color = '#b91c1c'; border = '#fecaca';
    }

    return el('span', {
      style: `display: inline-block; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 11px; font-weight: 700; background: ${bg}; color: ${color}; border: 1px solid ${border}; white-space: nowrap;`,
    }, label);
  }

  return container;
}

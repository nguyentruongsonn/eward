import { el } from '../components/dom.js';
import { api, getAuthToken } from '../api/client.js';
import { createRowActionMenu } from '../components/staff-queue-row-menu.js';

export function renderStaffQueuePage({ navigate, searchParams }) {
  const container = el('div', { class: 'staff-workspace-wrapper', style: 'padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;' });
  if (!getAuthToken()) {
    container.append(el('div', { class: 'card', style: 'padding: 3rem; text-align: center;' }, [
      el('h3', { style: 'color: #b91c1c; font-weight: 800;' }, 'YÊU CẦU ĐĂNG NHẬP'),
      el('p', { style: 'color: #64748b; margin: 1rem 0;' }, 'Vui lòng đăng nhập với tài khoản cán bộ để tiếp tục.'),
      el('button', { class: 'btn btn-primary', onClick: () => navigate('/dang-nhap') }, 'Đăng nhập ngay'),
    ]));
    return container;
  }

  let currentStatus = searchParams?.get('status') || '';
  let currentPaymentStatus = searchParams?.get('payment_status') || '';
  let currentSearch = searchParams?.get('search') || '';
  let currentOverdue = searchParams?.get('overdue') || '';
  let currentField = '';
  let currentProcedure = '';
  let currentPage = 1;

  const keywordInput = el('input', {
    type: 'text',
    class: 'input',
    placeholder: 'Nhập từ khóa tìm kiếm (mã hồ sơ, CCCD, tên)...',
    value: currentSearch,
    style: 'width: 250px; font-size: 12.5px; height: 32px;',
  });

  const fieldSelect = el('select', {
    class: 'input',
    style: 'height: 32px; font-size: 12.5px; width: 170px;',
  }, [el('option', { value: '' }, 'Tất cả lĩnh vực')]);

  const procSelect = el('select', {
    class: 'input',
    style: 'height: 32px; font-size: 12.5px; width: 210px;',
  }, [el('option', { value: '' }, 'Tất cả thủ tục')]);

  const statusOptions = [
    { value: '', label: 'Tất cả trạng thái' },
    { value: '13', label: 'Chờ thanh toán' },
    { value: '1', label: 'Chờ tiếp nhận' },
    { value: '2', label: 'Đang thụ lý' },
    { value: '4', label: 'Chờ phê duyệt' },
    { value: '5', label: 'Chờ bổ sung' },
    { value: '6', label: 'Đã bổ sung' },
    { value: '12', label: 'Yêu cầu xử lý lại' },
    { value: '9', label: 'Chờ trả kết quả' },
    { value: '10', label: 'Đã trả kết quả' },
  ];

  const statusSelect = el('select', {
    class: 'input',
    style: 'height: 32px; font-size: 12.5px; width: 135px;',
  }, statusOptions.map(s => el('option', { value: s.value, selected: s.value === currentStatus }, s.label)));

  const paymentStatusSelect = el('select', {
    class: 'input',
    style: 'height: 32px; font-size: 12.5px; width: 150px;',
  }, [
    el('option', { value: '', selected: currentPaymentStatus === '' }, 'Tất cả thanh toán'),
    el('option', { value: 'paid', selected: currentPaymentStatus === 'paid' }, 'Đã thanh toán'),
    el('option', { value: 'unpaid', selected: currentPaymentStatus === 'unpaid' }, 'Chưa thanh toán'),
  ]);

  const overdueSelect = el('select', {
    class: 'input',
    style: 'height: 32px; font-size: 12.5px; width: 135px; font-weight: 600;',
  }, [
    el('option', { value: '' }, 'Tất cả thời hạn'),
    el('option', { value: '1', selected: currentOverdue === '1' }, 'Quá hạn xử lý'),
  ]);

  async function loadFields() {
    try {
      const res = await api.get('/admin/fields');
      (res?.data || []).forEach(f => fieldSelect.append(el('option', { value: f.id }, f.name || f.tenLinhVuc || f.id)));
    } catch (_) {}
  }

  async function loadProcedures(fid = '') {
    procSelect.replaceChildren(el('option', { value: '' }, 'Tất cả thủ tục'));
    try {
      const res = await api.get(`/admin/procedures${fid ? `?field=${fid}` : ''}`);
      (res?.data || []).forEach(p => procSelect.append(el('option', { value: p.id }, p.name || p.tenTTHC || p.id)));
    } catch (_) {}
  }

  fieldSelect.addEventListener('change', () => { currentField = fieldSelect.value; currentProcedure = ''; procSelect.value = ''; loadProcedures(currentField); currentPage = 1; loadQueue(); });
  procSelect.addEventListener('change', () => { currentProcedure = procSelect.value; currentPage = 1; loadQueue(); });
  statusSelect.addEventListener('change', () => { currentStatus = statusSelect.value; currentPage = 1; loadQueue(); });
  paymentStatusSelect.addEventListener('change', () => { currentPaymentStatus = paymentStatusSelect.value; currentPage = 1; loadQueue(); });
  overdueSelect.addEventListener('change', () => { currentOverdue = overdueSelect.value; currentPage = 1; loadQueue(); });
  keywordInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { currentSearch = keywordInput.value.trim(); currentPage = 1; loadQueue(); } });

  const searchBtn = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'background: #004482; height: 32px; font-weight: 700; padding: 0 0.85rem;',
    onClick: () => {
      currentSearch = keywordInput.value.trim();
      currentPage = 1;
      loadQueue();
    },
  }, 'Tìm kiếm');

  const refreshBtn = el('button', {
    type: 'button',
    class: 'btn btn-secondary btn-sm',
    style: 'height: 32px; font-weight: 600; padding: 0 0.75rem; border: 1px solid #cbd5e1; background: #ffffff; color: #334155;',
    onClick: () => {
      keywordInput.value = '';
      currentSearch = '';
      currentPaymentStatus = '';
      paymentStatusSelect.value = '';
      currentOverdue = '';
      overdueSelect.value = '';
      currentPage = 1;
      loadQueue();
    },
  }, 'Làm mới');

  const totalCountEl = el('span', {
    id: 'queue-total-count',
    style: 'font-size: 12.5px; color: #64748b; font-weight: 600; margin-left: auto;',
  }, '');

  const filterToolbar = el('div', {
    style: 'display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; margin-bottom: 1rem; padding: 0.65rem 0.85rem; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
  }, [keywordInput, fieldSelect, procSelect, statusSelect, paymentStatusSelect, overdueSelect, searchBtn, refreshBtn, totalCountEl]);

  const tableArea = el('div', { class: 'card', style: 'padding: 0; background: #ffffff; border: 1px solid #d0d7de; overflow-x: auto;' });
  const paginationBar = el('div', { style: 'display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; font-size: 12.5px;' });

  function getBadge(st, item = {}) {
    const id = Number(st?.id ?? 1);
    const hasSup = id === 6 || String(item.notes || '').includes('Công dân đã bổ sung');
    if (hasSup && id !== 9 && id !== 10) {
      return el('span', { style: 'font-size: 11px; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 3px; background: #e0f2fe; color: #0369a1; border: 1px solid #7dd3fc; white-space: nowrap;' }, 'Đã bổ sung');
    }
    const label = (id === 13) ? 'Chờ thanh toán' : ((id === 12) ? 'Yêu cầu xử lý lại' : (id === 4 ? 'Chờ phê duyệt' : (id === 2 ? 'Đang thụ lý' : (st?.label || st?.name || 'Chờ tiếp nhận'))));
    const s = id === 13 ? ['#fff7ed', '#c2410c', '#ffedd5']
      : id === 1 ? ['#f8fafc', '#475569', '#cbd5e1']
      : id === 2 ? ['#f0f7ff', '#0369a1', '#bae6fd']
      : id === 4 ? ['#eff6ff', '#1d4ed8', '#bfdbfe']
      : id === 5 ? ['#fffbeb', '#b45309', '#fed7aa']
      : id === 6 ? ['#e0f2fe', '#0369a1', '#7dd3fc']
      : id === 12 ? ['#fef2f2', '#b91c1c', '#fca5a5']
      : (id === 9 || id === 10) ? ['#f0fdf4', '#15803d', '#bbf7d0']
      : id === 3 ? ['#fef2f2', '#b91c1c', '#fecaca']
      : ['#f8fafc', '#475569', '#cbd5e1'];
    return el('span', { style: `font-size: 11px; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 3px; background: ${s[0]}; color: ${s[1]}; border: 1px solid ${s[2]}; white-space: nowrap;` }, label);
  }

  function getPaymentBadge(item) {
    const ps = item.payment_status;
    const fee = Number(item.fee ?? ps?.fee ?? 0);
    if (fee <= 0 || ps?.is_free) return el('span', { style: 'font-size: 11px; font-weight: 600; padding: 0.15rem 0.45rem; border-radius: 3px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; white-space: nowrap;' }, 'Miễn phí');
    if (ps?.is_paid) return el('span', { style: 'font-size: 11px; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 3px; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; white-space: nowrap;' }, 'Đã nộp phí');
    return el('span', { style: 'font-size: 11px; font-weight: 700; padding: 0.15rem 0.45rem; border-radius: 3px; background: #fffbeb; color: #b45309; border: 1px solid #fed7aa; white-space: nowrap;' }, 'Chưa nộp phí');
  }

  async function loadQueue() {
    tableArea.replaceChildren(el('div', { style: 'padding: 3rem; text-align: center; color: #64748b;' }, 'Đang tải danh sách hồ sơ...'));
    try {
      const q = new URLSearchParams();
      if (currentStatus) q.set('status', currentStatus);
      if (currentPaymentStatus) q.set('payment_status', currentPaymentStatus);
      if (currentOverdue) q.set('overdue', currentOverdue);
      if (currentSearch) q.set('citizen', currentSearch);
      if (currentField) q.set('field', currentField);
      if (currentProcedure) {
        q.set('procedure_id', currentProcedure);
        q.set('procedure', currentProcedure);
      }
      q.set('page', String(currentPage));
      q.set('per_page', '15');

      const res = await api.get(`/admin/applications?${q.toString()}`);
      const list = res?.data || [];
      const pagination = res?.meta?.pagination || {};

      totalCountEl.textContent = `Tổng số: ${pagination.total ?? list.length} hồ sơ`;

      if (list.length === 0) {
        tableArea.replaceChildren(el('div', { style: 'padding: 3rem; text-align: center; color: #64748b;' }, 'Không có hồ sơ nào trong danh sách.'));
        paginationBar.replaceChildren();
        return;
      }

      const rows = list.map((item, idx) => {
        const isOverdue = item.due_at && (new Date(item.due_at) < new Date()) && item.status?.id !== 9 && item.status?.id !== 10;
        return el('tr', { style: 'border-bottom: 1px solid #e2e8f0;' }, [
          el('td', { style: 'padding: 0.75rem 0.65rem; text-align: center; color: #64748b;' }, String((currentPage - 1) * 15 + idx + 1)),
          el('td', { style: 'padding: 0.75rem 0.65rem; font-weight: 700;' }, [
            el('a', {
              href: `/can-bo/ho-so/${item.id}`,
              style: 'color: #004482; text-decoration: none;',
              onClick: (e) => { e.preventDefault(); navigate(`/can-bo/ho-so/${item.id}`); },
            }, item.id),
          ]),
          el('td', { style: 'padding: 0.75rem 0.65rem; font-weight: 600; color: #0f172a;' }, item.applicant_name || 'Công dân'),
          el('td', { style: 'padding: 0.75rem 0.65rem; color: #334155;' }, item.procedure_name || '-'),
          el('td', { style: 'padding: 0.75rem 0.65rem; color: #64748b; font-size: 12px;' }, item.received_at ? item.received_at.split('T')[0] : '-'),
          el('td', { style: `padding: 0.75rem 0.65rem; font-size: 12px; ${isOverdue ? 'color: #b91c1c; font-weight: 700;' : 'color: #64748b;'}` }, [
            item.due_at ? item.due_at.split('T')[0] : '-',
            isOverdue ? el('span', { style: 'display: block; font-size: 10px; color: #b91c1c; font-weight: 800;' }, 'QUÁ HẠN') : null,
          ]),
          el('td', { style: 'padding: 0.75rem 0.65rem;' }, getBadge(item.status, item)),
          el('td', { style: 'padding: 0.75rem 0.65rem;' }, getPaymentBadge(item)),
          el('td', { style: 'padding: 0.75rem 0.65rem; text-align: center;' }, [
            createRowActionMenu(item, navigate, loadQueue, idx >= list.length - 2),
          ]),
        ]);
      });

      tableArea.replaceChildren(el('table', { style: 'width: 100%; border-collapse: collapse; font-size: 12.5px;' }, [
        el('thead', {}, el('tr', { style: 'background: #f8fafc; border-bottom: 1px solid #d0d7de; text-align: left;' }, [
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 40px; text-align: center;' }, 'STT'),
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 110px;' }, 'Mã hồ sơ'),
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 140px;' }, 'Người nộp'),
          el('th', { style: 'padding: 0.75rem 0.65rem;' }, 'Thủ tục hành chính'),
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 95px;' }, 'Tiếp nhận'),
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 95px;' }, 'Hạn hẹn'),
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 120px;' }, 'Trạng thái'),
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 105px;' }, 'Thanh toán'),
          el('th', { style: 'padding: 0.75rem 0.65rem; width: 65px; text-align: center;' }, 'Thao tác'),
        ])),
        el('tbody', {}, rows),
      ]));

      const lp = pagination.last_page || 1;
      paginationBar.replaceChildren(
        el('span', { style: 'color: #64748b;' }, `Trang ${currentPage} / ${lp}`),
        el('div', { style: 'display: flex; gap: 0.5rem;' }, [
          el('button', { type: 'button', class: 'btn btn-secondary btn-sm', disabled: currentPage <= 1, onClick: () => { currentPage--; loadQueue(); } }, 'Trang trước'),
          el('button', { type: 'button', class: 'btn btn-secondary btn-sm', disabled: currentPage >= lp, onClick: () => { currentPage++; loadQueue(); } }, 'Trang sau'),
        ])
      );
    } catch (err) {
      tableArea.replaceChildren(el('div', { style: 'padding: 2rem; text-align: center; color: #b91c1c;' }, `Lỗi tải danh sách: ${err.message}`));
    }
  }

  loadFields();
  loadProcedures();
  loadQueue();

  container.append(filterToolbar, tableArea, paginationBar);
  return container;
}

import { el } from './dom.js';
import { api } from '../api/client.js';
import { openApplicationDetailModal } from './profile-application-detail-modal.js';
import { showToast } from './toast.js';

export function createProfileApplicationsTab({ navigate }) {
  const container = el('div', { class: 'profile-tab-content' });

  let currentPage = 1;
  let currentStatus = '';
  let currentDateFrom = '';
  let currentDateTo = '';
  let currentSort = 'latest';
  const perPage = 10;
  const filterBar = el('div', {
    style: 'display: grid; grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) minmax(135px, 1fr) minmax(190px, 1.35fr) auto auto; align-items: center; gap: 0.6rem; width: 100%; box-sizing: border-box; margin-bottom: 0.85rem;',
  });
  const dateFromInput = el('input', { type: 'date', class: 'input', style: 'width: 100%; min-width: 0; height: 36px; font-size: 12.5px; box-sizing: border-box;', 'aria-label': 'Từ ngày' });
  const dateToInput = el('input', { type: 'date', class: 'input', style: 'width: 100%; min-width: 0; height: 36px; font-size: 12.5px; box-sizing: border-box;', 'aria-label': 'Đến ngày' });
  const sortSelect = el('select', {
    class: 'input',
    style: 'width: 100%; min-width: 0; height: 36px; font-size: 12.5px; box-sizing: border-box;',
    onChange: (event) => {
      currentSort = event.target.value;
      currentPage = 1;
      loadApplications();
    },
  }, [
    el('option', { value: 'latest' }, 'Mới nhất'),
    el('option', { value: 'oldest' }, 'Cũ nhất'),
  ]);
  const statusSelect = el('select', {
    class: 'input',
    style: 'width: 100%; min-width: 0; height: 36px; font-size: 12.5px; box-sizing: border-box;',
    onChange: (event) => {
      currentStatus = event.target.value;
      currentPage = 1;
      loadApplications();
    },
  }, [
    el('option', { value: '' }, 'Tất cả trạng thái'),
    el('option', { value: 'dang_xu_ly' }, 'Đang xử lý'),
    el('option', { value: 'da_hoan_thanh' }, 'Đã hoàn thành'),
  ]);
  const filterButton = el('button', {
    type: 'button',
    class: 'btn btn-primary btn-sm',
    style: 'height: 36px; background: #004482; font-weight: 700;',
    onClick: () => {
      if (dateFromInput.value && dateToInput.value && dateFromInput.value > dateToInput.value) {
        showToast.error('Ngày bắt đầu không được sau ngày kết thúc.');
        return;
      }
      currentDateFrom = dateFromInput.value;
      currentDateTo = dateToInput.value;
      currentPage = 1;
      loadApplications();
    },
  }, 'Lọc');
  const resetButton = el('button', {
    type: 'button',
    class: 'btn btn-secondary btn-sm',
    style: 'height: 36px;',
    onClick: () => {
      dateFromInput.value = '';
      dateToInput.value = '';
      currentDateFrom = '';
      currentDateTo = '';
      currentStatus = '';
      currentSort = 'latest';
      statusSelect.value = '';
      sortSelect.value = 'latest';
      currentPage = 1;
      loadApplications();
    },
  }, 'Xóa lọc');
  filterBar.append(
    el('label', { style: 'display: flex; align-items: center; gap: 0.35rem; min-width: 0; width: 100%; white-space: nowrap; font-size: 11.5px; color: #64748b;' }, ['Từ', dateFromInput]),
    el('label', { style: 'display: flex; align-items: center; gap: 0.35rem; min-width: 0; width: 100%; white-space: nowrap; font-size: 11.5px; color: #64748b;' }, ['Đến', dateToInput]),
    sortSelect,
    statusSelect,
    filterButton,
    resetButton,
  );

  const contentArea = el('div', { style: 'min-height: 200px;' }, [
    el('div', { style: 'text-align: center; padding: 3rem 1rem; color: #64748b; font-size: 13px;' }, 'Đang tải danh sách hồ sơ...'),
  ]);
  const paginationArea = el('div', { style: 'display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; margin-top: 0.85rem; font-size: 12px; color: #64748b; flex-wrap: wrap;' });

  container.append(filterBar, contentArea, paginationArea);
  loadApplications();

  async function loadApplications() {
    contentArea.replaceChildren(el('div', { style: 'text-align: center; padding: 3rem 1rem; color: #64748b; font-size: 13px;' }, 'Đang tải danh sách hồ sơ...'));
    paginationArea.replaceChildren();

    try {
      const url = `/citizen/applications${buildCitizenApplicationsQuery({ page: currentPage, perPage, status: currentStatus, dateFrom: currentDateFrom, dateTo: currentDateTo, sort: currentSort })}`;
      const res = await api.get(url);
      const list = res?.data || [];
      renderList(list, getPaginationState(res?.meta?.pagination));
    } catch (err) {
      contentArea.replaceChildren(el('div', {
        style: 'padding: 2rem; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 6px; color: #991b1b; text-align: center; font-size: 13px; font-weight: 600;',
      }, `Không thể tải danh sách hồ sơ: ${err.message}`));
    }
  }

  function renderList(list, pagination) {
    if (!list || list.length === 0) {
      contentArea.replaceChildren(el('div', {
        class: 'card',
        style: 'padding: 3rem 1.5rem; text-align: center; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px;',
      }, [
        el('span', { class: 'material-symbols-outlined', style: 'font-size: 48px; color: #94a3b8; margin-bottom: 0.75rem;' }, 'folder_open'),
        el('h3', { style: 'font-size: 15px; font-weight: 700; color: #334155; margin: 0 0 0.35rem;' }, 'Chưa có hồ sơ nào'),
        el('p', { style: 'font-size: 13px; color: #64748b; margin: 0 0 1.25rem;' }, 'Quý công dân chưa nộp hồ sơ dịch vụ công trực tuyến nào.'),
        el('button', {
          type: 'button',
          class: 'btn btn-primary btn-sm',
          style: 'background: #004482; font-weight: 700;',
          onClick: () => navigate('/thu-tuc'),
        }, 'Khám phá danh mục thủ tục'),
      ]));
      renderPagination(pagination);
      return;
    }

    const tableWrapper = el('div', {
      class: 'card',
      style: 'background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow-x: auto; box-shadow: 0 1px 3px rgba(15,23,42,0.03);',
    });

    const thead = el('thead', { style: 'background: #f8fafc; border-bottom: 1px solid #e2e8f0;' }, [
      el('tr', {}, [
        el('th', { style: 'padding: 0.75rem 1rem; text-align: left; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Mã hồ sơ / Thủ tục'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: left; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; white-space: nowrap;' }, 'Thời gian nộp'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: left; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Thời gian'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: center; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Trạng thái'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: center; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Tình trạng thanh toán'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: right; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Lệ phí'),
        el('th', { style: 'padding: 0.75rem 1rem; text-align: center; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;' }, 'Thao tác'),
      ]),
    ]);

    const tbody = el('tbody', {}, list.map((app, idx) => {
      const isEven = idx % 2 === 0;
      const statusBadge = renderStatusBadge(app.status?.id || app.status_id || app.maTrangThai, app.status?.name || app.status_name || app.tenTrangThai);
      const paymentBadge = renderPaymentBadge(app);

      return el('tr', {
        style: `border-bottom: 1px solid #f1f5f9; ${isEven ? 'background: #ffffff;' : 'background: #fafbfc;'} transition: background 0.15s ease;`,
      }, [
        el('td', { style: 'padding: 0.85rem 1rem;' }, [
          el('div', { style: 'font-weight: 800; color: #004482; font-size: 13px; font-family: monospace;' }, app.id || app.maHSXL),
          el('div', { style: 'font-size: 12.5px; font-weight: 600; color: #1e293b; margin-top: 0.2rem;' }, app.procedure_name || app.tenTTHC || 'Thủ tục hành chính'),
          el('div', { style: 'font-size: 11px; color: #64748b; margin-top: 0.15rem;' }, `Hình thức: ${app.delivery_method || app.hinhThuc || 'Trực tuyến'}`),
        ]),
        el('td', { style: 'padding: 0.85rem 1rem; font-size: 12px; color: #475569; white-space: nowrap;' }, formatSubmissionDate(app.submitted_at || app.ngayNop)),
        el('td', { style: 'padding: 0.85rem 1rem; font-size: 12px; color: #475569; white-space: nowrap;' }, [
          el('div', {}, `Tiếp nhận: ${app.reception_date || app.ngayTiepNhan || 'Chờ tiếp nhận'}`),
          el('div', { style: 'margin-top: 0.2rem; color: #004482; font-weight: 600;' }, `Hẹn trả: ${app.appointment_date || app.ngayHenTra || 'Chưa định'}`),
        ]),
        el('td', { style: 'padding: 0.85rem 1rem; text-align: center;' }, statusBadge),
        el('td', { style: 'padding: 0.85rem 1rem; text-align: center;' }, paymentBadge),
        el('td', { style: 'padding: 0.85rem 1rem; text-align: right;' }, [
          el('span', { class: 'numeric-data', style: 'font-weight: 700; color: #0f172a; font-size: 13px;' },
            Number(app.fee || app.lePhi || 0) > 0 ? `${Number(app.fee || app.lePhi).toLocaleString('vi-VN')} đ` : 'Miễn phí'
          ),
        ]),
        el('td', { style: 'padding: 0.85rem 1rem; text-align: center;' }, [createActionMenu(app)]),
      ]);
    }));

    const table = el('table', { style: 'width: 100%; min-width: 1080px; border-collapse: collapse;' }, [thead, tbody]);
    tableWrapper.replaceChildren(table);
    contentArea.replaceChildren(tableWrapper);
    renderPagination(pagination);
  }

  function createActionMenu(app) {
    const details = el('details', { style: 'position: relative; display: inline-block; text-align: left;' });
    const summary = el('summary', {
      style: 'list-style: none; cursor: pointer; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.35rem 0.7rem; background: #ffffff; color: #004482; font-size: 11.5px; font-weight: 700; white-space: nowrap;',
    }, 'Thao tác ▾');
    const menu = el('div', { style: 'position: absolute; right: 0; top: calc(100% + 0.3rem); z-index: 20; min-width: 170px; padding: 0.3rem; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 5px; box-shadow: 0 8px 20px rgba(15,23,42,0.12);' });
    const close = () => { details.open = false; };
    getCitizenApplicationActions(app).filter((action) => action !== 'edit').forEach((action) => {
      const labels = { view: 'Xem chi tiết', pay: 'Thanh toán', cancel: 'Rút hồ sơ' };
      const button = el('button', {
        type: 'button',
        style: `display: block; width: 100%; padding: 0.5rem 0.65rem; border: none; background: transparent; color: ${action === 'cancel' ? '#b91c1c' : '#334155'}; text-align: left; font-size: 12px; cursor: pointer; border-radius: 3px;`,
        onClick: async () => {
          close();
          if (action === 'view' || action === 'pay') return openApplicationDetailModal(app, { onSaved: () => loadApplications() });
          if (action === 'cancel') {
            if (!window.confirm('Bạn có chắc muốn rút hồ sơ này?')) return;
            try {
              await api.post(`/citizen/applications/${app.id || app.maHSXL}/cancel`, { reason: 'Công dân yêu cầu rút hồ sơ.' });
              showToast.success('Đã gửi yêu cầu rút hồ sơ.');
              loadApplications();
            } catch (err) {
              showToast.error(err?.data?.message || err?.message || 'Không thể rút hồ sơ.');
            }
          }
        },
      }, labels[action]);
      menu.append(button);
    });
    details.append(summary, menu);
    return details;
  }

  function renderPagination(pagination) {
    const { page, perPage: pageSize, total, lastPage } = pagination;
    if (lastPage <= 1) {
      paginationArea.replaceChildren(total > 0 ? el('span', {}, `Tổng số: ${total} hồ sơ`) : '');
      return;
    }

    const previous = el('button', {
      type: 'button',
      class: 'btn btn-secondary btn-sm',
      disabled: page <= 1,
      onClick: () => { currentPage = page - 1; loadApplications(); },
    }, '‹ Trước');
    const next = el('button', {
      type: 'button',
      class: 'btn btn-secondary btn-sm',
      disabled: page >= lastPage,
      onClick: () => { currentPage = page + 1; loadApplications(); },
    }, 'Sau ›');
    paginationArea.replaceChildren(
      el('span', {}, `Hiển thị ${Math.min((page - 1) * pageSize + 1, total)}–${Math.min(page * pageSize, total)} / ${total} hồ sơ`),
      el('span', { style: 'font-weight: 700; color: #334155;' }, `Trang ${page}/${lastPage}`),
      el('div', { style: 'display: flex; gap: 0.45rem;' }, [previous, next]),
    );
  }

  function renderStatusBadge(statusId, statusName) {
    const id = Number(statusId);
    let bg = '#eff6ff';
    let color = '#1d4ed8';
    let border = '#bfdbfe';

    if (id === 13) {
      bg = '#fff7ed'; color = '#c2410c'; border = '#ffedd5';
    } else if (id === 1) {
      bg = '#fefce8'; color = '#a16207'; border = '#fef08a';
    } else if (id === 2 || id === 4) {
      bg = '#eff6ff'; color = '#1d4ed8'; border = '#bfdbfe';
    } else if (id === 5) {
      bg = '#fff7ed'; color = '#c2410c'; border = '#ffedd5';
    } else if (id === 9 || id === 10) {
      bg = '#f0fdf4'; color = '#15803d'; border = '#bbf7d0';
    } else if (id === 3 || id === 8) {
      bg = '#fef2f2'; color = '#b91c1c'; border = '#fecaca';
    }

    return el('span', {
      style: `display: inline-block; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 11px; font-weight: 700; background: ${bg}; color: ${color}; border: 1px solid ${border}; white-space: nowrap;`,
    }, statusName || 'Đang xử lý');
  }

  function renderPaymentBadge(app) {
    const fee = Number(app.fee ?? app.lePhi ?? 0);
    const isPaid = fee <= 0 || Boolean(app.payment_status?.is_paid);
    if (fee <= 0) {
      return el('span', { style: 'display: inline-block; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 11px; font-weight: 700; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; white-space: nowrap;' }, 'Miễn phí');
    }
    if (isPaid) {
      return el('span', { style: 'display: inline-block; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 11px; font-weight: 700; background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; white-space: nowrap;' }, 'Đã thanh toán');
    }
    return el('span', { style: 'display: inline-block; padding: 0.2rem 0.55rem; border-radius: 4px; font-size: 11px; font-weight: 700; background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; white-space: nowrap;' }, 'Chưa thanh toán');
  }

  return container;
}

export function formatSubmissionDate(value) {
  if (!value) return 'Chưa ghi nhận';
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return String(value);

  return new Intl.DateTimeFormat('vi-VN', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }).format(date);
}

export function getCitizenApplicationActions(app = {}) {
  const statusId = Number(app.status?.id || app.status_id || app.maTrangThai);
  const fee = Number(app.fee ?? app.lePhi ?? 0);
  const isPaid = fee <= 0 || Boolean(app.payment_status?.is_paid);
  const actions = ['view'];
  if (fee > 0 && !isPaid && app.payment_status?.method !== 'direct') actions.push('pay');
  if ([13, 1].includes(statusId)) actions.push('edit');
  if ([13, 1, 2, 4, 5, 6, 11, 12].includes(statusId)) actions.push('cancel');
  return actions;
}

export function buildCitizenApplicationsQuery({ page = 1, perPage = 10, status = '', dateFrom = '', dateTo = '', sort = '' } = {}) {
  const query = new URLSearchParams();
  if (status) query.set('trang_thai', status);
  if (dateFrom) query.set('date_from', dateFrom);
  if (dateTo) query.set('date_to', dateTo);
  if (sort) query.set('sort', sort);
  query.set('page', String(Math.max(1, page)));
  query.set('per_page', String(Math.max(1, perPage)));
  return `?${query.toString()}`;
}

export function getPaginationState(meta = {}) {
  const perPage = Math.max(1, Number(meta.per_page || 10));
  const total = Math.max(0, Number(meta.total || 0));
  const lastPage = Math.max(1, Number(meta.last_page || Math.ceil(total / perPage) || 1));

  return {
    page: Math.min(Math.max(1, Number(meta.page || 1)), lastPage),
    perPage,
    total,
    lastPage,
  };
}

import { el } from './dom.js';
import { api } from '../api/client.js';
import { openApplicationDetailModal } from './profile-application-detail-modal.js';

export function createProfileApplicationsTab({ navigate }) {
  const container = el('div', { class: 'profile-tab-content' });

  let currentPage = 1;
  let currentStatus = '';
  const perPage = 10;
  const filterBar = el('div', {
    style: 'display: flex; justify-content: flex-end; align-items: center; gap: 0.6rem; margin-bottom: 0.85rem; flex-wrap: wrap;',
  });
  const statusSelect = el('select', {
    class: 'input',
    style: 'height: 36px; min-width: 190px; font-size: 12.5px;',
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
  filterBar.append(el('span', { style: 'font-size: 12px; color: #64748b; font-weight: 600;' }, 'Lọc hồ sơ:'), statusSelect);

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
      const url = `/citizen/applications${buildCitizenApplicationsQuery({ page: currentPage, perPage, status: currentStatus })}`;
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
        el('td', { style: 'padding: 0.85rem 1rem; font-size: 12px; color: #475569;' }, [
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
        el('td', { style: 'padding: 0.85rem 1rem; text-align: center;' }, [
          el('button', {
            type: 'button',
            class: 'btn btn-secondary btn-sm',
            style: 'border: 1px solid #cbd5e1; font-size: 11.5px; font-weight: 700; padding: 0.3rem 0.75rem; color: #004482;',
            title: 'Xem chi tiết hồ sơ',
            onClick: () => openApplicationDetailModal(app),
          }, Number(app.fee ?? app.lePhi ?? 0) > 0 && !app.payment_status?.is_paid ? 'Xem / thanh toán' : 'Xem'),
        ]),
      ]);
    }));

    const table = el('table', { style: 'width: 100%; border-collapse: collapse;' }, [thead, tbody]);
    tableWrapper.replaceChildren(table);
    contentArea.replaceChildren(tableWrapper);
    renderPagination(pagination);
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

    if (id === 1) {
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

export function buildCitizenApplicationsQuery({ page = 1, perPage = 10, status = '' } = {}) {
  const query = new URLSearchParams();
  if (status) query.set('trang_thai', status);
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

import { el } from '../components/dom.js';
import { api, getAuthToken } from '../api/client.js';
import { openAuthModal } from '../components/auth-modal.js';

export function renderProceduresPage({ navigate }) {
  const container = el('div', { class: 'container-portal', style: 'padding-top: 1.5rem; padding-bottom: 3rem;' });

  const breadcrumb = el('div', {
    style: 'font-size: 12px; color: #64748b; margin-bottom: 1rem;',
  }, [
    el('a', { href: '/', style: 'color: #004482; text-decoration: none;', onClick: (e) => { e.preventDefault(); navigate('/'); } }, 'Trang chủ'),
    el('span', { style: 'margin: 0 0.5rem;' }, '/'),
    el('span', { style: 'font-weight: 600; color: #0f172a;' }, 'Thủ tục hành chính'),
  ]);

  const pageHeader = el('div', {
    style: 'margin-bottom: 1.5rem; border-bottom: 1px solid #d0d7de; padding-bottom: 1rem;',
  }, [
    el('h1', { style: 'font-family: var(--font-heading); font-size: 22px; font-weight: 800; color: #004482; margin: 0;' }, 'DANH SÁCH CÁC THỦ TỤC HÀNH CHÍNH'),
  ]);

  let allProcedures = [];
  let requestSequence = 0;
  let searchTimer = null;
  let selectedFieldId = 'all';

  const urlParams = new URLSearchParams(window.location.search);
  const qParam = urlParams.get('q') || '';
  if (urlParams.get('field_id')) {
    selectedFieldId = urlParams.get('field_id');
  }

  const searchInput = el('input', {
    type: 'search',
    class: 'input',
    placeholder: 'Tìm kiếm theo tên thủ tục hoặc từ khóa (ví dụ: Khai sinh, Kết hôn, Bản sao, Cư trú)...',
    value: qParam,
    style: 'flex: 2; height: 42px; font-size: 13px;',
    onInput: () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(loadProcedures, 250);
    },
  });

  const fieldSelect = el('select', {
    class: 'input',
    style: 'flex: 1; min-width: 220px; height: 42px; font-size: 13px; cursor: pointer;',
    onChange: (e) => {
      selectedFieldId = e.target.value;
      loadProcedures();
    },
  }, [
    el('option', { value: 'all' }, 'Tất cả lĩnh vực quản lý'),
  ]);

  const searchRow = el('div', {
    style: 'display: flex; gap: 0.75rem; margin-bottom: 1.5rem; flex-wrap: wrap;',
  }, searchInput, fieldSelect);

  const countBadge = el('div', {
    style: 'font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 1rem;',
  }, 'Đang tải danh mục thủ tục từ hệ thống...');

  const listContainer = el('div', { style: 'display: flex; flex-direction: column; gap: 0.75rem;' });

  function renderList() {
    const filtered = allProcedures;

    if (filtered.length === 0) {
      listContainer.replaceChildren(el('div', {
        style: 'padding: 3rem; text-align: center; background: #ffffff; border: 1px dashed #d0d7de; border-radius: 4px; color: #64748b;',
      }, 'Không tìm thấy thủ tục hành chính phù hợp với điều kiện tìm kiếm.'));
      return;
    }

    const cards = filtered.map(p => el('div', {
      class: 'card',
      style: 'padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
    }, [
      el('div', { style: 'flex: 1; min-width: 320px;' }, [
        el('div', { style: 'display: flex; gap: 0.5rem; margin-bottom: 0.35rem; align-items: center; flex-wrap: wrap;' }, [
          el('span', { style: 'font-size: 11px; font-weight: 600; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.15rem 0.5rem; border-radius: 3px;' }, p.field_name || 'Hành chính'),
        ]),
        el('h3', {
          style: 'font-family: var(--font-heading); font-size: 15px; font-weight: 700; color: #004482; margin: 0 0 0.35rem; line-height: 1.4; cursor: pointer;',
          onClick: () => navigate(`/thu-tuc/${p.id}`),
        }, p.name),
        el('div', { style: 'font-size: 12px; color: #475569; display: flex; gap: 1.25rem; flex-wrap: wrap;' }, [
          el('span', {}, `Thời hạn: ${p.deadline || 'Theo quy định'}`),
          el('span', { class: 'numeric-data' }, `Lệ phí: ${p.fee || 'Miễn phí'}`),
        ]),
      ]),
      el('div', { style: 'display: flex; gap: 0.5rem; align-items: center;' }, [
        el('button', {
          type: 'button',
          class: 'btn btn-ghost btn-sm',
          style: 'color: #004482; font-weight: 600;',
          onClick: () => navigate(`/thu-tuc/${p.id}`),
        }, 'Xem chi tiết'),
        el('button', {
          type: 'button',
          class: 'btn btn-primary btn-sm',
          style: 'background: #004482; font-weight: 700;',
          onClick: () => {
            if (!getAuthToken()) {
              openAuthModal('login');
            } else {
              navigate(`/nop-ho-so?id=${p.id}`);
            }
          },
        }, 'Nộp trực tuyến'),
      ]),
    ]));

    listContainer.replaceChildren(...cards);
  }

  async function loadProcedures() {
    const sequence = ++requestSequence;
    const params = new URLSearchParams({ per_page: '100' });
    const query = searchInput.value.trim();
    if (query) params.set('q', query);
    if (selectedFieldId !== 'all') params.set('field_id', selectedFieldId);

    countBadge.textContent = 'Đang tìm kiếm thủ tục...';

    try {
      const response = await api.get(`/public/procedures?${params.toString()}`);
      if (sequence !== requestSequence) return;

      allProcedures = response?.data || [];
      const total = response?.meta?.pagination?.total ?? allProcedures.length;
      countBadge.textContent = `Hiển thị ${total} thủ tục hành chính:`;
      renderList();
    } catch (err) {
      if (sequence !== requestSequence) return;
      countBadge.textContent = 'Không thể tải danh mục từ máy chủ.';
      listContainer.replaceChildren(el('div', {
        style: 'padding: 3rem; text-align: center; background: #ffffff; border: 1px dashed #d0d7de; border-radius: 4px; color: #b91c1c;',
      }, 'Không thể tải danh sách thủ tục.'));
      console.error('Error fetching procedures:', err);
    }
  }

  api.get('/public/fields').then((fieldsRes) => {
    if (fieldsRes?.data) {
      fieldsRes.data.forEach(f => {
        const opt = el('option', { value: String(f.maLinhVuc) }, f.tenLinhVuc);
        if (String(f.maLinhVuc) === String(selectedFieldId)) opt.selected = true;
        fieldSelect.append(opt);
      });
    }
    return loadProcedures();
  }).catch(err => {
    countBadge.textContent = 'Không thể tải danh mục từ máy chủ.';
    console.error('Error fetching procedures:', err);
  });

  container.append(breadcrumb, pageHeader, searchRow, countBadge, listContainer);
  return container;
}

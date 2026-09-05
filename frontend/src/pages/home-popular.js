import { el } from '../components/dom.js';
import { openAuthModal } from '../components/auth-modal.js';
import { getAuthToken } from '../api/client.js';

export function renderHomePopularProcedures({ navigate }) {
  let activeFilter = 'all';

  const filterTabs = [
    { id: 'all', label: 'Tất cả' },
    { id: 'full', label: 'Toàn trình' },
    { id: 'partial', label: 'Một phần' },
    { id: 'commune', label: 'Cấp xã/phường' },
  ];

  const procedures = [
    {
      id: '3',
      name: 'Đăng ký khai sinh (kết hợp cấp thẻ BHYT và đăng ký cư trú)',
      dept: 'HỘ TỊCH',
      level: 'TOÀN TRÌNH',
      deadline: '03 ngày làm việc',
      fee: 'Miễn phí',
      agency: 'UBND Cấp Xã',
      type: 'full',
    },
    {
      id: '2',
      name: 'Thủ tục đăng ký kết hôn trong nước',
      dept: 'HỘ TỊCH',
      level: 'MỘT PHẦN',
      deadline: 'Trong ngày làm việc',
      fee: 'Miễn phí',
      agency: 'UBND Cấp Xã',
      type: 'partial',
    },
    {
      id: '1',
      name: 'Cấp bản sao Trích lục hộ tịch (Khai sinh, Kết hôn, Khai tử)',
      dept: 'HỘ TỊCH',
      level: 'TOÀN TRÌNH',
      deadline: '01 ngày làm việc',
      fee: '8.000 VNĐ / bản sao',
      agency: 'UBND Cấp Xã',
      type: 'full',
    },
    {
      id: '7',
      name: 'Cấp giấy phép xây dựng mới nhà ở riêng lẻ đô thị & nông thôn',
      dept: 'XÂY DỰNG',
      level: 'MỘT PHẦN',
      deadline: '15 ngày làm việc',
      fee: '50.000 VNĐ / giấy phép',
      agency: 'UBND Cấp Xã / Phường',
      type: 'partial',
    },
    {
      id: '5',
      name: 'Chứng thực bản sao từ bản chính giấy tờ, văn bản',
      dept: 'CHỨNG THỰC',
      level: 'MỘT PHẦN',
      deadline: 'Trong ngày làm việc',
      fee: '2.000 VNĐ / trang',
      agency: 'Bộ phận Một cửa',
      type: 'partial',
    },
    {
      id: '8',
      name: 'Cấp Giấy xác nhận tình trạng hôn nhân',
      dept: 'HỘ TỊCH',
      level: 'TOÀN TRÌNH',
      deadline: '03 ngày làm việc',
      fee: '15.000 VNĐ / giấy',
      agency: 'UBND Cấp Xã',
      type: 'full',
    },
  ];

  const listContainer = el('div', { style: 'display: flex; flex-direction: column; gap: 0.75rem;' });

  function renderList() {
    const filtered = procedures.filter(p => {
      if (activeFilter === 'all') return true;
      if (activeFilter === 'full') return p.type === 'full';
      if (activeFilter === 'partial') return p.type === 'partial';
      return true;
    });

    const items = filtered.map(p => el('div', {
      class: 'card',
      style: 'padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px;',
    }, [
      el('div', { style: 'flex: 1; min-width: 320px;' }, [
        el('div', { style: 'display: flex; gap: 0.4rem; margin-bottom: 0.35rem; flex-wrap: wrap; align-items: center;' }, [
          el('span', {
            style: 'font-size: 10.5px; font-weight: 700; color: #1e293b; background: #f1f5f9; border: 1px solid #cbd5e1; padding: 0.1rem 0.45rem; border-radius: 3px;',
          }, p.dept),
          el('span', {
            style: 'font-size: 10.5px; font-weight: 600; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.1rem 0.45rem; border-radius: 3px;',
          }, p.level),
        ]),
        el('h3', {
          style: 'font-family: var(--font-heading); font-size: 14.5px; font-weight: 700; color: #004482; margin: 0 0 0.35rem; cursor: pointer; line-height: 1.4;',
          onClick: () => navigate(`/thu-tuc/${p.id}`),
        }, p.name),
        el('div', {
          style: 'display: flex; gap: 1.25rem; font-size: 12px; color: #475569; flex-wrap: wrap;',
        }, [
          el('span', {}, `Thời hạn: ${p.deadline}`),
          el('span', { class: 'numeric-data' }, `Lệ phí: ${p.fee}`),
        ]),
      ]),
      el('div', { style: 'display: flex; align-items: center; gap: 0.5rem;' }, [
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
              openAuthModal('login', () => navigate(`/nop-ho-so?id=${p.id}`));
            } else {
              navigate(`/nop-ho-so?id=${p.id}`);
            }
          },
        }, 'Nộp trực tuyến'),
      ]),
    ]));

    listContainer.replaceChildren(...items);
  }

  const tabsRow = el('div', {
    style: 'display: flex; gap: 0.25rem; background: #f1f5f9; padding: 0.2rem; border-radius: 4px;',
  }, filterTabs.map(tab => el('button', {
    type: 'button',
    style: `padding: 0.35rem 0.85rem; font-size: 12px; font-weight: 600; border-radius: 3px; transition: all 0.15s; ${activeFilter === tab.id ? 'background: #004482; color: #ffffff;' : 'background: transparent; color: #475569;'}`,
    onClick: () => {
      activeFilter = tab.id;
      Array.from(tabsRow.children).forEach((btn, idx) => {
        const isCur = filterTabs[idx].id === activeFilter;
        btn.style.background = isCur ? '#004482' : 'transparent';
        btn.style.color = isCur ? '#ffffff' : '#475569';
      });
      renderList();
    },
  }, tab.label)));

  const header = el('div', {
    style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;',
  }, [
    el('div', {}, [
      el('div', { class: 'section-meta-label', style: 'color: #004482; font-size: 11px; font-weight: 700;' }, 'THỰC HIỆN NHIỀU NHẤT'),
    ]),
    tabsRow,
  ]);

  renderList();

  return el('section', { class: 'container-portal', style: 'margin-top: 3rem; margin-bottom: 3.5rem;' }, header, listContainer);
}

import { el } from '../components/dom.js';
import { api } from '../api/client.js';

export function renderHomeFields({ navigate }) {
  const initialFields = [
    { id: 1, name: 'Hộ tịch', count: 4, desc: 'Khai sinh, kết hôn, trích lục hộ tịch, nhận cha mẹ con, giám hộ...' },
    { id: 4, name: 'Đất đai & Tài nguyên', count: 0, desc: 'Biến động quyền sử dụng đất, trích lục địa chính, cấp đổi...' },
    { id: 12, name: 'Xây dựng & Quy hoạch', count: 1, desc: 'Cấp phép xây dựng nhà ở riêng lẻ, mốc giới thiết kế, cải tạo...' },
    { id: 6, name: 'Lao động - TB & Xã hội', count: 0, desc: 'Chính sách người có công, trợ cấp bảo trợ xã hội, mai táng phí...' },
    { id: 5, name: 'Tư pháp & Pháp luật', count: 0, desc: 'Lý lịch tư pháp, khai nhận di sản, hòa giải mâu thuẫn tranh chấp...' },
    { id: 2, name: 'Chứng thực điện tử', count: 3, desc: 'Bản sao điện tử từ bản chính, chứng thực chữ ký, hợp đồng giao dịch...' },
    { id: 3, name: 'Quản lý Cư trú', count: 0, desc: 'Đăng ký thường trú, tạm trú, khai báo lưu trú, khai báo tạm vắng...' },
    { id: 11, name: 'Kinh doanh & Hộ cá thể', count: 1, desc: 'Thành lập hộ kinh doanh cá thể, thay đổi nội dung, tạm ngừng hoạt động...' },
  ];

  const gridContainer = el('div', {
    style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;',
  });

  function renderCards(fieldsList) {
    const cards = fieldsList.map(f => el('div', {
      class: 'card',
      style: 'padding: 1.25rem; display: flex; flex-direction: column; cursor: pointer; min-height: 100px; background: #ffffff; border: 1px solid #d0d7de; border-radius: 4px; transition: border-color 0.15s, box-shadow 0.15s;',
      onClick: () => navigate(`/thu-tuc?field_id=${f.id}`),
    }, [
      el('div', { style: 'display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.5rem;' }, [
        el('h3', { style: 'font-family: var(--font-heading); font-size: 15px; font-weight: 700; color: #004482; margin: 0;' }, f.name),
        el('span', {
          style: 'font-size: 11px; font-weight: 600; padding: 0.15rem 0.5rem; border-radius: 3px; border: 1px solid #cbd5e1; background: #f8fafc; color: #334155; white-space: nowrap;',
        }, `${f.count} thủ tục`),
      ]),
      el('p', { style: 'font-size: 12.5px; color: #475569; line-height: 1.5; margin: 0;' }, f.desc),
    ]));

    gridContainer.replaceChildren(...cards);
  }

  const header = el('div', {
    style: 'display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;',
  }, [
    el('div', {}, [
      el('div', { class: 'section-meta-label', style: 'color: #004482; font-size: 11px; font-weight: 700;' }, 'DANH MỤC LĨNH VỰC QUẢN LÝ'),
    ]),
    el('a', {
      href: '/thu-tuc',
      style: 'font-size: 12.5px; font-weight: 700; color: #004482; text-decoration: none;',
      onClick: (e) => { e.preventDefault(); navigate('/thu-tuc'); },
    }, 'Xem tất cả lĩnh vực'),
  ]);

  renderCards(initialFields);

  Promise.all([
    api.get('/public/fields'),
    api.get('/public/procedures?per_page=100'),
  ]).then(([fieldsRes, procsRes]) => {
    if (fieldsRes?.data && procsRes?.data) {
      const countMap = {};
      procsRes.data.forEach(p => {
        countMap[p.field_id] = (countMap[p.field_id] || 0) + 1;
      });
      const updated = initialFields.map(f => ({
        ...f,
        count: countMap[f.id] ?? f.count,
      }));
      renderCards(updated);
    }
  }).catch(() => {});

  return el('section', { class: 'container-portal', style: 'margin-top: 3rem;' }, header, gridContainer);
}

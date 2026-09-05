import { el } from './dom.js';
import { api } from '../api/client.js';

/**
 * Opens a modal drawer showing full procedure details
 *
 * @param {number|string} procedureId
 */
export async function openProcedureDetailModal(procedureId) {
  const overlay = el('div', {
    class: 'modal-overlay',
    style: 'position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 1rem;',
  });

  const box = el('div', {
    class: 'card',
    style: 'background: #fff; width: 100%; max-width: 800px; max-height: 85vh; display: flex; flex-direction: column; border-radius: 6px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);',
  });

  const headerNode = el('div', {
    style: 'padding: 1rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;',
  }, [
    el('h3', { style: 'font-size: 16px; font-weight: 800; color: #004482; margin: 0;' }, 'CHI TIẾT THỦ TỤC HÀNH CHÍNH'),
    el('button', {
      type: 'button',
      style: 'background: none; border: none; font-size: 20px; cursor: pointer; color: #94a3b8; font-weight: 700; line-height: 1;',
      onClick: () => overlay.remove(),
    }, '✕'),
  ]);

  const contentNode = el('div', {
    style: 'padding: 1.5rem; overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 1.25rem;',
  }, [
    el('div', { style: 'text-align: center; color: #64748b; padding: 2rem;' }, 'Đang tải thông tin chi tiết...'),
  ]);

  box.append(headerNode, contentNode);
  overlay.append(box);
  document.body.append(overlay);

  try {
    const res = await api.get(`/admin/procedures/${procedureId}`);
    const p = res?.data;
    if (!p) throw new Error('Không có dữ liệu chi tiết.');

    contentNode.innerHTML = '';
    contentNode.append(
      el('div', {}, [
        el('div', { style: 'display: flex; gap: 0.5rem; margin-bottom: 0.5rem; flex-wrap: wrap;' }, [
          el('span', { style: 'font-size: 11px; font-weight: 700; background: #eff6ff; color: #004482; border: 1px solid #bfdbfe; padding: 0.15rem 0.5rem; border-radius: 3px;' }, `MÃ: TTHC-${p.id}`),
          el('span', { style: 'font-size: 11px; font-weight: 600; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.15rem 0.5rem; border-radius: 3px;' }, p.field_name || '–'),
          el('span', { style: 'font-size: 11px; font-weight: 600; background: #dcfce7; color: #15803d; padding: 0.15rem 0.5rem; border-radius: 3px;' }, p.status || 'Công khai'),
        ]),
        el('h2', { style: 'font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 1rem 0; line-height: 1.4;' }, p.name),
      ]),
      createDetailSection('1. THÔNG TIN CHUNG', [
        { label: 'Cơ quan giải quyết', val: p.agency },
        { label: 'Đối tượng thực hiện', val: p.target },
        { label: 'Kết quả thực hiện', val: p.result },
      ]),
      createDetailSection('2. TRÌNH TỰ THỰC HIỆN', [{ label: 'Các bước', val: p.instructions }]),
      createDetailSection('3. YÊU CẦU, ĐIỀU KIỆN', [{ label: 'Điều kiện', val: p.requirements }]),
      createDetailSection('4. CĂN CỨ PHÁP LÝ', [{ label: 'Văn bản quy định', val: p.legal_basis }])
    );

    if (p.methods && p.methods.length) {
      contentNode.append(
        el('div', {}, [
          el('h4', { style: 'font-size: 13px; font-weight: 800; color: #004482; text-transform: uppercase; margin: 0 0 0.5rem 0;' }, '5. CÁCH THỨC THỰC HIỆN'),
          el('ul', { style: 'margin: 0; padding-left: 1.25rem; font-size: 13px; color: #334155; line-height: 1.6;' },
            p.methods.map((m) => el('li', {}, `${m.channel}: ${m.resolution_time || `${m.duration} ngày`} — ${m.description || 'Không có mô tả thêm'}`))
          ),
        ])
      );
    }

    if (p.fees && p.fees.length) {
      contentNode.append(
        el('div', {}, [
          el('h4', { style: 'font-size: 13px; font-weight: 800; color: #004482; text-transform: uppercase; margin: 0 0 0.5rem 0;' }, '6. PHÍ & LỆ PHÍ'),
          el('ul', { style: 'margin: 0; padding-left: 1.25rem; font-size: 13px; color: #334155; line-height: 1.6;' },
            p.fees.map((f) => el('li', {}, `${f.type}: ${Number(f.amount || 0).toLocaleString('vi-VN')} đ (Bắt buộc: ${f.required || 'Có'})`))
          ),
        ])
      );
    }
  } catch (err) {
    contentNode.innerHTML = '';
    contentNode.append(
      el('div', { style: 'padding: 2rem; text-align: center; color: #dc2626; font-weight: 600;' },
        `Lỗi tải chi tiết: ${err.message || 'Không thể kết nối máy chủ'}`
      )
    );
  }
}

function createDetailSection(title, rows) {
  return el('div', { style: 'border: 1px solid #e2e8f0; border-radius: 4px; padding: 0.85rem 1rem; background: #fcfcfc;' }, [
    el('h4', { style: 'font-size: 12.5px; font-weight: 800; color: #004482; margin: 0 0 0.6rem 0;' }, title),
    el('div', { style: 'display: flex; flex-direction: column; gap: 0.5rem; font-size: 13px;' },
      rows.map((r) => el('div', { style: 'display: flex; gap: 0.5rem; line-height: 1.5;' }, [
        el('span', { style: 'font-weight: 700; color: #475569; min-width: 140px;' }, `${r.label}:`),
        el('span', { style: 'color: #1e293b; flex: 1; word-break: break-word;' }, r.val || '–'),
      ]))
    ),
  ]);
}

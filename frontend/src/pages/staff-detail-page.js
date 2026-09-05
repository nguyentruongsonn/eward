import { el } from '../components/dom.js';
import { api, getAuthToken, getStoredUser } from '../api/client.js';
import { normalizeStaffRole } from '../components/staff-nav.js';
import { createStaffActionButtons } from '../components/staff-detail-actions.js';
import { renderStaffInfoTab } from '../components/staff-detail-info-tab.js';
import { renderStaffDossierTab } from '../components/staff-detail-dossier-tab.js';
import { renderStaffFeeTab } from '../components/staff-detail-fee-tab.js';
import { renderStaffResultTab } from '../components/staff-detail-result-tab.js';
import { renderStaffAuditSection } from '../components/staff-detail-audit.js';
import { openStaffWorkflowModal } from '../components/staff-workflow-diagram.js';

function remaining(dueAt, sid) {
  if (sid === 10) return { text: 'Đã hoàn thành', color: '#15803d', bg: '#dcfce7' };
  if (!dueAt) return { text: 'Chờ tiếp nhận', color: '#64748b', bg: '#f1f5f9' };
  const d = Math.round((new Date(dueAt) - new Date()) / 86400000);
  if (d < 0) return { text: `Quá hạn ${Math.abs(d)} ngày`, color: '#b91c1c', bg: '#fee2e2' };
  return d === 0 ? { text: 'Hạn chót hôm nay', color: '#b45309', bg: '#fef3c7' } : { text: `Còn ${d} ngày`, color: '#15803d', bg: '#dcfce7' };
}

const STATUS_STYLE = {
  1: { bg: '#f8fafc', color: '#475569', border: '#cbd5e1', label: 'Chờ tiếp nhận' },
  2: { bg: '#f0f7ff', color: '#0369a1', border: '#bae6fd', label: 'Đang thụ lý' },
  3: { bg: '#fef2f2', color: '#b91c1c', border: '#fecaca', label: 'Từ chối tiếp nhận' },
  4: { bg: '#eff6ff', color: '#1d4ed8', border: '#bfdbfe', label: 'Chờ phê duyệt' },
  5: { bg: '#fffbeb', color: '#b45309', border: '#fed7aa', label: 'Yêu cầu bổ sung' },
  6: { bg: '#f0fdf4', color: '#166534', border: '#bbf7d0', label: 'Đã bổ sung giấy tờ' },
  9: { bg: '#f0fdf4', color: '#15803d', border: '#bbf7d0', label: 'Đã xử lý xong' },
  10: { bg: '#f0fdf4', color: '#15803d', border: '#bbf7d0', label: 'Đã trả kết quả' },
  12: { bg: '#fef2f2', color: '#b91c1c', border: '#fca5a5', label: 'Yêu cầu xử lý lại' },
};
const cell = (lbl, val) => el('div', {}, [el('span', { style: 'color: #64748b; font-size: 11px; display: block;' }, lbl), typeof val === 'string' ? el('strong', { style: 'color: #0f172a;' }, val || '-') : val]);

export function renderStaffDetailPage({ params, navigate }) {
  const appId = decodeURIComponent(params?.id || '');
  const container = el('div', { class: 'staff-workspace-wrapper', style: 'padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;' });

  if (!getAuthToken()) {
    container.append(el('div', { class: 'card', style: 'padding: 2.5rem; text-align: center;' }, [
      el('h3', { style: 'color: #b91c1c; font-weight: 800; margin-bottom: 0.5rem;' }, 'YÊU CẦU ĐĂNG NHẬP'),
      el('button', { class: 'btn btn-primary', onClick: () => navigate('/dang-nhap') }, 'Đăng nhập ngay'),
    ]));
    return container;
  }

  const contentArea = el('div', { style: 'margin-top: 0.5rem;' }, [
    el('div', { style: 'text-align: center; padding: 3rem; color: #64748b;' }, 'Đang tải thông tin chi tiết hồ sơ...'),
  ]);

  async function handleAction(act, payload) {
    const ep = { accept: 'accept', reject: 'reject', confirmReception: 'confirm-reception', forward: 'forward', approve: 'approve', rework: 'rework', deliver: 'deliver', requestSupplement: 'supplement-requests' };
    if (!ep[act]) throw new Error('Hành động không được hỗ trợ');
    await api.post(`/admin/applications/${appId}/${ep[act]}`, payload);
    await loadDetail();
  }

  function renderDetail(app) {
    const sid = Number(app.status?.id ?? 1);
    const statusObj = STATUS_STYLE[sid] || { bg: '#f1f5f9', color: '#475569', border: '#cbd5e1', label: 'Đang xử lý' };
    const statusName = app.status?.name || app.status?.label || statusObj.label;
    const rem = remaining(app.due_at, sid);
    const isReceived = sid >= 2;

    const user = getStoredUser();
    const role = normalizeStaffRole(user?.vaiTro || user?.role);
    const isCaseOfficer = role === 'case-officer';

    const actionButtons = createStaffActionButtons({ app, role, handleAction, loadDetail });

    const workflowBtn = el('button', {
      type: 'button', class: 'btn btn-secondary btn-sm',
      style: 'height: 32px; font-size: 12px; font-weight: 700; border: 1px solid #cbd5e1; padding: 0 0.85rem; background: #ffffff; color: #004b87;',
      onClick: () => openStaffWorkflowModal(app),
    }, 'Quy trình');

    const actionToolbar = el('div', {
      class: 'card',
      style: 'padding: 0.75rem 1.25rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(15,23,42,0.04); margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;',
    }, [
      el('div', { style: 'display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;' }, [
        el('span', { style: 'font-size: 11px; font-weight: 800; color: #004b87; text-transform: uppercase; letter-spacing: 0.03em; margin-right: 0.25rem;' }, 'Thao tác nghiệp vụ:'),
        ...actionButtons,
        workflowBtn,
        actionButtons.length === 0 ? el('span', { style: 'font-size: 12.5px; color: #64748b; font-style: italic;' }, 'Không có thao tác khả dụng cho vai trò tại bước này.') : null,
      ]),
      el('div', { style: 'display: flex; gap: 0.5rem; align-items: center;' }, [
        el('button', { type: 'button', style: 'height: 32px; font-size: 12px; font-weight: 700; background: #004b87; color: #ffffff; border: none; padding: 0 0.85rem; border-radius: 4px; cursor: pointer;', onClick: () => navigate('/can-bo/ho-so') }, 'Danh sách hồ sơ'),
        el('button', { type: 'button', style: 'height: 32px; font-size: 12px; font-weight: 700; background: #004b87; color: #ffffff; border: none; padding: 0 0.85rem; border-radius: 4px; cursor: pointer;', onClick: () => loadDetail() }, 'Làm mới ⟳'),
      ]),
    ]);

    const supplementBanner = app.supplement_request ? el('div', {
      class: 'card', style: 'padding: 0.85rem 1.25rem; background: #fffbeb; border: 1px solid #fed7aa; border-left: 4px solid #d97706; border-radius: 8px; box-shadow: 0 1px 3px rgba(15,23,42,0.04); margin-bottom: 1rem;',
    }, [
      el('h4', { style: 'color: #92400e; font-size: 12.5px; font-weight: 800; margin: 0 0 0.25rem;' }, 'YÊU CẦU BỔ SUNG GIẤY TỜ'),
      el('p', { style: 'font-size: 12px; color: #78350f; margin: 0;' }, app.supplement_request.note || 'Cần bổ sung tài liệu để hoàn thiện hồ sơ.'),
    ]) : null;

    const isSupplemented = sid === 6 || String(app.notes || '').includes('Công dân đã bổ sung');
    const supplementedBanner = isSupplemented ? el('div', {
      class: 'card',
      style: 'padding: 0.85rem 1.25rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 8px; box-shadow: 0 1px 3px rgba(15,23,42,0.04); margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;',
    }, [
      el('div', {}, [
        el('div', { style: 'display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.2rem;' }, [
          el('span', { style: 'font-size: 10.5px; font-weight: 800; color: #166534; background: #dcfce7; padding: 0.15rem 0.5rem; border-radius: 3px; text-transform: uppercase;' }, 'THÔNG BÁO'),
          el('span', { style: 'font-size: 12.5px; font-weight: 800; color: #15803d;' }, 'CÔNG DÂN ĐÃ NỘP BỔ SUNG HỒ SƠ'),
        ]),
        el('p', { style: 'font-size: 12px; color: #166534; margin: 0;' }, 'Công dân đã hoàn thành bổ sung tài liệu. Cán bộ thụ lý kiểm tra tài liệu và tiếp tục thẩm định.'),
      ]),
      el('button', {
        type: 'button', class: 'btn btn-primary btn-sm',
        style: 'background: #166534; font-weight: 700; font-size: 12px; border: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 0.25rem;',
        onClick: () => switchTab('dossier'),
      }, [
        'Xem tài liệu bổ sung',
        el('span', { class: 'material-symbols-outlined', style: 'font-size: 16px;' }, 'arrow_forward'),
      ]),
    ]) : null;

    const headerCard = el('div', { class: 'card', style: 'padding: 1.25rem 1.5rem; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 3px rgba(15,23,42,0.04); margin-bottom: 1.25rem;' }, [
      el('div', { style: 'display: flex; align-items: flex-start; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.85rem;' }, [
        el('span', { style: 'font-size: 11px; font-weight: 800; color: #004b87; background: #eff6ff; border: 1px solid #bfdbfe; padding: 0.2rem 0.6rem; border-radius: 4px;' }, `MÃ HỒ SƠ: ${app.id}`),
        app.procedure_field ? el('span', { style: 'font-size: 11px; font-weight: 600; color: #475569; background: #f1f5f9; border: 1px solid #e2e8f0; padding: 0.2rem 0.5rem; border-radius: 4px;' }, app.procedure_field) : null,
      ]),
      el('h2', { style: 'font-family: var(--font-heading); font-size: 18px; font-weight: 800; color: #004b87; margin: 0 0 0.85rem;' }, app.procedure_name || 'Thủ tục hành chính'),
      el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.65rem; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12.5px; margin-bottom: 0.85rem;' }, [
        cell('Chủ hồ sơ:', app.applicant_name), cell('Số CCCD / ĐD:', app.applicant_id_card),
        cell('Số điện thoại:', app.applicant_phone), cell('Email:', app.applicant_email),
      ]),
      ...(isReceived ? [
        el('div', { style: 'display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 0.85rem; border-top: 1px solid #f1f5f9; padding-top: 0.85rem;' }, [
          el('div', { style: 'border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.75rem 1rem; background: #ffffff;' }, [
            el('div', { style: 'font-size: 11.5px; font-weight: 800; color: #004b87; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 0.5rem;' }, 'Thời gian xử lý'),
            el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 12.5px;' }, [
              cell('Ngày tiếp nhận:', app.received_at ? app.received_at.split('T')[0] : '-'), cell('Ngày hẹn trả:', app.due_at ? app.due_at.split('T')[0] : '-'),
              cell('Thời gian còn lại:', el('span', { style: `font-weight: 700; font-size: 11.5px; padding: 0.15rem 0.5rem; border-radius: 3px; background: ${rem.bg}; color: ${rem.color}; display: inline-block;` }, rem.text)),
              cell('Ngày trả thực tế:', app.delivered_at ? app.delivered_at.split('T')[0] : '-'), cell('Hình thức:', app.delivery_method || 'Trực tiếp tại Một cửa'),
              cell('Tình trạng:', el('span', { style: `font-weight: 700; font-size: 11.5px; padding: 0.15rem 0.55rem; border-radius: 3px; background: ${statusObj.bg}; color: ${statusObj.color}; border: 1px solid ${statusObj.border}; display: inline-block;` }, statusName)),
            ]),
          ]),
          el('div', { style: 'border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.75rem 1rem; background: #ffffff;' }, [
            el('div', { style: 'font-size: 11.5px; font-weight: 800; color: #004b87; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 0.5rem;' }, 'Người xử lý & Phân công'),
            el('div', { style: 'display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 12.5px;' }, [
              cell('Cán bộ tiếp nhận:', app.receiver_name || 'Bộ phận Một cửa'), cell('Cán bộ thụ lý:', app.processor_name || (sid >= 3 ? 'Chuyên viên chuyên môn' : 'Chưa phân công')),
              cell('Người phê duyệt:', app.approver_name || (sid >= 6 ? 'Chủ tịch / Phó Chủ tịch UBND' : 'Chờ duyệt')), cell('Đơn vị giải quyết:', app.department || 'UBND Xã/Phường'),
            ]),
          ]),
        ]),
      ] : []),
    ]);

    const tabs = [
      { id: 'info', label: 'Thông tin chung' }, { id: 'dossier', label: 'Thành phần hồ sơ' },
      { id: 'fee', label: 'Phí, lệ phí' }, { id: 'result', label: 'Hình thức nhận kết quả' },
    ];
    const tabBtns = el('div', { style: 'display: flex; width: 100%; box-sizing: border-box; gap: 0.4rem; background: #f0f7ff; border: 1px solid #bfdbfe; padding: 0.35rem; border-radius: 8px; margin-bottom: 1.25rem; overflow-x: auto;' });
    const tabBody = el('div', { class: 'tab-content-area' });

    function switchTab(tid) {
      tabBtns.replaceChildren(...tabs.map(t => {
        const on = t.id === tid;
        return el('button', {
          type: 'button',
          style: `flex: 1; text-align: center; justify-content: center; padding: 0.6rem 1rem; font-size: 13px; font-weight: 700; color: ${on ? '#ffffff' : '#004b87'}; border: 1px solid ${on ? '#004b87' : '#bae6fd'}; background: ${on ? '#004b87' : '#e0f2fe'}; box-shadow: ${on ? '0 2px 4px rgba(0,75,135,0.2)' : 'none'}; cursor: pointer; border-radius: 6px; white-space: nowrap; transition: all 0.15s ease;`,
          onClick: () => switchTab(t.id),
        }, t.label);
      }));
      const renders = { info: () => renderStaffInfoTab(app), dossier: () => renderStaffDossierTab(app), fee: () => renderStaffFeeTab(app), result: () => renderStaffResultTab(app, loadDetail) };
      tabBody.replaceChildren((renders[tid] || renders.info)());
    }

    switchTab('info');
    const layout = [actionToolbar];
    if (supplementBanner) layout.push(supplementBanner);
    if (supplementedBanner) layout.push(supplementedBanner);
    layout.push(headerCard, tabBtns, tabBody, renderStaffAuditSection(app, loadDetail));
    contentArea.replaceChildren(...layout);
  }

  async function loadDetail() {
    try {
      const res = await api.get(`/admin/applications/${appId}`);
      if (res?.data) renderDetail(res.data);
      else contentArea.replaceChildren(el('div', { class: 'card', style: 'padding: 2rem; color: #b91c1c; text-align: center;' }, [el('h3', {}, 'Không tìm thấy hồ sơ'), el('button', { class: 'btn btn-secondary', onClick: () => navigate('/can-bo/ho-so') }, 'Về Danh sách hồ sơ')]));
    } catch (err) {
      contentArea.replaceChildren(el('div', { class: 'card', style: 'padding: 2rem; text-align: center;' }, [el('h3', { style: 'color: #b91c1c; font-weight: 800;' }, 'KHÔNG THỂ TẢI THÔNG TIN HỒ SƠ'), el('p', { style: 'color: #64748b; margin: 0.5rem 0 1rem;' }, err.message || 'Lỗi kết nối máy chủ.'), el('button', { class: 'btn btn-primary', style: 'background: #004482;', onClick: () => loadDetail() }, 'Thử lại ⟳')]));
    }
  }

  loadDetail();
  container.append(contentArea);
  return container;
}

